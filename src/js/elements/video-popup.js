import "../../scss/elements/video-popup.scss";

/**
 * Video Popup — Bricks element script.
 *
 * Wires the .bricksfly-popup-btn inside a host element to a shared body-level
 * .bricksfly-popup-video-wrapper overlay. The host's own .bricksfly-popup-source
 * markup is treated as a template: the first one we see gets promoted to
 * <body>, duplicates from other instances are discarded.
 *
 * Exposes window.bricksflyVideoPopup(rootEl) for use by video-box.js (and any
 * other element that emits the same markup pattern).
 */
(function () {
	'use strict';

	var GLOBAL_WRAPPER_SELECTOR = 'body > .bricksfly-popup-video-wrapper';

	function ensureGlobalWrapper(rootEl) {
		var existing = document.querySelector(GLOBAL_WRAPPER_SELECTOR);
		if (existing) {
			// A wrapper is already on body — drop any template copy inside
			// this host so we don't accumulate hidden duplicates.
			var localSource = rootEl.querySelector('.bricksfly-popup-source');
			if (localSource) localSource.remove();
			return existing;
		}

		// Promote this host's template to body, drop the .bricksfly-popup-source
		// hidden wrapper around it.
		var localWrapper = rootEl.querySelector('.bricksfly-popup-source .bricksfly-popup-video-wrapper');
		if (!localWrapper) return null;

		document.body.appendChild(localWrapper);

		var leftover = rootEl.querySelector('.bricksfly-popup-source');
		if (leftover) leftover.remove();

		bindGlobalWrapper(localWrapper);
		return localWrapper;
	}

	function bindGlobalWrapper(wrapper) {
		if (wrapper.dataset.bricksflyPopupBound === '1') return;
		wrapper.dataset.bricksflyPopupBound = '1';

		var closeBtn = wrapper.querySelector('.bricksfly-popup-close');
		if (closeBtn) {
			closeBtn.addEventListener('click', function (e) {
				e.preventDefault();
				closePopup(wrapper);
			});
		}

		// Click on backdrop (anywhere outside the .bricksfly-popup-video box) closes.
		wrapper.addEventListener('click', function (e) {
			var videoBox = wrapper.querySelector('.bricksfly-popup-video');
			if (videoBox && !videoBox.contains(e.target)) {
				closePopup(wrapper);
			}
		});

		// ESC to close.
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && wrapper.classList.contains('is-open')) {
				closePopup(wrapper);
			}
		});
	}

	function openPopup(wrapper, url) {
		if (!wrapper || !url) return;

		var container = wrapper.querySelector('.bricksfly-popup-content-container');
		if (!container) return;

		// Fresh iframe per open so the video starts at 0 and previous
		// state doesn't linger across opens.
		container.innerHTML =
			'<iframe src="' + url + '" frameborder="0" ' +
			'allow="autoplay; encrypted-media; picture-in-picture" ' +
			'allowfullscreen></iframe>';

		wrapper.classList.add('is-open');
		document.body.classList.add('bricksfly-popup-open');
	}

	function closePopup(wrapper) {
		if (!wrapper) return;

		wrapper.classList.remove('is-open');
		document.body.classList.remove('bricksfly-popup-open');

		// Empty the container so the iframe stops loading/playing.
		var container = wrapper.querySelector('.bricksfly-popup-content-container');
		if (container) container.innerHTML = '';
	}

	function bindButton(btn, wrapper) {
		if (btn.dataset.bricksflyPopupBound === '1') return;
		btn.dataset.bricksflyPopupBound = '1';

		btn.addEventListener('click', function (e) {
			e.preventDefault();
			var url = btn.getAttribute('data-src') || '';
			if (!url) return;
			openPopup(wrapper, url);
		});
	}

	function initRoot(rootEl) {
		if (!rootEl) return;

		var wrapper = ensureGlobalWrapper(rootEl);
		if (!wrapper) return;

		var buttons = rootEl.querySelectorAll('.bricksfly-popup-btn');
		buttons.forEach(function (btn) {
			bindButton(btn, wrapper);
		});
	}

	function initAll() {
		document.querySelectorAll('.bricksfly-video-box').forEach(initRoot);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initAll);
	} else {
		initAll();
	}

	// Called by video-box.js (and the Bricks $scripts re-init path) with the
	// host element for each instance.
	window.bricksflyVideoPopup = initRoot;
})();
