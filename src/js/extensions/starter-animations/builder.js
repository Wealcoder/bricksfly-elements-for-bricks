import {
  getAnimTarget,
  handleChar,
  handleWave,
  handleTypewriter,
  runTypewriter,
} from "./shared.js";

/**
 * Builder-preview bundle.
 * Loaded only inside the Bricks builder iframe. Handles the "Play Animation"
 * button by receiving a postMessage from editor-panel.js and replaying the
 * animation with the panel's current settings applied directly to the DOM.
 *
 * No IntersectionObserver — elements stay fully visible in the builder;
 * only an explicit Play click triggers the hidden-state / animate sequence.
 */

// Mark the document so CSS knows we're in the builder iframe.
const markBuilder = () => {
  document.documentElement.classList.add("aab-builder");
  document.body && document.body.classList.add("aab-builder");
};
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", markBuilder);
} else {
  markBuilder();
}

// Animation-type class names emitted by class-aab-starter-animations.php.
const ALL_TYPES = [
  "reveal",
  "scale-up",
  "slide",
  "skew-reveal",
  "flip",
  "text-glow",
  "text-typewriter",
  "text-mask-wipe",
  "text-wave",
  "text-bg-clip",
  "text-char-animate",
  "zoom-in",
  "rotate-in",
  "combo-reveal",
  "text-pop",
];
const ALL_DIRECTIONS = ["bottom", "top", "left", "right", "center"];
const ALL_CHAR_PRESETS = [
  "revolve",
  "ball",
  "slide",
  "revolve_drop",
  "drop_vanish",
  "twister",
];

/**
 * Applies class-based settings from the panel onto the canvas element.
 * Bricks live-updates CSS variables (duration, delay, ease) but PHP-rendered
 * classes (type, direction, axis, preset, repeat) stay stale until page
 * refresh — this patches them before every play click.
 */
function applyStarterAnimClasses(el, settings) {
  ALL_TYPES.forEach((t) => el.classList.remove("aab-starter-animations-" + t));

  ALL_DIRECTIONS.forEach((d) => {
    el.classList.remove("aab-reveal-" + d, "aab-slide-" + d);
  });
  el.classList.remove(
    "aab-reveal-yes",
    "aab-flip-axis-x",
    "aab-flip-axis-y",
    "aab-flip-axis-container-x",
    "aab-flip-axis-container-y",
    "aab-repeat-yes",
    "aab-repeat-no",
  );
  ALL_CHAR_PRESETS.forEach((p) => el.classList.remove("aab-char-preset-" + p));

  if (!settings.type || settings.type === "none") return;

  el.classList.add("aab-starter-animations-" + settings.type);

  if (!settings.isContainer) {
    el.classList.add("aab-target-self");
  }

  if (settings.type === "reveal") {
    el.classList.add("aab-reveal-" + (settings.revealDirection || "bottom"));
    if (settings.revealFade) el.classList.add("aab-reveal-yes");
  }
  if (settings.type === "slide") {
    el.classList.add("aab-slide-" + (settings.slideDirection || "bottom"));
  }
  if (settings.type === "flip") {
    el.classList.add(
      settings.isContainer
        ? "aab-flip-axis-container-" + (settings.flipAxis || "x")
        : "aab-flip-axis-" + (settings.flipAxis || "x"),
    );
  }
  if (settings.type === "text-char-animate") {
    el.classList.add("aab-char-preset-" + (settings.charPreset || "revolve"));
  }
  if (settings.repeat === "yes") {
    el.classList.add("aab-repeat-yes");
  }
  // text-bg-clip needs --aab-bg-text-image as an inline CSS variable because
  // PHP sets it via apply_render_classes, not a live Bricks css[] property.
  if (settings.type === "text-bg-clip" && settings.bgTextImageUrl) {
    el.style.setProperty(
      "--aab-bg-text-image",
      "url(" + settings.bgTextImageUrl + ")",
    );
  }
}

// Fires when the editor panel "Play" button is clicked.
window.addEventListener("message", function (e) {
  if (!e.data || e.data.type !== "aab-play-starter-animation") return;

  const elementId = e.data.elementId;
  if (!elementId) return;

  const el = document.querySelector('[data-id="' + elementId + '"]');
  if (!el) return;

  // Remove any leftover state from a previous play.
  el.classList.remove(
    "aab-animate",
    "aab-preinit",
    "aab-playing",
    "aab-played",
  );

  // Apply class-based settings from the panel.
  const settings = e.data.settings;
  if (settings && settings.type && settings.type !== "none") {
    applyStarterAnimClasses(el, settings);
  }

  // Reset init flags and DOM so handlers re-run cleanly for the (possibly new) type.
  const target = getAnimTarget(el);
  if (target) {
    // Restore text cleared by the typewriter handler.
    if (target.dataset.typewriterText) {
      if (target._aabTypewriterTimer) {
        clearInterval(target._aabTypewriterTimer);
        target._aabTypewriterTimer = null;
      }
      if (target._aabTypewriterStart) {
        clearTimeout(target._aabTypewriterStart);
        target._aabTypewriterStart = null;
      }
      target.textContent = target.dataset.typewriterText;
      delete target.dataset.typewriterText;
      delete target.dataset.typewriterInit;
    }
    // Collapse char spans back to plain text.
    if (target.dataset.charInit) {
      target.innerHTML = target.textContent;
      delete target.dataset.charInit;
    }
    // Reset wave so data-text is re-applied on next init.
    if (target.dataset.waveInit) {
      target.removeAttribute("data-text");
      delete target.dataset.waveInit;
    }
    delete el.dataset.aabInit;
  }

  // Re-run init handlers for the current animation type.
  handleChar(el);
  handleWave(el);
  handleTypewriter(el);

  // Typewriter is JS-driven — aab-animate alone doesn't type the text.
  if (el.classList.contains("aab-starter-animations-text-typewriter")) {
    runTypewriter(el);
  }

  // Force reflow so class removal takes effect before re-adding.
  void el.offsetWidth;

  // aab-playing (not aab-preinit) gates hidden states in the builder.
  el.classList.add("aab-playing");

  requestAnimationFrame(function () {
    el.classList.add("aab-animate");
  });
});
