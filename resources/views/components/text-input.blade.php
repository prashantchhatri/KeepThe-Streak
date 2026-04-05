@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full rounded-xl border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 shadow-sm placeholder:text-slate-400 focus:border-indigo-400 focus:ring-indigo-400 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-indigo-500 dark:focus:ring-indigo-500']) }}>
