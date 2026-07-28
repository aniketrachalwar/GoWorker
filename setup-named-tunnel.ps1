# GoWorker Permanent Named Cloudflare Tunnel Installer
# This script automates logging in, creating the named tunnel, and configuring it as a persistent service.

$Subdomain = "goworker.mydomain.com"
$TunnelName = "goworker"
$CloudflaredPath = "$env:USERPROFILE\cloudflared\cloudflared.exe"

if (!(Test-Path $CloudflaredPath)) {
    Write-Host "Cloudflared not found in local path. Checking alternate locations..." -ForegroundColor Yellow
    $CloudflaredPath = "C:\Program Files (x86)\cloudflared\cloudflared.exe"
    if (!(Test-Path $CloudflaredPath)) {
        $CloudflaredPath = "C:\Program Files\cloudflared\cloudflared.exe"
    }
}

if (!(Test-Path $CloudflaredPath)) {
    Write-Error "cloudflared.exe could not be located. Please install it or place it at $env:USERPROFILE\cloudflared\cloudflared.exe"
    exit 1
}

Write-Host "1. Initiating Cloudflare Login..." -ForegroundColor Green
Write-Host "Please authorize the login in the browser window that opens." -ForegroundColor Yellow
Start-Process $CloudflaredPath -ArgumentList "tunnel login" -Wait

$CertPath = "$env:USERPROFILE\.cloudflared\cert.pem"
if (!(Test-Path $CertPath)) {
    Write-Error "Login failed or cert.pem not found. Please log in first."
    exit 1
}

Write-Host "2. Creating Named Tunnel '$TunnelName'..." -ForegroundColor Green
$existing = & $CloudflaredPath tunnel list | Select-String $TunnelName
if ($existing) {
    Write-Host "Reusing existing tunnel '$TunnelName'." -ForegroundColor Yellow
} else {
    & $CloudflaredPath tunnel create $TunnelName
}

# Find the credentials file
$credFile = Get-ChildItem -Path "$env:USERPROFILE\.cloudflared" -Filter "*.json" | Select-Object -First 1
if (!$credFile) {
    Write-Error "Credentials file not found in $env:USERPROFILE\.cloudflared"
    exit 1
}
$credPath = $credFile.FullName
Write-Host "Found credentials file: $credPath" -ForegroundColor Cyan

# Create configuration directory if not exists
$ConfigDir = "$env:USERPROFILE\.cloudflared"
New-Item -ItemType Directory -Force -Path $ConfigDir | Out-Null

# Write config.yml
$ConfigContent = @"
tunnel: $TunnelName
credentials-file: $credPath

ingress:
  - hostname: $Subdomain
    service: http://localhost/goworker
  - service: http_status:404
"@

$ConfigPath = "$ConfigDir\config.yml"
Set-Content -Path $ConfigPath -Value $ConfigContent
Write-Host "Wrote config.yml to $ConfigPath" -ForegroundColor Cyan

Write-Host "3. Routing DNS for $Subdomain..." -ForegroundColor Green
& $CloudflaredPath tunnel route dns $TunnelName $Subdomain

Write-Host "4. Installing Cloudflared as a Windows Service..." -ForegroundColor Green
# Stop and uninstall if already exists
& $CloudflaredPath service uninstall 2>$null
& $CloudflaredPath service install

Write-Host "5. Starting Cloudflared service..." -ForegroundColor Green
Start-Service -Name "cloudflared"

Write-Host "SUCCESS! Your permanent named tunnel is running at: https://$Subdomain" -ForegroundColor Green
