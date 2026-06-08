import "../../scss/elements/progressbar.scss";

/**
 * Progress Bar — Bricks Element Script
 *
 * Reads settings from data-aab-progressbar on the root element and
 * renders a line / circle (via ProgressBar.js) or a dots animation.
 */
(function () {
  "use strict";

  function parseSettings(raw) {
    if (!raw) return null;
    try {
      var tmp = document.createElement("textarea");
      tmp.innerHTML = raw;
      return JSON.parse(tmp.value);
    } catch (e) {
      return null;
    }
  }

  function destroyInstance(root) {
    if (root._aabProgressbar) {
      try {
        if (typeof root._aabProgressbar.destroy === "function") {
          root._aabProgressbar.destroy();
        }
      } catch (e) {}
      root._aabProgressbar = null;
    }
    if (root._aabProgressObserver) {
      try {
        root._aabProgressObserver.disconnect();
      } catch (e) {}
      root._aabProgressObserver = null;
    }
    if (root._aabProgressDotTimer) {
      clearInterval(root._aabProgressDotTimer);
      root._aabProgressDotTimer = null;
    }
    // Clear inner content rendered by previous ProgressBar.js instance
    var bar = root.querySelector(".progressbar");
    if (bar && !bar.classList.contains("dots")) {
      bar.innerHTML = "";
    }
    // Reset any active dots
    var activeDots = root.querySelectorAll(".dot.active");
    for (var i = 0; i < activeDots.length; i++) {
      activeDots[i].classList.remove("active");
    }
  }

  function runDotAnimation(root, percent) {
    var dots = root.querySelectorAll(".progressbar .dot");
    if (!dots.length) return;

    var animationDots = Math.floor((percent * 100) / 20);
    if (animationDots > dots.length) animationDots = dots.length;

    var count = 0;
    root._aabProgressDotTimer = setInterval(function () {
      if (count >= animationDots) {
        clearInterval(root._aabProgressDotTimer);
        root._aabProgressDotTimer = null;
        return;
      }
      dots[count].classList.add("active");
      count++;
    }, 500);
  }

  function buildProgressBar(root, settings) {
    if (typeof window.ProgressBar === "undefined") return null;

    var bar = root.querySelector(".progressbar");
    if (!bar) return null;

    var options = {
      strokeWidth: parseFloat(settings["stroke-width"]) || 2,
      trailWidth: parseFloat(settings["trail-width"]) || 1,
      color: settings.color || "#7DDED8",
      trailColor: settings["trail-color"] || "",
      duration: 1400,
    };

    if ("show" === settings["display-percentage"]) {
      // style: null disables ALL ProgressBar.js inline styles
      // (color, position, padding, margin, transform) so the user's
      // typography + color controls (driven by CSS) take effect.
      // Positioning is handled by progressbar.css instead.
      options.text = {
        value: settings.percentage + "%",
        style: null,
      };

      if ("line" === settings["progress-type"]) {
        root.style.setProperty(
          "--aab-progressbar-text-right",
          100 - settings.percentage + "%",
        );
      }
    }

    if ("line" === settings["progress-type"]) {
      return new window.ProgressBar.Line(bar, options);
    }
    if ("circle" === settings["progress-type"]) {
      return new window.ProgressBar.Circle(bar, options);
    }
    return null;
  }

  function initInstance(root) {
    if (!root) return;

    var raw = root.getAttribute("data-aab-progressbar");
    var settings = parseSettings(raw);
    if (!settings) return;

    // Always destroy previous to allow re-init in builder
    destroyInstance(root);

    var percent = (parseFloat(settings.percentage) || 0) / 100;
    var type = settings["progress-type"];

    var run = function () {
      if ("dot" === type) {
        runDotAnimation(root, percent);
        return;
      }
      var instance = buildProgressBar(root, settings);
      if (instance) {
        root._aabProgressbar = instance;
        instance.animate(percent);
      }
    };

    // In Bricks builder iframe, animate immediately (IntersectionObserver
    // can be unreliable inside re-rendered iframes)
    var inBuilder =
      !!(window.bricksData && window.bricksData.builder) ||
      (!!document.body.classList.contains("brx-body") && !!window.frameElement);

    if (inBuilder) {
      run();
      return;
    }

    root._aabProgressObserver = new IntersectionObserver(
      function (entries) {
        for (var i = 0; i < entries.length; i++) {
          if (entries[i].isIntersecting) {
            run();
            if (root._aabProgressObserver) {
              root._aabProgressObserver.disconnect();
              root._aabProgressObserver = null;
            }
            break;
          }
        }
      },
      { threshold: 0 },
    );

    root._aabProgressObserver.observe(root);
  }

  function initAll() {
    var roots = document.querySelectorAll(
      ".aab-progressbar[data-aab-progressbar]",
    );
    for (var i = 0; i < roots.length; i++) {
      initInstance(roots[i]);
    }
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initAll);
  } else {
    initAll();
  }

  // Bricks builder calls window.aabProgressbar() with NO arguments on each re-render.
  window.aabProgressbar = initAll;
})();
