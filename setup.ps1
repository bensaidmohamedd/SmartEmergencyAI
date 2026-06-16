$ErrorActionPreference = "Stop"
Set-Location $PSScriptRoot

$php84 = "C:\Users\HP\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe"
if (Test-Path "$php84\php.exe") { $env:Path = "$php84;" + $env:Path }

Write-Host "`n=== Smart Emergency AI — Installation ===`n" -ForegroundColor Cyan
php -r "echo 'PHP '.PHP_VERSION.PHP_EOL;"

if (-not (Test-Path "vendor")) {
    Write-Host "Installation Composer..."
    composer install --no-interaction
}

if (-not (Test-Path ".env")) {
    Copy-Item ".env.example" ".env"
    Write-Host ".env créé."
}

php artisan app:install
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

Write-Host "`n=== Prêt ! Lancez .\serve.ps1 ===`n" -ForegroundColor Green
