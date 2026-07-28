param (
    [string]$Domain = ""
)

$TunnelName = "goworker"
$CloudflaredPath = "$env:USERPROFILE\cloudflared\cloudflared.exe"

if (!(Test-Path $CloudflaredPath)) {
    Write-Host "Cloudflared executable not found in $env:USERPROFILE\cloudflared\cloudflared.exe" -ForegroundColor Red
    exit 1
}

$CertPath = "$env:USERPROFILE\.cloudflared\cert.pem"
if (!(Test-Path $CertPath)) {
    Write-Host "Initiating Cloudflare Login. Please authorize in your browser..." -ForegroundColor Yellow
    # Start login process in background
    Start-Process $CloudflaredPath -ArgumentList "tunnel login"
}

# Wait for cert.pem to be downloaded (max 5 minutes)
$timeout = 300
$elapsed = 0
while (!(Test-Path $CertPath) -and $elapsed -lt $timeout) {
    Start-Sleep -Seconds 5
    $elapsed += 5
}

if (!(Test-Path $CertPath)) {
    Write-Error "Cloudflare login was not authorized within 5 minutes. Aborting."
    exit 1
}

Write-Host "Cloudflare login authorized successfully!" -ForegroundColor Green

# Stop any running cloudflared instances
Stop-Process -Name cloudflared -Force -ErrorAction SilentlyContinue

# Auto-detect Domain from Cloudflare Account if not specified
if ([string]::IsNullOrEmpty($Domain)) {
    Write-Host "Auto-detecting Cloudflare domains..." -ForegroundColor Cyan
    try {
        # Fallback trick: run route dns with a fake hostname to trigger authorized zones output
        $testHost = "goworker.auto-detection-test-temp-zone.xyz"
        $errorOutput = & $CloudflaredPath tunnel route dns $TunnelName $testHost 2>&1 | Out-String
        if ($errorOutput -match "(?i)authorized zones:\s*([a-zA-Z0-9\.\,\s\-]+)") {
            $zones = $Matches[1].Split(",") | ForEach-Object { $_.Trim() }
            if ($zones.Count -gt 0 -and $zones[0] -ne "") {
                $Domain = $zones[0]
                Write-Host "Auto-detected domain: $Domain" -ForegroundColor Green
            }
        }
    } catch {
        Write-Warning "Could not auto-detect domain."
    }
}

if ([string]::IsNullOrEmpty($Domain)) {
    Write-Error "Domain name auto-detection failed, and no Domain parameter was supplied. Please supply the domain."
    exit 1
}

# Check if tunnel already exists
Write-Host "Checking if tunnel '$TunnelName' already exists..." -ForegroundColor Cyan
$existing = & $CloudflaredPath tunnel list | Select-String $TunnelName
if ($existing) {
    Write-Host "Tunnel '$TunnelName' already exists. Reusing..." -ForegroundColor Yellow
} else {
    Write-Host "Creating Named Tunnel '$TunnelName'..." -ForegroundColor Cyan
    & $CloudflaredPath tunnel create $TunnelName
}

# Find credentials file in .cloudflared folder
$credFile = Get-ChildItem -Path "$env:USERPROFILE\.cloudflared" -Filter "*.json" | Select-Object -First 1
if (!$credFile) {
    Write-Error "Tunnel credentials file (*.json) not found in $env:USERPROFILE\.cloudflared"
    exit 1
}

$TunnelId = $credFile.BaseName
$CredPath = $credFile.FullName
Write-Host "Found Tunnel ID: $TunnelId" -ForegroundColor Green

# Create configuration config.yml
$ConfigContent = @"
tunnel: $TunnelId
credentials-file: $CredPath

ingress:
  - hostname: goworker.$Domain
    service: http://localhost/goworker
  - service: http_status:404
"@

$ConfigPath = "$env:USERPROFILE\.cloudflared\config.yml"
Set-Content -Path $ConfigPath -Value $ConfigContent
Write-Host "Wrote config.yml successfully to $ConfigPath" -ForegroundColor Green

# Route DNS
$FullHostname = "goworker.$Domain"
Write-Host "Routing DNS for $FullHostname..." -ForegroundColor Cyan
& $CloudflaredPath tunnel route dns -f $TunnelName $FullHostname

# Install/Start Service
Write-Host "Installing Cloudflared as a persistent Windows Service..." -ForegroundColor Cyan
& $CloudflaredPath service uninstall 2>$null
& $CloudflaredPath service install

Write-Host "Starting Cloudflared service..." -ForegroundColor Cyan
Start-Service -Name "cloudflared"

# Verify service is running
Start-Sleep -Seconds 3
$service = Get-Service -Name "cloudflared" -ErrorAction SilentlyContinue
if ($service -and $service.Status -eq "Running") {
    Write-Host "SUCCESS: Cloudflared Service is Running!" -ForegroundColor Green
    Write-Host "Tunnel configured at: https://$FullHostname" -ForegroundColor Green
} else {
    Write-Error "Failed to start Cloudflared service."
    exit 1
}
