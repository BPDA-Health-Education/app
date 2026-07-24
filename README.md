BPDA Smart App — Full-stack scaffolding

Overview
This repository contains scaffolding for "BPDA Smart App": Laravel 11 + Jetstream (Inertia/Vue 3) SPA with multi-role access (Health Worker, Doctor, Admin, Super Admin), real-time sync, and video-call request flow.

Quick start (local)
1. Install PHP/Composer/Node: PHP 8.2+, Composer, Node 18+
2. From project root:
   composer install
   cp .env.example .env
   php artisan key:generate
   npm install
   npm run dev
3. Run migrations:
   php artisan migrate
4. Install Jetstream (Inertia + Vue) and configure per Jetstream docs.

Android APK (overview)
- This is a PWA-first webapp. To produce an APK, build a Trusted Web Activity wrapper using Android Studio
- See tasks/README_SNIPPET.md for a step-by-step outline to create an APK wrapper that points to the webapp homepage.

Security note
- Do NOT commit real secrets. Keep .env out of git and use SECRET managers in production.

Deliverables included:
- config/auth.php (multi-guard stub)
- migrations/ (users, medicines, prescriptions, items, assignments, calls, audit logs)
- models/ and controller skeletons
- resources/js/Pages/ Vue 3 placeholders
- resources/views/app.blade.php with GA/PostHog/Tawk stubs

Next steps (recommended)
- Install Laravel, Jetstream (Inertia) and run the dev server
- Wire broadcasting (Pusher/Redis) and configure real-time server
- Replace the video iframe placeholders with Jitsi/Daily integrations and TURN/STUN as needed

If you want, I can scaffold additional controllers, form requests and Vue forms for one feature (e.g., prescription Create) next.