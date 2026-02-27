<x-guest-layout>
    <div class="mb-4 text-center">
        <h2 class="text-xl font-bold">Finaliser votre inscription</h2>
        <p class="text-gray-600 text-sm">Vous allez être enregistré en tant que : <strong>{{ request()->query('role') }}</strong></p>
    </div>

    <form method="POST" action="{{ request()->fullUrl() }}">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Nom Complet')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Email Professionnel')" />
            <x-text-input id="email" class="block mt-1 w-full bg-gray-100" type="email" name="email_display" :value="request()->query('email')" required readonly />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="phone" :value="__('Numéro de téléphone')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" required />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Mot de passe')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmer le mot de passe')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="w-full justify-center">
                {{ __('Créer mon compte employé') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
