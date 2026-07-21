<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-surface-900 dark:text-white">
            Profile
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 ">
            <x-card class="p-4 sm:p-8 mt-5">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </x-card>

            <x-card class="p-4 sm:p-8 mt-5">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </x-card>

            <x-card class="p-4 sm:p-8 mt-5">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
