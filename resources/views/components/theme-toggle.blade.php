<button
    type="button"
    x-data="themeToggle()"
    @click="toggle()"
    class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-slate-200 bg-white/80 px-3 text-sm font-semibold text-slate-700 shadow-sm transition duration-200 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white active:scale-95 dark:border-slate-700 dark:bg-slate-900/80 dark:text-slate-200 dark:hover:bg-slate-800 dark:focus:ring-offset-slate-950"
    :aria-label="theme === 'dark' ? '{{ __('Switch to light mode') }}' : '{{ __('Switch to dark mode') }}'"
    :title="theme === 'dark' ? '{{ __('Switch to light mode') }}' : '{{ __('Switch to dark mode') }}'"
>
    <svg
        x-show="theme !== 'dark'"
        x-cloak
        xmlns="http://www.w3.org/2000/svg"
        class="h-4 w-4"
        viewBox="0 0 20 20"
        fill="currentColor"
        aria-hidden="true"
    >
        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.002 8.002 0 1010.586 10.586z" />
    </svg>

    <svg
        x-show="theme === 'dark'"
        x-cloak
        xmlns="http://www.w3.org/2000/svg"
        class="h-4 w-4"
        viewBox="0 0 20 20"
        fill="currentColor"
        aria-hidden="true"
    >
        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1.055a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.745.744a1 1 0 101.414-1.414l-.744-.745a1 1 0 00-1.415 1.415zm2.12-10.607a1 1 0 010 1.414l-.745.745a1 1 0 11-1.414-1.414l.744-.745a1 1 0 011.415 0zM17 11a1 1 0 100-2h-1.055a1 1 0 100 2H17zm-7 6a1 1 0 011 1v1.055a1 1 0 11-2 0V18a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.745-.744A1 1 0 104.307 5.72l.743.744zm1.415 8.486a1 1 0 00-1.414-1.414l-.744.744a1 1 0 101.414 1.414l.744-.744zM4.055 11a1 1 0 100-2H3a1 1 0 100 2h1.055z" clip-rule="evenodd" />
    </svg>

    <span class="text-xs font-semibold uppercase tracking-[0.14em]" x-text="theme === 'dark' ? '{{ __('Light') }}' : '{{ __('Dark') }}'">Theme</span>
</button>
