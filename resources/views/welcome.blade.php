<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }}</title>

        @include('layouts.partials.pwa')

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-surface-50 text-surface-900">
        <div class="min-h-screen flex flex-col items-center justify-center px-6">
            <div class="max-w-md w-full text-center">
                <h1 class="text-3xl font-semibold tracking-tight">
                    {{ config('app.name') }}
                </h1>

                <p class="mt-3 text-surface-500">
                    Pronostiquez les matchs du club et grimpez au classement.
                </p>

                <div class="mt-8 flex items-center justify-center gap-3">
                    @auth
                        <a
                            href="{{ route('dashboard') }}"
                            class="px-5 py-2.5 rounded-md bg-surface-900 text-white text-sm font-medium hover:bg-surface-700 transition"
                        >
                            Accéder au tableau de bord
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="px-5 py-2.5 rounded-md bg-surface-900 text-white text-sm font-medium hover:bg-surface-700 transition"
                        >
                            Se connecter
                        </a>
                        <a
                            href="{{ route('register') }}"
                            class="px-5 py-2.5 rounded-md border border-surface-300 text-sm font-medium hover:bg-surface-100 transition"
                        >
                            S'inscrire
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </body>
</html>
