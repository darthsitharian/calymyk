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

			if (!track || track.scrollWidth <= carousel.clientWidth) {
				return;
			}

			carousel.dataset.cmToolsReady = 'true';

			let direction = 0;
			let frame = 0;
			let lastTime = 0;

			const stop = () => {
				direction = 0;
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

				const maxShift = Math.max(0, track.scrollWidth - carousel.clientWidth);
				const current = Number(track.dataset.cmShift || 0);
				const next = Math.max(-maxShift, Math.min(0, current + direction * delta * 0.055));

				track.dataset.cmShift = String(next);
				track.style.transform = `translate3d(${next}px, 0, 0)`;

				if (next === 0 || next === -maxShift) {
					direction = 0;
					frame = 0;
					return;
				}

				frame = requestAnimationFrame(tick);
			};

			carousel.addEventListener('mousemove', (event) => {
				const rect = carousel.getBoundingClientRect();
				const x = event.clientX - rect.left;
				const edge = Math.max(90, rect.width * 0.18);

				if (x < edge) {
					direction = 1;
				} else if (x > rect.width - edge) {
					direction = -1;
				} else {
					stop();
					lastTime = 0;
					return;
				}

				lastTime = 0;

				if (!frame) {
					frame = requestAnimationFrame(tick);
				}
			});

			carousel.addEventListener('mouseleave', stop);
			window.addEventListener('resize', stop, { passive: true });
		});
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initToolsCarousels);
	} else {
		initToolsCarousels();
	}
})();
