# build-theme.ps1 — vytvoří cot-web-theme.zip pro WordPress

$SRC  = ".\cot-web-theme"
$OUT  = ".\cot-web-theme.zip"
$TEMP = ".\__theme_build_tmp"

Write-Host "Building WordPress theme ZIP..." -ForegroundColor Cyan

if (Test-Path $TEMP) { Remove-Item $TEMP -Recurse -Force }
if (Test-Path $OUT)  { Remove-Item $OUT  -Force }

New-Item -ItemType Directory -Path "$TEMP\cot-web-theme" | Out-Null
Copy-Item "$SRC\*" -Destination "$TEMP\cot-web-theme\" -Recurse

Compress-Archive -Path "$TEMP\cot-web-theme" -DestinationPath $OUT
Remove-Item $TEMP -Recurse -Force

$size = [math]::Round((Get-Item $OUT).Length / 1KB, 1)
Write-Host "Done: $OUT  ($size KB)" -ForegroundColor Green
Write-Host ""
Write-Host "Instalace:" -ForegroundColor Yellow
Write-Host "  WordPress admin -> Vzhled -> Motivy -> Nahrat motiv -> vybrat cot-web-theme.zip"
Write-Host "  Aktivovat motiv"
