<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-neutral-900 dark:text-white">
            Nouveau match
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-neutral-900">
                @if ($phases->isEmpty())
                    <p class="text-sm text-neutral-600 dark:text-neutral-300">
                        Aucune phase n'existe encore.
                        <a href="{{ route('admin.phases.create') }}" class="font-medium underline">Crée d'abord une phase</a>.
                    </p>
                @else
                    <form method="POST" action="{{ route('admin.matches.store') }}">
                        @csrf

                        @include('admin.matches._form')

                        <div class="mt-6 flex items-center gap-3">
                            <x-primary-button>Créer</x-primary-button>
                            <a href="{{ route('admin.matches.index') }}" class="text-sm text-neutral-600 hover:underline dark:text-neutral-300">
                                Annuler
                            </a>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
