@echo off
chcp 65001 >nul
title SIPANDU-WBK — Vite Dev Server (npm run dev)
echo ========================================================
echo   SIPANDU-WBK: VITE DEV SERVER (npm run dev)
echo ========================================================
echo.

if exist "D:\laragon\bin\nodejs\node-v22\npm.cmd" (
    set PATH=D:\laragon\bin\nodejs\node-v22;%PATH%
    call "D:\laragon\bin\nodejs\node-v22\npm.cmd" run dev
) else (
    call npm run dev
)
pause
