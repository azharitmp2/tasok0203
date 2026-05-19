<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Graduation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200 p-6">
                <form method="POST" action="{{ route('graduations.update', $graduation) }}" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div>
                        <x-input-label for="title" :value="__('Title')" />
                        <x-text-input id="title" name="title" type="text" value="{{ old('title', $graduation->title) }}" class="block mt-1 w-full" required />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="ceremony_date" :value="__('Ceremony Date')" />
                        <x-text-input id="ceremony_date" name="ceremony_date" type="date" value="{{ old('ceremony_date', $graduation->ceremony_date->format('Y-m-d')) }}" class="block mt-1 w-full" required />
                        <x-input-error :messages="$errors->get('ceremony_date')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="fee" :value="__('Registration Fee (RM)')" />
                        <x-text-input id="fee" name="fee" type="number" step="0.01" min="0" value="{{ old('fee', $graduation->fee) }}" class="block mt-1 w-full" required />
                        <x-input-error :messages="$errors->get('fee')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="status" :value="__('Status')" />
                        <select name="status" id="status" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach (['draft', 'open', 'closed'] as $s)
                                <option value="{{ $s }}" @selected(old('status', $graduation->status) === $s)>
                                    {{ ucfirst($s) }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Update') }}</x-primary-button>
                        <a href="{{ route('graduations.show', $graduation) }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                    </div>
                </form>
            </div>

            @can('delete', $graduation)
                <div class="bg-white shadow-sm sm:rounded-lg border border-red-200 p-6">
                    <h3 class="text-lg font-semibold text-red-700 mb-2">Danger Zone</h3>
                    <p class="text-sm text-gray-600 mb-4">Once you archive this graduation event session, it is safely soft-deleted from operations. This action can only be performed if no assigned students have registered payments.</p>
                    
                    <form method="POST" action="{{ route('graduations.destroy', $graduation) }}" onsubmit="return confirm('Are you sure you want to archive this graduation event?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Archive Graduation
                        </button>
                    </form>
                </div>
            @endcan
        </div>
    </div>
</x-app-layout>