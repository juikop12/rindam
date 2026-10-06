@echo off
chcp 65001 >nul
:: Cek apakah dijalankan sebagai Administrator
net session >nul 2>&1
if %errorLevel% == 0 (
    echo [OK] Menjalankan dengan hak akses Administrator...
) else (
    echo [INFO] Meminta hak akses Administrator...
    powershell -Command "Start-Process '%~f0' -Verb RunAs"
    exit /b
)

set HOSTS_FILE=%WINDIR%\System32\drivers\etc\hosts

:: Cek apakah domain rindam.test sudah terdaftar
findstr /i "rindam.test" "%HOSTS_FILE%" >nul
if %errorLevel% == 0 (
    echo [INFO] Domain rindam.test sudah terdaftar di Windows hosts!
) else (
    echo.>>"%HOSTS_FILE%"
    echo 127.0.0.1      rindam.test      # SIPANDU-WBK Local Domain>>"%HOSTS_FILE%"
    echo 127.0.0.1      rindam.local     # SIPANDU-WBK Local Domain>>"%HOSTS_FILE%"
    echo [BERHASIL] Domain rindam.test dan rindam.local telah ditambahkan ke Windows hosts!
)

:: Flush DNS cache
ipconfig /flushdns >nul
echo [SELESAI] DNS Cache telah dibersihkan (flushed).
echo.
echo ======================================================================
echo  DOMAIN LOKAL SIPANDU-WBK TELAH AKTIF:
echo  1. Pada Komputer Ini : http://rindam.test  atau  http://rindam.local
echo  2. HP / Laptop Lain  : http://192.168.100.20 (pada Wi-Fi yang sama)
echo ======================================================================
echo.
pause
