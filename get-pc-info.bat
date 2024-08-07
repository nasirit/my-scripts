@echo off
cls
echo PC Information:
echo User: %username%
systeminfo | findstr /i /c:"Host Name" /c:"OS Name" /c:"System Model" /c:"Total Physical Memory"
echo.
echo Processor Information:
wmic cpu get name
echo Disk Information:
wmic diskdrive get model,size
echo Printer Information:
wmic printer get name, description, status
ipconfig | findstr /i /c:"IPv4 Address"
echo.
for /f "delims=" %%i in ('"C:\Program Files (x86)\AnyDesk\AnyDesk.exe" --get-id') do set AnyID=%%i 
echo AnyDesk ID is: %AnyID%
echo.
pause
