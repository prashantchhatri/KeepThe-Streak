<nav class="mx-auto mb-6 w-full max-w-md px-4 pt-4">
    <div class="flex items-center justify-between rounded-xl bg-white px-4 py-2 shadow-sm">
        <a href="/dashboard" class="inline-block transform transition hover:scale-105">
            <img src="/images/logo.png" alt="KeepTheStreak" class="h-10 w-auto">
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-xl bg-slate-100 px-4 text-sm font-semibold text-slate-700 transition duration-200 hover:bg-slate-200 active:scale-95">
                {{ __('Logout') }}
            </button>
        </form>
    </div>
</nav>
