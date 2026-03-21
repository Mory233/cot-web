# build-plugin.ps1
# Vytvoří cot-dashboard.zip připravený pro import do WordPress.
#
# Spuštění (z kořene projektu):
#   .\build-plugin.ps1
#
# Výstup: cot-dashboard.zip  (v kořeni projektu)

$ErrorActionPreference = "Stop"

$SRC  = ".\cot-wordpress-plugin\cot-dashboard"
$OUT  = ".\cot-dashboard.zip"
$TEMP = ".\__plugin_build_tmp"

Write-Host "Building WordPress plugin ZIP..." -ForegroundColor Cyan

# Cleanup
if (Test-Path $TEMP) { Remove-Item $TEMP -Recurse -Force }
if (Test-Path $OUT)  { Remove-Item $OUT  -Force }

# Copy plugin folder into temp so the ZIP root is "cot-dashboard/"
New-Item -ItemType Directory -Path "$TEMP\cot-dashboard" | Out-Null
Copy-Item "$SRC\*" -Destination "$TEMP\cot-dashboard\" -Recurse

# Create ZIP
Compress-Archive -Path "$TEMP\cot-dashboard" -DestinationPath $OUT

# Cleanup temp
Remove-Item $TEMP -Recurse -Force

$size = [math]::Round((Get-Item $OUT).Length / 1KB, 1)
Write-Host "Done: $OUT  ($size KB)" -ForegroundColor Green
Write-Host ""
Write-Host "Jak nainstalovat:" -ForegroundColor Yellow
Write-Host "  A) WordPress admin  -> Pluginy -> Pridat novy -> Nahrat plugin -> vybrat cot-dashboard.zip"
Write-Host "  B) FTP              -> nahraj slozku cot-dashboard/ do /wp-content/plugins/"
