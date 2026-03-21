# COT Dashboard — Python Backend

FastAPI backend pro COT Report Dashboard.
Stahuje data z CFTC.gov (Legacy Futures Only) a vystavuje REST API pro WordPress plugin.

---

## Lokální spuštění

```bash
pip install -r requirements.txt
python main.py
```

Otevři **http://localhost:8000/docs** — Swagger UI se všemi endpointy.

První spuštění automaticky stáhne 25 let dat (~5–10 minut, ~22 trhů × 25 let × 52 týdnů).

### Rychlý test

```bash
curl http://localhost:8000/health
curl http://localhost:8000/api/status
curl http://localhost:8000/api/markets | python -m json.tool | head -60
curl "http://localhost:8000/api/chart/eurusd?weeks=52"
```

---

## Deploy na Railway.app (zdarma do 500 h/měsíc)

1. Zaregistruj se na [railway.app](https://railway.app)
2. **New Project → Deploy from GitHub repo** (pushni tento adresář)
3. Přidej **Volume**: Mount path `/data` (nutné pro perzistenci SQLite!)
4. **Environment variables**:
   ```
   SECRET_KEY=COT_SECRET_2024
   ```
   (nebo vlastní tajný klíč)
5. Po deployi zkopíruj vygenerovanou URL, např.
   `https://cot-app-production.railway.app`
6. Zadej tuto URL do WordPress pluginu (Settings → COT Dashboard)

### Perzistence dat

SQLite databáze `cot_data.db` se ukládá do `/data/` (Railway Volume).
**Bez Volume se databáze resetuje při každém restartu!**

### Automatická aktualizace

Scheduler spouští refresh každý **pátek v 22:00 UTC** (CFTC zveřejňuje data v úterý,
pátku je dán buffer pro zpracování).

Manuální refresh:
```bash
curl -X POST "https://твой-app.railway.app/api/refresh?secret=COT_SECRET_2024"
```

---

## API Endpoints

| Metoda | Endpoint | Popis |
|--------|----------|-------|
| GET | `/health` | Health check |
| GET | `/api/markets` | Přehled všech trhů (poslední data) |
| GET | `/api/chart/{key}?weeks=104` | Časová řada pro jeden trh |
| GET | `/api/status` | Statistiky DB + datum příštího pátku |
| POST | `/api/refresh?secret=...` | Manuální aktualizace dat |

### Parametr `weeks` pro `/api/chart/`

| Hodnota | Období |
|---------|--------|
| 52 | 1 rok |
| 104 | 2 roky (výchozí) |
| 260 | 5 let |
| 520 | 10 let |
| 1300 | 25 let |

---

## Struktura souborů

```
cot-backend/
├── main.py          ← FastAPI app, endpointy, scheduler
├── scraper.py       ← stahování a parsování CFTC dat
├── database.py      ← SQLite operace
├── requirements.txt
├── Procfile         ← Railway/Heroku start command
├── railway.toml     ← Railway konfigurace
└── README.md
```
