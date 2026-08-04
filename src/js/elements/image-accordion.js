import "../../scss/elements/image-accordion.scss";

/**
 * Image Accordion - Bricks element script.
 *
 * Frontend: self-initializes via DOMContentLoaded / readyState fallback.
 * Builder: a MutationObserver catches dynamic canvas mounts and Bricks
 *          re-init calls `window.bricksflyImageAccordion(el)` after each render.
 */
(function () {
	'use strict';

	function getWrapper(el) {
		if (!el || !el.classList) return null;
		if (el.classList.contains('bricksfly-image-accordion')) return el;
		return el.querySelector ? el.querySelector('.bricksfly-image-accordion') : null;
	}

	function openAccordion(items, activeItem) {
		items.forEach(function (item) {
			if (item === activeItem) {
				item.classList.add('accordion-hover-active');
			} else {
				item.classList.remove('accordion-hover-active');
			}
		});
	}

	function applyDefaultActive(wrapper) {
		var items = wrapper.querySelectorAll('.accordion-item');
		if (!items.length) return;

		var defaultIdx = parseInt(wrapper.getAttribute('data-default-active') || '1', 10) - 1;
		if (isNaN(defaultIdx) || defaultIdx < 0 || defaultIdx >= items.length) {
			defaultIdx = 0;
		}
		openAccordion(items, items[defaultIdx]);
	}

	function initElement(el) {
		var wrapper = getWrapper(el);
		if (!wrapper) return;

		// applyDefaultActive runs every call so live edits to the
		// "Default Open Item" control reflect immediately. Bricks doesn't
		// always swap the DOM node on attribute-only changes, so we can't
		// rely on a fresh-node init for this.
		applyDefaultActive(wrapper);

		// Idempotency for event binding only. A fresh DOM node (from a full
		// re-render) won't carry this flag, so listeners reattach correctly.
		if (wrapper.dataset.bricksflyAccordionInit === '1') return;
		wrapper.dataset.bricksflyAccordionInit = '1';

		var expand     = wrapper.getAttribute('data-expand') || 'hover';
		var breakpoint = parseInt(wrapper.getAttribute('data-breakpoint') || '0', 10);
		var items      = wrapper.querySelectorAll('.accordion-item');

		if (!items.length) return;

		// Responsive stacking. Bricks doesn't always set an `id` on the root,
		// but `data-script-id` is reliable when present; fall back to a class
		// scope so the rule still applies if neither is set.
		if (breakpoint > 0) {
			var scriptId = wrapper.getAttribute('data-script-id') || wrapper.id || '';
			var scope = scriptId ? '[data-script-id="' + scriptId + '"]' : '.bricksfly-image-accordion';
			var style = document.createElement('style');
			style.textContent = '@media (max-width: ' + breakpoint + 'px) { ' + scope + '.bricksfly-image-accordion { flex-direction: column !important; } }';
			wrapper.appendChild(style);
		}

		items.forEach(function (item) {
			if (expand === 'click') {
				item.addEventListener('click', function () {
					openAccordion(items, item);
				});
			} else {
				item.addEventListener('mouseenter', function () {
					openAccordion(items, item);
				});
				// Intentionally no per-item mouseleave: the next mouseenter on a
				// sibling switches the active item, and leaving the accordion
				// entirely keeps the last-hovered item open so the panel never
				// collapses to an empty state.
			}
		});
	}

	function scanAll(root) {
		(root || document).querySelectorAll('.bricksfly-image-accordion').forEach(initElement);
	}

	function boot() {
		scanAll(document);

		// Catch builder canvas mounts (childList) plus live edits to our
		// data-* attributes (Bricks updates these in place without
		// replacing the DOM node when only those settings change).
		var mo = new MutationObserver(function (mutations) {
			for (var i = 0; i < mutations.length; i++) {
				var m = mutations[i];

				if (m.type === 'attributes') {
					var target = m.target;
					if (target && target.classList && target.classList.contains('bricksfly-image-accordion')) {
						applyDefaultActive(target);
					}
					continue;
				}

				var added = m.addedNodes;
				for (var j = 0; j < added.length; j++) {
					var node = added[j];
					if (node.nodeType !== 1) continue;
					if (node.classList && node.classList.contains('bricksfly-image-accordion')) {
						initElement(node);
					}
					if (node.querySelectorAll) {
						scanAll(node);
					}
				}
			}
		});
		mo.observe(document.body, {
			childList: true,
			subtree: true,
			attributes: true,
			attributeFilter: ['data-default-active']
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}

	// Bricks $scripts entrypoint: the builder calls this with the element
	// root after each render so listeners reattach to the fresh DOM node.
	window.bricksflyImageAccordion = initElement;
})();
