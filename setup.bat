@echo off
title Smart Emergency AI — Installation
set "PHP84=C:\Users\HP\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe"
if exist "%PHP84%\php.exe" set "PATH=%PHP84%;%PATH%"
cd /d "%~dp0"

echo.
echo === Smart Emergency AI — Installation ===
echo.

where php >nul 2>&1
if errorlevel 1 (
    echo [ERREUR] PHP introuvable. Installez PHP 8.3+ ou ajustez le chemin dans setup.bat
    pause
    exit /b 1
)

php -r "echo 'PHP '.PHP_VERSION.PHP_EOL;"
echo.

if not exist vendor (
    echo Installation des dependances Composer...
    composer install --no-interaction
    if errorlevel 1 (
        echo [ERREUR] composer install a echoue
        pause
        exit /b 1
    )
)

if not exist .env (
    echo Creation du fichier .env...
    copy .env.example .env >nul
)

php artisan app:install
if errorlevel 1 (
    echo [ERREUR] Installation echouee
    pause
    exit /b 1
)

echo.
echo === Pret ! Lancez serve.bat pour demarrer ===
echo.
pause
