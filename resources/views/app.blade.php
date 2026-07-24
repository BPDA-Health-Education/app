<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BPDA Smart App</title>
    @routes
    @vite(['resources/js/app.js'])
    @inertiaHead
    <!-- PostHog & Google Analytics (insert your keys into .env.local) -->
    <script>/* POSTHOG & GA placeholder - add tracking ids in .env */</script>
</head>
<body>
    @inertia

    <!-- Tawk.to widget placeholder: add actual script in production -->
    <script>/* Tawk.to chat script placeholder */</script>

    <!-- PWA manifest link -->
    <link rel="manifest" href="/manifest.json">
</body>
</html>