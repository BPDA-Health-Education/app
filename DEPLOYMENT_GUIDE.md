# PalliCare Telemedicine - Namecheap cPanel Deployment Guide

## 📦 Package Contents

This ZIP file contains the complete PalliCare Telemedicine platform ready for deployment on Namecheap shared hosting.

---

## 🚀 Quick Deployment Steps

### Step 1: Extract ZIP in cPanel
1. **SSH / File Manager:**
   - SSH: `scp pallicare-telemedicine-app.zip user@server.com:~/public_html/`
   - OR use File Manager in cPanel to upload the ZIP

2. **Extract in cPanel File Manager:**
   - Right-click ZIP → "Extract"
   - OR use terminal: `unzip -q pallicare-telemedicine-app.zip`
   
3. **Result:** `public_html/app/` folder with all files

### Step 2: Create MySQL Database

1. **cPanel → MySQL Databases:**
   - Create new database (e.g., `youraccount_pallicare`)
   - Create database user (e.g., `youraccount_user`)
   - Set strong password
   - Assign user to database with ALL privileges

2. **Note credentials** - you'll need them for setup.php

### Step 3: Run Setup Wizard

1. **Visit:** `https://yourdomain.com/app/setup.php`
2. **Enter database credentials:**
   - Host: `localhost`
   - Database Name: `youraccount_pallicare`
   - Database Username: `youraccount_user`
   - Database Password: (from Step 2)
3. **Click:** "Run Setup & Create Tables"
4. **Success page** shows demo credentials

### Step 4: Delete setup.php

**⚠️ IMPORTANT for security:**
```bash
rm public_html/app/setup.php
```

Or use cPanel File Manager: Delete `setup.php`

### Step 5: Test Login

1. **Visit:** `https://yourdomain.com/app/`
2. **Login with demo user:**
   - Email: `admin@pallicare.dev`
   - Password: `password123`
   - Or other demo users from setup page

---

## 🔧 Configuration

### Critical Configuration Files

#### 1. `config/database.php`
- **Auto-configured** by setup.php
- Contains DB connection details
- Verify after setup that all values are correct

#### 2. `config/google.php` (Optional - for Google Docs/Meet)
- Leave empty to skip Google features initially
- To enable later:
  1. Create Google Cloud project
  2. Get OAuth credentials
  3. Update `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`
  4. Update `GOOGLE_REDIRECT_URI` to your domain
  5. Set `SUPER_ADMIN_ID` to designated admin user ID

#### 3. `.env` or Environment Variables (Optional)
- Can add `.env` file for secrets
- Example: `GOOGLE_CLIENT_SECRET=xxxx`
- Load in helpers.php if used

---

## 📝 Default Demo Users

**All passwords:** `password123`

| Email | Role | Purpose |
|-------|------|---------|
| `admin@pallicare.dev` | ADMIN | System administrator |
| `dr.karim@pallicare.dev` | DOCTOR | Sample doctor |
| `dr.mina@pallicare.dev` | DOCTOR | Sample doctor |
| `hw.rahim@pallicare.dev` | HEALTH_WORKER | Sample health worker |
| `hw.nasrin@pallicare.dev` | HEALTH_WORKER | Sample health worker |
| `hw.jalal@pallicare.dev` | HEALTH_WORKER | Pending (needs activation) |

**First Steps:**
1. Login as `admin@pallicare.dev`
2. Go to Users page
3. Change all demo passwords
4. Create real users for your team

---

## 🔒 Security Checklist

- [ ] Run setup.php and **delete it immediately**
- [ ] Change all default passwords
- [ ] Enable HTTPS/SSL (should be auto on Namecheap)
- [ ] Set strong DB password
- [ ] Configure `SUPER_ADMIN_ID` in config
- [ ] Disable public registration (edit auth/register.php)
- [ ] Configure email server for notifications (future feature)
- [ ] Review and update Privacy Policy & Terms
- [ ] Set up regular database backups via cPanel

---

## 📂 Directory Structure

```
public_html/app/
├── index.php                  # Entry point - load this in browser
├── setup.php                  # Database setup (DELETE after setup!)
│
├── config/
│   ├── database.php          # DB connection (auto-configured)
│   ├── google.php            # Google OAuth config (optional)
│   └── helpers.php           # Utility functions
│
├── api/                       # REST API endpoints
│   ├── auth/                 # Authentication
│   ├── prescriptions.php     # Prescription management
│   ├── google/               # Google Docs & OAuth
│   ├── video/                # Video calls & escalation
│   └── admin/                # Admin-only endpoints
│
├── assets/
│   ├── css/
│   │   ├── app.css          # Main styles
│   │   ├── mobile.css       # Mobile responsive
│   │   └── footer.css       # Footer & social
│   └── js/
│       ├── app.js           # Core app logic (SPA)
│       ├── admin.js         # Admin dashboard logic
│       └── pages.js         # Static page loaders
│
├── database/
│   └── schema.sql           # Database schema definition
│
├── pages/                    # Static pages
│   ├── about.html
│   ├── privacy.html
│   └── terms.html
│
└── [Documentation]
    ├── GOOGLE_DOCS_SETUP.md
    ├── IMPLEMENTATION_STATUS.md
    └── README.md
```

---

## 🔍 Testing the Installation

### 1. Dashboard
- [ ] Can you see dashboard stats?
- [ ] Do navigation links work?

