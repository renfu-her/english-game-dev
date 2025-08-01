@echo off
cd /d "D:\laragon\www\english-game"
echo Starting WebSocket Server...
pm2 start websocket-server.js --name websocket-server --watch
echo WebSocket Server started successfully!
pause 