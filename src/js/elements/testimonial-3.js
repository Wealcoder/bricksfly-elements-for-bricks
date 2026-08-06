import "../../scss/elements/testimonial-3.scss";

(function () {
    'use strict';

    function initTestimonial3(wrapper) {
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
        document.querySelectorAll('.aae-testimonial-3-wrapper').forEach(function (el) {
            initTestimonial3(el);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

    // Bricks may call this with the element wrapper or with no args.
    window.aaeTestimonial3 = function (el) {
        if (el) {
            var wrapper = el.classList && el.classList.contains('aae-testimonial-3-wrapper')
                ? el
                : (el.closest && el.closest('.aae-testimonial-3-wrapper')) || el.querySelector('.aae-testimonial-3-wrapper');
            initTestimonial3(wrapper);
        } else {
            initAll();
        }
    };
})();
