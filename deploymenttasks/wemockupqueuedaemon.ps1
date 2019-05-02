if (
    Get-Process -Name php
) {
Write-Host "Php running"
exit 0
} else {
    if (Test-Path C:\deployment\deployment\queuedaemon.bat) {
        Write-Host "Php not running - restarting daemon"
        C:\deployment\deployment\queuedaemon.bat
        exit 0
    } else {
        Write-Host "Script Not Available from deployment yet."
        exit 0
    }
}
