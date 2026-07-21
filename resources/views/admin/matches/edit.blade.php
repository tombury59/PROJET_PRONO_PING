<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-surface-900 dark:text-white">
            Modifier le match
        </h2>
    </x-slot>

    <div class="space-y-6 py-12">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-surface-900">
                <h3 class="mb-4 text-sm font-semibold text-surface-900 dark:text-white">Informations</h3>

                <form method="POST" action="{{ route('admin.matches.update', $match) }}">
                    @csrf
                    @method('PUT')

                    @include('admin.matches._form', ['match' => $match])

                    <div class="mt-6 flex items-center gap-3">
                        <x-primary-button>Enregistrer</x-primary-button>
                        <a href="{{ route('admin.matches.index', ['phase_id' => $match->phase_id]) }}" class="text-sm text-surface-600 hover:underline dark:text-surface-300">
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-surface-900">
                <h3 class="mb-1 text-sm font-semibold text-surface-900 dark:text-white">Résultat</h3>
                <p class="mb-4 text-sm text-surface-500 dark:text-surface-400">
                    Valider le résultat calcule automatiquement les points de tous les pronostics liés à ce match.
                </p>

                <form method="POST" action="{{ route('admin.matches.resultat.update', $match) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="score_j1" :value="$match->equipe1()" />
                            <x-text-input
                                id="score_j1"
                                name="score_j1"
                                type="number"
                                min="0"
                                class="mt-1 block w-full"
                                :value="old('score_j1', $match->score_j1)"
                                required
                            />
                            <x-input-error :messages="$errors->get('score_j1')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="score_j2" :value="$match->equipe2()" />
                            <x-text-input
                                id="score_j2"
                                name="score_j2"
                                type="number"
                                min="0"
                                class="mt-1 block w-full"
                                :value="old('score_j2', $match->score_j2)"
                                required
                            />
                            <x-input-error :messages="$errors->get('score_j2')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-6 flex items-center gap-3">
                        <x-primary-button>
                            {{ $match->resultat_saisi ? 'Corriger le résultat' : 'Valider le résultat' }}
                        </x-primary-button>

                        @if ($match->resultat_saisi)
                            <span class="text-sm text-success-700 dark:text-success-400">Résultat déjà saisi</span>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
