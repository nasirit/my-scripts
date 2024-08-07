@echo off
set defaultIP=192.168.10.1
set /p targetIP=Enter target IP address (leave blank for %defaultIP%): 
if "%targetIP%"=="" set targetIP=%defaultIP%

set soundFile="C:\Users\nasiruddin\Music\acoustic-guitar-loop-f-91bpm-132687.mp3"

:loop
ping -n 2 %targetIP% > nul
if errorlevel 1 (
    echo Target %targetIP% is offline
) else (
    echo Target %targetIP% is online
    start /min "sound" %soundFile%
)
timeout /t 300
goto loop
