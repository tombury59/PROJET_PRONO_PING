<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-neutral-900 dark:text-white">
                Questions bonus
            </h2>

            <a
                href="{{ route('admin.questions-bonus.create') }}"
                class="rounded-sm bg-neutral-900 px-4 py-2 text-sm font-medium text-white hover:bg-neutral-700 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-200"
            >
                Nouvelle question
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-5xl space-y-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md bg-green-50 px-4 py-3 text-sm text-green-700 dark:bg-green-900/30 dark:text-green-400">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-md bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-900/30 dark:text-red-400">
                    {{ session('error') }}
                </div>
            @endif

            @if ($phases->isNotEmpty())
                <form method="GET" action="{{ route('admin.questions-bonus.index') }}" class="max-w-xs">
                    <x-input-label for="phase_id" value="Filtrer par phase" />
                    <select
                        id="phase_id"
                        name="phase_id"
                        onchange="this.form.submit()"
                        class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                    >
                        @foreach ($phases as $phase)
                            <option value="{{ $phase->id }}" @selected($selectedPhaseId == $phase->id)>
                                {{ $phase->nom }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif

            <x-card class="overflow-hidden p-0">
                <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-800">
                    <thead class="bg-neutral-50 dark:bg-neutral-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Question</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Match lié</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Réponses</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800">
                        @forelse ($questions as $question)
                            <tr>
                                <td class="max-w-xs px-6 py-4 text-sm font-medium text-neutral-900 dark:text-white">
                                    {{ $question->question }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-neutral-600 dark:text-neutral-300">
                                    {{ $question->match ? $question->match->equipe1().' vs '.$question->match->equipe2() : '—' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    @if ($question->reponse_correcte)
                                        <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700 dark:bg-green-900/40 dark:text-green-400">
                                            Résolue : {{ $question->reponse_correcte }}
                                        </span>
                                    @else
                                        <span class="rounded-full bg-neutral-100 px-2 py-0.5 text-xs font-medium text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400">
                                            En attente
                                        </span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-neutral-600 dark:text-neutral-300">
                                    {{ $question->reponses_count }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                    <a href="{{ route('admin.questions-bonus.edit', $question) }}" class="font-medium text-neutral-700 underline-offset-2 hover:underline dark:text-neutral-300">
                                        Modifier
                                    </a>

                                    <form method="POST" action="{{ route('admin.questions-bonus.destroy', $question) }}" class="ml-3 inline" onsubmit="return confirm('Supprimer cette question ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-medium text-red-600 underline-offset-2 hover:underline dark:text-red-400">
                                            Supprimer
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-sm text-neutral-500 dark:text-neutral-400">
                                    Aucune question bonus pour cette phase.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </x-card>
        </div>
    </div>
</x-app-layout>
