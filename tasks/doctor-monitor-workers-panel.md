Title: Doctor: Monitor Workers Live Panel
Estimated effort: 4–6 days
Blockers: permissions, filtering by assignments, real-time feed availability

Overview
- Dashboard for doctors to watch assigned Health Workers' live activity and recent history.

Dependencies
- Real-time activity stream (Socket.IO) providing events per document or worker
- Assignment service mapping doctors to workers

Backend endpoints
- GET /api/doctor/:id/assigned-workers -> returns list of workers
- GET /api/doctor/:id/worker-activity?workerId= -> returns recent activity history

Frontend components
- LivePanel: subscribes to WS channel filtered to assigned worker IDs
- WorkerCard: shows status (online/offline), last action, last active timestamp, quick actions (message, escalate)

Sample React skeleton

function DoctorMonitor({doctorId}){
  const [workers, setWorkers] = useState([]);
  useEffect(()=>{ fetch(`/api/doctor/${doctorId}/assigned-workers`).then(r=>r.json()).then(setWorkers) },[]);
  return <div>{workers.map(w=> <WorkerCard key={w.id} worker={w} />)}</div>
}

WorkerCard listens to socket events by subscribing to `worker_${id}` channel and updates lastAction state.

Security
- Only return workers assigned to doctor (enforce server-side authorization)

UX
- Provide filters (active only, by location), search, and ability to open worker's live session snapshot.

Testing
- Mock WS events and ensure UI updates. Validate permission checks on backend.
