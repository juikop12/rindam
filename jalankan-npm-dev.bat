@echo off
chcp 65001 >nul
title SIPANDU-WBK — Vite Dev Server (npm run dev)
echo ========================================================
echo   SIPANDU-WBK: VITE DEV SERVER (npm run dev)
echo ========================================================
echo.

set PATH=D:\laragon\bin\nodejs\node-v22;%PATH%

echo Menjalankan Vite Dev Server pada http://localhost:5173 ...
echo Tekan Ctrl+C jika ingin menghentikan server dev.
echo.

call "D:\laragon\bin\nodejs\node-v22\npm.cmd" run dev
pause
