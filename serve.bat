@echo off
set "PHP84=C:\Users\HP\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe"
if exist "%PHP84%\php.exe" set "PATH=%PHP84%;%PATH%"
cd /d "%~dp0"
if not exist "storage\tmp" mkdir "storage\tmp"
if not exist ".env" (
    echo [INFO] Fichier .env manquant. Lancez setup.bat d'abord.
    pause
    exit /b 1
)
php artisan serve:app %*
