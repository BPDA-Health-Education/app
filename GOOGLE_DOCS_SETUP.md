# Google Docs Integration Setup Guide

## Overview
This guide covers setting up collaborative prescription editing using Google Docs API. This allows Health Workers and Doctors to edit prescriptions in real-time within Google Docs, with automatic sharing and permission management.

## Prerequisites
- Google Cloud Project (free tier available)
- Admin access to Google Cloud Console
- Domain with HTTPS (required for OAuth 2.0 redirect)

---

## Step 1: Create Google Cloud Project

1. Visit [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project
3. Enable these APIs:
   - **Google Drive API** (file storage & permissions)
   - **Google Docs API** (document structure)
   - **Google Meet API** (video calls, added for later telehealth feature)

### Enable APIs
- Navigate to "APIs & Services" → "Library"
- Search for each API and click "Enable"

---

## Step 2: Create OAuth 2.0 Credentials

1. Go to "APIs & Services" → "Credentials"
2. Click "Create Credentials" → "OAuth 2.0 Client ID"
3. Choose "Web application"
4. Add authorized redirect URIs:
   - `https://yourdomain.com/api/google/callback.php` (production)
   - `https://localhost/api/google/callback.php` (development)
5. Click "Create"
6. Download JSON credentials

---

## Step 3: Configure Application

### In `config/google.php`:

```php
define('GOOGLE_CLIENT_ID',     'YOUR_CLIENT_ID_HERE');
define('GOOGLE_CLIENT_SECRET', 'YOUR_CLIENT_SECRET_HERE');
define('GOOGLE_REDIRECT_URI',  'https://yourdomain.com/api/google/callback.php');
```

### Extract from downloaded JSON:
- `client_id` → `GOOGLE_CLIENT_ID`
- `client_secret` → `GOOGLE_CLIENT_SECRET`

---

## Step 4: User OAuth Flow

### First-Time Connection:
1. User clicks "Link Google Docs" on prescription detail page
2. Redirected to `/api/google/login.php`
3. Shown Google consent screen (requesting Drive & Docs access)
4. Redirected back to `/api/google/callback.php` with auth code
5. App exchanges code for access token
6. Token stored in `google_oauth_tokens` table

### Automatic Token Refresh:
- When token expires, app uses refresh_token to get new access_token
- No user action required

---

## Step 5: Workflow

### Creating Collaborative Document:

1. User on prescription detail page clicks "Link Google Docs"
2. Prompted for document title
3. App creates blank Google Doc in user's Drive
4. Document opened in new tab
5. Document ID stored in `google_docs_integrations` table

### Sharing Document:

1. Doctor or Health Worker shares document via "Share" button
2. Prompted for recipient email
3. Recipient granted "editor" permission
4. Can now collaboratively edit in real-time

### Accessing Document:

- Embedded link in prescription detail view
- Direct URL: `https://docs.google.com/document/d/{DOC_ID}/edit`
- Works across browsers (must have Google account)

---

## Database Schema

### `google_oauth_tokens` Table:
```sql
- id (VARCHAR 36, PK)
- user_id (VARCHAR 36, FK→users.id)
- access_token (TEXT)
- refresh_token (TEXT, nullable)
- expires_at (DATETIME)
- created_at, updated_at (DATETIME)
```

### `google_docs_integrations` Table:
```sql
- id (VARCHAR 36, PK)
- prescription_id (VARCHAR 36, FK→prescriptions.id)
- google_doc_id (VARCHAR 255)
- google_doc_url (VARCHAR 500)
- document_title (VARCHAR 200)
- last_synced_at (DATETIME, nullable)
- created_at, updated_at (DATETIME)
```

---

## API Endpoints

### POST `/api/google/docs.php`
**Create new collaborative document**

```json
{
  "prescriptionId": "rx-001",
  "title": "Prescription - Patient Name"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "integ-xyz",
    "docId": "google-doc-id",
    "docUrl": "https://docs.google.com/document/d/...",
    "title": "...",
    "createdAt": "2026-07-23T04:33:04Z"
  }
}
```

### GET `/api/google/docs.php?id=rx-001`
**Fetch document details for prescription**

### PATCH `/api/google/docs.php`
**Share document with another user**

```json
{
  "docId": "google-doc-id",
  "email": "dr.karim@pallicare.dev"
}
```

### DELETE `/api/google/docs.php?id=google-doc-id`
**Revoke access / trash document**

---

## Security Considerations

1. **OAuth Scopes**: Minimal permissions requested
   - `drive` (read/write Google Drive)
   - `documents` (read/write Google Docs)
   - `userinfo.email` (verify user email)

2. **Token Storage**: 
   - Refresh token stored securely in DB (not sent to client)
   - Access token has short lifetime (1 hour default)
   - Automatic refresh handled server-side

3. **Document Sharing**:
   - Only document owner/creator can share
   - Email validation on recipient side (Google's OAuth)
   - Editor-level access only (no ownership transfer)

4. **CSRF Protection**:
   - State token generated for OAuth flow
   - Verified on callback to prevent attack

---

## Testing

### Development (with localhost):
```bash
# Update config/google.php:
define('GOOGLE_REDIRECT_URI', 'https://localhost/api/google/callback.php');
# Then add to Google OAuth credentials as authorized URI
```

### Test Flow:
1. Visit prescription detail page
2. Click "Link Google Docs"
3. Authenticate with test Google account
4. Create sample document
5. Verify document appears in Google Drive
6. Test sharing with another email

---

## Troubleshooting

### "Google OAuth not connected" error
- User hasn't linked Google account yet
- Redirect them to `/api/google/login.php`

### "Failed to obtain access token"
- Check GOOGLE_CLIENT_ID and CLIENT_SECRET are correct
- Verify redirect URI matches exactly in Google Cloud console
- Check API credentials haven't been deleted

### Document creation fails
- Verify Google Drive API is enabled
- Check user has Google Drive quota available
- Review PHP error logs for CURL errors

### Token refresh fails
- Refresh token may have been revoked by user
- Force user to reconnect via `/api/google/login.php`
- Check database `google_oauth_tokens` row exists

---

## Next Steps

1. **Google Meet Integration** - Video telehealth calls between Health Workers and Doctors
2. **Real-time Sync** - Poll Google Docs for changes and sync to prescriptions DB
3. **Template Support** - Allow users to start from pre-approved prescription templates
4. **Offline Mode** - Cache documents locally for offline access (optional)
5. **Audit Logging** - Track who edited what and when in collaboration history

---

## References

- [Google Drive API Docs](https://developers.google.com/drive/api)
- [Google Docs API Docs](https://developers.google.com/docs/api)
- [OAuth 2.0 Flow](https://developers.google.com/identity/protocols/oauth2)
