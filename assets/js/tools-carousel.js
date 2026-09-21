(() => {
	'use strict';

	const initToolsCarousels = () => {
		if (window.matchMedia('(max-width: 768px)').matches) {
			return;
		}

		document.querySelectorAll('[data-cm-tools-carousel="true"]').forEach((carousel) => {
			if (carousel.dataset.cmToolsReady === 'true') {
				return;
			}

			const track = carousel.querySelector('.cm-tools-grid');

			if (!track) {
				return;
			}

			const maxScroll = Math.max(0, track.scrollWidth - carousel.clientWidth);

			if (maxScroll <= 0) {
				return;
			}

			carousel.dataset.cmToolsReady = 'true';

			let direction = 0;
			let frame = 0;
			let lastTime = 0;

			const stop = () => {
				direction = 0;
				lastTime = 0;

				if (frame) {
					cancelAnimationFrame(frame);
					frame = 0;
				}
			};

			const tick = (time) => {
				if (!direction) {
					frame = 0;
					return;
				}

				if (!lastTime) {
					lastTime = time;
				}

				const delta = Math.min(time - lastTime, 32);
				lastTime = time;

				const current = carousel.scrollLeft;
				const speed = 0.42;
				const next = Math.max(0, Math.min(maxScroll, current + direction * delta * speed));

				carousel.scrollLeft = next;

				if (next <= 0 || next >= maxScroll) {
					stop();
					return;
				}

				frame = requestAnimationFrame(tick);
			};

			const updateDirection = (event) => {
				const rect = carousel.getBoundingClientRect();
				const x = event.clientX - rect.left;
				const edgeWidth = Math.min(180, Math.max(120, rect.width * 0.14));

				if (x <= edgeWidth) {
					direction = -1;
				} else if (x >= rect.width - edgeWidth) {
					direction = 1;
				} else {
					stop();
					return;
				}

				if (!frame) {
					lastTime = 0;
					frame = requestAnimationFrame(tick);
				}
			};

			carousel.addEventListener('pointermove', updateDirection, { passive: true });
			carousel.addEventListener('pointerleave', stop, { passive: true });
			window.addEventListener('resize', stop, { passive: true });
		});
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initToolsCarousels);
	} else {
		initToolsCarousels();
	}
})();
