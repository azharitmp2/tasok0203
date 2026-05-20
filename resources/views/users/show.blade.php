<x-layouts::app :title="__('User Details')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-neutral-400">
                <a href="{{ route('users.index') }}"
                   class="hover:text-gray-700 dark:hover:text-neutral-200">
                    Users
                </a>
                <span aria-hidden="true">/</span>
                <span class="font-medium text-gray-900 dark:text-neutral-100">
                    {{ $user->name }}
                </span>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('users.index') }}"
                   class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200 dark:hover:bg-neutral-800">
                    Back
                </a>
                <a href="{{ route('users.edit', $user) }}"
                   class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700">
                    Edit
                </a>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="flex items-center gap-4 border-b border-neutral-200 px-6 py-5 dark:border-neutral-700">
                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-xl font-semibold text-white">
                    {{ $user->initials() }}
                </div>
                <div class="min-w-0 flex-1">
                    <h2 class="truncate text-lg font-semibold text-gray-900 dark:text-neutral-100">
                        {{ $user->name }}
                    </h2>
                    <p class="truncate text-sm text-gray-500 dark:text-neutral-400">
                        {{ $user->email }}
                    </p>
                </div>
                <div class="hidden gap-2 sm:flex">
                    @if ($user->email_verified_at)
                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-500/30">
                            Verified
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700 ring-1 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-300 dark:ring-amber-500/30">
                            Unverified
                        </span>
                    @endif

                    @if ($user->two_factor_confirmed_at)
                        <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700 ring-1 ring-indigo-600/20 dark:bg-indigo-500/10 dark:text-indigo-300 dark:ring-indigo-500/30">
                            2FA on
                        </span>
                    @endif
                </div>
            </div>

            <dl class="grid grid-cols-1 gap-x-6 gap-y-5 px-6 py-5 sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-neutral-400">
                        Name
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-neutral-100">
                        {{ $user->name }}
                    </dd>
                </div>

                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-neutral-400">
                        Email
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-neutral-100">
                        <a href="mailto:{{ $user->email }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                            {{ $user->email }}
                        </a>
                    </dd>
                </div>

                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-neutral-400">
                        Email verified
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-neutral-100">
                        {{ $user->email_verified_at?->format('d M Y, H:i') ?? '—' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-neutral-400">
                        Two-factor auth
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-neutral-100">
                        @if ($user->two_factor_confirmed_at)
                            Enabled — {{ $user->two_factor_confirmed_at->format('d M Y') }}
                        @else
                            Not enabled
                        @endif
                    </dd>
                </div>

                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-neutral-400">
                        Member since
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-neutral-100">
                        {{ $user->created_at?->format('d M Y') ?? '—' }}
                        @if ($user->created_at)
                            <span class="text-gray-500 dark:text-neutral-400">
                                ({{ $user->created_at->diffForHumans() }})
                            </span>
                        @endif
                    </dd>
                </div>

                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-neutral-400">
                        Last updated
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-neutral-100">
                        {{ $user->updated_at?->format('d M Y, H:i') ?? '—' }}
                    </dd>
                </div>

                <div class="sm:col-span-2">
                    <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-neutral-400">
                        User ID
                    </dt>
                    <dd class="mt-1 font-mono text-sm text-gray-900 dark:text-neutral-100">
                        #{{ $user->id }}
                    </dd>
                </div>
            </dl>
        </div>

    </div>
</x-layouts::app>