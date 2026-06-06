# Build PIKO POP branding files for production upload (FTP / cPanel File Manager)
$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent (Split-Path -Parent $MyInvocation.MyCommand.Path)
Set-Location $root

Write-Host "Compiling Tailwind CSS..."
npx --yes @tailwindcss/cli -i .\user_assets\css\input.css -o .\user_assets\css\output.css --minify

$stamp = Get-Date -Format 'yyyyMMdd-HHmm'
$outDir = Join-Path $root "deploy"
$zipPath = Join-Path $outDir "piko-pop-production-$stamp.zip"

if (-not (Test-Path $outDir)) {
    New-Item -ItemType Directory -Path $outDir | Out-Null
}

$paths = @(
    'application\helpers\common_helper.php',
    'application\hooks\Analytics_hook.php',
    'application\views\user\common\header.php',
    'application\views\user\common\footer.php',
    'application\views\user\home\banner.php',
    'application\views\user\home\categories.php',
    'application\views\user\home\marquee.php',
    'application\views\user\home\usp.php',
    'application\views\user\home\featured_category_products.php',
    'application\views\user\home\testimonials.php',
    'application\views\user\home\faq.php',
    'user_assets\css\input.css',
    'user_assets\css\output.css',
    'user_assets\images\hero.png',
    'user_assets\images\logo.png',
    'assets\images\hero.png',
    'assets\images\logo.png'
)

$missing = @()
foreach ($p in $paths) {
    if (-not (Test-Path (Join-Path $root $p))) {
        $missing += $p
    }
}

if ($missing.Count -gt 0) {
    Write-Warning "Missing files (skipped in package):"
    $missing | ForEach-Object { Write-Warning "  $_" }
}

$existing = $paths | Where-Object { Test-Path (Join-Path $root $_) }
if ($existing.Count -eq 0) {
    throw 'No files found to package.'
}

if (Test-Path $zipPath) {
    Remove-Item $zipPath -Force
}

Compress-Archive -Path ($existing | ForEach-Object { Join-Path $root $_ }) -DestinationPath $zipPath -CompressionLevel Optimal

Write-Host ""
Write-Host "Production package ready:"
Write-Host "  $zipPath"
Write-Host ""
Write-Host "Upload to server document root and extract (overwrite)."
Write-Host "Live test URL: https://pp.zbit.ltd/ (or your production domain)"
