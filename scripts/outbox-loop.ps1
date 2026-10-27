$ErrorActionPreference='Continue'
$mvpRoot=Split-Path -Parent $PSScriptRoot
Set-Location -LiteralPath $mvpRoot
$mvpPhp=Join-Path $mvpRoot '.tools\php\php.exe'
if(-not(Test-Path -LiteralPath $mvpPhp)){$mvpPhp=(Get-Command php).Source}
while($true){ & $mvpPhp artisan orders:notify --limit=20 *> (Join-Path $mvpRoot '.tools\runtime\notifications.log'); Start-Sleep -Seconds 15 }
