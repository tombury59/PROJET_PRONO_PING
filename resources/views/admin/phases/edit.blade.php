<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-surface-900 dark:text-white">
            Modifier la phase
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-surface-900">
                <form method="POST" action="{{ route('admin.phases.update', $phase) }}">
                    @csrf
                    @method('PUT')

                    @include('admin.phases._form', ['phase' => $phase])

                    <div class="mt-6 flex items-center gap-3">
                        <x-primary-button>Enregistrer</x-primary-button>
                        <a href="{{ route('admin.phases.index') }}" class="text-sm text-surface-600 hover:underline dark:text-surface-300">
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
