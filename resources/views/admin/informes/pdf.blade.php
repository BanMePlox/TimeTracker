<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe mensual — {{ ucfirst($mesNombre) }} {{ $anio }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; font-size: 13px; color: #1e293b; background: #fff; }

        .header { display: flex; align-items: center; justify-content: space-between; padding: 24px 32px; border-bottom: 2px solid #e2e8f0; margin-bottom: 24px; }
        .logo { font-size: 22px; font-weight: 800; }
        .logo span:first-child { color: #3b82f6; }
        .logo span:last-child  { color: #10b981; }
        .header-info { text-align: right; color: #64748b; font-size: 12px; }
        .header-info strong { display: block; font-size: 16px; color: #1e293b; margin-bottom: 2px; }

        .empleado-section { margin: 0 32px 32px; }
        .empleado-header { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 18px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center; }
        .empleado-name { font-size: 15px; font-weight: 700; color: #1e293b; }
        .empleado-meta { font-size: 12px; color: #64748b; margin-top: 2px; }
        .total-badge { background: #3b82f6; color: white; font-weight: 700; font-size: 14px; padding: 6px 14px; border-radius: 8px; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 12px; }
        thead tr { background: #f1f5f9; }
        th { text-align: left; padding: 8px 12px; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; }
        td { padding: 7px 12px; border-bottom: 1px solid #f1f5f9; color: #374151; }
        tr:last-child td { border-bottom: none; }
        .mono { font-family: 'Courier New', monospace; }
        .badge-en-curso { background: #fef3c7; color: #92400e; padding: 2px 8px; border-radius: 20px; font-size: 11px; }
        .deficit { color: #f97316; font-weight: 600; }
        .ok { color: #10b981; font-weight: 600; }

        .footer { margin-top: 40px; padding: 16px 32px; border-top: 1px solid #e2e8f0; text-align: center; color: #94a3b8; font-size: 11px; }
        .no-data { text-align: center; color: #94a3b8; padding: 40px; font-size: 14px; }

        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .empleado-section { page-break-inside: avoid; }
        }
    </style>
</head>
<body>

<!-- Print / Filter bar (hidden when printing) -->
<div class="no-print" style="background:#1e293b;padding:12px 32px;display:flex;align-items:center;justify-content:space-between;gap:12px">
    <form method="GET" style="display:flex;gap:10px;align-items:center">
        <select name="user_id" style="padding:6px 10px;border-radius:6px;border:none;font-size:13px">
            <option value="">Todos los empleados</option>
            @foreach($usuarios as $u)
                <option value="{{ $u->id }}" {{ $userId == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
            @endforeach
        </select>
        <select name="mes" style="padding:6px 10px;border-radius:6px;border:none;font-size:13px">
            @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ $mes == $m ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create(null, $m)->locale('es')->monthName }}
                </option>
            @endfor
        </select>
        <select name="anio" style="padding:6px 10px;border-radius:6px;border:none;font-size:13px">
            @for($y = now()->year; $y >= now()->year - 3; $y--)
                <option value="{{ $y }}" {{ $anio == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
        <button type="submit" style="padding:6px 14px;background:#3b82f6;color:white;border:none;border-radius:6px;font-size:13px;cursor:pointer">Filtrar</button>
        <a href="{{ route('admin.informes.index') }}" style="color:#94a3b8;font-size:12px;text-decoration:none">← Volver</a>
    </form>
    <button onclick="window.print()" style="padding:7px 18px;background:#10b981;color:white;border:none;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer">
        🖨 Imprimir / Guardar PDF
    </button>
</div>

<!-- Report Header -->
<div class="header">
    <div class="logo"><span>Time</span><span>Track</span></div>
    <div class="header-info">
        <strong>Informe de horas trabajadas</strong>
        {{ ucfirst($mesNombre) }} {{ $anio }} &nbsp;·&nbsp; Generado el {{ now()->format('d/m/Y H:i') }}
    </div>
</div>

@if(empty($resumen))
    <div class="no-data">No hay fichajes registrados para el período seleccionado.</div>
@else
    @foreach($resumen as $bloque)
    <div class="empleado-section">
        <div class="empleado-header">
            <div>
                <div class="empleado-name">{{ $bloque['usuario']->name }}</div>
                <div class="empleado-meta">{{ $bloque['usuario']->email }} &nbsp;·&nbsp; {{ $bloque['usuario']->horas_diarias }}h/día esperadas</div>
            </div>
            <div class="total-badge">{{ $bloque['total_horas'] }}h totales</div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Entrada</th>
                    <th>Salida</th>
                    <th>Horas</th>
                    <th>vs. Jornada</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bloque['dias'] as $fecha => $dia)
                    @foreach($dia['sesiones'] as $i => $sesion)
                    <tr>
                        @if($i === 0)
                        <td rowspan="{{ count($dia['sesiones']) }}" style="font-weight:600">
                            {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}<br>
                            <span style="font-size:11px;color:#64748b;font-weight:400">{{ \Carbon\Carbon::parse($fecha)->locale('es')->dayName }}</span>
                        </td>
                        @endif
                        <td class="mono">{{ $sesion['entrada']->created_at->format('H:i') }}</td>
                        <td class="mono">
                            @if($sesion['salida'])
                                {{ $sesion['salida']->created_at->format('H:i') }}
                            @else
                                <span class="badge-en-curso">En curso</span>
                            @endif
                        </td>
                        <td>{{ $sesion['horas'] !== null ? $sesion['horas'].'h' : '—' }}</td>
                        @if($i === 0)
                        <td rowspan="{{ count($dia['sesiones']) }}">
                            @php
                                $diffMin = $dia['minutos'] - ($bloque['usuario']->horas_diarias * 60);
                            @endphp
                            @if($diffMin < 0)
                                <span class="deficit">-{{ round(abs($diffMin)/60,2) }}h</span>
                            @elseif($dia['minutos'] > 0)
                                <span class="ok">+{{ round($diffMin/60,2) }}h</span>
                            @else
                                <span style="color:#94a3b8">—</span>
                            @endif
                        </td>
                        @endif
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach
@endif

<div class="footer">
    TimeTrack &nbsp;·&nbsp; Informe generado automáticamente &nbsp;·&nbsp; {{ now()->format('d/m/Y H:i:s') }}
</div>

</body>
</html>
