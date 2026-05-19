<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Late Student Registration') }} to {{ $graduation->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200 p-6">
                <form method="POST" action="{{ route('graduations.students.store', $graduation) }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="name" :value="__('Full Name')" />
                        <x-text-input id="name" name="name" type="text" value="{{ old('name') }}" class="block mt-1 w-full" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="ic" :value="__('IC Number (12 Digits)')" />
                            <x-text-input id="ic" name="ic" type="text" placeholder="960101145566" value="{{ old('ic') }}" class="block mt-1 w-full" required />
                            <x-input-error :messages="$errors->get('ic')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="matric_card" :value="__('Matric Card Identifier')" />
                            <x-text-input id="matric_card" name="matric_card" type="text" placeholder="M60443" value="{{ old('matric_card') }}" class="block mt-1 w-full" required />
                            <x-input-error :messages="$errors->get('matric_card')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="email" :value="__('Primary Email Address')" />
                            <x-text-input id="email" name="email" type="email" placeholder="student@example.my" value="{{ old('email') }}" class="block mt-1 w-full" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="phone" :value="__('Phone Number Contact')" />
                            <x-text-input id="phone" name="phone" type="text" placeholder="0123456789" value="{{ old('phone') }}" class="block mt-1 w-full" required />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-4 border-t border-gray-100">
                        <x-primary-button>{{ __('Add Student') }}</x-primary-button>
                        <a href="{{ route('graduations.show', $graduation) }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>