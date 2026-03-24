@echo off
set PHP_PATH="C:\Users\RSIAIBI-Develop\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.2_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe"
set NODE_PATH="C:\Program Files\nodejs\node.exe"
set NPM_PATH="C:\Program Files\nodejs\npm.cmd"

if "%1"=="serve" (
    echo Starting Laravel server with PHP 8.2...
    %PHP_PATH% artisan serve
) else if "%1"=="dev" (
    echo Starting Vite with Node.js...
    %NPM_PATH% run dev
) else if "%1"=="install" (
    echo Installing dependencies...
    %NPM_PATH% install
) else (
    echo Usage: run.bat [serve|dev|install]
)
