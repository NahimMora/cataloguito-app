param([int]$Port=8000,[switch]$NoBrowser)
$ErrorActionPreference='Stop'
$mvpRoot=Split-Path -Parent $PSScriptRoot
Set-Location -LiteralPath $mvpRoot
$mvpPhp=Join-Path $mvpRoot '.tools\php\php.exe'
if (-not(Test-Path -LiteralPath $mvpPhp)) { $mvpPhp=(Get-Command php -ErrorAction Stop).Source }
if (-not(Test-Path -LiteralPath '.env')) { throw 'Falta .env. Seguí la instalación de README.md.' }
if (-not(Test-Path -LiteralPath 'vendor\autoload.php')) { throw 'Faltan dependencias PHP. Ejecutá composer install.' }
if (-not(Test-Path -LiteralPath 'public\build\manifest.json')) { & npm.cmd run build; if ($LASTEXITCODE -ne 0) { throw 'Falló la compilación.' } }
New-Item -ItemType Directory -Path '.tools\runtime' -Force | Out-Null
$mvpEnv=Get-Content '.env' -Raw
$mvpDbPort=3306
if ($mvpEnv -match '(?m)^DB_PORT=(\d+)') { $mvpDbPort=[int]$Matches[1] }
if (-not(Get-NetTCPConnection -State Listen -LocalPort $mvpDbPort -ErrorAction SilentlyContinue)) {
    $mvpDbBinary=Join-Path $mvpRoot '.tools\mariadb-11.8.9-winx64\bin\mariadbd.exe'
    $mvpDbData=Join-Path $mvpRoot '.tools\db11'
    if (-not(Test-Path -LiteralPath $mvpDbBinary) -or -not(Test-Path -LiteralPath $mvpDbData)) { throw 'No hay base de datos activa. Iniciá tu MySQL/MariaDB y verificá .env.' }
    $mvpDbBase=Split-Path -Parent (Split-Path -Parent $mvpDbBinary)
    Start-Process -FilePath $mvpDbBinary -ArgumentList @('--no-defaults',('--basedir="'+$mvpDbBase+'"'),('--datadir="'+$mvpDbData+'"'),('--port='+$mvpDbPort),'--bind-address=127.0.0.1',('--log-error="'+$mvpDbData+'\error.log"'),'--character-set-server=utf8mb4','--collation-server=utf8mb4_unicode_ci') -WindowStyle Hidden
    for($mvpWait=0;$mvpWait -lt 30;$mvpWait++){if(Get-NetTCPConnection -State Listen -LocalPort $mvpDbPort -ErrorAction SilentlyContinue){break};Start-Sleep -Milliseconds 250}
}
if (-not(Get-NetTCPConnection -State Listen -LocalPort $Port -ErrorAction SilentlyContinue)) {
    $mvpWeb=Start-Process -FilePath $mvpPhp -ArgumentList @('artisan','serve','--host=127.0.0.1',('--port='+$Port)) -WorkingDirectory $mvpRoot -WindowStyle Hidden -PassThru -RedirectStandardOutput (Join-Path $mvpRoot '.tools\runtime\web.log') -RedirectStandardError (Join-Path $mvpRoot '.tools\runtime\web-error.log')
    $mvpWeb.Id | Set-Content '.tools\runtime\web.pid'
}
$mvpLoopFile=Join-Path $mvpRoot '.tools\runtime\outbox.pid'
$mvpLoopRunning=$false
if(Test-Path -LiteralPath $mvpLoopFile){$mvpLoopId=[int](Get-Content $mvpLoopFile);$mvpLoopRunning=$null -ne (Get-Process -Id $mvpLoopId -ErrorAction SilentlyContinue)}
if(-not $mvpLoopRunning){$mvpWorker=Start-Process powershell.exe -ArgumentList @('-NoProfile','-ExecutionPolicy','Bypass','-File',('"'+(Join-Path $PSScriptRoot 'outbox-loop.ps1')+'"')) -WorkingDirectory $mvpRoot -WindowStyle Hidden -PassThru;$mvpWorker.Id|Set-Content $mvpLoopFile}
for($mvpWait=0;$mvpWait -lt 30;$mvpWait++){try{$mvpReady=Invoke-WebRequest "http://127.0.0.1:$Port/up" -UseBasicParsing -TimeoutSec 2;if($mvpReady.StatusCode -eq 200){break}}catch{Start-Sleep -Milliseconds 250}}
Write-Host "Cataloguito disponible en http://127.0.0.1:$Port" -ForegroundColor Green
if(-not $NoBrowser){Start-Process "http://127.0.0.1:$Port"}
