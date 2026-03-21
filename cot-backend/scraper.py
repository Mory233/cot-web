import requests
import zipfile
import io
import csv
from datetime import datetime

# ── Legacy COT Futures-Only report (deacot{year}.zip / annual.txt)
#
# Source: https://www.cftc.gov/files/dea/history/deacot{year}.zip
# Covers ALL markets (forex, indices, bonds, crypto, commodities) in one file.
# Available from 1986 onward.
#
# Column mapping (looked up by header name):
#   "Market and Exchange Names"               → market name
#   "As of Date in Form YYYY-MM-DD"           → report date
#   "Open Interest (All)"                     → open interest
#   "Noncommercial Positions-Long (All)"      → Large Spec long
#   "Noncommercial Positions-Short (All)"     → Large Spec short
#   "Commercial Positions-Long (All)"         → Commercials long
#   "Commercial Positions-Short (All)"        → Commercials short
#   "Nonreportable Positions-Long (All)"      → Small Traders long
#   "Nonreportable Positions-Short (All)"     → Small Traders short

MARKETS = {
    # ── Forex ─────────────────────────────────────────────────────────────────
    "EURO FX - CHICAGO MERCANTILE EXCHANGE":                    "eurusd",
    "BRITISH POUND - CHICAGO MERCANTILE EXCHANGE":              "gbpusd",
    "JAPANESE YEN - CHICAGO MERCANTILE EXCHANGE":               "jpyusd",
    "SWISS FRANC - CHICAGO MERCANTILE EXCHANGE":                "chfusd",
    "CANADIAN DOLLAR - CHICAGO MERCANTILE EXCHANGE":            "cadusd",
    "AUSTRALIAN DOLLAR - CHICAGO MERCANTILE EXCHANGE":          "audusd",
    "NZ DOLLAR - CHICAGO MERCANTILE EXCHANGE":                  "nzdusd",
    "MEXICAN PESO - CHICAGO MERCANTILE EXCHANGE":               "mxnusd",
    "BRAZILIAN REAL - CHICAGO MERCANTILE EXCHANGE":             "brlusd",
    "USD INDEX - ICE FUTURES U.S.":                             "dxy",
    # ── Crypto ────────────────────────────────────────────────────────────────
    "BITCOIN - CHICAGO MERCANTILE EXCHANGE":                    "bitcoin",
    "MICRO BITCOIN - CHICAGO MERCANTILE EXCHANGE":              "bitcoin",
    "ETHER CASH SETTLED - CHICAGO MERCANTILE EXCHANGE":         "ethereum",
    "MICRO ETHER - CHICAGO MERCANTILE EXCHANGE":                "ethereum",
    "SOL - CHICAGO MERCANTILE EXCHANGE":                        "solana",
    "MICRO SOL - CHICAGO MERCANTILE EXCHANGE":                  "solana",
    "XRP - CHICAGO MERCANTILE EXCHANGE":                        "xrp",
    "MICRO XRP - CHICAGO MERCANTILE EXCHANGE":                  "xrp",
    # ── Indices ───────────────────────────────────────────────────────────────
    "S&P 500 CONSOLIDATED - CHICAGO MERCANTILE EXCHANGE":       "sp500",
    "E-MINI S&P 500 - CHICAGO MERCANTILE EXCHANGE":             "sp500",
    "NASDAQ-100 CONSOLIDATED - CHICAGO MERCANTILE EXCHANGE":    "nasdaq",
    "NASDAQ-100 Consolidated - CHICAGO MERCANTILE EXCHANGE":    "nasdaq",
    "DJIA CONSOLIDATED - CHICAGO BOARD OF OPTIONS EXCHANGE":    "djia",
    "DJIA Consolidated - CHICAGO BOARD OF TRADE":               "djia",
    "RUSSELL E-MINI - CHICAGO MERCANTILE EXCHANGE":             "russell2000",
    # ── Bonds ─────────────────────────────────────────────────────────────────
    "UST BOND - CHICAGO BOARD OF TRADE":                        "tbond30y",
    "UST 10Y NOTE - CHICAGO BOARD OF TRADE":                    "tnotes10y",
    "ULTRA UST BOND - CHICAGO BOARD OF TRADE":                  "ultra_tbond",
    "UST 5Y NOTE - CHICAGO BOARD OF TRADE":                     "tnotes5y",
    "UST 2Y NOTE - CHICAGO BOARD OF TRADE":                     "tnotes2y",
    "FED FUNDS - CHICAGO BOARD OF TRADE":                       "fedfunds",
    "SOFR-3M - CHICAGO MERCANTILE EXCHANGE":                    "sofr3m",
    # ── Energy ────────────────────────────────────────────────────────────────
    "WTI-PHYSICAL - NEW YORK MERCANTILE EXCHANGE":              "crudeoil",
    "NAT GAS NYME - NEW YORK MERCANTILE EXCHANGE":              "natgas",
    "GASOLINE RBOB - NEW YORK MERCANTILE EXCHANGE":             "gasoline",
    # ── Metals ────────────────────────────────────────────────────────────────
    "GOLD - COMMODITY EXCHANGE INC.":                           "gold",
    "SILVER - COMMODITY EXCHANGE INC.":                         "silver",
    "COPPER- #1 - COMMODITY EXCHANGE INC.":                     "copper",
    "PLATINUM - NEW YORK MERCANTILE EXCHANGE":                  "platinum",
    "PALLADIUM - NEW YORK MERCANTILE EXCHANGE":                 "palladium",
    # ── Grains ────────────────────────────────────────────────────────────────
    "WHEAT-SRW - CHICAGO BOARD OF TRADE":                       "wheat",
    "CORN - CHICAGO BOARD OF TRADE":                            "corn",
    "SOYBEANS - CHICAGO BOARD OF TRADE":                        "soybeans",
    "SOYBEAN MEAL - CHICAGO BOARD OF TRADE":                    "soy_meal",
    "SOYBEAN OIL - CHICAGO BOARD OF TRADE":                     "soy_oil",
    "ROUGH RICE - CHICAGO BOARD OF TRADE":                      "roughrice",
    "WHEAT-HRW - CHICAGO BOARD OF TRADE":                       "wheat_hrw",
    # ── Softs ─────────────────────────────────────────────────────────────────
    "COTTON NO. 2 - ICE FUTURES U.S.":                          "cotton",
    "COFFEE C - ICE FUTURES U.S.":                              "coffee",
    "SUGAR NO. 11 - ICE FUTURES U.S.":                          "sugar",
    "COCOA - ICE FUTURES U.S.":                                 "cocoa",
    "LUMBER - CHICAGO MERCANTILE EXCHANGE":                     "lumber",
    # ── Livestock ─────────────────────────────────────────────────────────────
    "LIVE CATTLE - CHICAGO MERCANTILE EXCHANGE":                "livecattle",
    "FEEDER CATTLE - CHICAGO MERCANTILE EXCHANGE":              "feedercattle",
    "LEAN HOGS - CHICAGO MERCANTILE EXCHANGE":                  "leanhogs",
    "CME MILK IV - CHICAGO MERCANTILE EXCHANGE":                "milk3",
    "NON FAT DRY MILK - CHICAGO MERCANTILE EXCHANGE":           "nonfatmilk",
    "BUTTER (CASH SETTLED) - CHICAGO MERCANTILE EXCHANGE":      "butter",
    "CHEESE (CASH-SETTLED) - CHICAGO MERCANTILE EXCHANGE":      "cheese",
}

