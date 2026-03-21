import os
import asyncio
from contextlib import asynccontextmanager
from datetime import datetime, timedelta

from fastapi import FastAPI, BackgroundTasks, HTTPException, Query
from fastapi.middleware.cors import CORSMiddleware
from apscheduler.schedulers.asyncio import AsyncIOScheduler
from apscheduler.triggers.cron import CronTrigger

from database import init_db, get_all_latest, get_series, get_stats, upsert_batch
from scraper import download_and_parse_year, download_last_25_years, DISPLAY_NAMES, CATEGORIES

SECRET_KEY = os.environ.get("SECRET_KEY", "COT_SECRET_2024")

scheduler = AsyncIOScheduler()


def _get_next_friday() -> str:
    today = datetime.utcnow().date()
    days = (4 - today.weekday()) % 7
    if days == 0:
        days = 7
    return (today + timedelta(days=days)).isoformat()


async def _scheduled_refresh():
    """Weekly background task — downloads and stores the current year."""
    year = datetime.now().year
    print(f"[{datetime.utcnow().isoformat()}] Scheduled weekly COT refresh (year {year})…")
    records = await asyncio.to_thread(download_and_parse_year, year)
    if records:
        await asyncio.to_thread(upsert_batch, records)
        print(f"Weekly refresh done: {len(records)} records")


@asynccontextmanager
async def lifespan(app: FastAPI):
    # ── Startup ────────────────────────────────────────────────────────────
    init_db()
    stats = get_stats()

    if stats["total_records"] < 1000:
        print("Database empty — data will be fetched on first POST /api/refresh")
    elif stats["last_update"]:
        last_dt = datetime.strptime(stats["last_update"], "%Y-%m-%d")
        if (datetime.now() - last_dt).days > 8:
            year = datetime.now().year
            print(f"Data older than 8 days — refreshing year {year} in background…")
            asyncio.create_task(asyncio.to_thread(download_and_parse_year, year))

    # Weekly refresh: every Friday at 22:00 UTC (CFTC publishes Tuesday,
    # but gives extra time for file availability).
    scheduler.add_job(
        _scheduled_refresh,
        CronTrigger(day_of_week="fri", hour=22, minute=0, timezone="UTC"),
        id="weekly_refresh",
        replace_existing=True,
    )
    scheduler.start()

    yield

    # ── Shutdown ───────────────────────────────────────────────────────────
    scheduler.shutdown(wait=False)


app = FastAPI(title="COT Dashboard API", version="1.0.0", lifespan=lifespan)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)


# ── Endpoints ──────────────────────────────────────────────────────────────

@app.get("/health")
def health():
    return {"status": "ok"}


@app.get("/api/markets")
def api_markets():
    """
    Latest COT snapshot for every tracked market.
    Sorted by category (forex→commodities→indices→bonds→crypto) then name.
    """
    return get_all_latest()


@app.get("/api/chart/{market_key}")
def api_chart(
    market_key: str,
    weeks: int = Query(default=104, description="52 | 104 | 260 | 520 | 1300"),
):
    """Time-series data for a single market."""
    valid = {52, 104, 260, 520, 1300}
    if weeks not in valid:
        weeks = 104

    series = get_series(market_key, weeks)
    if not series:
        raise HTTPException(status_code=404, detail=f"Market '{market_key}' not found")

    return {
        "market_key": market_key,
        "name":       DISPLAY_NAMES.get(market_key, market_key),
        "category":   CATEGORIES.get(market_key, "other"),
        **series,
    }


@app.get("/api/status")
def api_status():
    """Database statistics and scheduling info."""
    stats = get_stats()
    return {
        "last_update":    stats["last_update"],
        "total_records":  stats["total_records"],
        "markets_count":  stats["markets_count"],
        "next_friday":    _get_next_friday(),
        "db_size_mb":     stats["db_size_mb"],
    }


@app.post("/api/refresh")
async def api_refresh(
    background_tasks: BackgroundTasks,
    secret: str = Query(default=""),
):
    """
    Manually trigger a re-download of the current year.
    Requires the correct SECRET_KEY query parameter.
    """
    if secret != SECRET_KEY:
        raise HTTPException(status_code=403, detail="Invalid secret")

    year = datetime.now().year

    def _do():
        records = download_and_parse_year(year)
        if records:
            upsert_batch(records)
            print(f"Manual refresh done: {len(records)} records")

    background_tasks.add_task(_do)
    return {"status": "started", "message": f"Refresh of year {year} started in background"}


@app.get("/api/seed-year/{year}")
def api_seed_year(year: int, secret: str = Query(default="")):
    """
    Download ONE year synchronously (no background task — Railway-safe).
    Call this in a loop from PowerShell for years 2000-2026.
    """
    if secret != SECRET_KEY:
        raise HTTPException(status_code=403, detail="Invalid secret")
    if year < 2000 or year > datetime.now().year:  # Legacy data starts 1986, seed from 2000
        raise HTTPException(status_code=400, detail="Invalid year")

    records = download_and_parse_year(year)
    if records:
        upsert_batch(records)
        return {"year": year, "saved": len(records), "status": "ok"}
    return {"year": year, "saved": 0, "status": "no_data"}


@app.post("/api/seed")
async def api_seed(
    background_tasks: BackgroundTasks,
    secret: str = Query(default=""),
):
    """
    First-time seed: downloads 2000–current year one year at a time.
    Memory-safe — each year is fetched, stored, then freed.
    """
    if secret != SECRET_KEY:
        raise HTTPException(status_code=403, detail="Invalid secret")

    def _seed():
        current_year = datetime.now().year
        for year in range(2000, current_year + 1):  # Legacy data from 2000 onward
            print(f"[seed] Downloading year {year}…")
            try:
                records = download_and_parse_year(year)
                if records:
                    upsert_batch(records)
                    print(f"[seed] Year {year}: {len(records)} records")
                else:
                    print(f"[seed] Year {year}: no data")
            except Exception as e:
                print(f"[seed] Year {year}: error — {e}")
        print("[seed] Done!")

    background_tasks.add_task(_seed)
    return {"status": "started", "message": "Seed 2000–current started in background. Check /api/status."}


# ── Dev entrypoint ─────────────────────────────────────────────────────────

if __name__ == "__main__":
    import uvicorn

    port = int(os.environ.get("PORT", 8000))
    uvicorn.run("main:app", host="0.0.0.0", port=port, reload=True)
