import os
import sqlite3

# On Railway with a mounted /data Volume use that path; otherwise use cwd.
_DATA_DIR = "/data" if os.path.isdir("/data") else os.path.dirname(os.path.abspath(__file__))
DB_PATH = os.path.join(_DATA_DIR, "cot_data.db")

CREATE_TABLE_SQL = """
CREATE TABLE IF NOT EXISTS cot_data (
    id             INTEGER PRIMARY KEY AUTOINCREMENT,
    market_key     TEXT    NOT NULL,
    report_date    TEXT    NOT NULL,
    comm_long      INTEGER DEFAULT 0,
    comm_short     INTEGER DEFAULT 0,
    comm_net       INTEGER DEFAULT 0,
    spec_long      INTEGER DEFAULT 0,
    spec_short     INTEGER DEFAULT 0,
    spec_net       INTEGER DEFAULT 0,
    small_long     INTEGER DEFAULT 0,
    small_short    INTEGER DEFAULT 0,
    small_net      INTEGER DEFAULT 0,
    open_interest  INTEGER DEFAULT 0,
    UNIQUE(market_key, report_date)
);
"""
CREATE_INDEX_SQL = """
CREATE INDEX IF NOT EXISTS idx_market_date ON cot_data(market_key, report_date);
"""


def _connect() -> sqlite3.Connection:
    conn = sqlite3.connect(DB_PATH)
    conn.row_factory = sqlite3.Row
    return conn


def init_db() -> None:
    db_dir = os.path.dirname(DB_PATH)
    if db_dir:
        os.makedirs(db_dir, exist_ok=True)
    conn = _connect()
    try:
        conn.execute(CREATE_TABLE_SQL)
        conn.execute(CREATE_INDEX_SQL)
        conn.commit()
    finally:
        conn.close()


def upsert_batch(records: list) -> None:
    """INSERT OR REPLACE records in batches of 500."""
    if not records:
        return
    conn = _connect()
    try:
        sql = """
            INSERT OR REPLACE INTO cot_data
                (market_key, report_date,
                 comm_long, comm_short, comm_net,
                 spec_long, spec_short, spec_net,
                 small_long, small_short, small_net,
                 open_interest)
            VALUES
                (:market_key, :report_date,
                 :comm_long, :comm_short, :comm_net,
                 :spec_long, :spec_short, :spec_net,
                 :small_long, :small_short, :small_net,
                 :open_interest)
        """
        for i in range(0, len(records), 500):
            conn.executemany(sql, records[i : i + 500])
            conn.commit()
    finally:
        conn.close()


def get_series(market_key: str, weeks: int) -> dict:
    """
    Return time-series data for a market.
    COT Index is calculated on the full history so that even the tail
    of 'weeks' rows has a properly seeded rolling window.
    """
    from scraper import cot_index as calc_cot_index  # lazy — avoid circular import

    conn = _connect()
    try:
        rows = conn.execute(
            """
            SELECT report_date,
                   comm_long, comm_short, comm_net,
                   spec_long, spec_short, spec_net,
                   small_long, small_short, small_net,
                   open_interest
            FROM cot_data
            WHERE market_key = ?
            ORDER BY report_date ASC
            """,
            (market_key,),
        ).fetchall()

        if not rows:
            return {}

        all_comm_net   = [r["comm_net"]  for r in rows]
        all_small_net  = [r["small_net"] for r in rows]
        all_cot_52     = calc_cot_index(all_comm_net,  52)
        all_cot_26     = calc_cot_index(all_comm_net,  26)
        all_retail_52  = calc_cot_index(all_small_net, 52)
        all_retail_26  = calc_cot_index(all_small_net, 26)

        # Trim to requested window
        rows          = rows[-weeks:]
        all_cot_52    = all_cot_52[-weeks:]
        all_cot_26    = all_cot_26[-weeks:]
        all_retail_52 = all_retail_52[-weeks:]
        all_retail_26 = all_retail_26[-weeks:]

        return {
            "dates":          [r["report_date"]  for r in rows],
            "comm_long":      [r["comm_long"]     for r in rows],
            "comm_short":     [r["comm_short"]    for r in rows],
            "comm_net":       [r["comm_net"]      for r in rows],
            "spec_long":      [r["spec_long"]     for r in rows],
            "spec_short":     [r["spec_short"]    for r in rows],
            "spec_net":       [r["spec_net"]      for r in rows],
            "small_long":     [r["small_long"]    for r in rows],
            "small_short":    [r["small_short"]   for r in rows],
            "small_net":      [r["small_net"]     for r in rows],
            "open_interest":  [r["open_interest"] for r in rows],
            "cot_index":      all_cot_52,
            "cot_index_26":   all_cot_26,
            "retail_index":   all_retail_52,
            "retail_index_26": all_retail_26,
        }
    finally:
        conn.close()


