<!-- resources/views/suppliers/orders_pdf.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mes Commandes PDF</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            border: 1px solid #000;
            text-align: left;
        }
    </style>
</head>
<body>
    <h2>Mes Commandes</h2>
    <table>
        <thead>
            <tr>
                <th>ID Commande</th>
                <th>Date Commande</th>
                <th>Date Livraison</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}</td>
                <td>{{ $order->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
