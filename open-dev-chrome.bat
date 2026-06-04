@echo off
echo Membuka AgriMapGIS dengan GPS/Geolocation aktif...
start "" "C:\Program Files\Google\Chrome\Application\chrome.exe" ^
  --unsafely-treat-insecure-origin-as-secure=http://agrimapgis.test ^
  --user-data-dir="%TEMP%\chrome-dev-agrimapgis" ^
  --test-type ^
  http://agrimapgis.test/dashboard

