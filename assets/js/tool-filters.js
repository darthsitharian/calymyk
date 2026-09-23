(function () {
	'use strict';

	function initToolFilters() {
		const controls = document.querySelector('.cm-tools-archive__controls');
		const tagCloud = controls && controls.querySelector('.wp-block-tag-cloud');
		const grid = document.querySelector('.cm-tools-archive-grid');

		if (!controls || !tagCloud || !grid) {
			return;
		}

		const links = Array.from(tagCloud.querySelectorAll('a'));
		const items = Array.from(grid.children);

		if (!links.length || !items.length) {
			return;
		}

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
			button.dataset.slug = new URL(link.href, window.location.origin).pathname
				.split('/')
				.filter(Boolean)
				.pop() || '';

			filterBar.appendChild(button);
			filterButtons.push(button);
		});

		tagCloud.replaceWith(filterBar);

		function getFilterSlugFromUrl() {
			return new URLSearchParams(window.location.search).get('kategoria') || '';
		}

		function updateFilterUrl(slug, replace) {
			const url = new URL(window.location.href);

			if (slug) {
				url.searchParams.set('kategoria', slug);
			} else {
				url.searchParams.delete('kategoria');
			}

			if (replace) {
				window.history.replaceState({ toolFilter: slug }, '', url);
			} else {
				window.history.pushState({ toolFilter: slug }, '', url);
			}
		}

		function findButton(slug) {
			return filterButtons.find(function (button) {
				return button.dataset.slug === slug;
			}) || allButton;
		}

		function filterItems(slug, activeButton) {
			items.forEach(function (item) {
				const matches = !slug || item.classList.contains('tool_category-' + slug);
				item.hidden = !matches;
			});

			filterButtons.forEach(function (button) {
				const active = button === activeButton;
				button.classList.toggle('is-active', active);
				button.setAttribute('aria-pressed', active ? 'true' : 'false');
			});
		}


		filterButtons.forEach(function (button) {
			button.addEventListener('click', function () {
				const slug = button.dataset.slug;
				filterItems(slug, button);
				updateFilterUrl(slug, false);
			});
		});

		window.addEventListener('popstate', function () {
			const slug = getFilterSlugFromUrl();
			filterItems(slug, findButton(slug));
		});

		const initialSlug = getFilterSlugFromUrl();
		const initialButton = findButton(initialSlug);
		const normalizedSlug = initialButton.dataset.slug;

		filterItems(normalizedSlug, initialButton);

		if (initialSlug !== normalizedSlug) {
			updateFilterUrl(normalizedSlug, true);
		}
	}
	
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initToolFilters, { once: true });
	} else {
		initToolFilters();
	}
}());
