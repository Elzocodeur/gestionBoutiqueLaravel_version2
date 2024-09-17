<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carte de Fidélité - Crédit Facile</title>
    <link rel="stylesheet" href="{{ asset('css/client.css') }}">
</head>
<body>
    <div class="card">
        <div class="header">
            <div class="logo">CrédiShop</div>
            <div class="title">Carte de Fidélité</div>
        </div>
        <div class="badge">
            {{ substr($client->surname, 0, 1) }} <!-- Première lettre du nom -->
        </div>
        <div class="user-info">
            <strong>{{ $client->surname }}</strong>
            <p>Adresse : {{ $client->addrese }}</p>
            <p>Téléphone : {{ $client->telephone }}</p>
            <p>Catégorie : {{ ucfirst($client->categorie) }}</p>
        </div>
        <div class="qr-code">
            <img src="{{ url($client->qrcode) }}" alt="QR Code">
        </div>
        <div class="footer">
            Merci de votre confiance ! Ensemble, réalisons vos projets.
        </div>
    </div>
</body>
</html>
