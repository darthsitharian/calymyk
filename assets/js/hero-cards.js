(function () {
    'use strict';

    function initHeroCards() {
        document.querySelectorAll('.cm-featured-post-card').forEach(function (card) {
            if (card.dataset.cmHeroReady === 'true') return;

            const link = card.querySelector('.cm-featured-post-card__title a[href]');
            if (!link) return;

            card.dataset.cmHeroReady = 'true';
            card.dataset.cmPostUrl = link.href;
            card.setAttribute('role', 'link');
            card.setAttribute('tabindex', '0');

            card.addEventListener('click', function (event) {
                if (event.target.closest('a, button, input, select, textarea, summary')) return;
                window.location.href = card.dataset.cmPostUrl;
            });

            card.addEventListener('keydown', function (event) {
                if (event.key !== 'Enter' && event.key !== ' ') return;
                if (event.target.closest('a, button, input, select, textarea, summary')) return;
                event.preventDefault();
                window.location.href = card.dataset.cmPostUrl;
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initHeroCards);
    } else {
        initHeroCards();
    }
})();
