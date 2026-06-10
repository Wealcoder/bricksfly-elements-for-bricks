import "../../../scss/extensions/starter-animations.scss";
import {
  getAnimTarget,
  handleChar,
  handleWave,
  handleTypewriter,
  runTypewriter,
} from "./shared.js";

/**
 * Frontend-only bundle.
 * Drives IntersectionObserver-based animation on live pages.
 * This file must NOT be loaded inside the Bricks builder iframe —
 * starter-animations-preview.js handles that context instead.
 *
 * Safety guard: if this file is accidentally loaded inside the builder
 * iframe, we add the aab-builder class and bail so elements stay visible.
 */

function isBuilderIframe() {
  try {
    if (window.self === window.top) return false;
    return !!window.parent.document.getElementById("bricks-builder-iframe");
  } catch (e) {
    return false;
  }
}

if (isBuilderIframe()) {
  // Wrong context — mark the document and stop. The preview bundle handles
  // animation playback inside the builder iframe.
  const markBuilder = () => {
    document.documentElement.classList.add("aab-builder");
    document.body && document.body.classList.add("aab-builder");
  };
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", markBuilder);
  } else {
    markBuilder();
  }
} else {
  initFrontend();
}

function initFrontend() {
  // KEY BEHAVIOUR: aab-preinit is added only after the observer registers the
  // element. CSS hidden states are gated on .aab-preinit, so there is no flash
  // of hidden content on slow connections — elements stay visible until JS runs.

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        const wrapper = entry.target;
        const isRepeat = wrapper.classList.contains("aab-repeat-yes");
        const playedOnce = wrapper.classList.contains("aab-played");

        if (entry.isIntersecting) {
          if (isRepeat) {
            playAnimation(wrapper);
          } else if (!playedOnce) {
            playAnimation(wrapper);
            wrapper.classList.add("aab-played");
          }
        }
      });
    },
    { threshold: 0 },
  );

  function initStarterAnimations(wrapper) {
    if (!wrapper || !wrapper.classList) return;
    if (!wrapper.className.includes("aab-starter-animations-")) return;
    if (wrapper.dataset.aabInit) return;
    wrapper.dataset.aabInit = "1";

    handleChar(wrapper);
    handleWave(wrapper);
    handleTypewriter(wrapper);

    requestAnimationFrame(function () {
      wrapper.classList.add("aab-preinit");
      observer.observe(wrapper);
    });
  }

  function playAnimation(wrapper) {
    if (!wrapper) return;

    wrapper.classList.remove("aab-animate", "aab-preinit");

    const target = getAnimTarget(wrapper);

    if (
      wrapper.classList.contains("aab-starter-animations-text-char-animate")
    ) {
      if (target) {
        const originalText = target.textContent;
        target.innerHTML = originalText;
        delete target.dataset.charInit;
        handleChar(wrapper);
      }
    }

    if (wrapper.classList.contains("aab-starter-animations-text-wave")) {
      if (target) {
        target.removeAttribute("data-text");
        target.setAttribute("data-text", target.textContent.trim());
        delete target.dataset.waveInit;
      }
    }

    if (wrapper.classList.contains("aab-starter-animations-text-typewriter")) {
      runTypewriter(wrapper);
    }

    void wrapper.offsetWidth;

    wrapper.classList.add("aab-preinit");
    wrapper.classList.add("aab-animate");
  }

  // Public API — may be called by external code to replay an animation.
  window.aabReplayAnimation = function (wrapper) {
    playAnimation(wrapper);
  };

  function scanAll(root) {
    (root || document)
      .querySelectorAll('[class*="aab-starter-animations-"]')
      .forEach(initStarterAnimations);
  }

  function boot() {
    scanAll(document);

    const mo = new MutationObserver((mutations) => {
      mutations.forEach((m) => {
        m.addedNodes.forEach((node) => {
          if (node.nodeType !== 1) return;
          if (
            node.matches &&
            node.matches('[class*="aab-starter-animations-"]')
          ) {
            initStarterAnimations(node);
          }
          if (node.querySelectorAll) {
            scanAll(node);
          }
        });
      });
    });
    mo.observe(document.body, { childList: true, subtree: true });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", boot);
  } else {
    boot();
  }
}
