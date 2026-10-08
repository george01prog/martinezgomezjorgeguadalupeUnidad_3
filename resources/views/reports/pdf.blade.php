<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page { margin: 28px 30px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #1e293b; }
        h1 { margin: 0 0 3px; font-size: 16px; color: #312e81; }
        .app { margin: 0 0 8px; font-size: 10px; color: #64748b; }
        .meta { margin: 0 0 3px; color: #475569; }
        .summary { margin: 8px 0 2px; }
        .summary span {
            display: inline-block; margin-right: 8px; padding: 4px 8px;
            background: #eef2ff; color: #3730a3; border-radius: 3px; font-weight: bold;
        }
        table { width: 100%; margin-top: 10px; border-collapse: collapse; }
        thead { display: table-header-group; }
        th {
            padding: 5px 6px; background: #4f46e5; color: #ffffff; text-align: left;
            font-size: 8px; text-transform: uppercase;
        }
        td { padding: 4px 6px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        tr.even td { background: #f8fafc; }
        .empty { margin-top: 30px; text-align: center; color: #94a3b8; font-size: 11px; }
        .footer { margin-top: 14px; font-size: 8px; color: #94a3b8; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p class="app">{{ config('app.name') }}</p>

    <p class="meta">Generado: {{ $generatedAt }} · Por: {{ $generatedBy }}</p>
    <p class="meta">
        Filtros:
        {{ count($filterLines) ? implode('  |  ', $filterLines) : 'ninguno' }}
    </p>

    <div class="summary">
        @foreach ($summary as [$label, $value])
            <span>{{ $label }}: {{ $value }}</span>
        @endforeach
    </div>

    @if (count($rows))
        <table>
            <thead>
                <tr>
                    @foreach ($headers as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr class="{{ $loop->even ? 'even' : '' }}">
                        @foreach ($row as $cell)
                            <td>{{ $cell }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="empty">No hay datos para los filtros seleccionados.</p>
    @endif

    <p class="footer">{{ count($rows) }} registro(s) · {{ config('app.name') }}</p>
</body>
</html>