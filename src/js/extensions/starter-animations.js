import "../../scss/extensions/starter-animations.scss";

(function () {

  "use strict";

  /* --------------------------------------------
     GLOBAL OBSERVER (Simple & Stable)
  --------------------------------------------- */

  const observer = new IntersectionObserver((entries) => {

    entries.forEach((entry) => {

      const wrapper = entry.target;

      const isRepeat   = wrapper.classList.contains("aab-repeat-yes");
      const playedOnce = wrapper.classList.contains("aab-played");

      /* ---------------- PLAY ---------------- */

      if (entry.isIntersecting) {

        if (isRepeat) {

          playAnimation(wrapper);

        } else {

          if (!playedOnce) {
            playAnimation(wrapper);
            wrapper.classList.add("aab-played");
          }

        }

      }

    });

  }, { threshold: 0 });

  /* --------------------------------------------
     INIT (single element)
  --------------------------------------------- */

  function initStarterAnimations(wrapper) {

    if (!wrapper || !wrapper.classList) return;

    if (!wrapper.className.includes("aab-starter-animations-")) return;

    if (wrapper.dataset.aabInit) return;
    wrapper.dataset.aabInit = "1";

    handleChar(wrapper);
    handleWave(wrapper);
    handleTypewriter(wrapper);

    requestAnimationFrame(function () {
      observer.observe(wrapper);
    });
  }

  /* --------------------------------------------
     PLAY FUNCTION
  --------------------------------------------- */

  function getAnimTarget(wrapper) {
    return wrapper.firstElementChild || wrapper;
  }

  function playAnimation(wrapper) {

    if (!wrapper) return;

    wrapper.classList.remove("aab-animate");

    const target = getAnimTarget(wrapper);

    if (wrapper.classList.contains("aab-starter-animations-text-char-animate")) {

      if (target) {

        const originalText = target.textContent;
        target.innerHTML = originalText;
        target.dataset.charInit = "";
        handleChar(wrapper);

      }
    }

    if (wrapper.classList.contains("aab-starter-animations-text-wave")) {

      if (target) {

        target.removeAttribute("data-text");
        target.setAttribute("data-text", target.textContent.trim());
        target.dataset.waveInit = "";

      }
    }

    if (wrapper.classList.contains("aab-starter-animations-text-typewriter")) {
      runTypewriter(wrapper);
    }

    void wrapper.offsetWidth;

    wrapper.classList.add("aab-animate");
  }

  /* --------------------------------------------
     CHARACTER SPLIT
  --------------------------------------------- */

  function handleChar(wrapper) {

    if (!wrapper.className.includes("text-char")) return;

    const target = getAnimTarget(wrapper);

    if (!target || target.dataset.charInit) return;

    const text = target.textContent;
    if (!text) return;

    target.innerHTML = "";

    let globalIndex = 0;

    // Split by space but preserve spacing
    const words = text.split(" ");

    words.forEach((word, wordIndex) => {

      const wordSpan = document.createElement("span");
      wordSpan.classList.add("aab-word");
      wordSpan.style.display = "inline-block";

      [...word].forEach((char) => {

        const charSpan = document.createElement("span");
        charSpan.textContent = char;
        charSpan.style.display = "inline-block";
        charSpan.style.setProperty("--i", globalIndex);

        wordSpan.appendChild(charSpan);

        globalIndex++;
      });

      target.appendChild(wordSpan);

      // Add real space between words
      if (wordIndex < words.length - 1) {
        const space = document.createTextNode(" ");
        target.appendChild(space);
      }

    });

    target.dataset.charInit = "1";
  }

  /* --------------------------------------------
     TYPEWRITER
  --------------------------------------------- */

  function parseTimeMs(value) {
    if (!value) return 0;
    var v = ("" + value).trim();
    if (!v) return 0;
    if (v.slice(-2) === "ms") return parseFloat(v) || 0;
    if (v.slice(-1) === "s")  return (parseFloat(v) || 0) * 1000;
    return parseFloat(v) || 0;
  }

  function handleTypewriter(wrapper) {

    if (!wrapper.classList.contains("aab-starter-animations-text-typewriter")) return;

    var target = getAnimTarget(wrapper);
    if (!target || target.dataset.typewriterInit) return;

    target.dataset.typewriterText = target.textContent;
    target.textContent = "";
    target.dataset.typewriterInit = "1";
  }

  function runTypewriter(wrapper) {

    var target = getAnimTarget(wrapper);
    if (!target) return;

    if (target._aabTypewriterTimer) {
      clearInterval(target._aabTypewriterTimer);
      target._aabTypewriterTimer = null;
    }
    if (target._aabTypewriterStart) {
      clearTimeout(target._aabTypewriterStart);
      target._aabTypewriterStart = null;
    }

    var text = target.dataset.typewriterText || "";
    if (!text) return;

    target.textContent = "";

    var cs       = window.getComputedStyle(wrapper);
    var duration = parseTimeMs(cs.getPropertyValue("--aab-duration")) || 1500;
    var delay    = parseTimeMs(cs.getPropertyValue("--aab-delay"));
    var perChar  = duration / Math.max(text.length, 1);
    var i        = 0;

    target._aabTypewriterStart = setTimeout(function () {
      target._aabTypewriterTimer = setInterval(function () {
        if (i >= text.length) {
          clearInterval(target._aabTypewriterTimer);
          target._aabTypewriterTimer = null;
          return;
        }
        target.textContent += text.charAt(i);
        i++;
      }, perChar);
    }, delay);
  }

  /* --------------------------------------------
     WAVE SUPPORT
  --------------------------------------------- */

  function handleWave(wrapper) {

    if (!wrapper.classList.contains("aab-starter-animations-text-wave")) return;

    const target = getAnimTarget(wrapper);

    if (!target) return;

    target.setAttribute("data-text", target.textContent.trim());
    target.dataset.waveInit = "1";
  }

  /* --------------------------------------------
     MANUAL REPLAY
  --------------------------------------------- */

  window.aabReplayAnimation = function (wrapper) {
    playAnimation(wrapper);
  };

  /* --------------------------------------------
     BRICKS BOOTSTRAP — scan DOM + observe mutations
  --------------------------------------------- */

  function scanAll(root) {
    (root || document).querySelectorAll('[class*="aab-starter-animations-"]').forEach(initStarterAnimations);
  }

  function boot() {
    scanAll(document);

    const mo = new MutationObserver((mutations) => {
      mutations.forEach((m) => {
        m.addedNodes.forEach((node) => {
          if (node.nodeType !== 1) return;
          if (node.matches && node.matches('[class*="aab-starter-animations-"]')) {
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

})();
