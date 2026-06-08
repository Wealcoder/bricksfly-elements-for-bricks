import "../../scss/admin/aab-template-library.scss";

/* eslint-disable */
/**
 * Bricks Builder — Animation Addons Section Library
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

	if (!window.AAB_TEMPLATE_LIBRARY) {
		return;
	}

	var CFG = window.AAB_TEMPLATE_LIBRARY;
	var I18N = CFG.i18n || {};

	// Browse state.
	var state = {
		categories: [],
		type: 'block',
		category: '',
		colorType: '',
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
				return;
			}

			if (attempts > 80) {
				// 80 * 250ms = 20s — give up but keep the observer running
				// in case the toolbar appears later (e.g. on tab switch).
				clearInterval(iv);
				observeBuilderForReinjection();
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
			'#bricks-toolbar .ion-ios-undo, #bricks-toolbar [class*="undo"], #bricks-toolbar [data-balloon="Undo"], #bricks-toolbar [title="Undo"]'
		);
		var undoItem = undoEl ? (undoEl.closest('li') || undoEl.closest('.action') || undoEl) : null;

		// Fall back to the main toolbar menu if the undo button isn't in
		// the DOM yet (Bricks renders the toolbar asynchronously).
		var host = null;
		if (!undoItem) {
			var hosts = [
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
		// with the same `action` class. The label is hidden visually
		// (Bricks shows it as a tooltip via `data-balloon`), so the
		// button takes the same compact icon-only footprint as the
		// undo/redo/save controls next to it.
		var label = I18N.button_label || 'Import Section';
		var tag = host.tagName === 'UL' ? 'li' : 'div';
		var btn = document.createElement(tag);
		btn.id = 'aab-import-section-button';
		btn.className = 'aab-import-section-button action';
		btn.setAttribute('title', label);
		btn.setAttribute('data-balloon', label);
		btn.setAttribute('data-balloon-pos', 'bottom');
		btn.innerHTML =
			'<a href="#" class="aab-import-section-button__inner" aria-label="' + escapeAttr(label) + '">' +
				'<svg class="aab-import-section-button__icon" viewBox="0 0 24 24" aria-hidden="true">' +
					'<path d="M12 3a1 1 0 0 1 1 1v7h7a1 1 0 1 1 0 2h-7v7a1 1 0 1 1-2 0v-7H4a1 1 0 1 1 0-2h7V4a1 1 0 0 1 1-1z"/>' +
				'</svg>' +
				'<span class="screen-reader-text">' + escapeHtml(label) + '</span>' +
			'</a>';

		if (undoItem && undoItem.parentNode === host) {
			host.insertBefore(btn, undoItem);
		} else {
			host.appendChild(btn);
		}

		btn.addEventListener('click', function (e) {
			e.preventDefault();
			openLibraryModal();
		});

		return true;
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

	function closeLibraryModal() {
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

		// Grid click delegation — handle Insert.
		modal.querySelector('#aab-tl-grid').addEventListener('click', function (e) {
			var insertEl = e.target.closest('.aab-tl-card__insert');
			if (!insertEl) {
				return;
			}
			e.preventDefault();
			var card = insertEl.closest('.aab-tl-card');
			if (!card) {
				return;
			}
			handleInsert(card, insertEl);
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
	 *   { id, title, slug, count?, … }
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
			opt.textContent = cat.title || cat.name || cat.slug;
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
			url.searchParams.set('category', state.category);
		}
		if (state.colorType) {
			url.searchParams.set('color_type', state.colorType);
		}
		if (state.search) {
			url.searchParams.set('s', state.search);
		}
     
		fetch(url.toString(), { credentials: 'omit' })
			.then(function (r) { return r.json(); })
			.then(function (data) {
				// New shape: response is a flat array. Old shape: { templates: […] }.
	
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
			.catch(function () {
				if (reset) {
					renderError(modal);
				}
			})
			.finally(function () {
				state.loading = false;
				toggleLoading(modal, false);
			});
	}

	/**
	 * Mark each template as valid (Insert button enabled) if it is free or
	 * the user has a valid Pro license. `is_pro` from the new API is a
	 * proper boolean (the legacy API used strings, so accept both).
	 */
	function validateTemplates(list) {
		var configValid = !!(CFG.config && CFG.config.wcf_valid);
		return list.map(function (item) {
			var isPro = item.is_pro === true || String(item.is_pro) === '1';
			if (configValid || !isPro) {
				item.valid = 'yes';
			}
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
		var title   = item.title || '';
		var demoUrl = item.demo_url || item.template_demo_url || '';

		var actionBtn = '';
		if (item.valid === 'yes') {
			actionBtn =
				'<button type="button" class="aab-tl-card__insert">' +
					'<span class="aab-tl-card__insert-icon" aria-hidden="true">+</span>' +
					escapeHtml(I18N.insert || 'Insert') +
				'</button>';
		} else if (!CFG.pro_installed) {
			actionBtn =
				'<a class="aab-tl-card__pro" href="https://animation-addons.com" target="_blank" rel="noopener">' +
					escapeHtml(I18N.go_premium || 'Go Premium') +
				'</a>';
		} else if (CFG.pro_installed && CFG.pro_active && !(CFG.config && CFG.config.wcf_valid)) {
			actionBtn =
				'<a class="aab-tl-card__pro" href="' + escapeAttr(CFG.dashboard_link) + '" target="_blank" rel="noopener">' +
					escapeHtml(I18N.activate || 'Activate License') +
				'</a>';
		} else if (CFG.pro_installed && !CFG.pro_active) {
			actionBtn =
				'<a class="aab-tl-card__pro" href="' + escapeAttr(CFG.dashboard_link) + '" target="_blank" rel="noopener">' +
					escapeHtml(I18N.install_pro || 'Install Pro') +
				'</a>';
		}

		return (
			'<div class="aab-tl-card" ' +
				'data-id="' + escapeAttr(item.id) + '" ' +
				'data-demo="' + escapeAttr(demoUrl) + '">' +
				'<div class="aab-tl-card__thumb">' +
					(preview ? '<img loading="lazy" src="' + escapeAttr(preview) + '" alt="' + escapeAttr(title) + '">' : '') +
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

	function handleInsert(card, btn) {
		if (!CFG.post_id) {
			window.alert(I18N.insert_failed || 'Could not import this section.');
			return;
		}

		// Warn about reload + unsaved Vue state, since we have to reload
		// the iframe so Bricks Vue store picks up the new content.
		if (!window.confirm(I18N.unsaved_warning || 'This will reload the builder. Save your unsaved changes first?')) {
			return;
		}

		var originalLabel = btn.innerHTML;
		btn.disabled = true;
		btn.innerHTML = escapeHtml(I18N.inserting || 'Inserting…');

		var formData = new FormData();
		formData.append('action', 'aab_builder_insert_template');
		formData.append('nonce', CFG.nonce);
		formData.append('post_id', String(CFG.post_id));
		formData.append('template_id', card.dataset.id || '');

		fetch(CFG.ajaxurl, {
			method: 'POST',
			body: formData,
			credentials: 'same-origin'
		})
			.then(function (r) { return r.json(); })
			.then(function (resp) {
				if (resp && resp.success) {
					btn.innerHTML = escapeHtml(I18N.insert_success || 'Imported. Reloading…');
					setTimeout(function () {
						window.location.reload();
					}, 400);
				} else {
					var msg = (resp && resp.data && resp.data.message) || I18N.insert_failed;
					btn.disabled = false;
					btn.innerHTML = originalLabel;
					window.alert(msg);
				}
			})
			.catch(function () {
				btn.disabled = false;
				btn.innerHTML = originalLabel;
				window.alert(I18N.insert_failed || 'Could not import this section.');
			});
	}

	// --- Helpers ------------------------------------------------------

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
