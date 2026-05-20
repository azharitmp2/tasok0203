<x-layouts::app :title="__('Users')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-lg font-semibold text-gray-900 dark:text-neutral-100">
                    Users
                </h1>
                <p class="text-sm text-gray-500 dark:text-neutral-400">
                    {{ $users->count() }} {{ Str::plural('user', $users->count()) }} registered.
                </p>
            </div>

            <a href="{{ route('users.create') }}"
               class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700">
                Add user
            </a>
        </div>

        @if (session('status'))
            <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">
                {{ session('status') }}
            </div>
        @endif

        <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                <thead class="bg-neutral-50 dark:bg-neutral-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-neutral-400">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-neutral-400">E-mail</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-neutral-400">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-neutral-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse ($users as $user)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-neutral-100">
                                <a href="{{ route('users.show', $user) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                    {{ $user->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-neutral-400">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if ($user->email_verified_at)
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-500/30">
                                        Verified
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-300 dark:ring-amber-500/30">
                                        Unverified
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-sm">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('users.show', $user) }}"
                                       class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                                        View
                                    </a>
                                    <span class="text-neutral-300 dark:text-neutral-600">|</span>
                                    <a href="{{ route('users.edit', $user) }}"
                                       class="text-gray-700 hover:text-gray-900 dark:text-neutral-300 dark:hover:text-neutral-100">
                                        Edit
                                    </a>
                                    <span class="text-neutral-300 dark:text-neutral-600">|</span>
                                    <form method="POST"
                                          action="{{ route('users.destroy', $user) }}"
                                          class="inline"
                                          onsubmit="return confirm('Delete {{ $user->name }}? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-rose-600 hover:text-rose-800 dark:text-rose-400 dark:hover:text-rose-300">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-neutral-400">
                                No users yet.
                                <a href="{{ route('users.create') }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    Add the first one
                                </a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</x-layouts::app>