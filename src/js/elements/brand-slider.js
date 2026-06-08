import "../../scss/elements/brand-slider.scss";

(function () {
  "use strict";

  function initInstance(wrapper) {
    if (!wrapper) return;

    var swiperContainer = wrapper.querySelector(".swiper");
    if (!swiperContainer) return;

    if (swiperContainer.swiper) {
      try {
        swiperContainer.swiper.destroy(true, true);
      } catch (e) {}
    }

    var optionsStr = wrapper.getAttribute("data-swiper");
    if (!optionsStr) return;

    var options;
    try {
      options = JSON.parse(optionsStr);
    } catch (e) {
      return;
    }

    if (options.navigation) {
      options.navigation.nextEl = wrapper.querySelector(".aab-arrow-next");
      options.navigation.prevEl = wrapper.querySelector(".aab-arrow-prev");
    }

    if (options.pagination) {
      options.pagination.el = wrapper.querySelector(".swiper-pagination");
    }

    options.observer = true;
    options.observeParents = true;
    options.resizeObserver = true;

    // Manual hover handling to preserve linear motion offsets safely
    var pauseOnHover = options.autoplay && options.autoplay.pauseOnMouseEnter;
    if (pauseOnHover) {
      options.autoplay.pauseOnMouseEnter = false;
    }

    var swiper = new Swiper(swiperContainer, options);

    function syncSlideMargin() {
      var space = swiper.params.spaceBetween || 0;
      swiper.slides.forEach(function (slide) {
        slide.style.marginLeft = space + "px";
      });
    }

    syncSlideMargin();
    swiper.on("resize breakpoint update slidesLengthChange", syncSlideMargin);

    if (pauseOnHover && swiper.autoplay) {
      swiperContainer.addEventListener("mouseenter", function () {
        swiper.autoplay.stop();
      });
      swiperContainer.addEventListener("mouseleave", function () {
        swiper.autoplay.start();
      });
    }
  }

  function initAll() {
    document
      .querySelectorAll(".aab-brand-slider-wrapper")
      .forEach(function (wrapper) {
        initInstance(wrapper);
      });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initAll);
  } else {
    initAll();
  }

  window.aabBrandSlider = initAll;
})();
