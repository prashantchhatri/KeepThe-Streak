<script>
    (() => {
        const storageKey = 'keepthestreak-theme';
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');

        const readStoredTheme = () => {
            try {
                const storedTheme = window.localStorage.getItem(storageKey);
                return storedTheme === 'dark' || storedTheme === 'light' ? storedTheme : null;
            } catch (error) {
                return null;
            }
        };

        const resolveTheme = () => {
            return readStoredTheme() ?? (mediaQuery.matches ? 'dark' : 'light');
        };

        const applyTheme = (theme) => {
            const resolvedTheme = theme === 'dark' || theme === 'light' ? theme : resolveTheme();
            const isDark = resolvedTheme === 'dark';

            document.documentElement.classList.toggle('dark', isDark);
            document.documentElement.style.colorScheme = isDark ? 'dark' : 'light';
            document.documentElement.dataset.theme = resolvedTheme;

            const themeColorMeta = document.querySelector('meta[name="theme-color"]');
            if (themeColorMeta) {
                themeColorMeta.setAttribute('content', isDark ? '#020617' : '#4f46e5');
            }

            window.dispatchEvent(new CustomEvent('keepthestreak-theme-changed', {
                detail: { theme: resolvedTheme },
            }));

            return resolvedTheme;
        };

        const setTheme = (theme) => {
            try {
                window.localStorage.setItem(storageKey, theme);
            } catch (error) {
                // Ignore storage write failures and still apply the theme for this session.
            }

            return applyTheme(theme);
        };

        window.keepTheStreakTheme = {
            getTheme: resolveTheme,
            applyTheme,
            setTheme,
            toggleTheme() {
                return setTheme(resolveTheme() === 'dark' ? 'light' : 'dark');
            },
        };

        applyTheme(resolveTheme());

        const syncWithSystemTheme = () => {
            if (readStoredTheme() === null) {
                applyTheme(resolveTheme());
            }
        };

        if (typeof mediaQuery.addEventListener === 'function') {
            mediaQuery.addEventListener('change', syncWithSystemTheme);
        } else if (typeof mediaQuery.addListener === 'function') {
            mediaQuery.addListener(syncWithSystemTheme);
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('themeToggle', () => ({
                theme: resolveTheme(),

                init() {
                    const syncTheme = () => {
                        this.theme = resolveTheme();
                    };

                    syncTheme();
                    window.addEventListener('keepthestreak-theme-changed', syncTheme);
                },

                toggle() {
                    this.theme = window.keepTheStreakTheme.toggleTheme();
                },
            }));
        });
    })();
</script>
