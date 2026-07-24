Title: Real-time Activity Stream (WebSocket)
Estimated effort: 7–10 days
Blockers: WS auth, scale (many connections), privacy for PHI

Goal
- Show who is editing/writing in real time (who is typing, what content being edited).
- Activity stream should be permissioned (only admins or doctors see certain users).

Architecture recommendation
- Use Node.js + Socket.IO for server, Redis for pub/sub across nodes.
- Clients connect via secure WebSocket (wss) with short-lived auth token (JWT)

Server sample (Node.js + Socket.IO)

// server.js
const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const redisAdapter = require('socket.io-redis');
const ioApp = express();
const server = http.createServer(ioApp);
const io = new Server(server, { cors: { origin: '*' }});
io.adapter(redisAdapter({ host: '127.0.0.1', port: 6379 }));

io.use((socket,next)=>{
  const token = socket.handshake.auth.token;
  // verify token (JWT) and attach user
  next();
});

io.on('connection', socket =>{
  socket.on('start_edit', data =>{
    // broadcast to room
    const payload = { userId: socket.user.id, docId: data.docId, cursor: data.cursor };
    io.to(`doc_${data.docId}`).emit('user_editing', payload);
  });
  socket.on('join_doc', data => socket.join(`doc_${data.docId}`));
});

server.listen(3001);

Client example (socket.io-client)

const socket = io('https://yourdomain', { auth: { token: JWT } });
socket.emit('join_doc', {docId});
textarea.addEventListener('input', () => socket.emit('start_edit', {docId, cursor: getCursor()}));

Privacy & PHI
- Never transmit full PHI content in events. Only transmit metadata (user id, cursor position, action type). If snippet needed, scrub PII before send.

Scaling
- Use Redis adapter and scale multiple Node workers behind load balancer.
- Use sticky sessions or pass socket.io through load balancer with websocket support.

Frontend UI
- Activity panel: list of active editors, small live indicator, tooltip with last action and time
- Optional: show live cursors (use OT/CRDT for collaborative editing if required)

Testing
- Simulate 40 concurrent clients with simple WS script
- Validate auth, join/leave events, performance under load

Deliverables
- server.js + Dockerfile
- small client widget component
- integration tests
