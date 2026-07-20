<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-neutral-900 dark:text-white">
            Nouvelle phase
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-neutral-900">
                <form method="POST" action="{{ route('admin.phases.store') }}">
                    @csrf

                    @include('admin.phases._form')

                    <div class="mt-6 flex items-center gap-3">
                        <x-primary-button>Créer</x-primary-button>
                        <a href="{{ route('admin.phases.index') }}" class="text-sm text-neutral-600 hover:underline dark:text-neutral-300">
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
