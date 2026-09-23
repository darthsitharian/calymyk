(function () {
	'use strict';

	function initToolCarousels() {
		const carousels = document.querySelectorAll('.cm-tools-query[data-cm-tools-carousel="true"]');

		carousels.forEach(function (carousel) {
			const grid = carousel.querySelector('.cm-tools-grid');
			const items = grid ? Array.from(grid.children) : [];

			if (!grid || items.length < 2) {
				return;
			}

			carousel.classList.add('is-carousel');
			grid.style.setProperty('--cm-tool-items', String(items.length));

			items.forEach(function (item, index) {
				item.style.setProperty('--cm-tool-index', String(index));
			});

		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initToolCarousels, { once: true });
	} else {
		initToolCarousels();
	}
}());
