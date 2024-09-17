<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Utilisateur</title>
</head>
<body>
    @if (session('success'))
        <div>{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('user.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" value="{{ old('nom') }}" required>
        </div>
        <div>
            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" value="{{ old('prenom') }}" required>
        </div>
        <div>
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
        </div>
        <div>
            <label for="role">Rôle :</label>
            <select id="role" name="role" required>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="boutiquier" {{ old('role') == 'boutiquier' ? 'selected' : '' }}>Boutiquier</option>
            </select>
        </div>
        <div>
            <label for="photo">Photo :</label>
            <input type="file" id="photo" name="photo" required>
        </div>
        <div>
            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div>
            <label for="password_confirmation">Confirmer le mot de passe :</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>
        </div>

        <button type="submit">S'inscrire</button>
    </form>
</body>
</html>
