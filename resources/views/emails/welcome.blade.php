<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue chez Nous !</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f4f4f4;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
        }
        .footer {
            background-color: #f4f4f4;
            padding: 10px;
            text-align: center;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Bienvenue chez Nous !</h1>
        </div>
        <div class="content">
            <p>Bonjour {{ $user->prenom ." ". $user->nom }},</p>
            <p>Nous sommes ravis de vous accueillir sur notre plateforme. Votre compte a été créé avec succès.</p>
            <p>N'hésitez pas à explorer toutes nos fonctionnalités et à nous contacter si vous avez des questions.</p>
            <p>Cordialement,<br>L'équipe de Notre Entreprise</p>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} Notre Entreprise. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>