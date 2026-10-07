@echo off
title SIPANDU-WBK - Cloudflare Tunnel Online (Akses Internet Publik)
chcp 65001 >nul
cls

echo ======================================================================
echo   SIPANDU-WBK - CLOUDFLARE TUNNEL ONLINE (HTTPS RESMI & STABIL)
echo ======================================================================
echo.

rem Cari file executable cloudflared
set "CLOUDFLARED_BIN=C:\platform-tools\cloudflared.exe"
if not exist "%CLOUDFLARED_BIN%" (
    for /f "tokens=*" %%i in ('where cloudflared 2^>nul') do set "CLOUDFLARED_BIN=%%i"
)

if not exist "%CLOUDFLARED_BIN%" (
    echo [ERROR] cloudflared.exe tidak ditemukan di C:\platform-tools maupun PATH!
    echo Sedang mengunduh cloudflared otomatis...
    curl.exe -L -o "C:\platform-tools\cloudflared.exe" "https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-windows-amd64.exe"
    set "CLOUDFLARED_BIN=C:\platform-tools\cloudflared.exe"
)

rem Cek apakah server port 8000 sedang aktif
powershell -NoProfile -Command "$conn = Get-NetTCPConnection -LocalPort 8000 -State Listen -ErrorAction SilentlyContinue; if (!$conn) { exit 1 }" >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo [INFO] Web server di port 8000 belum menyala.
    echo Menyalakan server Laravel di latar belakang...
    set "PHP_BIN=D:\laragon\bin\php\php-8.3.26-Win32-vs16-x64\php.exe"
    if not exist "%PHP_BIN%" set "PHP_BIN=php"
    start /b "" "%PHP_BIN%" artisan serve --host=0.0.0.0 --port=8000 >nul 2>&1
    timeout /t 3 >nul
)

echo [OK] Web server lokal port 8000 aktif.
echo [INFO] Memulai koneksi Cloudflare Tunnel (Anti-Putus / Auto-Reconnect)...
echo.
echo URL publik akan disimpan di: public_tunnel_url.txt
echo ======================================================================
echo.

:run_tunnel
powershell -NoProfile -ExecutionPolicy Bypass -Command "& {
    $bin = '%CLOUDFLARED_BIN%';
    $urlFile = 'public_tunnel_url.txt';
    $psi = New-Object System.Diagnostics.ProcessStartInfo;
    $psi.FileName = $bin;
    $psi.Arguments = 'tunnel --url http://127.0.0.1:8000 --no-autoupdate';
    $psi.RedirectStandardError = $true;
    $psi.RedirectStandardOutput = $true;
    $psi.UseShellExecute = $false;
    $psi.CreateNoWindow = $true;
    $proc = [System.Diagnostics.Process]::Start($psi);

    $foundUrl = $false;
    while (-not $proc.HasExited) {
        $line = $proc.StandardError.ReadLine();
        if ($line) {
            if ($line -match 'https://[a-zA-Z0-9-]+\.trycloudflare\.com') {
                $url = $matches[0];
                if (-not $foundUrl) {
                    $foundUrl = $true;
                    Set-Content -Path $urlFile -Value $url -Encoding UTF8;
                    Write-Host '';
                    Write-Host '======================================================================' -ForegroundColor Green;
                    Write-Host '  TUNNEL AKTIF & ONLINE! SIAP DIAKSES DARI INTERNET / HP / LAPTOP LAIN' -ForegroundColor Green;
                    Write-Host '======================================================================' -ForegroundColor Green;
                    Write-Host '';
                    Write-Host '  👉 URL PUBLIK ANDA : ' -NoNewline;
                    Write-Host $url -ForegroundColor Yellow;
                    Write-Host '';
                    Write-Host '  Fitur:' -ForegroundColor Cyan;
                    Write-Host '  * HTTPS Resmi (Otomatis SSL Aktif, Aman untuk Chrome & Android)' -ForegroundColor Gray;
                    Write-Host '  * Stabil berjam-jam tanpa batas kuota/sesi timeout' -ForegroundColor Gray;
                    Write-Host '  * Bisa diakses dari jaringan mana saja (bahkan beda Wi-Fi / Paket Data HP)' -ForegroundColor Gray;
                    Write-Host '======================================================================' -ForegroundColor Green;
                    Write-Host 'Tekan Ctrl + C untuk mematikan tunnel.';
                    Write-Host '';
                }
            }
        }
    }
}"

echo.
echo [PERINGATAN] Koneksi tunnel terputus (jaringan tidak stabil atau koneksi di-reset).
echo [RECONNECT] Menghubungkan ulang secara otomatis dalam 3 detik...
timeout /t 3 >nul
goto run_tunnel
