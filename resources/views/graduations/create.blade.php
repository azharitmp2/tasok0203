<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('New Graduation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200 p-6">
                <form method="POST" action="{{ route('graduations.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="title" :value="__('Title')" />
                        <x-text-input id="title" name="title" type="text" value="{{ old('title') }}" class="block mt-1 w-full" required autofocus />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="ceremony_date" :value="__('Ceremony Date')" />
                        <x-text-input id="ceremony_date" name="ceremony_date" type="date" value="{{ old('ceremony_date') }}" class="block mt-1 w-full" required />
                        <x-input-error :messages="$errors->get('ceremony_date')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="fee" :value="__('Registration Fee (RM)')" />
                        <x-text-input id="fee" name="fee" type="number" step="0.01" min="0" value="{{ old('fee') }}" class="block mt-1 w-full" required />
                        <x-input-error :messages="$errors->get('fee')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="status" :value="__('Status')" />
                        <select name="status" id="status" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach (['draft', 'open', 'closed'] as $s)
                                <option value="{{ $s }}" @selected(old('status') === $s)>
                                    {{ ucfirst($s) }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Save') }}</x-primary-button>
                        <a href="{{ route('graduations.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>