CATEGORIES = {
    "eurusd": "forex",   "gbpusd": "forex",   "jpyusd": "forex",
    "chfusd": "forex",   "cadusd": "forex",   "audusd": "forex",
    "nzdusd": "forex",   "mxnusd": "forex",   "brlusd": "forex",  "dxy": "forex",
    "bitcoin": "crypto", "ethereum": "crypto", "solana": "crypto", "xrp": "crypto",
    "sp500": "indices",  "nasdaq": "indices",  "djia": "indices",  "russell2000": "indices",
    "tbond30y": "bonds", "tnotes10y": "bonds", "ultra_tbond": "bonds",
    "tnotes5y": "bonds", "tnotes2y": "bonds",  "fedfunds": "bonds", "sofr3m": "bonds",
    "crudeoil": "energy", "gasoline": "energy", "natgas": "energy",
    "gold": "metals",    "silver": "metals",   "copper": "metals",
    "platinum": "metals","palladium": "metals",
    "wheat": "grains",   "corn": "grains",     "soybeans": "grains",
    "soy_meal": "grains","soy_oil": "grains",  "roughrice": "grains", "wheat_hrw": "grains",
    "cotton": "softs",   "coffee": "softs",    "sugar": "softs",
    "cocoa": "softs",    "lumber": "softs",
    "livecattle": "livestock",   "feedercattle": "livestock", "leanhogs": "livestock",
    "milk3": "livestock","nonfatmilk": "livestock", "butter": "livestock", "cheese": "livestock",
}

