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

			const controls = document.createElement('div');
			controls.className = 'cm-tools-query__controls';

			const pauseButton = document.createElement('button');
			pauseButton.type = 'button';
			pauseButton.className = 'cm-tools-query__pause';
			pauseButton.setAttribute('aria-pressed', 'false');
			pauseButton.setAttribute('aria-label', 'Wstrzymaj automatyczne przewijanie narzędzi');
			pauseButton.textContent = 'Wstrzymaj';

			pauseButton.addEventListener('click', function () {
				const paused = carousel.classList.toggle('is-paused');
				pauseButton.setAttribute('aria-pressed', paused ? 'true' : 'false');
				pauseButton.setAttribute(
					'aria-label',
					paused
						? 'Wznów automatyczne przewijanie narzędzi'
						: 'Wstrzymaj automatyczne przewijanie narzędzi'
				);
				pauseButton.textContent = paused ? 'Wznów' : 'Wstrzymaj';
			});

			controls.appendChild(pauseButton);
			carousel.appendChild(controls);
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initToolCarousels, { once: true });
	} else {
		initToolCarousels();
	}
}());
