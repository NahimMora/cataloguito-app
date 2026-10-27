param([Parameter(Mandatory=$true)][string]$Archive)
$ErrorActionPreference='Stop'
$mvpRoot=Split-Path -Parent $PSScriptRoot
Set-Location -LiteralPath $mvpRoot
$mvpRestore=Join-Path $mvpRoot '.tools\restore-check'
if(Test-Path -LiteralPath $mvpRestore){$mvpRestore=Join-Path $mvpRoot ('.tools\restore-check-'+[Guid]::NewGuid().ToString('N'))}
New-Item -ItemType Directory -Path $mvpRestore | Out-Null
Expand-Archive -LiteralPath $Archive -DestinationPath $mvpRestore
$mvpManifest=Get-Content (Join-Path $mvpRestore 'manifest.json') -Raw | ConvertFrom-Json
$mvpImages=@(Get-ChildItem (Join-Path $mvpRestore 'images') -File -Recurse)
if($mvpImages.Count -ne $mvpManifest.images){throw 'La cantidad de imágenes no coincide con el manifiesto.'}
if(-not $mvpManifest.file_hashes -or -not $mvpManifest.sql_sha256){throw 'El respaldo no tiene hashes de integridad.'}
foreach($mvpHash in $mvpManifest.file_hashes.PSObject.Properties){
    $mvpImagePath=Join-Path $mvpRestore $mvpHash.Name
    if((Get-FileHash -LiteralPath $mvpImagePath -Algorithm SHA256).Hash.ToLowerInvariant() -ne $mvpHash.Value){throw ('Imagen dañada: '+$mvpHash.Name)}
}
if((Get-FileHash -LiteralPath (Join-Path $mvpRestore 'database.sql') -Algorithm SHA256).Hash.ToLowerInvariant() -ne $mvpManifest.sql_sha256){throw 'SQL dañado.'}
# Database is deliberately fixed to a dedicated restore-check target.
$mvpClient=Join-Path $mvpRoot '.tools\mariadb-11.8.9-winx64\bin\mariadb.exe'
if(-not(Test-Path -LiteralPath $mvpClient)){throw 'Esta verificación local requiere el cliente MariaDB portátil del proyecto.'}
$mvpSettings=Join-Path $mvpRestore 'client.cnf'
[IO.File]::WriteAllText($mvpSettings,"[client]`nhost=127.0.0.1`nport=3318`nuser=vitrina`npassword=vitrina-local-only`n")
try {
    $mvpSqlPath=Join-Path $mvpRestore 'database.sql'
    # Import via redirected stdin; no shell interpolation or password in process arguments.
    $mvpInfo=New-Object Diagnostics.ProcessStartInfo
    $mvpInfo.FileName=$mvpClient
    $mvpInfo.Arguments='--defaults-extra-file="'+$mvpSettings+'" vitrina_restore_test'
    $mvpInfo.UseShellExecute=$false; $mvpInfo.CreateNoWindow=$true; $mvpInfo.RedirectStandardInput=$true
    $mvpImport=New-Object Diagnostics.Process; $mvpImport.StartInfo=$mvpInfo
    [void]$mvpImport.Start(); $mvpSql=[IO.File]::OpenRead($mvpSqlPath)
    try{$mvpSql.CopyTo($mvpImport.StandardInput.BaseStream)}finally{$mvpSql.Dispose();$mvpImport.StandardInput.Close()}
    $mvpImport.WaitForExit(); if($mvpImport.ExitCode -ne 0){throw 'Falló la restauración de prueba.'}
    & $mvpClient ('--defaults-extra-file='+$mvpSettings) vitrina_restore_test -e 'SELECT COUNT(*) AS shops FROM shops; SELECT COUNT(*) AS products FROM products; SELECT COUNT(*) AS orders FROM orders; SELECT COUNT(*) AS media FROM media;'
    if($LASTEXITCODE -ne 0){throw 'Falló la verificación de las tablas restauradas.'}
    Write-Host ('Restauración SQL verificada. Imágenes íntegras: '+$mvpImages.Count) -ForegroundColor Green
} finally {Remove-Item -LiteralPath $mvpSettings -ErrorAction SilentlyContinue}