DISPLAY_NAMES = {
    "eurusd": "Euro FX (EUR)",           "gbpusd": "British Pound (GBP)",     "jpyusd": "Japanese Yen (JPY)",
    "chfusd": "Swiss Franc (CHF)",       "cadusd": "Canadian Dollar (CAD)",   "audusd": "Australian Dollar (AUD)",
    "nzdusd": "New Zealand Dollar (NZD)","mxnusd": "Mexican Peso (MXN)",      "brlusd": "Brazilian Real (BRL)",
    "dxy": "US Dollar Index (DXY)",
    "bitcoin": "Bitcoin (BTC)",   "ethereum": "Ethereum (ETH)",
    "solana": "Solana (SOL)",     "xrp": "XRP",
    "sp500": "S&P 500 E-Mini",    "nasdaq": "Nasdaq 100 E-Mini",
    "djia": "Dow Jones E-Mini",   "russell2000": "Russell 2000 E-Mini",
    "tbond30y": "30Y T-Bond",     "tnotes10y": "10Y T-Note",    "ultra_tbond": "Ultra T-Bond",
    "tnotes5y": "5Y T-Note",      "tnotes2y": "2Y T-Note",
    "fedfunds": "Fed Funds 30D",  "sofr3m": "SOFR 3M",
    "crudeoil": "Crude Oil WTI",  "gasoline": "Gasoline RBOB",  "natgas": "Natural Gas",
    "gold": "Gold",               "silver": "Silver",           "copper": "Copper",
    "platinum": "Platinum",       "palladium": "Palladium",
    "wheat": "Wheat SRW",         "corn": "Corn",               "soybeans": "Soybeans",
    "soy_meal": "Soybean Meal",   "soy_oil": "Soybean Oil",     "roughrice": "Rough Rice",
    "wheat_hrw": "Wheat HRW",
    "cotton": "Cotton",           "coffee": "Coffee",           "sugar": "Sugar No.11",
    "cocoa": "Cocoa",             "lumber": "Lumber",
    "livecattle": "Live Cattle",  "feedercattle": "Feeder Cattle", "leanhogs": "Lean Hogs",
    "milk3": "Milk Class III",    "nonfatmilk": "Nonfat Dry Milk",
    "butter": "Butter",           "cheese": "Cheese",
}

MARKETS_UPPER = {k.strip().upper(): v for k, v in MARKETS.items()}

# Numeric fields that get summed when merging duplicate (market_key, report_date)
_NUMERIC = (
    "comm_long", "comm_short", "comm_net",
    "spec_long", "spec_short", "spec_net",
    "small_long", "small_short", "small_net",
    "open_interest",
)


def _safe_int(val: str) -> int:
    try:
        return int(val.strip().replace(",", ""))
    except (ValueError, AttributeError):
        return 0


def _merge_records(records: list) -> list:
    """Merge duplicate (market_key, report_date) rows by summing numeric fields."""
    merged: dict = {}
    for r in records:
        k = (r["market_key"], r["report_date"])
        if k not in merged:
            merged[k] = dict(r)
        else:
            for field in _NUMERIC:
                merged[k][field] += r[field]
            merged[k]["comm_net"]  = merged[k]["comm_long"]  - merged[k]["comm_short"]
            merged[k]["spec_net"]  = merged[k]["spec_long"]  - merged[k]["spec_short"]
            merged[k]["small_net"] = merged[k]["small_long"] - merged[k]["small_short"]
    return list(merged.values())


def _make_record(market_key, report_date, open_interest,
                 comm_long, comm_short, spec_long, spec_short,
                 small_long, small_short) -> dict:
    return {
        "market_key":    market_key,
        "report_date":   report_date,
        "comm_long":     comm_long,
        "comm_short":    comm_short,
        "comm_net":      comm_long  - comm_short,
        "spec_long":     spec_long,
        "spec_short":    spec_short,
        "spec_net":      spec_long  - spec_short,
        "small_long":    small_long,
        "small_short":   small_short,
        "small_net":     small_long - small_short,
        "open_interest": open_interest,
    }


def _col(header: dict, *names: str) -> int:
    """Return the index of the first matching column name (case-insensitive, strip quotes)."""
    for name in names:
        key = name.strip().lower()
        if key in header:
            return header[key]
    return -1


