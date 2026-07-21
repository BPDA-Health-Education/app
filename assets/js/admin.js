/* ── ADMIN DASHBOARD MODULES ──────────────────── */

async function pgAdminCalls() {
  if (ROLE !== 'ADMIN') { go('dashboard'); return; }
  const res = await api('api/admin/video-calls.php');
  if (!res.success) { setPage(errPage(em(res), "pgAdminCalls()")); return; }
  
  const calls = res.data || [];
  setPage(`
    <div class="page-header"><h1>Admin Call Queue</h1></div>
    <div class="table-wrap">
      ${calls.length === 0 ? `<div class="empty-state"><h3>No pending calls</h3></div>` : `
      <table><thead><tr><th>Requester</th><th>Current Receiver</th><th>Created</th><th>Actions</th></tr></thead>
      <tbody>${calls.map(c => `<tr>
        <td>${c.reqName}</td>
        <td>${c.recName}</td>
        <td>${fd(c.createdAt)}</td>
        <td>
          <button class="btn btn-sm btn-secondary" onclick="routeCall('${c.id}')">Route</button>
        </td>
      </tr>`).join('')}</tbody></table>`}
    </div>
  `);
}

async function routeCall(callId) {
  const newId = prompt('Enter New Receiver ID:');
  if (!newId) return;
  const res = await api(`api/admin/video-calls.php?id=${callId}`, {
    method: 'PATCH',
    body: JSON.stringify({ action: 'ROUTE', newReceiverId: newId })
  });
  if (res.success) { toast('Call routed!'); pgAdminCalls(); }
  else toast(em(res), 'error');
}