### 2. Prescriptions
- [ ] Can create new prescription (as Health Worker)?
- [ ] Can view prescription list?
- [ ] Can filter by type/status?

### 3. Users (Admin)
- [ ] Can view all users?
- [ ] Can see three tabs (Doctors/Health Workers/Suspended)?
- [ ] Can reset a user's password?

### 4. Mobile
- [ ] Test on phone/tablet
- [ ] Sidebar collapses?
- [ ] Buttons are touchable?

### 5. OAuth (Optional, if configured)
- [ ] Google login shows "Link Google Docs"?
- [ ] Can create Google Doc?

---

## 🆘 Troubleshooting

### "403 - Forbidden" or Blank Page
**Cause:** PHP not executable or wrong file permissions
**Solution:** 
```bash
chmod 755 public_html/app/*.php
chmod 755 public_html/app/api/*.php
chmod -R 755 public_html/app/
```

### "Can't connect to MySQL server"
**Cause:** Wrong database credentials in `config/database.php`
**Solution:**
1. Verify DB name, user, password in cPanel → MySQL
2. Re-run setup.php with correct credentials
3. Check DB host (usually `localhost`)

### "setup.php won't run / SQL errors"
**Cause:** Database already exists or schema issues
**Solution:**
1. Delete the database in cPanel
2. Recreate empty database
3. Re-run setup.php

### "404 Not Found" on API calls
**Cause:** `.htaccess` redirect issues
**Solution:**
1. Check `index.php` exists in `public_html/app/`
2. Verify `.htaccess` settings (if using one)
3. Try accessing `https://yourdomain.com/app/api/auth/login.php` directly

### "Login fails / Session errors"
**Cause:** Session path or PHP settings
**Solution:**
1. Check `/tmp` folder is writable: `chmod 777 /tmp`
2. Or specify session.save_path in php.ini:
   ```ini
   session.save_path = "/home/youraccount/tmp"
   ```
3. Restart PHP if possible

### "Styles not loading / CSS missing"
**Cause:** CSS file path issue
**Solution:**
1. Verify CSS files in `public_html/app/assets/css/`
2. Check browser console for 404 errors
3. Verify CSS link paths are relative: `assets/css/app.css`

---

## 📞 Next Steps After Deployment

### 1. Customize Branding
- Edit `index.php`: Change "PalliCare Community Clinic" to your organization
- Edit `pages/about.html`, `privacy.html`, `terms.html`
- Update footer links and social media URLs

### 2. Enable Google Features (Optional)
- Follow GOOGLE_DOCS_SETUP.md for OAuth configuration
- Set `GOOGLE_CLIENT_ID` and `GOOGLE_CLIENT_SECRET`

### 3. Setup Email Notifications (Optional, Future)
- Configure SMTP in `config/` or PHP settings
- Implement in `api/` endpoints

### 4. User Training
- Provide role-specific guides:
  - Health Worker workflow
  - Doctor review workflow
  - Admin management tasks

### 5. Data Migration (Optional)
- If importing existing prescription data
- Use database import tools in cPanel
- Ensure data format matches schema

---

## 📊 Server Requirements Verification

**Check in cPanel → Select PHP Version:**

- ✅ PHP 7.4+ (recommend 8.1+)
- ✅ MySQL 5.7+ or MariaDB 10.3+
- ✅ OpenSSL (for HTTPS/OAuth)
- ✅ cURL (for Google API calls)
- ✅ 50MB disk space minimum

**Enable Extensions (if not enabled):**
- pdo_mysql
- curl
- json
- spl
- reflection

---

## 🔄 Updates & Maintenance

### Regular Backups
- cPanel → Backup → Download full backup monthly
- Or use automated backup service

### Database Optimization
```sql
-- In cPanel → phpMyAdmin, run periodically:
OPTIMIZE TABLE prescriptions;
OPTIMIZE TABLE prescription_items;
OPTIMIZE TABLE users;
```

### Log Monitoring
- Check `error_log` in cPanel File Manager
- Monitor for PHP errors or suspicious activity

### Security Patches
- Keep PHP version updated (cPanel → Select PHP Version)
- Monitor GitHub releases for security updates

---

## 📞 Support Resources

- **Google Docs Setup:** See `GOOGLE_DOCS_SETUP.md`
- **Feature Status:** See `IMPLEMENTATION_STATUS.md`
- **API Documentation:** Inline code comments in `api/` folder
- **Database Schema:** `database/schema.sql`

---

## ⚡ Quick Command Reference

### SSH Access (if available)
```bash
# Connect
ssh youraccount@yourserver.com

# Navigate to app
cd public_html/app

# Change file permissions
chmod 755 *.php
chmod -R 755 api/

# View PHP errors
tail -f /home/youraccount/public_html/error_log

# Restart PHP (ask Namecheap support)
# Not available on shared hosting - contact support
```

### cPanel File Manager
1. Right-click file → Permissions → Set to 755
2. Extract ZIP by right-clicking → Extract
3. Delete files: Select → Delete

---

## ✅ Deployment Complete!

Once you've followed all steps:
1. ✅ Extract ZIP in public_html
2. ✅ Create MySQL database
3. ✅ Run setup.php
4. ✅ Delete setup.php
5. ✅ Login and test
6. ✅ Configure branding

**Your PalliCare instance is live!** 🎉

---

**Version:** 1.0.0  
**Deployment Date:** 2026-07-23  
**For Help:** Contact Namecheap support or app developers
