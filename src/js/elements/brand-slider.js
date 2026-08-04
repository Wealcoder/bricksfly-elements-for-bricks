import "../../scss/elements/brand-slider.scss";

(function () {
  "use strict";

  function initInstance(wrapper) {
    if (!wrapper) return;

    var swiperContainer = wrapper.querySelector(".swiper");
    if (!swiperContainer) return;

    var baseStr = wrapper.getAttribute("data-swiper");
    if (!baseStr) return;

    var baseOptions;
    try {
      baseOptions = JSON.parse(baseStr);
    } catch (e) {
      return;
    }

    // Per-range behavioral options: { minWidth: {full options} }. Swiper cannot change
    // speed/autoplay/loop/navigation/pagination via its own `breakpoints`, so we re-init
    // Swiper with the active range's options whenever the viewport crosses a range.
    var responsive = {};
    var responsiveStr = wrapper.getAttribute("data-swiper-responsive");
    if (responsiveStr) {
      try {
        responsive = JSON.parse(responsiveStr) || {};
      } catch (e) {
        responsive = {};
      }
    }

    // Ascending list of responsive min-width keys.
    var rangeKeys = Object.keys(responsive)
      .map(function (k) {
        return parseInt(k, 10);
      })
      .filter(function (k) {
        return !isNaN(k);
      })
      .sort(function (a, b) {
        return a - b;
      });

    // Mobile-first resolution: the largest min-width key <= viewport width wins.
    // Returns the matched key, or -1 for the base range (no responsive override).
    function activeRangeKey() {
      var w = window.innerWidth;
      var match = -1;
      for (var i = 0; i < rangeKeys.length; i++) {
        if (rangeKeys[i] <= w) match = rangeKeys[i];
        else break;
      }
      return match;
    }

    function optionsForKey(key) {
      var src = key === -1 ? baseOptions : responsive[key] || baseOptions;
      // Shallow clone so per-instance element refs don't leak across re-inits.
      var opts = {};
      for (var k in src) {
        if (Object.prototype.hasOwnProperty.call(src, k)) opts[k] = src[k];
      }

      // Resolve nav/pagination selectors to this wrapper's elements.
      if (opts.navigation) {
        opts.navigation = {
          nextEl: wrapper.querySelector(".bricksfly-arrow-next"),
          prevEl: wrapper.querySelector(".bricksfly-arrow-prev"),
        };
      }
      if (opts.pagination) {
        opts.pagination = {
          el: wrapper.querySelector(".swiper-pagination"),
          clickable: true,
        };
      }

      // Keep the native layout breakpoints so slidesPerView/spaceBetween still react
      // instantly within the active behavioral range.
      if (baseOptions.breakpoints) opts.breakpoints = baseOptions.breakpoints;

      opts.observer = true;
      opts.observeParents = true;
      opts.resizeObserver = true;

      return opts;
    }

    function build(key) {
      if (swiperContainer.swiper) {
        try {
          swiperContainer.swiper.destroy(true, true);
        } catch (e) {}
      }

      var options = optionsForKey(key);

      // Manual hover handling to preserve linear motion offsets safely.
      var pauseOnHover = options.autoplay && options.autoplay.pauseOnMouseEnter;
      if (pauseOnHover) options.autoplay.pauseOnMouseEnter = false;

      // The arrow / pagination elements are always present in the DOM (so any range can
      // use them), but Swiper only wires up the modules it's given. Toggle their visibility
      // to match the active range, otherwise disabled arrows would still show.
      var prevEl = wrapper.querySelector(".bricksfly-arrow-prev");
      var nextEl = wrapper.querySelector(".bricksfly-arrow-next");
      var pagEl = wrapper.querySelector(".swiper-pagination");
      var navOn = !!options.navigation;
      var pagOn = !!options.pagination;
      if (prevEl) prevEl.style.display = navOn ? "" : "none";
      if (nextEl) nextEl.style.display = navOn ? "" : "none";
      if (pagEl) pagEl.style.display = pagOn ? "" : "none";

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

    var currentKey = activeRangeKey();
    build(currentKey);

    // Re-init only when the active behavioral range actually changes (avoids thrashing
    // on every resize tick). Layout changes within a range are handled by Swiper natively.
    if (rangeKeys.length) {
      var resizeTimer = null;
      window.addEventListener("resize", function () {
        if (resizeTimer) window.clearTimeout(resizeTimer);
        resizeTimer = window.setTimeout(function () {
          var key = activeRangeKey();
          if (key !== currentKey) {
            currentKey = key;
            build(key);
          }
        }, 150);
      });
    }
  }

  function initAll() {
    document
      .querySelectorAll(".bricksfly-brand-slider-wrapper")
      .forEach(function (wrapper) {
        initInstance(wrapper);
      });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initAll);
  } else {
    initAll();
  }

  window.bricksflyBrandSlider = initAll;
})();
