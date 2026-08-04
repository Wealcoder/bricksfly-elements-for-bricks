import "../../scss/elements/testimonial.scss";

(function () {
    'use strict';

    function initTestimonial(wrapper) {
        if (!wrapper) return;

        var swiperEl = wrapper.querySelector('.wcf__slider');
        if (!swiperEl) return;

        var swiperWrapper = wrapper.querySelector('.swiper-wrapper');
        if (!swiperWrapper) return;

        // Read settings from data-settings on every init so re-renders in
        // the Bricks builder pick up updated values (do NOT remove the attr).
        var settingsData = swiperWrapper.getAttribute('data-settings');
        if (!settingsData) return;

        var options;
        try {
            options = JSON.parse(settingsData);
        } catch (e) {
            return;
        }

        // Navigation: resolve elements within this instance
        if (options.navigation) {
            var nextBtn = wrapper.querySelectorAll('.wcf-arrow-next');
            var prevBtn = wrapper.querySelectorAll('.wcf-arrow-prev');
            options.navigation.nextEl = nextBtn.length ? nextBtn[nextBtn.length - 1] : null;
            options.navigation.prevEl = prevBtn.length ? prevBtn[prevBtn.length - 1] : null;
        }

        // Pagination: resolve element and set fraction renderer
        if (options.pagination) {
            var paginationEls = wrapper.querySelectorAll('.swiper-pagination');
            options.pagination.el = paginationEls.length ? paginationEls[paginationEls.length - 1] : null;

            if (options.pagination.type === 'fraction') {
                options.pagination.formatFractionCurrent = function (number) {
                    return ('0' + number).slice(-2);
                };
                options.pagination.formatFractionTotal = function (number) {
                    return ('0' + number).slice(-2);
                };
                options.pagination.renderFraction = function (currentClass, totalClass) {
                    return '<span class="' + currentClass + '"></span>' +
                        '<span class="mid-line"></span>' +
                        '<span class="' + totalClass + '"></span>';
                };
            }
        }

        // Destroy existing Swiper instance if re-initializing
        if (swiperEl.swiper) {
            try { swiperEl.swiper.destroy(true, true); } catch (e) {}
        }

        new Swiper(swiperEl, options);
    }

    function initAll() {
        document.querySelectorAll('.bricksfly-testimonial-wrapper').forEach(function (el) {
            initTestimonial(el);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

    // Bricks calls this from $scripts. With an element it inits that one;
    // without it (some builder re-render paths) it re-inits all instances.
    window.bricksflyTestimonial = function (el) {
        if (el) {
            var wrapper = el.classList && el.classList.contains('bricksfly-testimonial-wrapper')
                ? el
                : el.querySelector('.bricksfly-testimonial-wrapper');
            initTestimonial(wrapper);
        } else {
            initAll();
        }
    };
})();
