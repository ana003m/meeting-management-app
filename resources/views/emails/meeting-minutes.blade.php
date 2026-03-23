<!DOCTYPE html>
<html>
<head>
    <title>Записник од состанок</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        h2 { color: #007bff; margin-top: 30px; }
        .summary { background: #f8f9fa; padding: 15px; border-left: 4px solid #007bff; }
        .action-items { background: #f8f9fa; padding: 15px; border-left: 4px solid #28a745; }
        .decisions { background: #f8f9fa; padding: 15px; border-left: 4px solid #17a2b8; }
        ul { margin: 10px 0; }
        li { margin: 5px 0; }
        .footer { margin-top: 30px; font-size: 12px; color: #666; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h1>Записник од состанок: {{ $meeting->title }}</h1>

    <p><strong>Датум:</strong> {{ $meeting->start_time->format('d.m.Y H:i') }}</p>
    <p><strong>Локација:</strong> {{ $meeting->location ?? 'Н/А' }}</p>

    <h2>Резиме</h2>
    <div class="summary">
        {{ $minutes->summary }}
    </div>

    <h2>Акциони точки</h2>
    <div class="action-items">
        @if(count($minutes->action_items) > 0)
            <ul>
                @foreach($minutes->action_items as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        @else
            <p>Нема акциони точки</p>
        @endif
    </div>

    @if($minutes->decisions && count($minutes->decisions) > 0)
        <h2>Донесени одлуки</h2>
        <div class="decisions">
            <ul>
                @foreach($minutes->decisions as $decision)
                    <li>{{ $decision }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="footer">
        <p>Ова е автоматски генериран емаил од апликацијата за управување со состаноци.</p>
        <p>Генерирано на: {{ $minutes->generated_at->format('d.m.Y H:i:s') }}</p>
    </div>
</div>
</body>
</html>
