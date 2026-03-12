<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Docs - TimeTrack</title>
    <link rel="icon" type="image/png" href="/Logo.png">
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css">
    <style>
        body { margin: 0; background: #f8fafc; }
        .topbar { display: none !important; }
        .swagger-ui .info { margin: 24px 0 16px; }
        .swagger-ui .info .title { font-size: 2rem; }
        #api-header {
            background: #0f172a;
            color: white;
            padding: 14px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        #api-header h1 { font-size: 1.1rem; font-weight: 700; margin: 0; }
        #api-header span.blue { color: #60a5fa; }
        #api-header span.green { color: #4ade80; }
        #api-header a {
            margin-left: auto;
            font-size: 0.8rem;
            color: #94a3b8;
            text-decoration: none;
        }
        #api-header a:hover { color: white; }
    </style>
</head>
<body>

<div id="api-header">
    <svg width="24" height="24" fill="none" stroke="#60a5fa" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <h1><span class="blue">Time</span><span class="green">Track</span> — Documentación API</h1>
    <a href="{{ route('admin.dashboard') }}">← Volver al panel</a>
</div>

<div id="swagger-ui"></div>

<script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
<script>
    SwaggerUIBundle({
        url: '/api/openapi.yaml',
        dom_id: '#swagger-ui',
        presets: [SwaggerUIBundle.presets.apis, SwaggerUIBundle.SwaggerUIStandalonePreset],
        layout: 'BaseLayout',
        deepLinking: true,
        displayRequestDuration: true,
        filter: true,
        tryItOutEnabled: true,
        requestSnippetsEnabled: true,
    });
</script>
</body>
</html>
