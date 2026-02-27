<section>
    <header>
        <p class="mt-1 text-sm text-gray-600">Mettez à jour vos informations de profil et votre numéro de téléphone.</p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Nom')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div>
            <x-input-label for="phone" :value="__('Numéro de téléphone')" />
            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->phone)" required />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Enregistrer') }}</x-primary-button>
            @if (session('status') === 'profile-updated')
                <p class="text-sm text-gray-600">{{ __('Enregistré.') }}</p>
            @endif
        </div>
    </form>
</section>