<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-neutral-900 dark:text-white">
            Classement
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <x-card class="mt-5 space-y-4 p-4">
            @if ($phases->isNotEmpty())
                <form method="GET" action="{{ route('classement.index') }}" class="max-w-xs">
                    <x-input-label for="vue" value="Vue" />
                    <select
                        id="vue"
                        name="vue"
                        onchange="this.form.submit()"
                        class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                    >
                        @foreach ($phases as $p)
                            <option value="{{ $p->id }}" @selected((string) $selection === (string) $p->id)>
                                {{ $p->nom }}
                            </option>
                        @endforeach
                        <option value="global" @selected($selection === 'global')>
                            Toutes les phases (global)
                        </option>
                    </select>
                </form>
            @endif

            @if ($phasesIncluses->count() > 1)
                <div class="rounded-md bg-amber-50 px-4 py-3 text-sm text-amber-700 dark:bg-amber-900/20 dark:text-amber-400">
                    Classement cumulé sur : {{ $phasesIncluses->pluck('nom')->join(' → ') }}
                    (reset non activé sur {{ $phase->nom }}).
                </div>
            @endif

            @if ($monRang)
                <div class="rounded-md bg-neutral-100 px-4 py-3 text-sm text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300">
                    Tu es actuellement <span class="font-semibold">#{{ $monRang }}</span> sur {{ $classement->count() }}.
                </div>
            @endif

            <div class="overflow-hidden rounded-lg border border-neutral-200 dark:border-neutral-800">
                <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-800">
                    <thead class="bg-neutral-50 dark:bg-neutral-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Rang</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Pseudo</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Points</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800">
                        @forelse ($classement as $i => $entree)
                            <tr class="{{ $entree['user']->id === auth()->id() ? 'bg-neutral-50 dark:bg-neutral-800/60' : '' }}">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-neutral-500 dark:text-neutral-400">
                                    #{{ $i + 1 }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-neutral-900 dark:text-white">
                                    {{ $entree['user']->pseudo }}
                                    @if ($entree['user']->id === auth()->id())
                                        <span class="ml-1 text-xs text-neutral-400">(toi)</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-semibold text-neutral-700 dark:text-neutral-300">
                                    {{ $entree['points'] }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-sm text-neutral-500 dark:text-neutral-400">
                                    Aucun classement disponible pour l'instant.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
