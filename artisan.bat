@echo off
set "PHP84=C:\Users\HP\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe"
set "PATH=%PHP84%;%PATH%"
cd /d "%~dp0"
php artisan %*
