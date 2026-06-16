$php84 = "C:\Users\HP\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe"
if (Test-Path "$php84\php.exe") {
    $env:Path = "$php84;" + $env:Path
}
Set-Location $PSScriptRoot
if (-not (Test-Path "storage\tmp")) { New-Item -ItemType Directory -Path "storage\tmp" | Out-Null }
php artisan serve:app @args
