<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-neutral-900 dark:text-white">
            Utilisateurs
        </h2>
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

            <x-card class="overflow-hidden p-0">
                <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-800">
                    <thead class="bg-neutral-50 dark:bg-neutral-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Pseudo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Rôle</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Inscrit le</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Pronostics</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Réponses bonus</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800">
                        @forelse ($users as $user)
                            <tr>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-neutral-900 dark:text-white">
                                    {{ $user->pseudo }}
                                    @if ($user->id === auth()->id())
                                        <span class="ml-1 text-xs text-neutral-400">(toi)</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    @if ($user->id === auth()->id())
                                        <span class="rounded-full bg-neutral-100 px-2 py-0.5 text-xs font-medium text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400">
                                            {{ $user->isAdmin() ? 'Admin' : 'Joueur' }}
                                        </span>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.role.update', $user) }}">
                                            @csrf
                                            @method('PATCH')
                                            <select
                                                name="role"
                                                onchange="this.form.submit()"
                                                class="rounded-md border-neutral-300 py-1 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                                            >
                                                <option value="joueur" @selected(! $user->isAdmin())>Joueur</option>
                                                <option value="admin" @selected($user->isAdmin())>Admin</option>
                                            </select>
                                        </form>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-neutral-600 dark:text-neutral-300">
                                    {{ $user->created_at->format('d/m/Y') }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-neutral-600 dark:text-neutral-300">
                                    {{ $user->pronostics_count }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-neutral-600 dark:text-neutral-300">
                                    {{ $user->reponses_bonus_count }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                    @unless ($user->id === auth()->id())
                                        <form
                                            method="POST"
                                            action="{{ route('admin.users.destroy', $user) }}"
                                            onsubmit="return confirm('Supprimer {{ $user->pseudo }} ? Ses pronostics et réponses bonus seront aussi supprimés.');"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-medium text-red-600 underline-offset-2 hover:underline dark:text-red-400">
                                                Supprimer
                                            </button>
                                        </form>
                                    @endunless
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-neutral-500 dark:text-neutral-400">
                                    Aucun utilisateur inscrit.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </x-card>
        </div>
    </div>
</x-app-layout>
