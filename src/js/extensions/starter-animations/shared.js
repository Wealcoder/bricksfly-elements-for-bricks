/**
 * Shared animation helpers used by both the frontend and builder-preview
 * bundles. Not a webpack entry point — imported by the two consumer files.
 */

export function parseTimeMs(value) {
  if (!value) return 0;
  const v = ("" + value).trim();
  if (!v) return 0;
  if (v.slice(-2) === "ms") return parseFloat(v) || 0;
  if (v.slice(-1) === "s") return (parseFloat(v) || 0) * 1000;
  return parseFloat(v) || 0;
}

export function getAnimTarget(wrapper) {
  return wrapper.firstElementChild || wrapper;
}

export function handleChar(wrapper) {
  if (!wrapper.className.includes("text-char")) return;

  const target = getAnimTarget(wrapper);
  if (!target || target.dataset.charInit) return;

  const text = target.textContent;
  if (!text) return;

  target.innerHTML = "";
  let globalIndex = 0;
  const words = text.split(" ");

  words.forEach((word, wordIndex) => {
    const wordSpan = document.createElement("span");
    wordSpan.classList.add("bricksfly-word");
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

    if (wordIndex < words.length - 1) {
      target.appendChild(document.createTextNode(" "));
    }
  });

  target.dataset.charInit = "1";
}

export function handleTypewriter(wrapper) {
  if (!wrapper.classList.contains("bricksfly-starter-animations-text-typewriter"))
    return;
  const target = getAnimTarget(wrapper);
  if (!target || target.dataset.typewriterInit) return;

  target.dataset.typewriterText = target.textContent;
  target.textContent = "";
  target.dataset.typewriterInit = "1";
}

export function runTypewriter(wrapper) {
  const target = getAnimTarget(wrapper);
  if (!target) return;

  if (target._aabTypewriterTimer) {
    clearInterval(target._aabTypewriterTimer);
    target._aabTypewriterTimer = null;
  }
  if (target._aabTypewriterStart) {
    clearTimeout(target._aabTypewriterStart);
    target._aabTypewriterStart = null;
  }

  const text = target.dataset.typewriterText || "";
  if (!text) return;

  target.textContent = "";

  const cs = window.getComputedStyle(wrapper);
  const duration =
    parseTimeMs(cs.getPropertyValue("--bricksfly-duration")) || 1500;
  const delay = parseTimeMs(cs.getPropertyValue("--bricksfly-delay"));
  const perChar = duration / Math.max(text.length, 1);
  let i = 0;

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

export function handleWave(wrapper) {
  if (!wrapper.classList.contains("bricksfly-starter-animations-text-wave")) return;
  const target = getAnimTarget(wrapper);
  if (!target) return;
  target.setAttribute("data-text", target.textContent.trim());
  target.dataset.waveInit = "1";
}
