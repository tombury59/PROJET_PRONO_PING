<section>
    <header>
        <h2 class="text-lg font-medium text-neutral-900 dark:text-white">
            {{ __('Information du compte') }}
        </h2>

        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
            <!-- {{ __("Update your account's pseudo.") }} -->
            {{ __("Modifier votre pseudo.") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="pseudo" :value="__('Pseudo')" />
            <x-text-input id="pseudo" name="pseudo" type="text" class="mt-1 block w-full" :value="old('pseudo', $user->pseudo)" required autofocus autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('pseudo')" />
        </div>

        <div class="flex items-center gap-4">
            <!-- <x-primary-button>{{ __('Save') }}</x-primary-button> -->
            <x-primary-button>{{ __('Sauvegarder') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-neutral-500 dark:text-neutral-400"
                >{{ __('Sauvegardé.') }}</p>
            @endif
        </div>
    </form>
</section>
