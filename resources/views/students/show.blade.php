<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Student Profile: {{ $student->matric_card }}
            </h2>
            @can('update', $student)
                <a href="{{ route('graduations.students.edit', [$graduation, $student]) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Update Details / Upload Receipt
                </a>
            @endcan
        </div>
    </x-slot>

    <x-breadcrumbs :links="[
            'Graduations' => route('graduations.index'),
            $graduation->title => route('graduations.show', $graduation),
            'Student: ' . $student->matric_card => null
        ]" />

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200 p-6">
                
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-gray-100 pb-4 mb-4 gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $student->name }}</h3>
                        <p class="text-sm text-gray-500">Assigned Session: {{ $graduation->title }}</p>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        @if ($student->isVerified())
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">✓ Fully Verified</span>
                        @elseif ($student->hasPaid())
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 border border-amber-200">⏳ Pending Review</span>
                        @else
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-800 border border-slate-200">🛑 Payment Outstanding</span>
                        @endif

                        @if($student->hasPaid() && !$student->isVerified())
                            @can('verify', $student)
                                <form method="POST" action="{{ route('graduations.students.verify', [$graduation, $student]) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="inline-flex items-center px-3 py-1 bg-green-600 border border-transparent rounded-full font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 transition ease-in-out duration-150 shadow-xs">
                                        Approve Payment
                                    </button>
                                </form>
                            @endcan
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                    <div><strong>Identification (IC):</strong> {{ $student->ic }}</div>
                    <div><strong>Email Address:</strong> {{ $student->email }}</div>
                    <div><strong>Phone Contact:</strong> {{ $student->phone }}</div>
                    <div><strong>Submission State:</strong> {{ $student->paid_at ? 'Paid on '.$student->paid_at->format('d M Y H:i') : 'No files submitted' }}</div>
                </div>

                @if($student->payment_receipt)
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200 flex justify-between items-center shadow-2xs">
                        <span class="text-sm text-gray-600 font-medium">Linked Proof Transaction Receipt:</span>
                        <a href="{{ Storage::url($student->payment_receipt) }}" target="_blank" class="font-semibold text-sm text-indigo-600 hover:text-indigo-900 underline inline-flex items-center gap-1">
                            Open Attachment Document
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </div>
                @endif

                @if($student->hasPaid() && !$student->isVerified())
                    @can('verify', $student)
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <form method="POST" action="{{ route('graduations.students.verify', [$graduation, $student]) }}">
                                @csrf
                                @method('PATCH')
                                <x-primary-button class="w-full justify-center text-center py-2.5 bg-indigo-600 hover:bg-indigo-700">
                                    Approve Financial Verification
                                </x-primary-button>
                            </form>
                        </div>
                    @endcan
                @endif

            </div>
        </div>
    </div>
</x-app-layout>