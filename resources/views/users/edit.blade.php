<x-layouts::app :title="__('Edit User')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-neutral-400">
                <a href="{{ route('users.index') }}"
                   class="hover:text-gray-700 dark:hover:text-neutral-200">
                    Users
                </a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('users.show', $user) }}"
                   class="hover:text-gray-700 dark:hover:text-neutral-200">
                    {{ $user->name }}
                </a>
                <span aria-hidden="true">/</span>
                <span class="font-medium text-gray-900 dark:text-neutral-100">
                    Edit
                </span>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('users.show', $user) }}"
                   class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200 dark:hover:bg-neutral-800">
                    Cancel
                </a>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="flex items-center gap-4 border-b border-neutral-200 px-6 py-5 dark:border-neutral-700">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-base font-semibold text-white">
                    {{ $user->initials() }}
                </div>
                <div class="min-w-0">
                    <h2 class="truncate text-lg font-semibold text-gray-900 dark:text-neutral-100">
                        Edit {{ $user->name }}
                    </h2>
                    <p class="truncate text-sm text-gray-500 dark:text-neutral-400">
                        Update profile information. Leave the password fields empty to keep the current password.
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-6 px-6 py-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-neutral-200">
                        Name
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name', $user->name) }}"
                           required
                           autofocus
                           autocomplete="name"
                           class="mt-1 block w-full rounded-md border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 @error('name') border-rose-500 @enderror" />
                    @error('name')
                        <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-neutral-200">
                        Email
                    </label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email', $user->email) }}"
                           required
                           autocomplete="email"
                           class="mt-1 block w-full rounded-md border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 @error('email') border-rose-500 @enderror" />
                    @if ($user->email_verified_at)
                        <p class="mt-1 text-xs text-gray-500 dark:text-neutral-400">
                            Verified on {{ $user->email_verified_at->format('d M Y') }}. Changing the email will require re-verification.
                        </p>
                    @else
                        <p class="mt-1 text-xs text-amber-600 dark:text-amber-400">
                            This email has not been verified yet.
                        </p>
                    @endif
                    @error('email')
                        <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="border-t border-neutral-200 pt-5 dark:border-neutral-700">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-neutral-100">
                        Change password
                    </h3>
                    <p class="mt-1 text-xs text-gray-500 dark:text-neutral-400">
                        Optional — leave blank to keep the current password.
                    </p>

                    <div class="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-neutral-200">
                                New password
                            </label>
                            <input type="password"
                                   id="password"
                                   name="password"
                                   autocomplete="new-password"
                                   class="mt-1 block w-full rounded-md border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 @error('password') border-rose-500 @enderror" />
                            <p class="mt-1 text-xs text-gray-500 dark:text-neutral-400">
                                Minimum 8 characters.
                            </p>
                            @error('password')
                                <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-neutral-200">
                                Confirm new password
                            </label>
                            <input type="password"
                                   id="password_confirmation"
                                   name="password_confirmation"
                                   autocomplete="new-password"
                                   class="mt-1 block w-full rounded-md border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between border-t border-neutral-200 pt-5 dark:border-neutral-700">
                    <button type="button"
                            onclick="document.getElementById('delete-user-form').submit();"
                            class="inline-flex items-center rounded-md border border-rose-300 bg-white px-3 py-1.5 text-sm font-medium text-rose-700 hover:bg-rose-50 dark:border-rose-900/60 dark:bg-neutral-900 dark:text-rose-300 dark:hover:bg-rose-950/40">
                            Delete user
                    </button>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('users.show', $user) }}"
                           class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200 dark:hover:bg-neutral-800">
                            Cancel
                        </a>
                        <button type="submit"
                                class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700">
                            Save changes
                        </button>
                    </div>
                </div>
            </form>

            <form id="delete-user-form"
                  method="POST"
                  action="{{ route('users.destroy', $user) }}"
                  onsubmit="return confirm('Delete {{ $user->name }}? This cannot be undone.');"
                  class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>

    </div>
</x-layouts::app>