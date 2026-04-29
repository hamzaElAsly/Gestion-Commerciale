<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
</head>
<body class="min-h-screen bg-gray-100">

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-700 via-indigo-600 to-blue-500 p-8">
    <div class="bg-white rounded-3xl shadow-xl p-10 w-full max-w-xl">
        <h1 class="text-4xl font-bold mb-2">Créer un compte</h1>
        <p class="text-gray-500 mb-8">Rejoignez votre espace de gestion</p>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf
            <input type="text" name="name" placeholder="Nom complet" required class="w-full border rounded-xl px-4 py-3">
            <input type="email" name="email" placeholder="Adresse email" required class="w-full border rounded-xl px-4 py-3">
            <input type="password" name="password" placeholder="Mot de passe" required class="w-full border rounded-xl px-4 py-3">
            <input type="password" name="password_confirmation" placeholder="Confirmer le mot de passe" required class="w-full border rounded-xl px-4 py-3">
            <button class="w-full bg-gradient-to-r from-blue-700 to-indigo-500 text-white py-4 rounded-xl font-bold text-lg">
                S'inscrire
            </button>
            <p class="text-center">
                Déjà inscrit ?
                <a href="{{ route('login') }}" class="text-blue-600 font-semibold"> Se connecter </a>
            </p>
        </form>
    </div>
</div>
</body>
</html>