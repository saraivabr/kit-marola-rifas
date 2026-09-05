<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover" />
    <meta name="theme-color" content="#f8fafc" id="meta-theme-color" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="default" />
    <meta name="apple-mobile-web-app-title" content="Kit Marola" />
    <link rel="manifest" href="/manifest.json" />
    <link rel="apple-touch-icon" href="/images/logo.png" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script>
      (function() {
        try {
          if (!sessionStorage.getItem('theme_v2_reset')) {
            sessionStorage.setItem('theme_v2_reset', '1');
            localStorage.setItem('theme', 'light');
          }
          const storedTheme = localStorage.getItem('theme');
          if (storedTheme === 'dark') {
            document.documentElement.classList.add('dark');
          } else {
            document.documentElement.classList.remove('dark');
          }
        } catch (e) {
          document.documentElement.classList.remove('dark');
        }
      })();
    </script>
    @routes
    @vite('resources/js/app.ts')
    @inertiaHead
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Raleway:wght@800&display=swap" rel="stylesheet">
  </head>
  <body>
    @inertia
  </body>
</html>