def _build_header(row: list) -> dict:
    """Map lowercase column name → index from the CSV header row."""
    return {col.strip().strip('"').lower(): i for i, col in enumerate(row)}


def parse_legacy_csv_rows(content: str) -> list:
    """
    Parse Legacy COT Futures-Only CSV (deacot{year}.zip / annual.txt).

    Commercials  = Commercial Positions
    Large Spec   = Noncommercial Positions
    Small Traders = Nonreportable Positions
    """
    reader = csv.reader(io.StringIO(content))
    header_row = next(reader, None)
    if header_row is None:
        return []
    h = _build_header(header_row)

    i_market = _col(h, "market and exchange names")
    i_date   = _col(h, "as of date in form yyyy-mm-dd")
    i_oi     = _col(h, "open interest (all)")
    i_sl     = _col(h, "noncommercial positions-long (all)")
    i_ss     = _col(h, "noncommercial positions-short (all)")
    i_cl     = _col(h, "commercial positions-long (all)")
    i_cs     = _col(h, "commercial positions-short (all)")
    i_nrl    = _col(h, "nonreportable positions-long (all)")
    i_nrs    = _col(h, "nonreportable positions-short (all)")

    print(f"[legacy] header cols: {len(header_row)}, "
          f"OI={i_oi} CommL={i_cl} SpecL={i_sl} NRL={i_nrl}")

    records = []
    for row in reader:
        if len(row) < 10:
            continue
        market_name = row[i_market].strip().upper() if i_market >= 0 else ""
        if market_name not in MARKETS_UPPER:
            continue
        report_date = row[i_date].strip().strip('"') if i_date >= 0 else ""
        if not report_date:
            continue

        g = lambda i: _safe_int(row[i]) if 0 <= i < len(row) else 0
        records.append(_make_record(
            market_key    = MARKETS_UPPER[market_name],
            report_date   = report_date,
            open_interest = g(i_oi),
            comm_long     = g(i_cl),
            comm_short    = g(i_cs),
            spec_long     = g(i_sl),
            spec_short    = g(i_ss),
            small_long    = g(i_nrl),
            small_short   = g(i_nrs),
        ))

    return _merge_records(records)


def _fetch_zip_content(url: str, label: str) -> str | None:
    """Download a CFTC ZIP and return the text content of the .txt file inside."""
    try:
        resp = requests.get(url, timeout=120)
        if resp.status_code == 404:
            print(f"[{label}]: file not found (404), skipping")
            return None
        resp.raise_for_status()

        with zipfile.ZipFile(io.BytesIO(resp.content)) as zf:
            names = zf.namelist()
            txt_file = next(
                (n for n in names if n.lower().endswith(".txt")),
                None,
            )
            if txt_file is None:
                print(f"[{label}]: no .txt file in archive")
                return None
            with zf.open(txt_file) as f:
                return f.read().decode("utf-8", errors="replace")

    except Exception as e:
        print(f"[{label}]: error ({e})")
        return None


def download_and_parse_year(year: int) -> list:
    """
    Download and parse the CFTC Legacy COT Futures-Only report for a given year.
    Single file covers ALL markets (forex, crypto, indices, bonds, commodities).
    URL: https://www.cftc.gov/files/dea/history/deacot{year}.zip
    """
    content = _fetch_zip_content(
        f"https://www.cftc.gov/files/dea/history/deacot{year}.zip",
        f"legacy/{year}",
    )
    if not content:
        return []

    rows = parse_legacy_csv_rows(content)
    print(f"[legacy/{year}]: {len(rows)} records")
    return rows


def download_last_25_years() -> None:
    from database import upsert_batch
    current_year = datetime.now().year
    for year in range(current_year - 24, current_year + 1):
        print(f"Downloading year {year}...")
        records = download_and_parse_year(year)
        if records:
            upsert_batch(records)
            print(f"Year {year}: {len(records)} records saved")
        else:
            print(f"Year {year}: no data")


def cot_index(values: list, window: int = 52) -> list:
    result = []
    for i, val in enumerate(values):
        if i < window - 1:
            result.append(None)
            continue
        window_slice = values[i - window + 1 : i + 1]
        lo = min(window_slice)
        hi = max(window_slice)
        if hi == lo:
            result.append(50.0)
        else:
            result.append(round((val - lo) / (hi - lo) * 100, 2))
    return result
