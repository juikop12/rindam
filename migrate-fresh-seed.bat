@echo off
chcp 65001 >nul
title SIPANDU-WBK — Reset & Seed Database
echo ========================================================
echo   SIPANDU-WBK: RESET & SEED DATABASE
echo ========================================================
echo.

set PHP_PATH=D:\laragon\bin\php\php-8.3.26-Win32-vs16-x64\php.exe

if not exist "%PHP_PATH%" (
    set PHP_PATH=php
)

echo Menjalankan: php artisan migrate:fresh --seed...
echo.

"%PHP_PATH%" artisan migrate:fresh --seed

echo.
echo Membersihkan cache aplikasi...
"%PHP_PATH%" artisan optimize:clear

echo.
echo ========================================================
echo   SELESAI! Database telah berhasil direset dan di-seed.
echo ========================================================
echo.
pause
