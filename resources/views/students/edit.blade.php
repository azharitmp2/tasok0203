<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Update Student Registration') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200 p-6">
                <form method="POST" action="{{ route('graduations.students.update', [$graduation, $student]) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div>
                        <x-input-label for="name" :value="__('Full Name')" />
                        <x-text-input id="name" name="name" type="text" value="{{ old('name', $student->name) }}" class="block mt-1 w-full" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="ic" :value="__('IC Number (12 Digits)')" />
                            <x-text-input id="ic" name="ic" type="text" value="{{ old('ic', $student->ic) }}" class="block mt-1 w-full" required />
                            <x-input-error :messages="$errors->get('ic')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="matric_card" :value="__('Matric Card Identifier')" />
                            <x-text-input id="matric_card" name="matric_card" type="text" value="{{ old('matric_card', $student->matric_card) }}" class="block mt-1 w-full" required />
                            <x-input-error :messages="$errors->get('matric_card')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="email" :value="__('Primary Email Address')" />
                            <x-text-input id="email" name="email" type="email" value="{{ old('email', $student->email) }}" class="block mt-1 w-full" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="phone" :value="__('Phone Number Contact')" />
                            <x-text-input id="phone" name="phone" type="text" value="{{ old('phone', $student->phone) }}" class="block mt-1 w-full" required />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <x-input-label for="payment_receipt" :value="__('Upload Proof Transaction Receipt (PDF, JPG, PNG - Maximum 2MB)')" />
                        <input id="payment_receipt" name="payment_receipt" type="file" accept=".pdf,image/jpeg,image/png" class="mt-2 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-white focus:outline-none" />
                        <x-input-error :messages="$errors->get('payment_receipt')" class="mt-2" />

                        @if ($student->payment_receipt)
                            <p class="mt-3 text-xs text-gray-500 italic flex items-center gap-1">
                                Currently attached file path discovered: 
                                <a class="text-indigo-600 font-semibold underline hover:text-indigo-900" target="_blank" href="{{ Storage::url($student->payment_receipt) }}">View Current Receipt</a>
                            </p>
                        @endif
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Save Changes') }}</x-primary-button>
                        <a href="{{ route('graduations.students.show', [$graduation, $student]) }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>