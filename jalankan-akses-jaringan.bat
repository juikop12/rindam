@echo off
title SIPANDU-WBK - Server Akses Jaringan Lokal (LAN / Wi-Fi)
chcp 65001 >nul
cls

rem Ambil IP Komputer saat ini secara otomatis
for /f "tokens=*" %%a in ('powershell -NoProfile -Command "(Get-NetIPAddress -AddressFamily IPv4 -InterfaceAlias 'Wi-Fi*').IPAddress" 2^>nul') do set "HOST_IP=%%a"
if "%HOST_IP%"=="" (
    for /f "tokens=*" %%a in ('powershell -NoProfile -Command "(Get-NetIPAddress -AddressFamily IPv4 | Where-Object { $_.InterfaceAlias -notlike '*Loopback*' -and $_.IPAddress -notlike '169.254*' } | Select-Object -First 1).IPAddress" 2^>nul') do set "HOST_IP=%%a"
)
if "%HOST_IP%"=="" set "HOST_IP=192.168.100.20"

echo ======================================================================
echo   SIPANDU-WBK - SERVER AKSES JARINGAN LOKAL (LAN / WI-FI)
echo ======================================================================
echo.
echo  IP Komputer Host Terdeteksi : %HOST_IP%
echo.
echo  Buka di Browser Perangkat Lain (HP Android / iPhone / Laptop lain):
echo  ----------------------------------------------------------------------
echo  👉 Alamat Utama : http://%HOST_IP%:8000
echo  👉 Alamat Laragon: http://%HOST_IP%
echo  ----------------------------------------------------------------------
echo.
echo  PENTING AGAR TIDAK GAGAL:
echo  1. WAJIB ketik "http://" di depan alamat (jangan hanya mengetik angkanya,
echo     karena Chrome/Safari di HP akan otomatis mengarahkan ke https://).
echo  2. Pastikan HP terhubung ke Wi-Fi yang SAMA (bukan paket data seluler).
echo  3. Jika masih tidak terbuka, klik kanan file 'buka-firewall.bat' lalu
echo     pilih "Run as administrator" untuk membuka izin Firewall Windows.
echo  4. Jika Wi-Fi kantor mengaktifkan AP Isolation, gunakan Mobile Hotspot
echo     dari laptop lalu sambungkan HP ke Hotspot tersebut.
echo.
echo ======================================================================
echo   Menjalankan server di port 8000... Tekan Ctrl + C untuk berhenti.
echo ======================================================================
echo.
set "PHP_PATH=D:\laragon\bin\php\php-8.3.26-Win32-vs16-x64\php.exe"
if not exist "%PHP_PATH%" set "PHP_PATH=php"

"%PHP_PATH%" artisan serve --host=0.0.0.0 --port=8000
pause
