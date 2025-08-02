import { WebSocketServer } from 'ws';

const server = new WebSocketServer({ port: 3000 });

server.on('connection', socket => {
  console.log('Client connected');
  socket.send('Hello from server');
});
