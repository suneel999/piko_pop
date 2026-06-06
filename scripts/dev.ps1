param(
    [Parameter(Position = 0)]
    [ValidateSet('build', 'package', 'status', 'help')]
    [string]$Command = 'help'
)

$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent (Split-Path -Parent $MyInvocation.MyCommand.Path)
Set-Location $root

function Show-Help {
    Write-Host 'PIKO POP dev commands'
    Write-Host '  .\scripts\dev.ps1 build    Compile Tailwind CSS'
    Write-Host '  .\scripts\dev.ps1 package  Build FTP upload zip (branding files)'
    Write-Host '  .\scripts\dev.ps1 status   Show environment + git status'
    Write-Host ''
    Write-Host 'Workflow (AWS):'
    Write-Host '  local   -> XAMPP (APP_ENV=local)'
    Write-Host '  staging -> git push -> GitHub Actions -> AWS staging server'
    Write-Host '  main    -> git push -> GitHub Actions -> AWS production server'
    Write-Host '  Setup: deploy/aws/bootstrap-ubuntu.sh on fresh Lightsail/EC2'
}

function Invoke-Build {
    npm run build:css
}

function Invoke-Package {
    & (Join-Path $root 'scripts\build-production-package.ps1')
}

function Show-Status {
    if (Test-Path (Join-Path $root '.env')) {
        Get-Content (Join-Path $root '.env') | Where-Object { $_ -match '^(APP_ENV|APP_URL|DB_)' }
    } else {
        Write-Warning '.env not found. Copy .env.example to .env'
    }

    if (Get-Command git -ErrorAction SilentlyContinue) {
        if (Test-Path (Join-Path $root '.git')) {
            git status -sb
            git remote -v 2>$null
        } else {
            Write-Warning 'Git not initialized. Run: git init'
        }
    }
}

switch ($Command) {
    'build' { Invoke-Build }
    'package' { Invoke-Package }
    'status' { Show-Status }
    default { Show-Help }
}
