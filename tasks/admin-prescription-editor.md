Title: Admin Prescription Direct Editing Interface
Estimated effort: 5–8 days
Blockers: permission model, large-content storage, audit/version UI

Overview
- Provide an admin UI to edit prescription content directly, with versioning, audit trail, and optional approval workflow.

Database schema (MySQL example)

CREATE TABLE prescriptions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  patient_id INT NOT NULL,
  current_version_id INT DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE prescription_versions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  prescription_id INT NOT NULL,
  author_id INT NOT NULL,
  content TEXT NOT NULL,
  note VARCHAR(255),
  status ENUM('draft','pending_approval','approved','rejected') DEFAULT 'draft',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX(prescription_id)
);

When admin approves a version, update prescriptions.current_version_id.

Backend endpoints (PHP examples)
- GET /admin/prescriptions/:id -> returns current and versions
- POST /admin/prescriptions/:id/versions {content, note} -> create new version (status=draft)
- POST /admin/prescriptions/:id/versions/:vid/submit -> set status=pending_approval
- POST /admin/prescriptions/:id/versions/:vid/approve -> set status=approved and update prescriptions.current_version_id

Controller stub (pseudo-PHP)

function create_version($prescription_id, $author_id, $content, $note) {
  $pdo->prepare('INSERT INTO prescription_versions (prescription_id, author_id, content, note) VALUES (?, ?, ?, ?)')->execute([$prescription_id,$author_id,$content,$note]);
}

Frontend (React) mockup
- Modal editor with autosave: call POST /admin/prescriptions/:id/versions with content periodically
- Show diff between versions (use diff-match-patch) and preview render

Sample React skeleton

function AdminPrescriptionEditor({prescriptionId}) {
  const [content,setContent] = useState('');
  useEffect(()=>{ fetch(`/admin/prescriptions/${prescriptionId}`).then(r=>r.json()).then(j=>setContent(j.current_content)) },[]);
  const autosave = useRef(null);
  function handleChange(e){ setContent(e.target.value); clearTimeout(autosave.current); autosave.current=setTimeout(()=>saveDraft(),2000); }
  async function saveDraft(){ await fetch(`/admin/prescriptions/${prescriptionId}/versions`, {method:'POST',headers:{'Content-Type':'application/json'}, body:JSON.stringify({content})}) }
  return <textarea value={content} onChange={handleChange} />
}

Audit & version UI
- Show version list with timestamp, author, status, and diff/preview
- Allow rollback to previous approved version

Security & permissions
- Only admins with 'prescription.edit' permission can create/approve

Testing
- Unit test for DB migrations
- Integration tests: create version -> submit -> approve -> verify current_version updated

Notes & blockers
- Large TEXT storage ok in MySQL; consider external document store if very large
- If offline edits required, use a client-side queue + sync
