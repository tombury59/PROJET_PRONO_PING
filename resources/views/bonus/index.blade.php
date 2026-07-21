<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-neutral-900 dark:text-white">
            Questions bonus {{ $phase ? '— '.$phase->nom : '' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl space-y-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md bg-green-50 px-4 py-3 text-sm text-green-700 dark:bg-green-900/30 dark:text-green-400">
                    {{ session('status') }}
                </div>
            @endif

            @if (! $phase)
                <x-card class="p-6 text-sm text-neutral-500 dark:text-neutral-400">
                    Aucune phase en cours pour le moment.
                </x-card>
            @elseif ($questions->isEmpty())
                <x-card class="p-6 text-sm text-neutral-500 dark:text-neutral-400">
                    Aucune question bonus pour cette phase pour l'instant.
                </x-card>
            @else
                @foreach ($questions as $question)
                    @php($reponse = $question->reponses->first())
                    @php($resolue = $question->reponse_correcte !== null)

                    <x-card class="p-6">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <div>
                                <p class="font-semibold text-neutral-900 dark:text-white">
                                    {{ $question->question }}
                                </p>
                                @if ($question->match)
                                    <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                                        Lié au match {{ $question->match->equipe1() }} vs {{ $question->match->equipe2() }}
                                    </p>
                                @endif
                            </div>

                            @if ($resolue)
                                <span class="shrink-0 rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700 dark:bg-green-900/40 dark:text-green-400">
                                    Résolue : {{ $question->reponse_correcte }}
                                </span>
                            @else
                                <span class="shrink-0 rounded-full bg-neutral-100 px-2 py-0.5 text-xs font-medium text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400">
                                    Ouverte
                                </span>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('bonus.store', $question) }}" class="mt-4 flex flex-wrap items-end gap-3">
                            @csrf

                            <div class="flex-1">
                                <x-input-label :for="'reponse_'.$question->id" value="Ta réponse" />
                                <x-text-input
                                    :id="'reponse_'.$question->id"
                                    name="reponse"
                                    type="text"
                                    class="mt-1 block w-full"
                                    value="{{ old('reponse', $reponse->reponse ?? '') }}"
                                    :disabled="$resolue"
                                    required
                                />
                            </div>

                            @unless ($resolue)
                                <x-primary-button>
                                    {{ $reponse ? 'Modifier' : 'Valider' }}
                                </x-primary-button>
                            @endunless
                        </form>

                        @if ($resolue && $reponse)
                            <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                                Tu as répondu « {{ $reponse->reponse }} » — {{ $reponse->points_obtenus ?? 0 }} point(s).
                            </p>
                        @endif

                        <x-input-error :messages="$errors->get('reponse')" class="mt-2" />
                    </x-card>
                @endforeach
            @endif
        </div>
    </div>
</x-app-layout>
