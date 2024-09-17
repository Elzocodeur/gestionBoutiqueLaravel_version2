<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cartes de Fidélité Multi-niveaux</title>
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
        .gold-gradient {
            background: linear-gradient(45deg, #FFD700, #FFA500);
        }
        .silver-gradient {
            background: linear-gradient(45deg, #C0C0C0, #A9A9A9);
        }
        .bronze-gradient {
            background: linear-gradient(45deg, #CD7F32, #8B4513);
        }
    </style>
</head>
<body class="bg-gray-100 p-4">
    <div class="max-w-6xl mx-auto space-y-8">
        <!-- Silver Card -->
        <div class="fidelity-card silver-gradient text-white rounded-xl shadow-2xl overflow-hidden transition-transform duration-300 hover:scale-105">
            <div class="card-header p-6 relative overflow-hidden">
                <div class="shine absolute inset-0"></div>
                <h2 class="text-3xl font-bold mb-1 relative z-10">Carte Silver</h2>
                <p class="text-xl opacity-90 relative z-10">Gestion Boutique ODC</p>
            </div>
            <div class="card-body bg-white p-6">
                <div class="client-info space-y-2 mb-6 text-gray-800">
                    <p><strong>Nom:</strong> <span id="silverClientName">{{ $client->user_id?$client->user->prenom ." ". $client->user->nom:$client->surname  }}</span></p>
                    <p><strong>Numéro de client:</strong> <span id="silverClientNumber">{{ $client->telephone }}</span></p>
                    <p><strong>Adresse:</strong> <span id="silverExpirationDate">{{ $client->adresse }}</span></p>
                </div>
                <div class="qr-code flex justify-center mb-6">
                    <img src="{{ $path??"https://api.qrserver.com/v1/create-qr-code/?size=150x150&tel=778133537" }}" alt="QR Code" class="rounded-lg shadow-md">
                </div>
                <div class="points-container bg-gray-100 rounded-lg p-4 text-center">
                    <p class="text-lg font-semibold text-gray-800">Points de fidélité</p>
                    <p id="silverPointsDisplay" class="text-3xl font-bold text-gray-600">{{ $client->max_montant }}</p>
                </div>
            </div>
            <div class="card-footer bg-gray-50 p-4 text-center">
                <p class="text-sm text-gray-700">Découvrez nos offres Silver exclusives !</p>
                <button class="silver-points-btn mt-2 bg-gray-600 text-white py-2 px-4 rounded-full text-sm hover:bg-gray-700 transition-colors duration-300">Ajouter des points</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            const cards = ['gold', 'silver', 'bronze'];
            cards.forEach(card => {
                const pointsDisplay = document.getElementById(`${card}PointsDisplay`);
                const addPointsBtn = document.querySelector(`.${card}-points-btn`);
                
                addPointsBtn.addEventListener('click', () => {
                    let currentPoints = parseInt(pointsDisplay.textContent, 10);
                    let pointsToAdd = card === 'gold' ? 200 : (card === 'silver' ? 150 : 100);
                    currentPoints += pointsToAdd;
                    pointsDisplay.textContent = currentPoints;
                    
                    pointsDisplay.classList.add('text-green-600', 'scale-110');
                    setTimeout(() => {
                        pointsDisplay.classList.remove('text-green-600', 'scale-110');
                    }, 300);
                });
            });
        });
    </script>
</body>
</html>