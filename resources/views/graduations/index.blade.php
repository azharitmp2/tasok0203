<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Graduations') }}
            </h2>
            @can('create', App\Models\Graduation::class)
                <a href="{{ route('graduations.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    + New Graduation
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-800 rounded-lg shadow-sm">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-3">Title</th>
                            <th scope="col" class="px-6 py-3">Date</th>
                            <th scope="col" class="px-6 py-3">Fee</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Students</th>
                            <th scope="col" class="px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($graduations as $g)
                            @php
                                $colors = [
                                    'draft'  => 'bg-slate-100 text-slate-700 border-slate-200',
                                    'open'   => 'bg-green-100 text-green-700 border-green-200',
                                    'closed' => 'bg-amber-100 text-amber-700 border-amber-200',
                                ];
                            @endphp
                            <tr class="bg-white hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $g->title }}</td>
                                <td class="px-6 py-4">{{ $g->ceremony_date->format('d M Y') }}</td>
                                <td class="px-6 py-4">RM {{ number_format((float) $g->fee, 2) }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border {{ $colors[$g->status] }}">
                                        {{ ucfirst($g->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-700">{{ $g->students_count }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('graduations.show', $g) }}" class="font-medium text-indigo-600 hover:text-indigo-900">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    No graduations available yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $graduations->links() }}
            </div>
        </div>
    </div>
</x-app-layout>