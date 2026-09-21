(function () {
	'use strict';

	function getSlug(url) {
		try {
			const parsed = new URL(url, window.location.origin);
			const parts = parsed.pathname.split('/').filter(Boolean);
			return parts.length ? parts[parts.length - 1] : '';
		} catch (error) {
			return '';
		}
	}

	function initToolFilters() {
		const controls = document.querySelector('.cm-tools-archive__controls');
		const tagCloud = controls && controls.querySelector('.wp-block-tag-cloud');
		const grid = document.querySelector('.cm-tools-archive-grid');

		if (!controls || !tagCloud || !grid) {
			return;
		}

		const filterLinks = Array.from(tagCloud.querySelectorAll('a'));
		const cards = Array.from(grid.querySelectorAll('.cm-tool-archive-card'));

		if (!filterLinks.length || !cards.length) {
			return;
		}

		const cardCategories = new Map();

		cards.forEach(function (card) {
			const slugs = Array.from(
				card.querySelectorAll('.cm-tool-archive-card__category a')
			)
				.map(function (link) {
					return getSlug(link.href);
				})
				.filter(Boolean);

			cardCategories.set(card, slugs);
		});

		function setActive(activeLink) {
			filterLinks.forEach(function (link) {
				const active = link === activeLink;
				link.classList.toggle('is-active', active);
				link.setAttribute('aria-current', active ? 'true' : 'false');
			});
		}

		function filterCards(slug, activeLink) {
			cards.forEach(function (card) {
				const categories = cardCategories.get(card) || [];
				card.hidden = Boolean(slug) && !categories.includes(slug);
			});

		setActive(activeLink);
		}

		filterLinks.forEach(function (link) {
			link.addEventListener('click', function (event) {
				event.preventDefault();
				filterCards(getSlug(link.href), link);
			});
		});

		filterCards('', null);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initToolFilters, { once: true });
	} else {
		initToolFilters();
	}
}());
