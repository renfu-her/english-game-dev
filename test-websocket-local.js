import { WebSocketServer } from 'ws';

const server = new WebSocketServer('ws://127.0.0.1:3000');

server.on('connection', socket => {
  console.log('Client connected');
  socket.send('Hello from server');
});
