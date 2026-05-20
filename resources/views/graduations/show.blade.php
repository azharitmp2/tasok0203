<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $graduation->title }}
            </h2>
            @can('update', $graduation)
                <a href="{{ route('graduations.edit', $graduation) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Edit Settings
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
    <x-breadcrumbs :links="[
            'Graduations' => route('graduations.index'),
            $graduation->title => null // Last item has null URL so it stays text-only
        ]" />

            @if (session('status'))
                <div class="p-4 bg-green-100 border border-green-200 text-green-800 rounded-lg shadow-sm">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200 p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <span class="text-xs font-semibold uppercase text-gray-400">Ceremony Date</span>
                    <p class="text-lg font-bold text-gray-800">{{ $graduation->ceremony_date->format('d F Y') }}</p>
                </div>
                <div>
                    <span class="text-xs font-semibold uppercase text-gray-400">Registration Fee</span>
                    <p class="text-lg font-bold text-gray-800">RM {{ number_format((float) $graduation->fee, 2) }}</p>
                </div>
                <div>
                    <span class="text-xs font-semibold uppercase text-gray-400">Portal Status</span>
                    <p class="mt-1">
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ $graduation->status === 'open' ? 'bg-green-100 text-green-700 border-green-200' : ($graduation->status === 'closed' ? 'bg-amber-100 text-amber-700 border-amber-200' : 'bg-slate-100 text-slate-700 border-slate-200') }}">
                            {{ ucfirst($graduation->status) }}
                        </span>
                    </p>
                </div>
            </div>

            @foreach ($students as $student)
                @can('delete', $student)
                    <form method="POST" action="{{ route('graduations.students.destroy', [$graduation, $student]) }}" id="row-delete-{{ $student->id }}" onsubmit="return confirm('Are you sure you want to completely remove {{ $student->name }} from this roster?');">
                        @csrf
                        @method('DELETE')
                    </form>
                @endcan
            @endforeach

            @can('viewAny', App\Models\Student::class)
                <form method="POST" action="{{ route('graduations.students.bulk', $graduation) }}" id="bulk-form" class="hidden" onsubmit="
                    if (document.querySelectorAll('input[name=\'ids[]\']:checked').length === 0) {
                        alert('You must select at least one student checkbox row to execute batch operations.');
                        return false;
                    }
                    return confirm('Are you sure you want to apply this action to all selected student rows?');
                "></form>
            @endcan

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                    
                    <form method="GET" action="{{ route('graduations.show', $graduation) }}" class="flex gap-2 w-full max-w-sm">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                        <input type="hidden" name="direction" value="{{ request('direction') }}">
                        
                        <div class="relative w-full">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, IC, email, matric..." class="rounded-md border-gray-300 text-sm w-full focus:border-indigo-500 focus:ring-indigo-500 shadow-sm pr-12">
                            @if(request('search') || request('status') || request('sort'))
                                <a href="{{ route('graduations.show', $graduation) }}" class="absolute right-3 top-2.5 text-xs text-gray-400 hover:text-gray-600 underline">Clear</a>
                            @endif
                        </div>
                        <button type="submit" class="inline-flex items-center px-3 py-2 bg-slate-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-700 active:bg-slate-800 transition ease-in-out duration-150 shadow-sm">
                            Search
                        </button>
                    </form>
                    
                    @can('create', App\Models\Student::class)
                        <div class="flex items-center flex-wrap gap-3 w-full lg:w-auto justify-end">
                            <a href="{{ route('graduations.students.export', array_filter([
                                'graduation' => $graduation,
                                'search' => request('search'),
                                'status' => request('status'),
                            ])) }}" class="inline-flex items-center px-3 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm text-center whitespace-nowrap">
                                Export CSV
                            </a>

                            <form method="POST" action="{{ route('graduations.students.import', $graduation) }}" enctype="multipart/form-data" class="inline-flex items-center gap-2 bg-gray-100 p-1.5 rounded-md border border-gray-200 shadow-sm">
                                @csrf
                                <input type="file" name="csv" accept=".csv,text/csv" class="text-xs text-gray-700 file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300 cursor-pointer" required />
                                <button type="submit" class="inline-flex items-center px-2.5 py-1 bg-slate-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-700 active:bg-slate-800 transition ease-in-out duration-150">
                                    Import CSV
                                </button>
                            </form>

                            <a href="{{ route('graduations.students.create', $graduation) }}" class="inline-flex items-center px-3 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm whitespace-nowrap">
                                + Add Student
                            </a>
                        </div>
                    @endcan
                </div>

                @php
                    $statuses = [
                        '' => ['label' => 'All', 'class' => 'bg-slate-100 text-slate-700 border-slate-300'],
                        'verified' => ['label' => 'Verified', 'class' => 'bg-green-100 text-green-700 border-green-300'],
                        'pending' => ['label' => 'Pending Review', 'class' => 'bg-amber-100 text-amber-700 border-amber-300'],
                        'not_paid' => ['label' => 'Not Paid', 'class' => 'bg-gray-100 text-gray-600 border-gray-300'],
                    ];
                    $currentStatus = request('status', '');
                @endphp
                <div class="px-6 py-2 bg-gray-50 border-b border-gray-100 flex flex-wrap gap-2">
                    @foreach ($statuses as $value => $cfg)
                        <a href="{{ route('graduations.show', array_filter([
                            'graduation' => $graduation,
                            'status' => $value ?: null,
                            'search' => request('search'),
                            'sort' => request('sort'),
                            'direction' => request('direction'),
                        ])) }}" class="px-3 py-1 text-xs font-semibold rounded-full border transition duration-150 {{ $cfg['class'] }} {{ $currentStatus === $value ? 'ring-2 ring-indigo-500 font-bold border-indigo-500 shadow-sm' : 'opacity-70 hover:opacity-100' }}">
                            {{ $cfg['label'] }}
                        </a>
                    @endforeach
                </div>

                @can('viewAny', App\Models\Student::class)
                    <div class="px-6 py-3 bg-indigo-50 border-b border-gray-200 flex items-center gap-3">
                        <span class="text-xs font-bold uppercase text-indigo-700 tracking-wider">Batch Operations:</span>
                        @csrf
                        <select name="action" form="bulk-form" class="rounded-md border-gray-300 text-xs py-1 px-2 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="verify">Verify Payments</option>
                            <option value="delete">Delete Selected</option>
                        </select>
                        <button type="submit" form="bulk-form" class="inline-flex items-center px-3 py-1 bg-indigo-600 border border-transparent rounded-md font-semibold text-2xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 transition ease-in-out duration-150 shadow-sm">
                            Apply to Selected
                        </button>
                    </div>
                @endcan

                @php
                    $sort = request('sort', 'created_at');
                    $direction = request('direction', 'desc');
                    
                    $sortLink = function (string $column, string $label) use ($graduation, $sort, $direction) {
                        $isActive = $sort === $column;
                        $newDirection = $isActive && $direction === 'asc' ? 'desc' : 'asc';
                        $arrow = $isActive ? ($direction === 'asc' ? ' ▲' : ' ▼') : '';
                        
                        $url = route('graduations.show', array_merge(
                            request()->except(['page']),
                            ['graduation' => $graduation, 'sort' => $column, 'direction' => $newDirection]
                        ));
                        
                        return '<a href="' . e($url) . '" class="hover:underline inline-flex items-center font-bold text-gray-700">' . e($label) . '<span class="text-indigo-600 font-normal text-2xs">' . $arrow . '</span></a>';
                    };
                @endphp

                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-3 w-4">
                                @can('viewAny', App\Models\Student::class)
                                    <input type="checkbox" onchange="document.querySelectorAll('input[name=\'ids[]\']').forEach(cb => cb.checked = this.checked)" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 cursor-pointer" />
                                @endcan
                            </th>
                            <th scope="col" class="px-6 py-3">{!! $sortLink('name', 'Name') !!}</th>
                            <th scope="col" class="px-6 py-3">{!! $sortLink('ic', 'IC') !!}</th>
                            <th scope="col" class="px-6 py-3">{!! $sortLink('matric_card', 'Matric') !!}</th>
                            <th scope="col" class="px-6 py-3">Email</th>
                            <th scope="col" class="px-6 py-3">Payment Status</th>
                            <th scope="col" class="px-6 py-3"><span class="sr-only">Manage</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($students as $student)
                            <tr class="bg-white hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4">
                                    @can('viewAny', App\Models\Student::class)
                                        <input type="checkbox" name="ids[]" value="{{ $student->uuid }}" form="bulk-form" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 cursor-pointer" />
                                    @endcan
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $student->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap font-mono text-xs text-gray-600">{{ $student->ic }}</td>
                                <td class="px-6 py-4">{{ $student->matric_card }}</td>
                                <td class="px-6 py-4">{{ $student->email }}</td>
                                <td class="px-6 py-4">
                                    @if($student->isVerified())
                                        <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">Verified</span>
                                    @elseif($student->hasPaid())
                                        <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800 border border-amber-200">Pending Review</span>
                                    @else
                                        <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-slate-100 text-slate-800 border border-slate-200">Unpaid</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right flex items-center justify-end gap-3">
                                    <a href="{{ route('graduations.students.show', [$graduation, $student]) }}" class="font-medium text-indigo-600 hover:text-indigo-900">Manage</a>
                                    
                                    @can('delete', $student)
                                        <button type="submit" form="row-delete-{{ $student->id }}" class="font-medium text-red-600 hover:text-red-900">Remove</button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    @if(request('search') || request('status'))
                                        No student records match the active filter criteria.
                                    @else
                                        No student rosters imported to this session.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($students->hasPages())
                    <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                        {{ $students->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>