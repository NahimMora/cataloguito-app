<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Nueva solicitud de pedido</title>
</head>

<body style="font-family:Arial,sans-serif;background:#f7f6f2;padding:30px;color:#20242c">
    <div style="max-width:560px;background:white;padding:30px;margin:auto;border-radius:12px">
        <p style="color:#b93828;font-size:13px;font-weight:bold">CATALOGUITO · {{ $order->shop->name }}</p>
        <h1 style="font-size:25px">Nueva solicitud {{ $order->displayNumber() }}</h1>
        <p>{{ $order->buyer_name }} confirmó una solicitud desde tu catálogo.</p>
        <table style="width:100%;border-collapse:collapse">
            @foreach ($order->items as $line)
                <tr>
                    <td style="padding:12px 0;border-bottom:1px solid #eee">{{ $line->quantity }} × {{ $line->name }}
                    </td>
                    <td style="text-align:right;border-bottom:1px solid #eee">
                        {{ \App\Support\Money::format($line->total_minor) }}</td>
                </tr>
            @endforeach
        </table>
        <p><strong>Total estimado: {{ \App\Support\Money::format($order->total_minor) }}</strong></p>
        <p>Teléfono informado: {{ $order->buyer_phone }}</p>
        @if ($order->note)
            <p>{{ $order->note }}</p>
        @endif
        <p style="margin:30px 0"><a href="{{ route('orders.show', $order->public_id) }}"
                style="background:#b93828;color:white;padding:13px 20px;border-radius:7px;text-decoration:none">Revisar
                solicitud en mi panel</a></p>
        <p style="font-size:12px;color:#666">Este aviso confirma el registro de una solicitud. No prueba envío de
            WhatsApp, venta realizada ni pago. Coordiná disponibilidad y condiciones con tu cliente.</p>
    </div>
</body>

</html>
