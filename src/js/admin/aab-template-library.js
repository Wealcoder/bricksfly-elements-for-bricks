import "../../scss/admin/aab-template-library.scss";

/* eslint-disable */
/**
 * Bricks Builder â€” Animation Addons Section Library
 *
 * Ports the Elementor template-library "import section" UX to Bricks:
 *   1. Injects an "Import Section" button into the Bricks main toolbar.
 *   2. Opens a custom modal listing remote templates from
 *      block.animation-addons.com (categories, color filter, search, paging).
 *   3. On Insert, proxies the remote JSON through this site's AJAX,
 *      appends it to `_bricks_page_content_2`, and reloads the builder so
 *      Vue picks up the new elements.
 *
 * Designed to be defensive: Bricks is a Vue.js SPA without a public panel
 * API, so we mount via DOM polling / MutationObserver and never touch
 * Bricks' internal Vuex/store directly.
 *
 * @since 1.0.0
 */
(function ($, window, document) {
	'use strict';

	if (!window.BRICKSFLY_TEMPLATE_LIBRARY) {
		return;
	}

	var CFG = window.BRICKSFLY_TEMPLATE_LIBRARY;
	var I18N = CFG.i18n || {};
	// esc_html__() sends "'" as "&#039;"; show real characters in the UI.
	Object.keys(I18N).forEach(function (k) {
		if (typeof I18N[k] === 'string') { I18N[k] = decodeEntities(I18N[k]); }
	});

	// Browse state.
	var state = {
		categories: [],
		type: 'block',
		category: '',
		colorType: '',
		premium: '', // all sections, free ones listed first
		search: '',
		page: 1,
		loading: false,
		exhausted: false,
		templates: []
	};

	// --- Boot ---------------------------------------------------------

	$(document).ready(function () {
		// Bricks builder boots asynchronously; the toolbar isn't in the
		// DOM at DOMContentLoaded. Poll until the toolbar appears, then
		// inject the button + observe for re-renders.
		mountWhenBuilderReady();
        
	});

	function mountWhenBuilderReady() {
		var attempts = 0;
		var iv = setInterval(function () {
			attempts++;

			if (injectToolbarButton()) {
				clearInterval(iv);
				observeBuilderForReinjection();
				watchCanvasActions();
				return;
			}

			if (attempts > 80) {
				// 80 * 250ms = 20s â€” give up but keep the observer running
				// in case the toolbar appears later (e.g. on tab switch).
				clearInterval(iv);
				observeBuilderForReinjection();
				watchCanvasActions();
			}
		}, 250);
	}

	/**
	 * Inject the "Import Section" button into the Bricks main toolbar.
	 *
	 * Bricks renders multiple toolbars across versions; we try the most
	 * likely host elements in order. Returns true once the button is
	 * placed so the poller can stop.
	 */
	function injectToolbarButton() {
		if (document.getElementById('aab-import-section-button')) {
			return true;
		}

		// Find the undo button so we can drop our control immediately
		// before it. Bricks renders the undo control as a toolbar item
		// whose icon class is `ion-ios-undo` (with a `title="Undo"`
		// tooltip on the wrapping anchor/li). We resolve to the closest
		// `li` so the inserted node lives at the same level in the menu.
		var undoEl = document.querySelector(
			'#bricks-toolbar-top ul.group-wrapper.end > li.undo, #bricks-toolbar .ion-ios-undo, #bricks-toolbar [class*="undo"], #bricks-toolbar [data-balloon="Undo"], #bricks-toolbar [title="Undo"]'
		);
		var undoItem = undoEl ? (undoEl.closest('li') || undoEl.closest('.action') || undoEl) : null;

		// Fall back to the main toolbar menu if the undo button isn't in
		// the DOM yet (Bricks renders the toolbar asynchronously).
		var host = null;
		if (!undoItem) {
			// The right-hand group of the toolbar ends with Save; never append
			// after it, or the button would sit to the right of Save.
			var hosts = [
				'#bricks-toolbar-top ul.group-wrapper.end',
				'#bricks-toolbar ul.menu',
				'#bricks-toolbar',
				'.bricks-toolbar',
				'header#bricks-toolbar'
			];
			for (var i = 0; i < hosts.length; i++) {
				host = document.querySelector(hosts[i]);
				if (host) {
					break;
				}
			}
			if (!host) {
				return false;
			}
		} else {
			host = undoItem.parentNode;
		}

		// Match Bricks' own toolbar buttons: an `<li>` wrapping an `<a>`
		// with the same `action` class. Unlike the surrounding icon-only
		// controls, this one is branded: it shows the BricksFly logo to the
		// left of a visible "Import Section" label so the entry point reads
		// as a first-class, recognisable action rather than a bare glyph.
		var label = I18N.button_label || 'Import Section';
		var logoUrl = I18N.logo_url || (window.BRICKSFLY_TEMPLATE_LIBRARY && window.BRICKSFLY_TEMPLATE_LIBRARY.logo_url) || '';
		var tag = host.tagName === 'UL' ? 'li' : 'div';
		var btn = document.createElement(tag);
		btn.id = 'aab-import-section-button';
		btn.className = 'aab-import-section-button action';
		btn.setAttribute('title', label);
		btn.setAttribute('data-balloon', label);
		btn.setAttribute('data-balloon-pos', 'bottom');

		// The logo is decorative (the adjacent text already names the action),
		// so it's marked aria-hidden and the accessible name comes from the
		// visible label + aria-label on the anchor. Fall back to a "+" glyph
		// only if the logo URL wasn't localized for some reason.
		var mark = logoUrl
			? '<img class="aab-import-section-button__logo" src="' + escapeAttr(logoUrl) + '" alt="" aria-hidden="true" decoding="async" />'
			: '<svg class="aab-import-section-button__icon" viewBox="0 0 24 24" aria-hidden="true">' +
					'<path d="M12 3a1 1 0 0 1 1 1v7h7a1 1 0 1 1 0 2h-7v7a1 1 0 1 1-2 0v-7H4a1 1 0 1 1 0-2h7V4a1 1 0 0 1 1-1z"/>' +
				'</svg>';

		btn.innerHTML =
			'<a href="#" class="aab-import-section-button__inner" aria-label="' + escapeAttr(label) + '">' +
				mark +
				'<span class="aab-import-section-button__label">' + escapeHtml(label) + '</span>' +
			'</a>';

		if (undoItem && undoItem.parentNode === host) {
			host.insertBefore(btn, undoItem);
		} else {
			host.appendChild(btn);
		}

		btn.addEventListener('click', function (e) {
			e.preventDefault();
			startImport(btn);
		});

		return true;
	}

	/**
	 * Entry point of the Import Section buttons (toolbar and canvas).
	 *
	 * With BricksFly Pro, a section copied on the BricksFly site (Live Copy) may be
	 * waiting. Then there are two sources, so the user picks one: A. Cloud (the
	 * library) or B. Live Paste. Without Pro, or with nothing copied, the library
	 * opens straight away, as before. Pro answers from a background check, so the
	 * click does not wait for the network.
	 */
	var importChecking = false;

	function startImport(trigger) {
		var api = window.BRICKSFLY_LIVE_PASTE_API;
		if (!api || typeof api.enabled !== 'function' || typeof api.peek !== 'function' || !api.enabled()) {
			openLibraryModal();
			return;
		}

		if (importChecking) {
			return;
		}
		importChecking = true;
		// Visible feedback while we ask the bridge (usually well under two seconds).
		if (trigger && trigger.style) { trigger.style.cursor = 'progress'; trigger.style.opacity = '0.6'; }

		api.peek(function (res) {
			importChecking = false;
			if (trigger && trigger.style) { trigger.style.cursor = ''; trigger.style.opacity = ''; }

			if (res && res.found && res.payload) {
				showSourceChooser(api, res);
			} else {
				openLibraryModal();
			}
		}, 2500);
	}

	function showSourceChooser(api, res) {
		var old = document.getElementById('aab-tl-source');
		if (old) { old.parentNode.removeChild(old); }

		var count = parseInt(res.count, 10) || 0;
		var liveDesc = (count === 1 ? (I18N.source_live_desc_one || 'Paste the section you copied with Live Copy (1 element).') : (I18N.source_live_desc || 'Paste the section you copied with Live Copy (%d elements).')).replace('%d', String(count));
		var shortcut = api.shortcut ? '<kbd class="aab-tl-source__kbd">' + escapeHtml(api.shortcut) + '</kbd>' : '';
		var previous = document.activeElement;

		var box = document.createElement('div');
		box.id = 'aab-tl-source';
		box.className = 'aab-tl-source';
		box.setAttribute('role', 'dialog');
		box.setAttribute('aria-modal', 'true');
		box.setAttribute('aria-labelledby', 'aab-tl-source-title');
		box.innerHTML =
			'<div class="aab-tl-source__backdrop"></div>' +
			'<div class="aab-tl-source__dialog">' +
				'<h2 id="aab-tl-source-title" class="aab-tl-source__title">' + escapeHtml(I18N.source_title || 'Import Section') + '</h2>' +
				'<p class="aab-tl-source__sub">' + escapeHtml(I18N.source_sub || 'Where do you want to import from?') + '</p>' +
				'<button type="button" class="aab-tl-source__card" data-source="cloud">' +
					'<span class="aab-tl-source__key" aria-hidden="true">A</span>' +
					'<span class="aab-tl-source__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M7 18a4 4 0 0 1-.6-7.96A5.5 5.5 0 0 1 17 9.5 4 4 0 0 1 17 18H7z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg></span>' +
					'<span class="aab-tl-source__text"><strong>' + escapeHtml(I18N.source_cloud || 'Cloud') + '</strong><small>' + escapeHtml(I18N.source_cloud_desc || 'Browse ready-made sections from the BricksFly library.') + '</small></span>' +
				'</button>' +
				'<button type="button" class="aab-tl-source__card" data-source="live">' +
					'<span class="aab-tl-source__key" aria-hidden="true">B</span>' +
					'<span class="aab-tl-source__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M13 3 5 13.5h6L10 21l8-10.5h-6L13 3z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg></span>' +
					'<span class="aab-tl-source__text"><strong>' + escapeHtml(I18N.source_live || 'Live Paste') + '</strong><small>' + escapeHtml(liveDesc) + '</small></span>' +
					shortcut +
				'</button>' +
				'<button type="button" class="aab-tl-source__cancel">' + escapeHtml(I18N.cancel || 'Cancel') + '</button>' +
			'</div>';
		document.body.appendChild(box);

		function close() {
			document.removeEventListener('keydown', onKey, true);
			if (box.parentNode) { box.parentNode.removeChild(box); }
			if (previous && previous.focus) { try { previous.focus(); } catch (e) { } }
		}

		function choose(source) {
			close();
			if (source === 'live') {
				api.paste(res.payload);
			} else {
				// Cloud picked: this copy is not offered again until something new is copied.
				if (typeof api.dismiss === 'function') { api.dismiss(res.payload); }
				openLibraryModal();
			}
		}

		function onKey(e) {
			var key = String(e.key || '').toLowerCase();
			if (key === 'escape') {
				e.preventDefault();
				e.stopPropagation();
				close();
			} else if (key === 'a' || key === '1') {
				e.preventDefault();
				e.stopPropagation();
				choose('cloud');
			} else if (key === 'b' || key === '2') {
				e.preventDefault();
				e.stopPropagation();
				choose('live');
			} else if (key === 'tab') {
				var items = box.querySelectorAll('button');
				var first = items[0];
				var last = items[items.length - 1];
				if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
				else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
			}
		}

		document.addEventListener('keydown', onKey, true);
		box.querySelector('.aab-tl-source__backdrop').addEventListener('click', close);
		box.querySelector('.aab-tl-source__cancel').addEventListener('click', close);
		Array.prototype.forEach.call(box.querySelectorAll('.aab-tl-source__card'), function (card) {
			card.addEventListener('click', function () { choose(card.getAttribute('data-source')); });
		});

		var firstCard = box.querySelector('.aab-tl-source__card');
		if (firstCard) { firstCard.focus(); }
	}

	/**
	 * Canvas "Import" button. Bricks shows a small action bar (+ Insert, layout,
	 * width, height ...) for the selected container / block / section, inside the
	 * preview iframe. We add the BricksFly logo as another action there, before
	 * "+ Insert". The paste then goes relative to that selected element.
	 *
	 * Bricks renders the bar with Vue and rebuilds it whenever the selection
	 * changes, so the button is added again by a MutationObserver. The iframe
	 * document has none of our CSS, hence the small inline stylesheet.
	 */
	var canvasDoc = null;
	var canvasObserver = null;

	function getCanvasDoc() {
		var frame = document.getElementById('bricks-builder-iframe');
		try {
			return frame && frame.contentDocument && frame.contentDocument.body ? frame.contentDocument : null;
		} catch (e) {
			return null;
		}
	}

	// One action item for either canvas bar. `kind` is "bar" (the selected
	// element's actions, labelled with title) or "blank" (the empty-page actions,
	// labelled with data-balloon like Bricks' own tiles there).
	function buildCanvasItem(doc, kind) {
		var label = I18N.button_label || 'Import Section';
		var logoUrl = I18N.logo_url || (window.BRICKSFLY_TEMPLATE_LIBRARY && window.BRICKSFLY_TEMPLATE_LIBRARY.logo_url) || '';
		var item = doc.createElement(kind === 'blank' ? 'span' : 'li');
		item.className = 'action aab-canvas-import';
		if (kind === 'blank') {
			item.setAttribute('data-balloon', label);
		} else {
			item.setAttribute('title', label);
		}
		item.setAttribute('aria-label', label);
		item.setAttribute('role', 'button');
		item.setAttribute('tabindex', '0');
		item.innerHTML = logoUrl
			? '<img src="' + escapeAttr(logoUrl) + '" alt="" aria-hidden="true" draggable="false" />'
			: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 18a4 4 0 0 1-.6-7.96A5.5 5.5 0 0 1 17 9.5 4 4 0 0 1 17 18H7z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>';

		function open(e) {
			e.preventDefault();
			e.stopPropagation();
			startImport(item);
		}
		item.addEventListener('click', open);
		item.addEventListener('keydown', function (e) {
			if (e.key === 'Enter' || e.key === ' ') { open(e); }
		});

		return item;
	}

	function addCanvasButton(doc) {
		// 1) Actions of the selected element: before "+ Insert". Only on elements
		// that can hold a section (the ones that have "+ Insert").
		var bar = doc.getElementById('bricks-preview-element-actions');
		if (bar && !bar.querySelector('.aab-canvas-import')) {
			var insertItem = bar.querySelector('li.action.add');
			if (insertItem) {
				bar.insertBefore(buildCanvasItem(doc, 'bar'), insertItem);
			}
		}

		// 2) Empty page: the row of tiles (Section, Layout, Templates, Help).
		var blank = doc.querySelector('#bricks-blank-canvas .actions');
		if (blank && !blank.querySelector('.aab-canvas-import')) {
			blank.insertBefore(buildCanvasItem(doc, 'blank'), blank.firstChild);
		}
	}

	function prepareCanvas(doc) {
		if (!doc.getElementById('aab-canvas-style')) {
			var style = doc.createElement('style');
			style.id = 'aab-canvas-style';
			style.textContent =
				'#bricks-preview-element-actions li.aab-canvas-import{align-items:center;justify-content:center;cursor:pointer}' +
				'#bricks-preview-element-actions li.aab-canvas-import img{display:block;width:62%;height:62%;object-fit:contain;pointer-events:none}' +
				'#bricks-preview-element-actions li.aab-canvas-import svg{display:block;width:62%;height:62%;pointer-events:none}' +
				'#bricks-blank-canvas .actions .aab-canvas-import{display:inline-flex;align-items:center;justify-content:center;cursor:pointer}' +
				'#bricks-blank-canvas .actions .aab-canvas-import img{display:block;width:24px;height:24px;object-fit:contain;pointer-events:none}' +
				'#bricks-blank-canvas .actions .aab-canvas-import svg{display:block;width:22px;height:22px;pointer-events:none}';
			doc.head.appendChild(style);
		}
		addCanvasButton(doc);
	}

	function watchCanvasActions() {
		var tick = function () {
			var doc = getCanvasDoc();
			if (!doc) {
				return;
			}

			if (doc !== canvasDoc) {
				// New iframe document (first load or a canvas reload).
				if (canvasObserver) { canvasObserver.disconnect(); }
				canvasDoc = doc;
				prepareCanvas(doc);
				var pending = false;
				canvasObserver = new MutationObserver(function () {
					if (pending) { return; }
					pending = true;
					window.requestAnimationFrame(function () {
						pending = false;
						if (canvasDoc) { prepareCanvas(canvasDoc); }
					});
				});
				canvasObserver.observe(doc.body, { childList: true, subtree: true });
			}
		};

		tick();
		// The iframe document is replaced when Bricks reloads the canvas.
		setInterval(tick, 1000);
	}

	/**
	 * Bricks re-renders parts of the toolbar on view changes (e.g. switching
	 * structure / settings panel). If our button gets nuked, re-inject.
	 */
	function observeBuilderForReinjection() {
		var rootCandidates = [
			document.getElementById('bricks-builder'),
			document.getElementById('bricks-toolbar'),
			document.body
		];
		var root = rootCandidates.find(Boolean);
		if (!root) {
			return;
		}

		var debounced;
		var observer = new MutationObserver(function () {
			clearTimeout(debounced);
			debounced = setTimeout(function () {
				if (!document.getElementById('aab-import-section-button')) {
					injectToolbarButton();
				}
			}, 150);
		});

		observer.observe(root, { childList: true, subtree: true });
	}

	// --- Modal --------------------------------------------------------

	function openLibraryModal() {
		if (document.getElementById('aab-tl-modal')) {
			document.getElementById('aab-tl-modal').classList.add('aab-tl-modal--visible');
			return;
		}

		var modal = document.createElement('div');
		modal.id = 'aab-tl-modal';
		modal.className = 'aab-tl-modal aab-tl-modal--visible';
		modal.innerHTML = renderModalShell();
		document.body.appendChild(modal);

		bindModalEvents(modal);
		preloadCategories().then(function () {
			renderCategoryDropdown(modal);
			fetchTemplates({ reset: true });
		});
	}

	/**
	 * Full-screen preview of a section's demo page inside the library.
	 * Header: Back to library, section name, previous / next section, device
	 * switcher (desktop / tablet / mobile), open in a new tab and the same
	 * Insert / Pro action as the card. Arrow keys move between sections.
	 */
	var PREVIEW_DEVICES = { desktop: '100%', tablet: '820px', mobile: '390px' };
	var previewState = { index: -1, device: 'desktop', timer: 0 };

	function previewCards() {
		return Array.prototype.slice.call(document.querySelectorAll('#aab-tl-grid .aab-tl-card')).filter(function (c) {
			return c.getAttribute('data-demo');
		});
	}

	function closePreview() {
		var box = document.getElementById('aab-tl-preview');
		if (box && box.parentNode) { box.parentNode.removeChild(box); }
		clearTimeout(previewState.timer);
		document.removeEventListener('keydown', previewKeys, true);
	}

	function previewKeys(ev) {
		if (!document.getElementById('aab-tl-preview') || document.getElementById('aab-tl-options') || document.querySelector('.aab-tl-busy')) { return; }
		if (ev.key === 'Escape') {
			ev.stopPropagation();
			ev.preventDefault();
			closePreview();
		} else if (ev.key === 'ArrowLeft' || ev.key === 'ArrowRight') {
			ev.preventDefault();
			stepPreview(ev.key === 'ArrowRight' ? 1 : -1);
		}
	}

	function stepPreview(dir) {
		var cards = previewCards();
		var next = previewState.index + dir;
		if (next < 0 || next >= cards.length) { return; }
		showPreview(cards[next]);
	}

	function setPreviewDevice(box, device) {
		previewState.device = PREVIEW_DEVICES[device] ? device : 'desktop';
		var holder = box.querySelector('.aab-tl-preview__device');
		holder.style.width = PREVIEW_DEVICES[previewState.device];
		holder.classList.toggle('is-framed', previewState.device !== 'desktop');
		box.querySelectorAll('.aab-tl-preview__dev').forEach(function (btn) {
			var on = btn.getAttribute('data-device') === previewState.device;
			btn.classList.toggle('is-active', on);
			btn.setAttribute('aria-pressed', on ? 'true' : 'false');
		});
	}

	function showPreview(card) {
		var box = document.getElementById('aab-tl-preview');
		var url = card && card.getAttribute('data-demo');
		if (!box || !url) { return; }
		var cards = previewCards();
		previewState.index = cards.indexOf(card);

		// More sections load as the user nears the end of the list.
		if (previewState.index >= cards.length - 3 && !state.exhausted && !state.loading) {
			fetchTemplates({ reset: false });
		}

		var titleEl = card.querySelector('.aab-tl-card__title');
		var title = titleEl ? titleEl.textContent : '';
		box.querySelector('.aab-tl-preview__title').textContent = title;
		box.querySelector('.aab-tl-preview__count').textContent = (previewState.index + 1) + ' / ' + cards.length + (state.exhausted ? '' : '+');
		box.querySelector('.aab-tl-preview__prev').disabled = previewState.index <= 0;
		box.querySelector('.aab-tl-preview__next').disabled = previewState.index >= cards.length - 1;
		box.querySelector('.aab-tl-preview__external').setAttribute('href', url);
		box.querySelector('.aab-tl-preview__slow-link').setAttribute('href', url);

		var insertBtn = card.querySelector('.aab-tl-card__insert');
		var proLink = card.querySelector('.aab-tl-card__pro');
		box.querySelector('.aab-tl-preview__action').innerHTML = insertBtn
			? '<button type="button" class="aab-tl-card__insert aab-tl-preview__insert"><span class="aab-tl-card__insert-icon" aria-hidden="true">+</span>' + escapeHtml(I18N.insert || 'Insert') + '</button>'
			: (proLink ? '<a class="aab-tl-card__pro aab-tl-preview__pro" href="' + escapeAttr(proLink.getAttribute('href') || '') + '" target="_blank" rel="noopener">' + escapeHtml(proLink.textContent) + '</a>' : '');
		var ins = box.querySelector('.aab-tl-preview__insert');
		if (ins) {
			ins.addEventListener('click', function (e) {
				e.preventDefault();
				openInsertOptions(card, card.querySelector('.aab-tl-card__insert') || ins);
			});
		}

		// Load the demo; show a spinner, and a hint if it is slow or blocked.
		var frameWrap = box.querySelector('.aab-tl-preview__frame');
		frameWrap.classList.add('is-loading');
		frameWrap.classList.remove('is-slow');
		clearTimeout(previewState.timer);
		var old = box.querySelector('.aab-tl-preview__device iframe');
		if (old && old.parentNode) { old.parentNode.removeChild(old); }
		var frame = document.createElement('iframe');
		frame.setAttribute('title', title);
		frame.setAttribute('referrerpolicy', 'no-referrer');
		frame.addEventListener('load', function () {
			clearTimeout(previewState.timer);
			frameWrap.classList.remove('is-loading', 'is-slow');
		});
		frame.src = url;
		box.querySelector('.aab-tl-preview__device').appendChild(frame);
		previewState.timer = setTimeout(function () { frameWrap.classList.add('is-slow'); }, 10000);
	}

	function openPreview(card) {
		if (!card || !card.getAttribute('data-demo')) { return; }
		closePreview();
		var box = document.createElement('div');
		box.id = 'aab-tl-preview';
		box.className = 'aab-tl-preview';
		var devIcons = {
			desktop: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 5h18v11H3zM8 20h8M12 16v4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
			tablet: '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="3" width="14" height="18" rx="2" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M11 18h2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
			mobile: '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="7" y="3" width="10" height="18" rx="2" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M11 18h2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>'
		};
		var devLabels = { desktop: I18N.dev_desktop || 'Desktop', tablet: I18N.dev_tablet || 'Tablet', mobile: I18N.dev_mobile || 'Mobile' };
		var devButtons = Object.keys(PREVIEW_DEVICES).map(function (d) {
			return '<button type="button" class="aab-tl-preview__dev" data-device="' + d + '" title="' + escapeAttr(devLabels[d]) + '" aria-label="' + escapeAttr(devLabels[d]) + '">' + devIcons[d] + '</button>';
		}).join('');
		box.innerHTML =
			'<div class="aab-tl-preview__bar">' +
				'<button type="button" class="aab-tl-preview__back"><span aria-hidden="true">&larr;</span> ' + escapeHtml(I18N.back_to_library || 'Back to library') + '</button>' +
				'<span class="aab-tl-preview__title"></span>' +
				'<span class="aab-tl-preview__nav">' +
					'<button type="button" class="aab-tl-preview__step aab-tl-preview__prev" aria-label="' + escapeAttr(I18N.prev_section || 'Previous section') + '" title="' + escapeAttr(I18N.prev_section || 'Previous section') + '"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 5l-7 7 7 7" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>' +
					'<span class="aab-tl-preview__count"></span>' +
					'<button type="button" class="aab-tl-preview__step aab-tl-preview__next" aria-label="' + escapeAttr(I18N.next_section || 'Next section') + '" title="' + escapeAttr(I18N.next_section || 'Next section') + '"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5l7 7-7 7" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>' +
				'</span>' +
				'<span class="aab-tl-preview__devices" role="group">' + devButtons + '</span>' +
				'<span class="aab-tl-preview__actions">' +
					'<a class="aab-tl-preview__external" href="#" target="_blank" rel="noopener noreferrer">' + escapeHtml(I18N.open_new_tab || 'Open in new tab') + ' &#8599;</a>' +
					'<span class="aab-tl-preview__action"></span>' +
				'</span>' +
			'</div>' +
			'<div class="aab-tl-preview__frame">' +
				'<span class="aab-tl-spinner"></span>' +
				'<div class="aab-tl-preview__slow">' + escapeHtml(I18N.preview_slow || 'Taking long? Some sites block previews.') + ' <a class="aab-tl-preview__slow-link" href="#" target="_blank" rel="noopener noreferrer">' + escapeHtml(I18N.open_new_tab || 'Open in new tab') + '</a></div>' +
				'<div class="aab-tl-preview__device"></div>' +
			'</div>';
		document.body.appendChild(box);

		box.querySelector('.aab-tl-preview__back').addEventListener('click', closePreview);
		box.querySelector('.aab-tl-preview__prev').addEventListener('click', function () { stepPreview(-1); });
		box.querySelector('.aab-tl-preview__next').addEventListener('click', function () { stepPreview(1); });
		box.querySelectorAll('.aab-tl-preview__dev').forEach(function (btn) {
			btn.addEventListener('click', function () { setPreviewDevice(box, btn.getAttribute('data-device')); });
		});
		document.addEventListener('keydown', previewKeys, true);
		setPreviewDevice(box, previewState.device);
		showPreview(card);
		box.querySelector('.aab-tl-preview__back').focus();
	}

	function closeLibraryModal() {
		closePreview();
		var modal = document.getElementById('aab-tl-modal');
		if (modal) {
			modal.classList.remove('aab-tl-modal--visible');
			setTimeout(function () {
				if (modal && modal.parentNode) {
					modal.parentNode.removeChild(modal);
				}
			}, 200);
		}
	}

	function renderModalShell() {
		var typeTabs = '';
		var types = CFG.template_types || {};
		var first = true;
		Object.keys(types).forEach(function (key) {
			typeTabs +=
				'<button type="button" class="aab-tl-tab' +
					(first ? ' is-active' : '') +
					'" data-type="' + escapeAttr(key) + '">' +
					escapeHtml(types[key].label || key) +
				'</button>';
			first = false;
		});

		return (
			'<div class="aab-tl-modal__backdrop"></div>' +
			'<div class="aab-tl-modal__dialog" role="dialog" aria-modal="true" aria-label="' + escapeAttr(I18N.modal_title || 'Section Library') + '">' +
				'<div class="aab-tl-modal__header">' +
					'<div class="aab-tl-modal__title">' + escapeHtml(I18N.modal_title || 'Section Library') + '</div>' +
					'<div class="aab-tl-modal__tabs">' + typeTabs + '</div>' +
					'<button type="button" class="aab-tl-modal__close" aria-label="' + escapeAttr(I18N.close || 'Close') + '">&times;</button>' +
				'</div>' +
				'<div class="aab-tl-modal__filters">' +
					'<div class="aab-tl-filter">' +
						'<select id="aab-tl-filter-category"><option value="">' + escapeHtml(I18N.category || 'Category') + '</option></select>' +
					'</div>' +
					'<div class="aab-tl-filter">' +
						'<select id="aab-tl-filter-color">' +
							'<option value="">' + escapeHtml(I18N.all_colors || 'All') + '</option>' +
							'<option value="lite">' + escapeHtml(I18N.light || 'Light') + '</option>' +
							'<option value="dark">' + escapeHtml(I18N.dark || 'Dark') + '</option>' +
						'</select>' +
					'</div>' +
					'<div class="aab-tl-filter">' +
						'<select id="aab-tl-filter-mode">' +
							'<option value="" selected>' + escapeHtml(I18N.all_items || 'All') + '</option>' +
							'<option value="no">' + escapeHtml(I18N.free || 'Free') + '</option>' +
							'<option value="yes">' + escapeHtml(I18N.pro || 'Pro') + '</option>' +
						'</select>' +
					'</div>' +
					'<div class="aab-tl-search">' +
						'<input type="search" id="aab-tl-search" placeholder="' + escapeAttr(I18N.search || 'Search') + '">' +
					'</div>' +
				'</div>' +
				'<div class="aab-tl-modal__body">' +
					'<div class="aab-tl-grid" id="aab-tl-grid"></div>' +
					'<div class="aab-tl-loadmore" id="aab-tl-loadmore"></div>' +
				'</div>' +
				'<div class="aab-tl-modal__loading" hidden>' +
					'<span class="aab-tl-spinner"></span>' +
					'<span class="aab-tl-loading-label">' + escapeHtml(I18N.loading || 'Loading') + '</span>' +
				'</div>' +
			'</div>'
		);
	}

	function bindModalEvents(modal) {
		modal.querySelector('.aab-tl-modal__close').addEventListener('click', closeLibraryModal);
		modal.querySelector('.aab-tl-modal__backdrop').addEventListener('click', closeLibraryModal);

		// Tabs (template type).
		modal.querySelectorAll('.aab-tl-tab').forEach(function (tab) {
			tab.addEventListener('click', function () {
				modal.querySelectorAll('.aab-tl-tab').forEach(function (t) {
					t.classList.remove('is-active');
				});
				tab.classList.add('is-active');
				state.type = tab.dataset.type;
				fetchTemplates({ reset: true });
			});
		});

		// Category change.
		modal.querySelector('#aab-tl-filter-category').addEventListener('change', function (e) {
			state.category = e.target.value;
			fetchTemplates({ reset: true });
		});

		// Color change.
		modal.querySelector('#aab-tl-filter-color').addEventListener('change', function (e) {
			state.colorType = e.target.value;
			fetchTemplates({ reset: true });
		});

		// Free / Pro change.
		modal.querySelector('#aab-tl-filter-mode').value = state.premium;
		modal.querySelector('#aab-tl-filter-mode').addEventListener('change', function (e) {
			state.premium = e.target.value;
			fetchTemplates({ reset: true });
		});

		// Search (debounced).
		var searchTimer;
		modal.querySelector('#aab-tl-search').addEventListener('input', function (e) {
			clearTimeout(searchTimer);
			var value = e.target.value;
			searchTimer = setTimeout(function () {
				state.search = value;
				fetchTemplates({ reset: true });
			}, 350);
		});

		// Infinite scroll on the body.
		var body = modal.querySelector('.aab-tl-modal__body');
		body.addEventListener('scroll', function () {
			if (state.loading || state.exhausted) {
				return;
			}
			if (body.scrollTop + body.clientHeight >= body.scrollHeight - 200) {
				fetchTemplates({ reset: false });
			}
		});

		// Grid click delegation â€” handle Insert.
		modal.querySelector('#aab-tl-grid').addEventListener('click', function (e) {
			// Preview opens the demo inside the library (plain clicks only, so
			// "open in new tab" shortcuts keep working).
			var previewEl = e.target.closest('.aab-tl-card__preview');
			if (previewEl && !(e.ctrlKey || e.metaKey || e.shiftKey || e.button)) {
				e.preventDefault();
				openPreview(previewEl.closest('.aab-tl-card'));
				return;
			}
			var insertEl = e.target.closest('.aab-tl-card__insert');
			if (!insertEl) {
				return;
			}
			e.preventDefault();
			var card = insertEl.closest('.aab-tl-card');
			if (!card) {
				return;
			}
			openInsertOptions(card, insertEl);
		});

		// Esc closes the modal.
		document.addEventListener('keydown', escListener);

		function escListener(ev) {
			if (ev.key === 'Escape') {
				closeLibraryModal();
				document.removeEventListener('keydown', escListener);
			}
		}
	}

	// --- Data fetch ---------------------------------------------------

	/**
	 * themecrowdy categories endpoint returns a flat array of
	 *   { id, title, slug, count?, â€¦ }
	 * (older Elementor API used `name`; we accept both).
	 */
	function preloadCategories() {
		if (state.categories && state.categories.length) {
			return Promise.resolve(state.categories);
		}
		return fetch(CFG.remote_category, { credentials: 'omit' })
			.then(function (r) { return r.json(); })
			.then(function (cats) {
				state.categories = Array.isArray(cats) ? cats : [];
				return state.categories;
			})
			.catch(function () {
				state.categories = [];
				return state.categories;
			});
	}

	function renderCategoryDropdown(modal) {
		var select = modal.querySelector('#aab-tl-filter-category');
		if (!select) {
			return;
		}
		state.categories.forEach(function (cat) {
			var opt = document.createElement('option');
			// API returns numeric `id`; we filter by slug because the new
			// endpoint indexes by taxonomy slug. Fall back to id if slug
			// is missing.
			opt.value = String(cat.slug || cat.id);
			opt.textContent = decodeEntities(cat.title || cat.name || cat.slug);
			select.appendChild(opt);
		});
	}

	function fetchTemplates(opts) {
		var modal = document.getElementById('aab-tl-modal');
		if (!modal) {
			return;
		}

		var reset = !!(opts && opts.reset);
		if (reset) {
			state.page = 1;
			state.exhausted = false;
			state.templates = [];
			modal.querySelector('#aab-tl-grid').innerHTML = '';
		}

		// Every load gets a ticket. A slower, older request (e.g. before the user
		// changed a filter) must neither add cards nor clear the loading state.
		if (state.controller && typeof state.controller.abort === 'function') {
			try { state.controller.abort(); } catch (e) {}
		}
		state.controller = (typeof AbortController === 'function') ? new AbortController() : null;
		state.ticket = (state.ticket || 0) + 1;
		var ticket = state.ticket;

		state.loading = true;
		toggleLoading(modal, true);

		// themecrowdy bricks-sections endpoint accepts:
		//   page, per_page, type (block|page), category (slug), s (search)
		var url = new URL(CFG.remote_api);
		var type = state.type || CFG.default_type || 'block';
		url.searchParams.set('page', state.page);
		url.searchParams.set('per_page', 24);
		url.searchParams.set('type', type);
		if (state.category) {
			// The library reads `cat`; `category` is kept for older servers.
			url.searchParams.set('cat', state.category);
			url.searchParams.set('category', state.category);
		}
		if (state.premium) {
			url.searchParams.set('premium', state.premium);
		}
		// No free_first: the library then returns the newest sections first
		// (Free and Pro mixed), so new additions show on top.
		if (state.colorType) {
			url.searchParams.set('color_type', state.colorType);
		}
		if (state.search) {
			url.searchParams.set('s', state.search);
		}
     
		fetch(url.toString(), state.controller ? { credentials: 'omit', signal: state.controller.signal } : { credentials: 'omit' })
			.then(function (r) { return r.json(); })
			.then(function (data) {
				if (ticket !== state.ticket) { return; }
				// New shape: response is a flat array. Old shape: { templates: [...] }.
	
				var list = Array.isArray(data) ? data : (data && data.sections) || [];
				if (!list.length) {
					state.exhausted = true;
					if (reset) {
						renderEmpty(modal);
					}
				} else {
					list = validateTemplates(list);
					state.templates = state.templates.concat(list);
				
					appendTemplates(modal, list);
					state.page += 1;
				}
			})
			.catch(function (err) {
				if (window.console && err && err.name !== 'AbortError') { console.error('BricksFly library:', err); }
				if (ticket === state.ticket && reset) {
					renderError(modal);
				}
			})
			.finally(function () {
				if (ticket !== state.ticket) { return; }
				state.loading = false;
				toggleLoading(modal, false);
			});
	}

	function isProItem(item) {
		return item.is_pro === true || String(item.is_pro) === '1';
	}

	/**
	 * Free sections always get an Insert button. Pro sections get a "Pro"
	 * badge and a link to BricksFly Pro, unless the separate BricksFly Pro
	 * plugin is active (it sets `pro_library`). Either way the template
	 * library decides whether it releases a section when Insert is clicked.
	 */
	function validateTemplates(list) {
		return list.map(function (item) {
			item.valid = (!isProItem(item) || CFG.pro_library) ? 'yes' : 'no';
			return item;
		});
	}

	function appendTemplates(modal, templates) {
		var grid = modal.querySelector('#aab-tl-grid');
		var html = templates.map(renderTemplateCard).join('');
		grid.insertAdjacentHTML('beforeend', html);
		
	}

	/**
	 * themecrowdy item shape:
	 *   { id, slug, title, date, thumbnail (direct URL), demo_url,
	 *     type ('block'|'page'), is_pro (bool),
	 *     json_file: { id, url },
	 *     categories: [{ id, title, slug }] }
	 *
	 * Legacy fields (`preview.url`, `json_url`, `template_demo_url`) are
	 * still accepted as a fallback so a partial mid-migration response
	 * stays renderable.
	 */
	function renderTemplateCard(item) {
		var preview = item.thumbnail
			|| (item.preview && item.preview.url)
			|| '';
		var title   = decodeEntities(item.title || '');
		var demoUrl = item.demo_url || item.template_demo_url || '';

		var actionBtn = '';
		if (item.valid === 'yes') {
			actionBtn =
				'<button type="button" class="aab-tl-card__insert">' +
					'<span class="aab-tl-card__insert-icon" aria-hidden="true">+</span>' +
					escapeHtml(I18N.insert || 'Insert') +
				'</button>';
		} else {
			// Get / activate BricksFly Pro, or Pro's own action (see
			// bricksfly_pro_action()). Opens in a new tab so the editor stays open.
			var proAction = CFG.pro_action || {};
			actionBtn =
				'<a class="aab-tl-card__pro" href="' + escapeAttr(proAction.url || CFG.pro_url || 'https://bricksfly.com/pricing/') + '" target="_blank" rel="noopener">' +
					escapeHtml(proAction.label || I18N.get_pro || 'Get BricksFly Pro') +
				'</a>';
		}

		var badge = isProItem(item)
			? '<span class="aab-tl-card__badge aab-tl-card__badge--pro">' + escapeHtml(I18N.pro || 'Pro') + '</span>'
			: '<span class="aab-tl-card__badge aab-tl-card__badge--free">' + escapeHtml(I18N.free || 'Free') + '</span>';

		// Live preview link â€” only when the section provides a `demo_url`.
		// Opens the demo in a new tab; overlaid on the thumbnail so it never
		// competes with the footer's insert/upsell action.
		var previewLabel = I18N.preview || 'Preview';
		var previewLink = demoUrl
			? '<a class="aab-tl-card__preview" href="' + escapeAttr(demoUrl) + '" ' +
					'target="_blank" rel="noopener noreferrer" ' +
					'title="' + escapeAttr(previewLabel) + '" ' +
					'aria-label="' + escapeAttr(previewLabel + ': ' + title) + '">' +
					'<svg class="aab-tl-card__preview-icon" viewBox="0 0 24 24" aria-hidden="true">' +
						'<path d="M12 5c-5 0-9 4.5-10 7 1 2.5 5 7 10 7s9-4.5 10-7c-1-2.5-5-7-10-7zm0 11a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-2a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>' +
					'</svg>' +
					'<span class="aab-tl-card__preview-label">' + escapeHtml(previewLabel) + '</span>' +
				'</a>'
			: '';

		return (
			'<div class="aab-tl-card" ' +
				'data-id="' + escapeAttr(item.id) + '" ' +
				'data-demo="' + escapeAttr(demoUrl) + '">' +
				'<div class="aab-tl-card__thumb">' +
					(preview ? '<img loading="lazy" src="' + escapeAttr(preview) + '" alt="' + escapeAttr(title) + '">' : '') +
					previewLink +
					badge +
				'</div>' +
				'<div class="aab-tl-card__footer">' +
					'<p class="aab-tl-card__title">' + escapeHtml(title) + '</p>' +
					actionBtn +
				'</div>' +
			'</div>'
		);
	}

	function renderEmpty(modal) {
		modal.querySelector('#aab-tl-grid').innerHTML =
			'<div class="aab-tl-empty">' + escapeHtml(I18N.empty || 'No templates found.') + '</div>';
	}

	function renderError(modal) {
		modal.querySelector('#aab-tl-grid').innerHTML =
			'<div class="aab-tl-empty aab-tl-empty--error">' + escapeHtml(I18N.fetch_failed || 'Failed to load.') + '</div>';
	}

	function toggleLoading(modal, isLoading) {
		var loader = modal.querySelector('.aab-tl-modal__loading');
		if (!loader) {
			return;
		}
		if (isLoading) {
			loader.removeAttribute('hidden');
		} else {
			loader.setAttribute('hidden', '');
		}
	}

	// --- Insert -------------------------------------------------------

	/**
	 * Insert a template as a PREVIEW-BEFORE-COMMIT change.
	 *
	 * The server resolves the section (elements + global classes/variables) but
	 * does NOT save or reload. We feed the result into Bricks' OWN paste pipeline
	 * so the section lands in the live in-memory canvas as an unsaved, undoable
	 * change â€” exactly like a manual paste. Nothing is written to the document
	 * until the user clicks Bricks' Save button. Selecting another template just
	 * pastes again (each paste is its own undo step), and undo/redo + existing
	 * unsaved edits are preserved because we never touch the DB or reload.
	 */
	// --- Insert options popup -----------------------------------------

	var OPTIONS_KEY = 'bricksflyInsertOptions.v2';

	function loadInsertOptions() {
		var saved = {};
		try { saved = JSON.parse(window.localStorage.getItem(OPTIONS_KEY) || '{}') || {}; } catch (e) {}
		return {
			import_images: saved.import_images === true,
			import_classes: saved.import_classes !== false
		};
	}

	function saveInsertOptions(opts) {
		try { window.localStorage.setItem(OPTIONS_KEY, JSON.stringify(opts)); } catch (e) {}
	}

	function closeInsertOptions() {
		var el = document.getElementById('aab-tl-options');
		if (el && el.parentNode) {
			el.parentNode.removeChild(el);
		}
	}

	/**
	 * Clicking Insert opens a small settings popup first. The chosen options are
	 * remembered and sent with the insert request.
	 */
	function openInsertOptions(card, btn) {
		closeInsertOptions();

		var opts = loadInsertOptions();
		// Open the settings by default only when the user changed them before.
		var isCustom = opts.import_images || !opts.import_classes;
		var titleEl = card.querySelector('.aab-tl-card__title');
		var title = titleEl ? titleEl.textContent : '';

		// One radio card: a plain-language name and what it does.
		function optCard(group, value, label, desc, checked) {
			return (
				'<label class="aab-tl-opt' + (checked ? ' is-selected' : '') + '">' +
					'<input type="radio" name="' + group + '" value="' + value + '"' + (checked ? ' checked' : '') + '>' +
					'<span class="aab-tl-opt__text">' +
						'<span class="aab-tl-opt__label">' + escapeHtml(label) + '</span>' +
						'<span class="aab-tl-opt__desc">' + escapeHtml(desc) + '</span>' +
					'</span>' +
				'</label>'
			);
		}

		function group(title, cards) {
			return '<div class="aab-tl-opt-group"><div class="aab-tl-opt-group__title">' + escapeHtml(title) + '</div>' + cards + '</div>';
		}

		function summaryText(copy) {
			return copy
				? (I18N.opt_summary || 'This section will look like the preview. Its images are copied to your site.')
				: (I18N.opt_summary_link || 'This section will look like the preview. Its photos keep loading from the BricksFly library.');
		}

		var box = document.createElement('div');
		box.id = 'aab-tl-options';
		box.className = 'aab-tl-options';
		box.innerHTML =
			'<div class="aab-tl-options__backdrop"></div>' +
			'<div class="aab-tl-options__dialog" role="dialog" aria-modal="true" aria-label="' + escapeAttr(I18N.options_title || 'Import settings') + '">' +
				'<div class="aab-tl-options__head">' +
					'<div class="aab-tl-options__title">' + escapeHtml(I18N.options_title || 'Import settings') + '</div>' +
					(title ? '<div class="aab-tl-options__sub">' + escapeHtml(title) + '</div>' : '') +
				'</div>' +
				'<div class="aab-tl-options__body">' +
					'<p class="aab-tl-options__summary">' + escapeHtml(summaryText(opts.import_images)) + '</p>' +
					'<button type="button" class="aab-tl-options__toggle" aria-expanded="' + (isCustom ? 'true' : 'false') + '">' + escapeHtml(isCustom ? (I18N.opt_hide || 'Hide settings') : (I18N.opt_change || 'Change settings')) + '</button>' +
					'<div class="aab-tl-options__adv"' + (isCustom ? '' : ' hidden') + '>' +
						group(I18N.opt_design || 'Design',
							optCard('design', 'original', I18N.des_keep || 'Keep original design', I18N.des_keep_desc || 'The section looks like the preview. Its global classes are added to your site.', opts.import_classes) +
							optCard('design', 'site', I18N.des_site || 'Use this site’s style', I18N.des_site_desc || 'The section follows your site’s styles. The template’s global classes are not added.', !opts.import_classes)
						) +
						group(I18N.opt_images || 'Images',
							optCard('images', 'copy', I18N.img_copy || 'Copy images to my site', I18N.img_copy_desc || 'Download the images into your Media Library.', opts.import_images) +
							optCard('images', 'link', I18N.img_link || 'Keep demo image links', I18N.img_link_desc || 'Use the images from the library server. Nothing is downloaded.', !opts.import_images)
						) +
					'</div>' +
				'</div>' +
				'<div class="aab-tl-options__foot">' +
					'<button type="button" class="aab-tl-options__cancel">' + escapeHtml(I18N.cancel || 'Cancel') + '</button>' +
					'<button type="button" class="aab-tl-options__confirm">' + escapeHtml(I18N.insert_section || 'Insert Section') + '</button>' +
				'</div>' +
			'</div>';

		document.body.appendChild(box);

		function onKey(ev) {
			if (ev.key === 'Escape') {
				ev.stopPropagation();
				done();
			}
		}
		function done() {
			document.removeEventListener('keydown', onKey, true);
			closeInsertOptions();
		}
		document.addEventListener('keydown', onKey, true);

		box.querySelector('.aab-tl-options__backdrop').addEventListener('click', done);
		box.querySelector('.aab-tl-options__cancel').addEventListener('click', done);

		// "Change settings" shows / hides the choices; the radio cards highlight
		// the selected one.
		var toggle = box.querySelector('.aab-tl-options__toggle');
		var adv = box.querySelector('.aab-tl-options__adv');
		toggle.addEventListener('click', function () {
			var open = adv.hasAttribute('hidden');
			if (open) { adv.removeAttribute('hidden'); } else { adv.setAttribute('hidden', ''); }
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
			toggle.textContent = open ? (I18N.opt_hide || 'Hide settings') : (I18N.opt_change || 'Change settings');
		});

		// Radio cards: keep the 'selected' look in sync.
		box.querySelectorAll('input[type="radio"]').forEach(function (radio) {
			radio.addEventListener('change', function () {
				box.querySelectorAll('input[name="' + radio.name + '"]').forEach(function (r) {
					r.closest('.aab-tl-opt').classList.toggle('is-selected', r.checked);
				});
				if (radio.name === 'images') {
					box.querySelector('.aab-tl-options__summary').textContent = summaryText(radio.value === 'copy');
				}
			});
		});

		box.querySelector('.aab-tl-options__confirm').addEventListener('click', function () {
			var chosen = {
				import_images: box.querySelector('[name="images"]:checked').value === 'copy',
				import_classes: box.querySelector('[name="design"]:checked').value === 'original'
			};
			saveInsertOptions(chosen);
			done();
			handleInsert(card, btn, chosen);
		});

		box.querySelector('.aab-tl-options__confirm').focus();
	}

	/**
	 * Notice dialog for a failed insert (tone "error") or a successful insert that
	 * has notes (tone "warn"). Says what went wrong and what the user can do, and
	 * offers "Try again" when a retry callback is given.
	 *
	 * opts: { tone, title, message, hint, items: [{message, hint}], retry }
	 */
	function showNotice(opts) {
		closeInsertOptions();

		var tone = opts.tone === 'warn' ? 'warn' : 'error';
		var body = '';

		// Texts may carry HTML entities from WordPress; show them as plain text.
		function txt(v) { return escapeHtml(decodeEntities(v)); }

		var icon = tone === 'warn'
			? '<svg class="aab-tl-notice__icon" viewBox="0 0 24 24" width="22" height="22" aria-hidden="true"><path fill="currentColor" d="M12 2.5 1.8 20.2a1 1 0 0 0 .9 1.5h18.6a1 1 0 0 0 .9-1.5L12 2.5Zm1 14.7h-2v-2h2v2Zm0-4h-2V8.5h2v4.7Z"/></svg>'
			: '<svg class="aab-tl-notice__icon" viewBox="0 0 24 24" width="22" height="22" aria-hidden="true"><path fill="currentColor" d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm4.2 12.8-1.4 1.4L12 13.4l-2.8 2.8-1.4-1.4 2.8-2.8-2.8-2.8 1.4-1.4 2.8 2.8 2.8-2.8 1.4 1.4-2.8 2.8 2.8 2.8Z"/></svg>';

		if (opts.message) {
			body += '<p class="aab-tl-notice__msg">' + txt(opts.message) + '</p>';
		}
		if (opts.hint) {
			body +=
				'<div class="aab-tl-notice__tip">' +
					'<div class="aab-tl-notice__label">' + txt(I18N.err_what_to_do || 'What you can do') + '</div>' +
					'<p class="aab-tl-notice__hint">' + txt(opts.hint) + '</p>' +
				'</div>';
		}
		(opts.items || []).forEach(function (item) {
			var tags = '';
			(item.tags || []).forEach(function (t) { tags += '<code class="aab-tl-notice__tag">' + txt(t) + '</code>'; });
			body +=
				'<div class="aab-tl-notice__item">' +
					'<div class="aab-tl-notice__msg">' + txt(item.message || '') + '</div>' +
					(tags ? '<div class="aab-tl-notice__tags">' + tags + '</div>' : '') +
					(item.hint ? '<div class="aab-tl-notice__hint">' + txt(item.hint) + '</div>' : '') +
				'</div>';
		});
		if (opts.code) {
			body += '<div class="aab-tl-notice__code">' + txt(I18N.err_code || 'Error code') + ': ' + escapeHtml(String(opts.code)) + '</div>';
		}

		var box = document.createElement('div');
		box.id = 'aab-tl-options';
		box.className = 'aab-tl-options aab-tl-notice aab-tl-notice--' + tone;
		box.innerHTML =
			'<div class="aab-tl-options__backdrop"></div>' +
			'<div class="aab-tl-options__dialog" role="alertdialog" aria-modal="true" aria-label="' + escapeAttr(opts.title || '') + '">' +
				'<div class="aab-tl-options__head">' + icon + '<div class="aab-tl-options__title">' + txt(opts.title || '') + '</div></div>' +
				'<div class="aab-tl-options__body aab-tl-notice__body">' + body + '</div>' +
				'<div class="aab-tl-options__foot">' +
					'<button type="button" class="aab-tl-options__cancel">' + txt(tone === 'warn' ? (I18N.warn_ok || 'Got it') : (I18N.close || 'Close')) + '</button>' +
					(opts.retry ? '<button type="button" class="aab-tl-options__confirm">' + txt(I18N.err_retry || 'Try again') + '</button>' : '') +
				'</div>' +
			'</div>';
		document.body.appendChild(box);

		function onKey(ev) {
			if (ev.key === 'Escape') {
				ev.stopPropagation();
				done();
			}
		}
		function done() {
			document.removeEventListener('keydown', onKey, true);
			closeInsertOptions();
		}
		document.addEventListener('keydown', onKey, true);

		box.querySelector('.aab-tl-options__backdrop').addEventListener('click', done);
		box.querySelector('.aab-tl-options__cancel').addEventListener('click', done);

		var retryBtn = box.querySelector('.aab-tl-options__confirm');
		if (retryBtn) {
			retryBtn.addEventListener('click', function () {
				done();
				opts.retry();
			});
			retryBtn.focus();
		} else {
			box.querySelector('.aab-tl-options__cancel').focus();
		}
	}

	/**
	 * Lock the screen while a section is being imported and say so in the middle,
	 * so nobody clicks another card or closes the window half way.
	 */
	var busyEl = null;
	function blockKeysWhileBusy(e) {
		if (busyEl) { e.stopPropagation(); e.preventDefault(); }
	}
	function showBusy(sectionTitle) {
		hideBusy();
		busyEl = document.createElement('div');
		busyEl.className = 'aab-tl-busy';
		busyEl.setAttribute('role', 'alertdialog');
		busyEl.setAttribute('aria-live', 'assertive');
		busyEl.setAttribute('aria-busy', 'true');
		busyEl.innerHTML =
			'<div class="aab-tl-busy__box">' +
				'<span class="aab-tl-busy__spinner" aria-hidden="true"></span>' +
				'<p class="aab-tl-busy__title">' + escapeHtml(I18N.importing_title || 'Importing section…') + '</p>' +
				(sectionTitle ? '<p class="aab-tl-busy__name">' + escapeHtml(sectionTitle) + '</p>' : '') +
				'<p class="aab-tl-busy__hint">' + escapeHtml(I18N.importing_hint || 'Please wait. Do not close this window.') + '</p>' +
			'</div>';
		document.body.appendChild(busyEl);
		document.addEventListener('keydown', blockKeysWhileBusy, true);
	}
	function hideBusy() {
		document.removeEventListener('keydown', blockKeysWhileBusy, true);
		if (busyEl && busyEl.parentNode) { busyEl.parentNode.removeChild(busyEl); }
		busyEl = null;
	}

	function handleInsert(card, btn, options) {
		options = options || loadInsertOptions();

		if (!CFG.post_id) {
			showNotice({
				title: I18N.err_title,
				message: I18N.insert_failed || 'Could not import this section.',
				hint: I18N.err_paste_hint
			});
			return;
		}

		var originalLabel = btn.innerHTML;
		btn.disabled = true;
		btn.innerHTML = escapeHtml(I18N.inserting || 'Inserting...');
		var titleNode = card.querySelector('.aab-tl-card__title');
		showBusy(titleNode ? decodeEntities(titleNode.textContent) : '');

		// info: { message, hint } - what went wrong and what to do about it.
		function fail(info) {
			info = info || {};
			hideBusy();
			btn.disabled = false;
			btn.innerHTML = originalLabel;
			showNotice({
				tone: 'error',
				title: I18N.err_title || 'This section could not be imported',
				message: info.message || I18N.insert_failed || 'Could not import this section.',
				hint: info.hint || I18N.err_paste_hint,
				code: info.code || '',
				// Retrying cannot fix these: the user has to install / activate Pro
				// or ask for access first.
				retry: (['pro_elements', 'pro_item', 'permission', 'session'].indexOf(info.code) !== -1)
					? null
					: function () { handleInsert(card, btn, options); }
			});
		}

		var formData = new FormData();
		formData.append('action', 'bricksfly_builder_insert_template');
		formData.append('nonce', CFG.nonce);
		formData.append('post_id', String(CFG.post_id));
		formData.append('template_id', card.dataset.id || '');
		formData.append('import_images', options.import_images ? '1' : '0');
		formData.append('import_classes', options.import_classes ? '1' : '0');

		fetch(CFG.ajaxurl, {
			method: 'POST',
			body: formData,
			credentials: 'same-origin'
		})
			.then(function (r) {
				return r.text().then(function (text) { return { status: r.status, text: text }; });
			})
			.then(function (res) {
				var resp = null;
				try { resp = JSON.parse(res.text); } catch (e) { resp = null; }

				// Not a JSON object: an expired nonce answers "-1" (403), a PHP
				// fatal / timeout answers an HTML or empty page.
				if (!resp || typeof resp !== 'object') {
					if (res.status === 403 || String(res.text).trim() === '-1') {
						return fail({ code: 'session', message: I18N.err_session, hint: I18N.err_sess_hint });
					}
					return fail({ code: 'server', message: I18N.err_server, hint: I18N.err_server_hint });
				}

				if (!resp.success || !resp.data) {
					var errData = resp.data || {};
					return fail({ code: errData.code, message: errData.message, hint: errData.hint });
				}

				var data = resp.data;
				var elements = data.content;
				if (!Array.isArray(elements) || !elements.length) {
					return fail({ code: 'empty', message: I18N.insert_failed });
				}

				// Bricks' paste reads its clipboard envelope; write the section
				// into it (with its globals so CSS/vars carry over) then trigger
				// the native paste into the canvas.
				var envelope = {
					source: 'bricksCopiedElements',
					content: elements,
					globalClasses: Array.isArray(data.global_classes) ? data.global_classes : [],
					globalVariables: Array.isArray(data.globalVariables) ? data.globalVariables : [],
					components: [],
					globalElements: [],
					sourceUrl: ''
				};

				// Bricks' paste ignores `globalVariables` in the envelope, so add
				// them to the builder ourselves (never replacing a variable the
				// site already has).
				// The server only knows saved classes; the builder also holds the ones
				// added by earlier, unsaved inserts. Match against the builder itself so
				// Bricks never has to ask "replace this class?".
				var renamedClasses = reconcileClassesWithBuilder(envelope);
				var varsOk = mergeGlobalVariables(envelope.globalVariables);

				pasteIntoCanvas(JSON.stringify(envelope), function (ok) {
					if (ok) {
						hideBusy();
						btn.disabled = false;
						btn.innerHTML = originalLabel;
						closeLibraryModal();

						// Inserted, but with notes (images left remote, SVGs skipped, ...).
						var notes = Array.isArray(data.warnings) ? data.warnings.slice() : [];
						if (renamedClasses.length && I18N.warn_classes_renamed) {
							notes.push({
								message: I18N.warn_classes_renamed,
								tags: renamedClasses.slice(0, 5),
								hint: I18N.warn_classes_renamed_hint || ''
							});
						}
						if (!varsOk && I18N.warn_vars) {
							notes.push({ message: I18N.warn_vars });
						}
						if (notes.length) {
							showNotice({
								tone: 'warn',
								title: I18N.warn_title || 'Section inserted, with notes',
								items: notes
							});
						}
					} else {
						fail({ code: 'paste', message: I18N.err_paste, hint: I18N.err_paste_hint });
					}
				});
			})
			.catch(function () {
				fail({ code: 'network', message: I18N.err_network, hint: I18N.err_net_hint });
			});
	}

	/**
	 * Canonical form of a class' styling, so two classes with the same styling
	 * compare equal whatever their key order or value types.
	 */
	function canonStyle(value) {
		if (value === null || typeof value === 'undefined') { return null; }
		if (Array.isArray(value)) {
			return value.length ? value.map(canonStyle) : {};
		}
		if (typeof value === 'object') {
			var out = {};
			Object.keys(value).sort().forEach(function (k) { out[k] = canonStyle(value[k]); });
			return out;
		}
		if (value === true) { return '1'; }
		if (value === false) { return ''; }
		return String(value);
	}

	function sameClassStyle(a, b) {
		return JSON.stringify(canonStyle({ settings: a.settings || {}, selectors: a.selectors || [] })) ===
			JSON.stringify(canonStyle({ settings: b.settings || {}, selectors: b.selectors || [] }));
	}

	function freeClassId(taken) {
		var id;
		do { id = Math.random().toString(36).slice(2, 8); } while (taken[id] || id.length < 6);
		return id;
	}

	/**
	 * Make the section's global classes safe to paste into this builder:
	 *  - same name + same styling  -> reuse the site's class (not added again)
	 *  - same name + other styling -> added under "name-2" (the site's class is untouched)
	 *  - same id, other name       -> gets a fresh id
	 * Element references are re-pointed. Returns the names that had to be renamed.
	 */
	function reconcileClassesWithBuilder(envelope) {
		var renamed = [];
		var incoming = envelope.globalClasses;
		if (!Array.isArray(incoming) || !incoming.length) { return renamed; }

		var resolved = resolvePasteInstance();
		var state = resolved && resolved.instance && resolved.instance.$_state;
		if (!state || !Array.isArray(state.globalClasses)) { return renamed; }

		try {
			var byName = {};
			var byId = {};
			var takenNames = {};
			var takenIds = {};
			state.globalClasses.forEach(function (c) {
				if (!c) { return; }
				if (c.name) { byName[c.name] = c; takenNames[c.name] = true; }
				if (c.id) { byId[c.id] = c; takenIds[c.id] = true; }
			});

			var idMap = {};
			var keep = [];

			incoming.forEach(function (cls) {
				if (!cls || !cls.id || !cls.name) { keep.push(cls); return; }
				var sameName = byName[cls.name] || null;
				var sameId = byId[cls.id] || null;

				if (sameName && sameClassStyle(sameName, cls)) {
					idMap[cls.id] = sameName.id;
					return;
				}
				if (sameId && !sameName && sameClassStyle(sameId, cls)) {
					return;
				}
				if (sameName || sameId) {
					// An earlier insert may already have added this very class as "name-2".
					var copy = null;
					var re = new RegExp('^' + cls.name.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '-\\d+$');
					Object.keys(byName).some(function (n) {
						if (re.test(n) && sameClassStyle(byName[n], cls)) { copy = byName[n]; return true; }
						return false;
					});
					if (copy) {
						idMap[cls.id] = copy.id;
						return;
					}

					var i = 2;
					while (takenNames[cls.name + '-' + i]) { i++; }
					var newName = cls.name + '-' + i;
					var newId = freeClassId(takenIds);
					renamed.push(cls.name);
					idMap[cls.id] = newId;
					takenNames[newName] = true;
					takenIds[newId] = true;
					cls = JSON.parse(JSON.stringify(cls));
					cls.name = newName;
					cls.id = newId;
				}
				keep.push(cls);
			});

			if (Object.keys(idMap).length) {
				(envelope.content || []).forEach(function (el) {
					var refs = el && el.settings && el.settings._cssGlobalClasses;
					if (!Array.isArray(refs)) { return; }
					el.settings._cssGlobalClasses = refs.map(function (r) { return (typeof r === 'string' && idMap[r]) ? idMap[r] : r; });
				});
			}
			envelope.globalClasses = keep;
		} catch (e) {
			return renamed;
		}
		return renamed;
	}

	/**
	 * Add the section's global variables to the builder state. A variable the
	 * site already has (same name) is left exactly as it is. Returns false only
	 * when there were variables to add but the builder state was not reachable.
	 */
	function mergeGlobalVariables(incoming) {
		if (!Array.isArray(incoming) || !incoming.length) { return true; }
		var resolved = resolvePasteInstance();
		var state = resolved && resolved.instance && resolved.instance.$_state;
		if (!state || !Array.isArray(state.globalVariables)) { return false; }

		try {
			var list = state.globalVariables.slice();
			var names = {};
			var ids = {};
			list.forEach(function (v) {
				if (v && v.name) { names[v.name] = true; }
				if (v && v.id) { ids[v.id] = true; }
			});

			var added = false;
			incoming.forEach(function (v) {
				if (!v || !v.name || names[v.name]) { return; }
				var copy = JSON.parse(JSON.stringify(v));
				while (!copy.id || ids[copy.id]) {
					copy.id = Math.random().toString(36).slice(2, 8);
				}
				names[copy.name] = true;
				ids[copy.id] = true;
				list.push(copy);
				added = true;
			});

			if (added) { state.globalVariables = list; }
			return true;
		} catch (e) {
			return false;
		}
	}

	// --- Native Bricks paste (preview, no reload) ---------------------

	// Candidate paste method names across Bricks versions, priority order.
	var PASTE_METHOD_NAMES = ['$_pasteElements', '$_paste', 'pasteElements', '$_insertElements'];
	var _pasteCache = null; // { instance, method }

	/**
	 * Write the Bricks clipboard envelope, then run Bricks' native paste so the
	 * section appears in the canvas as an unsaved/undoable change. Works on
	 * secure contexts (clipboard API) and plain HTTP (temporary shim). Invokes
	 * `done(true|false)`.
	 */
	function pasteIntoCanvas(payload, done) {
		var resolved = resolvePasteInstance();
		if (!resolved) {
			done(false);
			return;
		}

		function callPaste() {
			// Bricks regenerates ids and inserts relative to the active element;
			// without an active element it appends to the document root, which is
			// the desired "add this section" behaviour for the library.
			try {
				var r = resolved.instance[resolved.method]();
				if (r && typeof r.then === 'function') {
					r.then(function () { done(true); }, function () { done(false); });
				} else {
					done(true);
				}
			} catch (e) {
				done(false);
			}
		}

		var secure = !!(navigator.clipboard && window.isSecureContext && navigator.clipboard.writeText);
		if (secure) {
			navigator.clipboard.writeText(payload).then(callPaste, callPaste);
			return;
		}

		// Non-secure (local HTTP): shim clipboard.readText + isSecureContext so
		// Bricks' paste still reads our payload, then restore on the next tick.
		if (!runWithClipboardShim(payload, callPaste)) {
			done(false);
		}
	}

	/**
	 * Find a Bricks Vue component instance exposing a paste method. Searches the
	 * main builder document and the canvas iframe; caches the winner. Mirrors the
	 * discovery used by the paste feature: Vue 3 exposes the mounted root via
	 * `node._vnode.component` / `node.__vue_app__._instance`, and methods live on
	 * the instance's `proxy` or `ctx`.
	 */
	function resolvePasteInstance() {
		if (_pasteCache && _pasteCache.instance && typeof _pasteCache.instance[_pasteCache.method] === 'function') {
			return _pasteCache;
		}

		var seen = (typeof Set === 'function') ? new Set() : null;

		function methodOn(inst) {
			if (!inst) { return null; }
			for (var i = 0; i < PASTE_METHOD_NAMES.length; i++) {
				try { if (typeof inst[PASTE_METHOD_NAMES[i]] === 'function') { return PASTE_METHOD_NAMES[i]; } } catch (e) {}
			}
			return null;
		}

		function hitOn(comp) {
			if (!comp) { return null; }
			var cands = [comp.proxy, comp.ctx];
			for (var c = 0; c < cands.length; c++) {
				var m = methodOn(cands[c]);
				if (m) { return { instance: cands[c], method: m }; }
			}
			return null;
		}

		function visitComp(comp, depth) {
			if (!comp || depth > 80) { return null; }
			if (seen) { if (seen.has(comp)) { return null; } seen.add(comp); }
			var hit = hitOn(comp);
			if (hit) { return hit; }
			return visitVNode(comp.subTree, depth + 1);
		}

		function visitVNode(vnode, depth) {
			if (!vnode || depth > 80) { return null; }
			if (vnode.component) {
				var f = visitComp(vnode.component, depth + 1);
				if (f) { return f; }
			}
			if (Array.isArray(vnode.children)) {
				for (var i = 0; i < vnode.children.length; i++) {
					var f2 = visitVNode(vnode.children[i], depth + 1);
					if (f2) { return f2; }
				}
			}
			return null;
		}

		var docs = [document];
		try {
			var ifr = document.getElementById('bricks-builder-iframe');
			var idoc = ifr && (ifr.contentDocument || (ifr.contentWindow && ifr.contentWindow.document));
			if (idoc && idoc !== document) { docs.push(idoc); }
		} catch (e) {}

		var roots = ['.brx-body', '#bricks-builder', '#app', 'body'];
		for (var d = 0; d < docs.length; d++) {
			for (var r = 0; r < roots.length; r++) {
				var node = docs[d].querySelector(roots[r]);
				if (!node) { continue; }
				var comps = [];
				if (node._vnode && node._vnode.component) { comps.push(node._vnode.component); }
				if (node.__vue_app__ && node.__vue_app__._instance) { comps.push(node.__vue_app__._instance); }
				if (node.__vueParentComponent) { comps.push(node.__vueParentComponent); }
				for (var c = 0; c < comps.length; c++) {
					var found = visitComp(comps[c], 0);
					if (found) { _pasteCache = found; return found; }
				}
			}
		}

		return null;
	}

	/**
	 * Temporarily make Bricks' secure-context-gated clipboard paste work on a
	 * plain-HTTP builder: override navigator.clipboard.readText() to return
	 * `payload` and force isSecureContext truthy, run `fn`, then restore on the
	 * next tick (the native read is async). Returns false if it can't be shimmed.
	 */
	function runWithClipboardShim(payload, fn) {
		var hadClipboard = 'clipboard' in navigator;
		var originalClipboard = navigator.clipboard;
		var secureDescriptor = Object.getOwnPropertyDescriptor(window, 'isSecureContext');

		function restore() {
			try {
				if (hadClipboard) {
					Object.defineProperty(navigator, 'clipboard', { value: originalClipboard, configurable: true, writable: true });
				} else {
					delete navigator.clipboard;
				}
			} catch (e) {}
			try {
				if (secureDescriptor) {
					Object.defineProperty(window, 'isSecureContext', secureDescriptor);
				}
			} catch (e) {}
		}

		try {
			Object.defineProperty(navigator, 'clipboard', {
				value: { readText: function () { return Promise.resolve(payload); } },
				configurable: true,
				writable: true
			});
			Object.defineProperty(window, 'isSecureContext', { value: true, configurable: true });
		} catch (e) {
			restore();
			return false;
		}

		try {
			fn();
		} finally {
			setTimeout(restore, 0);
		}
		return true;
	}

	// --- Helpers ------------------------------------------------------

	/**
	 * The library sends titles the way WordPress stores them ("&#8211;", "&amp;").
	 * Turn those back into plain text; output is escaped again where it is used.
	 */
	function decodeEntities(str) {
		var box = document.createElement('textarea');
		box.innerHTML = String(str == null ? '' : str);
		return box.value;
	}

	function escapeHtml(str) {
		return String(str == null ? '' : str)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#39;');
	}

	function escapeAttr(str) {
		return escapeHtml(str);
	}

})(window.jQuery, window, document);
