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

		const links = Array.from(tagCloud.querySelectorAll('a'));
		const cards = Array.from(grid.querySelectorAll('.cm-tool-archive-card'));

		if (!links.length || !cards.length) {
			return;
		}

		// Turn the taxonomy links into local filter controls.
		const filterBar = document.createElement('div');
		filterBar.className = 'cm-tools-archive__filters';
		filterBar.setAttribute('role', 'group');
		filterBar.setAttribute('aria-label', 'Kategorie narzędzi');

		const allButton = document.createElement('button');
		allButton.type = 'button';
		allButton.className = 'cm-tools-archive__filter is-active';
		allButton.textContent = 'Wszystkie';
		allButton.dataset.slug = '';
		filterBar.appendChild(allButton);

		const filterButtons = [allButton];

		links.forEach(function (link) {
			const button = document.createElement('button');
			button.type = 'button';
			button.className = 'cm-tools-archive__filter';
			button.textContent = link.textContent.trim();
			button.dataset.slug = getSlug(link.href);
			filterBar.appendChild(button);
			filterButtons.push(button);
		});

		tagCloud.replaceWith(filterBar);

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

		function filterCards(slug, activeButton) {
			cards.forEach(function (card) {
				const categories = cardCategories.get(card) || [];
				card.hidden = Boolean(slug) && !categories.includes(slug);
			});

			filterButtons.forEach(function (button) {
				const active = button === activeButton;
				button.classList.toggle('is-active', active);
				button.setAttribute('aria-pressed', active ? 'true' : 'false');
			});
		}

		filterButtons.forEach(function (button) {
			button.addEventListener('click', function () {
				filterCards(button.dataset.slug, button);
			});
		});

		filterCards('', allButton);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initToolFilters, { once: true });
	} else {
		initToolFilters();
	}
}());
