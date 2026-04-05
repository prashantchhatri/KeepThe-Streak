<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold uppercase tracking-widest text-slate-700 shadow-sm transition ease-in-out duration-150 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white disabled:opacity-25 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800 dark:focus:ring-offset-slate-900']) }}>
    {{ $slot }}
</button>
