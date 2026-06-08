import "../../scss/elements/video-box-slider.scss";

(function () {
    'use strict';

    var globalCloseBound = false;

    function bindGlobalClose() {
        if (globalCloseBound) return;

        // Close popup
        document.addEventListener('click', function (e) {
            var closeBtn = e.target.closest('.aab-popup-close');
            if (!closeBtn) return;

            e.preventDefault();

            var wrapper = closeBtn.closest('.aab-popup-video-wrapper');
            if (!wrapper) return;

            var inner = wrapper.querySelector('.aab-popup-video');
            var container = wrapper.querySelector('.aab-popup-content-container');

            if (typeof gsap === 'object') {
                gsap.timeline({
                    onComplete: function () {
                        wrapper.style.cssText = '';
                        if (inner) inner.style.cssText = '';
                        if (container) container.innerHTML = '';
                    }
                })
                .to(inner, { scaleY: 0.01, opacity: 0, duration: 0.4, ease: 'power2.inOut' })
                .to(wrapper, { scaleY: 0.01, duration: 0.5, ease: 'power2.inOut' }, '-=0.2')
                .to(wrapper, { opacity: 0, visibility: 'hidden', duration: 0.3, ease: 'power2.inOut' }, '-=0.3');
            } else {
                wrapper.style.opacity = '0';
                wrapper.style.visibility = 'hidden';
                wrapper.style.transition = 'all 0.4s';
                if (container) container.innerHTML = '';
            }
        });

        // Close on backdrop click
        document.addEventListener('click', function (e) {
            if (!e.target.classList.contains('aab-popup-video-wrapper')) return;
            var closeBtn = e.target.querySelector('.aab-popup-close');
            if (closeBtn) closeBtn.click();
        });

        globalCloseBound = true;
    }

    function buildSwiperOptions(swiperWrapperEl, root) {
        var raw = swiperWrapperEl.getAttribute('data-settings');
        if (!raw) return null;

        var options;
        try {
            options = JSON.parse(raw);
        } catch (e) {
            return null;
        }

        // Scope navigation/pagination to this element instance
        if (options.navigation) {
            var nextBtn = root.querySelector('.wcf-arrow-next');
            var prevBtn = root.querySelector('.wcf-arrow-prev');
            options.navigation.nextEl = nextBtn || null;
            options.navigation.prevEl = prevBtn || null;
        }

        if (options.pagination) {
            var pagEl = root.querySelector('.swiper-pagination');
            options.pagination.el = pagEl || null;

            if (options.pagination.type === 'fraction') {
                options.pagination.formatFractionCurrent = function (n) { return ('0' + n).slice(-2); };
                options.pagination.formatFractionTotal   = function (n) { return ('0' + n).slice(-2); };
                options.pagination.renderFraction = function (currentClass, totalClass) {
                    return '<span class="' + currentClass + '"></span>' +
                        '<span class="mid-line"></span>' +
                        '<span class="' + totalClass + '"></span>';
                };
            }
        }

        // Builder: Swiper needs to recalc after Bricks re-renders the slides
        // (changing slidesToShow, adding slides, panel resizing, etc.).
        // Observer watches the swiper element, observeParents watches the
        // canvas wrapper. Without these the builder keeps showing the
        // first-init slide count regardless of new panel values.
        options.observer = true;
        options.observeParents = true;
        options.observeSlideChildren = true;

        // Don't strip data-settings — keep it so subsequent re-inits (panel
        // changes in the builder) can re-read fresh options. Stripping it
        // worked on the frontend because there's only one init per page;
        // it broke the builder where init runs again after every edit.
        return options;
    }

    function initSwiper(root) {
        if (typeof Swiper === 'undefined') return;

        var swiperEl = root.querySelector('.wcf__slider');
        if (!swiperEl) return;

        var swiperWrapper = root.querySelector('.swiper-wrapper');
        if (!swiperWrapper) return;

        var options = buildSwiperOptions(swiperWrapper, root);
        if (!options) return;

        if (swiperEl.swiper) {
            swiperEl.swiper.destroy(true, true);
        }

        new Swiper(swiperEl, options);
    }

    function transferPopup(root) {
        var source = root.querySelector('.aab-popup-source');
        if (!source) return;

        var overlay = source.querySelector('.aab-popup-video-wrapper');
        if (overlay) {
            var ownerId = root.id || '';
            if (ownerId) {
                var existing = document.body.querySelector('.aab-popup-video-wrapper[data-owner="' + ownerId + '"]');
                if (existing) existing.remove();
                overlay.setAttribute('data-owner', ownerId);
            }
            document.body.appendChild(overlay);
        }
        source.remove();
    }

    function bindPopupButtons(root) {
        var ownerId = root.id || '';
        var buttons = root.querySelectorAll('.aab-popup-btn');

        buttons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var url = btn.getAttribute('data-src');
                if (!url) return;

                var popupWrapper = ownerId
                    ? document.body.querySelector('.aab-popup-video-wrapper[data-owner="' + ownerId + '"]')
                    : document.body.querySelector('.aab-popup-video-wrapper');

                if (!popupWrapper) return;

                var container = popupWrapper.querySelector('.aab-popup-content-container');
                if (container) {
                    container.innerHTML = '<iframe src="' + url + '" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen referrerpolicy="strict-origin-when-cross-origin"></iframe>';
                }

                var inner = popupWrapper.querySelector('.aab-popup-video');

                if (typeof gsap === 'object') {
                    gsap.timeline({ defaults: { ease: 'power2.inOut' } })
                        .to(popupWrapper, { scaleY: 0.01, x: 1, opacity: 1, visibility: 'visible', duration: 0.4 })
                        .to(popupWrapper, { scaleY: 1, duration: 0.6 })
                        .to(inner, { scaleY: 1, opacity: 1, visibility: 'visible', duration: 0.6 }, '-=0.4');
                } else {
                    popupWrapper.style.cssText = 'opacity:1;visibility:visible;transform:none;transition:all 0.4s;z-index:9999;';
                    if (inner) inner.style.cssText = 'opacity:1;visibility:visible;transform:none;transition:all 0.4s;';
                }
            });
        });
    }

    function initElement(el) {
        if (!el) return;

        var root = el.classList && el.classList.contains('aab-video-box-slider')
            ? el
            : el.querySelector('.aab-video-box-slider');
        if (!root) return;

        bindGlobalClose();
        transferPopup(root);
        bindPopupButtons(root);
        initSwiper(root);
    }

    function initAll() {
        document.querySelectorAll('.aab-video-box-slider').forEach(function (el) {
            var owner = el.closest('[id^="brxe-"]') || el;
            initElement(owner);
        });
    }

    document.addEventListener('DOMContentLoaded', initAll);

    // Bricks builder re-init: $scripts may invoke with the element wrapper
    // (per-instance re-render) or with no argument (a global re-init pass).
    // Handle both — the no-arg path is what fires after the user changes
    // "Slides to Show" in the panel; missing it left the canvas stuck on
    // the old slide count.
    window.aabVideoBoxSlider = function (el) {
        if (el) {
            initElement(el);
        } else {
            initAll();
        }
    };
})();
