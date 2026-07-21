<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-surface-900 dark:text-white">
            Modifier la question bonus
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
            <x-card class="p-6">
                <form method="POST" action="{{ route('admin.questions-bonus.update', $question) }}">
                    @csrf
                    @method('PUT')

                    @include('admin.questions-bonus._form', ['question' => $question])

                    <div class="mt-6 flex items-center gap-3">
                        <x-primary-button>Enregistrer</x-primary-button>
                        <a href="{{ route('admin.questions-bonus.index') }}" class="text-sm text-surface-600 hover:underline dark:text-surface-300">
                            Annuler
                        </a>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
