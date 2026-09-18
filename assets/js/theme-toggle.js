(function () {
    'use strict';

    const STORAGE_KEY = 'calymyk-theme';

    const root = document.documentElement;
    const toggle = document.querySelector('.cm-theme-toggle');

    if (!toggle) {
        return;
    }

    function getTheme() {
        return localStorage.getItem(STORAGE_KEY) || 'dark';
    }

    function applyTheme(theme) {
        const isLight = theme === 'light';

        root.dataset.theme = theme;

        toggle.setAttribute(
            'aria-pressed',
            isLight ? 'true' : 'false'
        );

        toggle.setAttribute(
            'aria-label',
            isLight
                ? 'Przełącz na tryb ciemny'
                : 'Przełącz na tryb jasny'
        );

        const sun = toggle.querySelector('.cm-theme-toggle__icon--sun');
        const moon = toggle.querySelector('.cm-theme-toggle__icon--moon');

        if (sun) {
            sun.style.display = isLight ? 'none' : 'block';
        }

        if (moon) {
            moon.style.display = isLight ? 'block' : 'none';
        }
    }

    function toggleTheme() {
        const currentTheme = getTheme();
        const nextTheme = currentTheme === 'light'
            ? 'dark'
            : 'light';

        localStorage.setItem(STORAGE_KEY, nextTheme);
        applyTheme(nextTheme);
    }

    applyTheme(getTheme());

    toggle.addEventListener('click', toggleTheme);
})();
