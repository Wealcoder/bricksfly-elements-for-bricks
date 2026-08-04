import "../../scss/elements/testimonial-2.scss";

(function () {
    'use strict';

    function initTestimonial2(wrapper) {
        if (!wrapper) return;

        var swiperContainer = wrapper.querySelector('.wcf__slider.swiper');
        if (!swiperContainer) return;

        var options = {};
        try {
            options = JSON.parse(wrapper.getAttribute('data-swiper-options') || '{}');
        } catch (e) {
            return;
        }

        if (options.navigation) {
            var nextEl = wrapper.querySelector('.wcf-arrow-next');
            var prevEl = wrapper.querySelector('.wcf-arrow-prev');
            if (nextEl && prevEl) {
                options.navigation.nextEl = nextEl;
                options.navigation.prevEl = prevEl;
            }
        }

        if (options.pagination) {
            var paginationEl = wrapper.querySelector('.swiper-pagination');
            if (paginationEl) {
                options.pagination.el = paginationEl;
            }
        }

        // Always destroy a previous instance so re-renders in the Bricks
        // builder pick up the new options instead of bailing out.
        if (swiperContainer.swiper) {
            try { swiperContainer.swiper.destroy(true, true); } catch (e) {}
        }

        new Swiper(swiperContainer, options);
    }

    function initAll() {
        document.querySelectorAll('.bricksfly-testimonial-2-wrapper').forEach(function (el) {
            initTestimonial2(el);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

    // Bricks may call this with the element wrapper or with no args.
    window.bricksflyTestimonial2 = function (el) {
        if (el) {
            var wrapper = el.classList && el.classList.contains('bricksfly-testimonial-2-wrapper')
                ? el
                : (el.closest && el.closest('.bricksfly-testimonial-2-wrapper')) || el.querySelector('.bricksfly-testimonial-2-wrapper');
            initTestimonial2(wrapper);
        } else {
            initAll();
        }
    };
})();
