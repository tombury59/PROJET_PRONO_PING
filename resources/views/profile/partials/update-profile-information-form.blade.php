<section>
    <header>
        <h2 class="text-lg font-medium text-surface-900 dark:text-white">
            {{ __('Information du compte') }}
        </h2>

        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
            <!-- {{ __("Update your account's pseudo.") }} -->
            {{ __("Modifier votre pseudo.") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div x-data="{ apercu: null }">
            <x-input-label :value="__('Photo de profil')" />
            <div class="mt-2 flex items-center gap-4">
                <template x-if="apercu">
                    <span class="inline-flex size-16 shrink-0 items-center justify-center overflow-hidden rounded-full bg-surface-200 dark:bg-surface-700">
                        <img :src="apercu" alt="" class="h-full w-full object-cover" />
                    </span>
                </template>
                <template x-if="! apercu">
                    <x-avatar :user="$user" class="size-16" />
                </template>

                <div class="text-sm">
                    <input
                        id="avatar"
                        name="avatar"
                        type="file"
                        accept="image/png,image/jpeg,image/webp"
                        x-on:change="const f = $event.target.files[0]; apercu = f ? URL.createObjectURL(f) : null"
                        class="block text-sm text-surface-600 file:mr-3 file:rounded-md file:border-0 file:bg-surface-900 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-white hover:file:bg-surface-700 dark:text-surface-300 dark:file:bg-white dark:file:text-surface-900"
                    />
                    <p class="mt-1 text-xs text-surface-500 dark:text-surface-400">JPG, PNG ou WebP, 2 Mo max.</p>

                    @if ($user->avatar_path)
                        <label class="mt-2 inline-flex items-center gap-2 text-xs text-surface-600 dark:text-surface-400">
                            <input type="checkbox" name="supprimer_avatar" value="1" class="rounded border-surface-300 dark:border-surface-700" />
                            Supprimer la photo actuelle
                        </label>
                    @endif
                </div>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

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
                    class="text-sm text-surface-500 dark:text-surface-400"
                >{{ __('Sauvegardé.') }}</p>
            @endif
        </div>
    </form>
</section>
