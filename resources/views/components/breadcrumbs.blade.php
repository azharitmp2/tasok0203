@props(['links' => []])

<nav class="flex mb-4 overflow-x-auto whitespace-nowrap text-sm text-gray-500 py-1" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="{{ route('dashboard') }}" wire:navigate class="inline-flex items-center text-gray-600 hover:text-indigo-600 transition font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1/1 0 001 1h3m10-11l2 2m-2-2v10a1/1 0 01-1 1h3m-6 0a1/1 0 001-1v-4a1/1 0 011-1h2a1/1 0 011 1v4a1/1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>
        </li>

        @foreach ($links as $label => $url)
            <li>
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1 md:mx-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                    
                    @if ($url)
                        <a href="{{ $url }}" wire:navigate class="text-gray-600 hover:text-indigo-600 transition font-medium">
                            {{ $label }}
                        </a>
                    @else
                        <span class="text-gray-400 font-normal truncate max-w-[180px] sm:max-w-xs">
                            {{ $label }}
                        </span>
                    @endif
                </div>
            </li>
        @endforeach
    </ol>
</nav>