def get_all_latest() -> list:
    """
    Return the latest record for every tracked market, enriched with
    COT Index (52-week), week-over-week change, display name, and category.
    """
    from scraper import cot_index as calc_cot_index, DISPLAY_NAMES, CATEGORIES  # lazy

    conn = _connect()
    try:
        market_keys = [
            r["market_key"]
            for r in conn.execute(
                "SELECT DISTINCT market_key FROM cot_data"
            ).fetchall()
        ]

        results = []
        for key in market_keys:
            # Skip markets removed from current CATEGORIES (e.g. old DB data)
            if key not in CATEGORIES:
                continue

            # Fetch last 53 rows (need ≥52 to compute index for latest row)
            rows = conn.execute(
                """
                SELECT report_date, comm_net, spec_net, small_net, open_interest
                FROM cot_data
                WHERE market_key = ?
                ORDER BY report_date DESC
                LIMIT 53
                """,
                (key,),
            ).fetchall()

            if not rows:
                continue

            rows = list(reversed(rows))  # chronological order

            comm_net_vals  = [r["comm_net"] for r in rows]
            cot_indices_52 = calc_cot_index(comm_net_vals, 52)
            cot_indices_26 = calc_cot_index(comm_net_vals, 26)

            latest = rows[-1]
            prev   = rows[-2] if len(rows) >= 2 else rows[-1]

            results.append({
                "market_key":   key,
                "name":         DISPLAY_NAMES.get(key, key),
                "category":     CATEGORIES.get(key, "other"),
                "comm_net":     latest["comm_net"],
                "spec_net":     latest["spec_net"],
                "small_net":    latest["small_net"],
                "open_interest":latest["open_interest"],
                "cot_index_52": cot_indices_52[-1],
                "cot_index_26": cot_indices_26[-1],
                "change_comm":  latest["comm_net"]  - prev["comm_net"],
                "change_spec":  latest["spec_net"]  - prev["spec_net"],
                "change_small": latest["small_net"] - prev["small_net"],
                "last_date":    latest["report_date"],
            })

        # Sort: category order then alphabetically by display name
        cat_order = {
            "forex": 0, "crypto": 1, "indices": 2, "bonds": 3,
            "energy": 4, "metals": 5, "grains": 6, "softs": 7, "livestock": 8,
        }
        results.sort(key=lambda x: (cat_order.get(x["category"], 99), x["name"]))
        return results
    finally:
        conn.close()


def get_stats() -> dict:
    """Return high-level database statistics."""
    conn = _connect()
    try:
        total   = conn.execute("SELECT COUNT(*) AS c FROM cot_data").fetchone()["c"]
        markets = conn.execute(
            "SELECT COUNT(DISTINCT market_key) AS c FROM cot_data"
        ).fetchone()["c"]
        last    = conn.execute(
            "SELECT MAX(report_date) AS d FROM cot_data"
        ).fetchone()["d"]

        db_size_mb = 0.0
        if os.path.exists(DB_PATH):
            db_size_mb = round(os.path.getsize(DB_PATH) / (1024 * 1024), 2)

        return {
            "total_records":  total,
            "markets_count":  markets,
            "last_update":    last,
            "db_size_mb":     db_size_mb,
        }
    finally:
        conn.close()
