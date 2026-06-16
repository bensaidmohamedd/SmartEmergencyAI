# Exécuter en tant qu'administrateur : clic droit > Exécuter avec PowerShell (admin)
$php84 = "C:\Users\HP\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe"

if (-not (Test-Path "$php84\php.exe")) {
    Write-Error "PHP 8.4 introuvable : $php84"
    exit 1
}

$machinePath = [Environment]::GetEnvironmentVariable("Path", "Machine") -split ';' |
    Where-Object { $_ -and $_ -ne $php84 }

$newMachinePath = ($php84, $machinePath) -join ';'
[Environment]::SetEnvironmentVariable("Path", $newMachinePath, "Machine")

$userPath = [Environment]::GetEnvironmentVariable("Path", "User") -split ';' |
    Where-Object { $_ -and $_ -ne $php84 -and $_ -ne "C:\xampp\php" }

$newUserPath = ($php84, $userPath) -join ';'
[Environment]::SetEnvironmentVariable("Path", $newUserPath, "User")

Write-Host "PATH mis a jour. PHP 8.4 est maintenant prioritaire."
Write-Host "Fermez et rouvrez votre terminal, puis tapez : php -v"
Write-Host "Attendu : PHP 8.4.22"
