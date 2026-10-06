@echo off
chcp 65001 >nul
title Sinkronisasi SIPANDU-WBK ke GitHub
echo ========================================================
echo    SIPANDU-WBK — SINKRONISASI KE REPOSITORI GITHUB
echo ========================================================
echo.

set GIT_PATH="D:\laragon\bin\git\cmd\git.exe"

if not exist %GIT_PATH% (
    set GIT_PATH=git
)

%GIT_PATH% status
echo.
set /p msg="Masukkan ringkasan update (Tekan Enter untuk default): "
if "%msg%"=="" set msg=Update sistem via Antigravity

echo.
echo Menambahkan perubahan ke Git...
%GIT_PATH% add .

echo Membuat commit...
%GIT_PATH% commit -m "%msg%"

echo Mengirim ke GitHub (push origin main)...
%GIT_PATH% push origin main

echo.
echo ========================================================
echo   SELESAI! Seluruh kode telah tersinkron ke GitHub.
echo ========================================================
echo.
pause
