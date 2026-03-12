@echo off
chcp 65001 >nul
title PHP Ollama Web UI Launcher

echo.
echo ========================================
echo     PHP Ollama Web UI Launcher
echo ========================================
echo.

echo [1/3] Checking PHP installation...
php --version >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] PHP is not installed or not in PATH
    echo Please install PHP and make sure it's in your system PATH
    echo.
    pause
    exit /b 1
)
echo [OK] PHP is installed

echo.
echo [2/3] Checking Ollama server status...
curl -s http://localhost:11434/api/tags >nul 2>&1
if %errorlevel% neq 0 (
    echo [WARNING] Ollama server is not running
    echo Starting Ollama...
    start "" ollama serve
    timeout /t 5 /nobreak >nul
    echo.
) else (
    echo [OK] Ollama server is running
)

echo.
echo [3/3] Starting PHP Web server...
echo Access at: http://localhost:8080/
echo Press Ctrl+C to stop
echo.
php -S 0.0.0.0:8080 index.php

pause
