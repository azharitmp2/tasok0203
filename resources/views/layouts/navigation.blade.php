<nav x-data="{ open: false }" class="w-full md:w-64 bg-white border-b md:border-b-0 md:border-r border-gray-200 flex-shrink-0">
    <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 bg-white">
        <div class="flex items-center gap-2">
            <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center">
                <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
            </a>
            <span class="font-bold text-lg text-gray-800 tracking-tight">KonvoJTM</span>
        </div>

        <div class="flex items-center md:hidden">
            <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden md:flex md:h-[calc(100vh-77px)] flex-col justify-between overflow-y-auto">
        
        <div class="px-4 py-6 space-y-2 flex-1">
            
            <a href="{{ route('dashboard') }}" 
               wire:navigate
               class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition duration-150 {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700 font-semibold shadow-xs' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1/1 0 001 1h3m10-11l2 2m-2-2v10a1/1 0 01-1 1h3m-6 0a1/1 0 001-1v-4a1/1 0 011-1h2a1/1 0 011 1v4a1/1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>

            <a href="{{ route('graduations.index') }}" 
               wire:navigate
               class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition duration-150 {{ request()->routeIs('graduations.*') ? 'bg-indigo-50 text-indigo-700 font-semibold shadow-xs' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-5.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" /></svg>
                Graduations
            </a>

        </div>

        <div class="border-t border-gray-100 p-4 bg-gray-50/50">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1 pr-2">
                    <p class="text-2xs font-semibold text-gray-400 uppercase tracking-wider">Logged in as</p>
                    <p class="text-sm font-bold text-gray-700 truncate">{{ Auth::user()->name }}</p>
                </div>
                
                <form method="POST" action="{{ route('logout') }}" class="inline-flex flex-shrink-0">
                    @csrf
                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 rounded-md hover:bg-red-50 transition duration-150" title="Logout">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>

    </div>
</nav>