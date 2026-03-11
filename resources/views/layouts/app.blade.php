<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TimeTrack')</title>
    <link rel="icon" type="image/png" href="/Logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1d4ed8',
                    }
                }
            }
        }
    </script>
    @stack('styles')
</head>
<body class="bg-gray-100 min-h-screen">
    @yield('content')
    @stack('scripts')
</body>
</html>
