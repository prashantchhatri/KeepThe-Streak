<nav class="mx-auto mb-6 w-full max-w-md px-4 pt-4">
    <div class="flex items-center justify-between rounded-xl border border-slate-200/80 bg-white/90 px-4 py-2 shadow-sm shadow-slate-200/60 backdrop-blur dark:border-slate-800 dark:bg-slate-900/85 dark:shadow-black/20">
        <a href="/dashboard" class="inline-block transform transition hover:scale-105">
            <img src="/images/logo.png" alt="KeepTheStreak" class="h-10 w-auto">
        </a>

        <div class="flex items-center gap-2">
            <x-theme-toggle />

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-xl bg-slate-100 px-4 text-sm font-semibold text-slate-700 transition duration-200 hover:bg-slate-200 active:scale-95 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                    {{ __('Logout') }}
                </button>
            </form>
        </div>
    </div>
</nav>
