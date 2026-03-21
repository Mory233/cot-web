# COT Dashboard — WordPress Plugin

Instalace a konfigurace na Wedos / WordPress.

---

## Předpoklady

- WordPress 5.8 nebo novější
- PHP 7.4 nebo novější
- Python backend nasazený na Railway.app (viz `cot-backend/README.md`)
- URL backendu, např. `https://cot-app-production.railway.app`

---

## Instalace

### Metoda A — přes FTP / Správce souborů

1. Nahraj celou složku `cot-dashboard/` do:
   ```
   /wp-content/plugins/cot-dashboard/
   ```
   Výsledná struktura:
   ```
   wp-content/plugins/cot-dashboard/
   ├── cot-dashboard.php
   ├── cot-dashboard.js
   └── cot-dashboard.css
   ```

2. V administraci WordPress přejdi na **Pluginy → Nainstalované pluginy**.
3. Najdi **COT Dashboard** a klikni **Aktivovat**.

### Metoda B — přes ZIP

1. Zazipuj složku `cot-dashboard/` → `cot-dashboard.zip`
2. V administraci přejdi na **Pluginy → Přidat nový → Nahrát plugin**.
3. Vyber ZIP a klikni **Nainstalovat** → **Aktivovat**.

---

## Konfigurace

1. Přejdi na **Nastavení → COT Dashboard**.
2. Do pole **Railway API URL** zadej URL svého backendu, např.:
   ```
   https://cot-app-production.railway.app
   ```
3. Do pole **Secret Token** zadej stejný token, jaký máš nastaven
   jako `SECRET_KEY` na Railway (výchozí: `COT_SECRET_2024`).
4. Klikni **Test připojení** — měl bys vidět zelené „✓ Připojení OK".
5. Ulož nastavení kliknutím na **Uložit nastavení**.

---

## Vložení dashboardu na stránku

Na libovolné stránce nebo příspěvku vlož shortcode:

```
[cot_dashboard]
```

Dashboard se automaticky zobrazí s daty z CFTC.gov.

**Tip:** Shortcode lze použít vícekrát na stejné stránce — každá instance
dostane unikátní ID a funguje nezávisle.

---

## Manuální aktualizace dat

- **Přes admin UI:** Nastavení → COT Dashboard → tlačítko „Manuální aktualizace dat"
- **Přes API (curl):**
  ```bash
  curl -X POST "https://твой-app.railway.app/api/refresh?secret=COT_SECRET_2024"
  ```
- **Automaticky:** Backend se aktualizuje každý pátek ve 22:00 UTC.

---

## Řešení problémů

| Symptom | Příčina | Řešení |
|---------|---------|--------|
| „Chyba připojení k API" | Špatná URL nebo backend offline | Zkontroluj URL a status na Railway |
| Prázdná tabulka | Backend stahuje první data (25 let) | Počkej 5–10 minut a obnov stránku |
| Grafy se nezobrazují | Blokovaný CDN Chart.js | Zkontroluj Content Security Policy serveru |
| „✗ Nelze se připojit" | CORS nebo firewall | Backend má CORS `allow_origins=["*"]`, zkontroluj firewall Wedos |

---

## Technické poznámky

- Plugin nenačítá jQuery ani žádné jiné WP závislosti.
- Chart.js (v4) se načítá dynamicky z CDN pouze pokud ještě není na stránce k dispozici.
- Všechny CSS třídy mají prefix `cot-` a jsou scopovány pod `.cot-dashboard-container`.
- Data se stahují pouze z Railway backendu — WordPress server nekomunikuje s CFTC.gov přímo.
