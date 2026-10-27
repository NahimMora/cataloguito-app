$ErrorActionPreference='Stop'
$mvpRoot=Split-Path -Parent $PSScriptRoot
# Only terminate processes whose executable/command explicitly belongs to this workspace.
foreach($mvpName in @('web','outbox')){
    $mvpPidFile=Join-Path $mvpRoot ('.tools\runtime\'+$mvpName+'.pid')
    if(Test-Path -LiteralPath $mvpPidFile){
        $mvpPidValue=[int](Get-Content -LiteralPath $mvpPidFile)
        $mvpProc=Get-CimInstance Win32_Process -Filter "ProcessId=$mvpPidValue" -ErrorAction SilentlyContinue
        if($mvpProc -and (($mvpProc.ExecutablePath -like ($mvpRoot+'\*')) -or ($mvpProc.CommandLine -like ('*'+$mvpRoot+'*')))){
            Get-CimInstance Win32_Process -Filter "ParentProcessId=$mvpPidValue" | Where-Object {$_.ExecutablePath -like ($mvpRoot+'\*')} | ForEach-Object {Stop-Process -Id $_.ProcessId -ErrorAction SilentlyContinue}
            Stop-Process -Id $mvpPidValue -ErrorAction SilentlyContinue
        }
        Remove-Item -LiteralPath $mvpPidFile
    }
}
Write-Host 'Servidor y avisos locales detenidos. La base de datos queda disponible.'
