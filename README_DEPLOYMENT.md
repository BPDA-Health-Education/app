# 🏥 PalliCare Telemedicine Platform

**Version:** 1.0.0  
**Status:** Production-Ready  
**Last Updated:** 2026-07-23

---

## 📋 Quick Start

**You have 5 minutes to get started:**

1. **Upload ZIP** → Extract in `public_html/app/` folder
2. **Create Database** → cPanel → MySQL (DB name, user, password)
3. **Run Setup** → Visit `https://yourdomain.com/app/setup.php`
4. **Enter credentials** → Database name, user, password
5. **Delete setup.php** → Security critical!
6. **Login** → Email: `admin@pallicare.dev` | Password: `password123`

📖 **See DEPLOYMENT_GUIDE.md for detailed instructions**

---

## ✨ Features Included

### ✅ Prescription Management
- Create, edit, review prescriptions
- Multi-step workflow (DRAFT → SUBMITTED → REVIEWED)
- General Practice & Dental templates
- Real-time status tracking

### ✅ Google Docs Integration
- Collaborative prescription editing
- Real-time sync with Google Docs
- Automatic sharing with assigned doctor
- OAuth 2.0 authentication

### ✅ Video Telehealth
- On-demand video calls (Google Meet)
- Automatic escalation to Admin if Doctor offline
- Call history & audit trail
- Load-balanced admin queue

### ✅ Admin Dashboard
- Global prescription viewer
- User management (Doctors/Health Workers/Suspended)
- Role-based access control (RBAC)
- Admin-only password reset with super-admin override

### ✅ Mobile Ready
- Fully responsive design
- Works on all devices (320px - 1920px)
- Touch-optimized buttons
- Fast SPA navigation

### ✅ Security
- Bcrypt password hashing
- httpOnly session cookies
- Role-based access control
- CSRF protection on OAuth
- Audit logging

---

## 🎯 Default Demo Users

| Email | Password | Role |
|-------|----------|------|
| `admin@pallicare.dev` | `password123` | Admin |
| `dr.karim@pallicare.dev` | `password123` | Doctor |
| `hw.rahim@pallicare.dev` | `password123` | Health Worker |

**⚠️ Change all passwords immediately after login!**

---

## 📂 What's Inside

```
app/
├── index.php              # Entry point
├── setup.php              # Database setup (delete after!)
├── config/                # Configuration files
├── api/                   # REST API endpoints
├── assets/                # CSS & JavaScript
├── database/              # Database schema
├── pages/                 # Static pages (About, Privacy, Terms)
└── [Docs]
    ├── DEPLOYMENT_GUIDE.md    # ← Start here!
    ├── GOOGLE_DOCS_SETUP.md
    └── IMPLEMENTATION_STATUS.md
```

---

## 🚀 After Deployment

### 1. Secure Your Installation
- [ ] Delete `setup.php`
- [ ] Change all demo passwords
- [ ] Configure `SUPER_ADMIN_ID` in `config/database.php`
- [ ] Enable HTTPS (should be auto)

### 2. Customize
- [ ] Update organization name in footer
- [ ] Edit About Us, Privacy Policy, Terms pages
- [ ] Add social media links (Facebook, Instagram, etc.)
- [ ] Configure Twak.to chat widget (optional)

### 3. Enable Google Features (Optional)
- [ ] Create Google Cloud project
- [ ] Get OAuth credentials
- [ ] Update `config/google.php` with credentials
- [ ] See GOOGLE_DOCS_SETUP.md for details

### 4. Add Users
- [ ] Login as admin
- [ ] Go to Users page
- [ ] Create doctors and health workers
- [ ] Assign health workers to doctors

---

## 🔒 Security Checklist

- [ ] HTTPS enabled
- [ ] setup.php deleted
- [ ] Default passwords changed
- [ ] Database user has limited permissions
- [ ] All file permissions set correctly (755)
- [ ] Regular backups configured
- [ ] Error logs monitored

---

## 📊 System Requirements

- PHP 7.4 or higher (recommend 8.1+)
- MySQL 5.7 or higher (or MariaDB 10.3+)
- 50MB disk space
- HTTPS/SSL certificate
- cURL enabled (for Google APIs)

**Verify in cPanel → Select PHP Version**

---

## 🆘 Need Help?

### Common Issues

**"Can't connect to database"**
- Verify database credentials in cPanel
- Check MySQL host (usually `localhost`)
- Re-run setup.php if needed

**"Blank page / 403 error"**
- Set file permissions: `chmod 755 *.php`
- Check PHP version is 7.4+
- Review error_log in cPanel

**"Login fails"**
- Verify setup.php ran successfully
- Check cookies are enabled in browser
- Try private/incognito mode

**"Styles not loading"**
- Check CSS files exist in `assets/css/`
- Verify relative paths in HTML
- Check browser console for 404 errors

### Documentation

- **Setup Instructions:** See `DEPLOYMENT_GUIDE.md`
- **Google Oauth Setup:** See `GOOGLE_DOCS_SETUP.md`
- **Feature Status:** See `IMPLEMENTATION_STATUS.md`
- **Database Schema:** See `database/schema.sql`

---

## 📞 Contact & Support

For issues with:
- **Deployment:** Contact Namecheap support
- **Database:** See DEPLOYMENT_GUIDE.md troubleshooting
- **Features:** Check IMPLEMENTATION_STATUS.md
- **Google APIs:** See GOOGLE_DOCS_SETUP.md

---

## 📝 Next Steps

1. ✅ Extract this ZIP in cPanel
2. ✅ Follow DEPLOYMENT_GUIDE.md
3. ✅ Login and explore dashboard
4. ✅ Create test prescriptions
5. ✅ Invite team members
6. ✅ Enable Google features (optional)
7. ✅ Train users on system

---

## 📊 Feature Roadmap

**Current (v1.0):**
- ✅ Prescription management
- ✅ Google Docs integration
- ✅ Video telehealth with escalation
- ✅ Admin dashboard & RBAC
- ✅ Mobile responsive

**Coming Soon:**
- ⏳ Email notifications
- ⏳ SMS alerts (Twilio)
- ⏳ Advanced reporting & analytics
- ⏳ AI-powered prescription suggestions

---

## 🎉 Ready to Go!

Your telemedicine platform is ready to serve your community.

**Start here:** Read DEPLOYMENT_GUIDE.md for step-by-step setup.

Good luck! 🚀

---

**PalliCare Telemedicine Platform v1.0.0**  
Built for health workers, doctors, and administrators  
Deployed: 2026-07-23
