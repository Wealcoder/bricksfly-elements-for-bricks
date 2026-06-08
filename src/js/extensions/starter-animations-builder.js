/**
 * Bricks Builder iframe bridge for Starter Animations.
 *
 * The Bricks builder renders text widgets (heading, text, text-basic,
 * text-link) via a Vue `<contenteditable>` template that doesn't reliably
 * surface classes our `bricks/element/render_attributes` filter adds.
 * Without those classes, the public starter-animations.js never sees the
 * element and the animation never plays in the builder canvas.
 *
 * This script runs ONLY inside the builder iframe. It reads the per-element
 * attribute map Bricks already ships in `bricksData.loadData.htmlAttributes`
 * (populated server-side from our PHP filter), finds the matching canvas
 * DOM nodes, and copies the `aab-*` classes plus `--aab-*` inline styles
 * onto them. A MutationObserver re-applies after Vue swaps DOM nodes on
 * AJAX re-renders.
 */
(function () {
	'use strict';

	if (typeof window.bricksData === 'undefined') return;

	var loadData = window.bricksData.loadData || {};
	var htmlAttributes = loadData.htmlAttributes || {};

	// Class prefixes / exact names emitted by class-aab-starter-animations.php.
	var OUR_CLASS_RE = /^(aab-starter-animations-|aab-target-self$|aab-reveal-|aab-slide-|aab-flip-axis|aab-char-preset-|aab-repeat-)/;

	function isOurClass(cls) {
		return typeof cls === 'string' && OUR_CLASS_RE.test(cls);
	}

	function findElementNode(uid) {
		// Bricks DOM nodes carry data-script-id == uid in some paths (loops).
		var byScript = document.querySelector('[data-script-id="' + cssEscape(uid) + '"]');
		if (byScript) return byScript;

		// Normal case: root class .brxe-{element_id}. uid may carry an
		// instance suffix for nested components — strip it.
		var elementId = uid.split('-')[0];
		return document.querySelector('.brxe-' + cssEscape(elementId));
	}

	function cssEscape(str) {
		if (window.CSS && typeof window.CSS.escape === 'function') {
			return window.CSS.escape(str);
		}
		return String(str).replace(/[^a-zA-Z0-9_-]/g, '\\$&');
	}

	function getOurStyleString(rootStyle) {
		if (!rootStyle) return '';
		var raw = Array.isArray(rootStyle) ? rootStyle.join(';') : String(rootStyle);
		// Keep only declarations involving --aab-* custom props.
		return raw
			.split(';')
			.map(function (s) { return s.trim(); })
			.filter(function (s) { return s.indexOf('--aab-') === 0; })
			.join('; ');
	}

	function applyEntry(uid, attrs) {
		if (!attrs || !attrs._root) return;
		var rootClasses = Array.isArray(attrs._root.class) ? attrs._root.class : [];
		var ourClasses = rootClasses.filter(isOurClass);
		var ourStyle = getOurStyleString(attrs._root.style);

		if (!ourClasses.length && !ourStyle) return;

		var node = findElementNode(uid);
		if (!node) return;

		ourClasses.forEach(function (cls) {
			if (!node.classList.contains(cls)) {
				node.classList.add(cls);
			}
		});

		if (ourStyle) {
			var existing = node.getAttribute('style') || '';
			if (existing.indexOf(ourStyle) === -1) {
				var sep = existing && existing.charAt(existing.length - 1) !== ';' ? '; ' : ' ';
				node.setAttribute('style', (existing + sep + ourStyle).trim());
			}
		}
	}

	function applyAll() {
		Object.keys(htmlAttributes).forEach(function (uid) {
			applyEntry(uid, htmlAttributes[uid]);
		});
	}

	var rafScheduled = false;
	function scheduleApply() {
		if (rafScheduled) return;
		rafScheduled = true;
		requestAnimationFrame(function () {
			rafScheduled = false;
			applyAll();
		});
	}

	function boot() {
		applyAll();

		var observer = new MutationObserver(function (mutations) {
			for (var i = 0; i < mutations.length; i++) {
				if (mutations[i].addedNodes && mutations[i].addedNodes.length) {
					scheduleApply();
					return;
				}
			}
		});

		observer.observe(document.body, { childList: true, subtree: true });
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
