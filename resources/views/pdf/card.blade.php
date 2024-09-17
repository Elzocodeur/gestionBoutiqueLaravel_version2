<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carte de Fidélité Premium</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <style>
        @keyframes shine {
            0% {background-position: -100px;}
            100% {background-position: 300px;}
        }
        .shine {
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            background-size: 200px 100%;
            animation: shine 1.5s infinite linear;
        }
    </style>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen p-4">
    <div class="fidelity-card bg-white w-full max-w-md rounded-xl shadow-2xl overflow-hidden transition-transform duration-300 hover:scale-105">
        <div class="card-header bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-6 relative overflow-hidden">
            <div class="shine absolute inset-0"></div>
            <h2 class="text-2xl font-bold mb-1 relative z-10">Carte de Fidélité Premium</h2>
            <p class="text-lg opacity-90 relative z-10">Nom du magasin</p>
        </div>
        <div class="card-body p-6">
            <div class="client-info space-y-2 mb-6">
                <p><strong>Nom:</strong> <span id="silverClientName">{{ $client->prenom ." ". $client->nom  }}</span></p>
                <p><strong>Email:</strong> <span id="silverClientNumber">{{ $client->email }}</span></p>
            </div>
            <div class="qr-code flex justify-center mb-6">
                <img src="{{ $path??"https://api.qrserver.com/v1/create-qr-code/?size=150x150&tel=778133537" }}" alt="QR Code" class="rounded-lg shadow-md">
            </div>
        </div>
        <div class="card-footer bg-gray-50 p-4 text-center">
            <p class="text-sm text-gray-600">Utilisez cette carte pour obtenir des réductions exclusives !</p>
            <button id="addPointsBtn" class="mt-2 bg-indigo-600 text-white py-2 px-4 rounded-full text-sm hover:bg-indigo-700 transition-colors duration-300">Ajouter des points</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            const addPointsBtn = document.getElementById('addPointsBtn');
            const pointsDisplay = document.getElementById('pointsDisplay');
            
            addPointsBtn.addEventListener('click', () => {
                let currentPoints = parseInt(pointsDisplay.textContent, 10);
                currentPoints += 100;
                pointsDisplay.textContent = currentPoints;
                
                // Animation d'ajout de points
                pointsDisplay.classList.add('text-green-600', 'scale-110');
                setTimeout(() => {
                    pointsDisplay.classList.remove('text-green-600', 'scale-110');
                }, 300);
            });
        });
    </script>
</body>
</html>