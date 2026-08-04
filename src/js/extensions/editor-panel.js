(function () {
  "use strict";

  var lastClickedElementId = null;

  function getBricksGlobalProperties() {
    var roots = [".brx-body", "#bricks-builder", "#app", "body"];
    for (var i = 0; i < roots.length; i++) {
      var node = document.querySelector(roots[i]);
      var app = node && node.__vue_app__;
      var gp =
        app &&
        app._context &&
        app._context.config &&
        app._context.config.globalProperties;
      if (gp) return gp;
    }

    var all = document.querySelectorAll("div, section, main, body");
    for (var j = 0; j < all.length; j++) {
      if (all[j].__vue_app__) {
        var ctx = all[j].__vue_app__._context;
        if (ctx && ctx.config && ctx.config.globalProperties)
          return ctx.config.globalProperties;
      }
    }
    return null;
  }

  function findActiveIdInObject(obj, depth) {
    if (!obj || typeof obj !== "object" || depth > 3) return null;
    if (obj.activeElement && obj.activeElement.id) return obj.activeElement.id;
    if (obj.activeId) return obj.activeId;
    for (var k in obj) {
      try {
        var v = obj[k];
        if (v && typeof v === "object") {
          if (v.activeElement && v.activeElement.id) return v.activeElement.id;
          if (v.activeId) return v.activeId;
        }
      } catch (e) {}
    }
    return null;
  }

  function getActiveElement() {
    try {
      var gp = getBricksGlobalProperties();
      if (gp && gp.$_state && gp.$_state.activeElement)
        return gp.$_state.activeElement;
      if (gp && gp.$_activeElement) {
        if (gp.$_activeElement.value) return gp.$_activeElement.value;
        return gp.$_activeElement;
      }
    } catch (e) {}
    return null;
  }

  function getActiveElementId() {
    var ae = getActiveElement();

    if (ae && ae.id) return ae.id;

    try {
      var gp = getBricksGlobalProperties();
      if (gp && gp.$_state && gp.$_state.activeId) return gp.$_state.activeId;
      if (gp && gp.$_data) {
        var found = findActiveIdInObject(gp.$_data, 0);
        if (found) return found;
      }
    } catch (e) {}

    try {
      var iframe = document.getElementById("bricks-builder-iframe");
      var doc =
        iframe && (iframe.contentDocument || iframe.contentWindow.document);
      if (doc) {
        var sel = doc.querySelector(
          '.brxe-active, .bricks-element-active, [data-active="true"]',
        );
        if (sel) return sel.getAttribute("data-id");
      }
    } catch (e) {}

    if (lastClickedElementId) return lastClickedElementId;

    return null;
  }

  function bricksToStarterAnimSettings(s) {
    if (!s || typeof s !== "object") return null;
    function g(key, def) {
      var v = s[key];
      return v === undefined || v === null || v === "" ? def : v;
    }

    // Container elements use a separate control key
    var containerType = g("_bricksfly_starter_anim_container", "none");
    if (containerType && containerType !== "none") {
      return {
        isContainer: true,
        type: containerType,
        slideDirection: g("_bricksfly_slide_direction_container", "bottom"),
        flipAxis: g("_bricksfly_flip_axis_container", "x"),
        repeat: g("_bricksfly_repeat_on_enter_container", "no"),
      };
    }

    var bgImg = s._bricksfly_bg_text_image;
    return {
      isContainer: false,
      type: g("_bricksfly_starter_anim", "none"),
      revealDirection: g("_bricksfly_reveal_direction", "bottom"),
      revealFade: !!s._bricksfly_reveal_fade,
      slideDirection: g("_bricksfly_slide_direction", "bottom"),
      flipAxis: g("_bricksfly_flip_axis", "x"),
      charPreset: g("_bricksfly_char_preset", "revolve"),
      repeat: g("_bricksfly_repeat_on_enter", "no"),
      bgTextImageUrl:
        bgImg && typeof bgImg === "object" && bgImg.url ? bgImg.url : "",
    };
  }

  function attachIframeClickTracker() {
    var iframe = document.getElementById("bricks-builder-iframe");
    if (!iframe) {
      setTimeout(attachIframeClickTracker, 500);
      return;
    }

    function attach() {
      try {
        var doc = iframe.contentDocument || iframe.contentWindow.document;
        if (!doc) return;
        if (doc._aabClickTrackerAttached) return;
        doc._aabClickTrackerAttached = true;

        doc.addEventListener(
          "click",
          function (e) {
            var node = e.target;
            while (node && node !== doc.body) {
              if (node.hasAttribute && node.hasAttribute("data-id")) {
                lastClickedElementId = node.getAttribute("data-id");
                break;
              }
              node = node.parentElement;
            }
          },
          true,
        );

        doc.addEventListener(
          "mouseover",
          function (e) {
            var node = e.target;
            while (node && node !== doc.body) {
              if (
                node.hasAttribute &&
                node.hasAttribute("data-id") &&
                node.matches(
                  '.brxe-active, .bricks-element-active, [data-active="true"]',
                )
              ) {
                lastClickedElementId = node.getAttribute("data-id");
                return;
              }
              node = node.parentElement;
            }
          },
          true,
        );
      } catch (e) {
        console.warn("[AAB] Iframe access error:", e);
      }
    }

    if (
      iframe.contentDocument &&
      iframe.contentDocument.readyState === "complete"
    ) {
      attach();
    }
    iframe.addEventListener("load", attach);
  }

  function playActiveAnimation() {
    var iframe = document.getElementById("bricks-builder-iframe");
    if (!iframe || !iframe.contentWindow) {
      console.warn("[AAB] Bricks iframe not found.");
      return;
    }

    var elementId = getActiveElementId();
    if (!elementId) {
      console.warn(
        "[AAB] Could not determine active element ID. Click the element in the canvas first, then click PLAY ANIMATION.",
      );
      return;
    }

    var ae = getActiveElement();
    var s = ae && ae.settings ? ae.settings : null;
    var dispatched = [];

    // console.log("[AAB] PLAY clicked — elementId:", elementId, "| settings:", s);

    function clone(obj) {
      try {
        return JSON.parse(JSON.stringify(obj));
      } catch (e) {
        return null;
      }
    }

    var starterType = s && s._bricksfly_starter_anim;
    var containerType = s && s._bricksfly_starter_anim_container;

    if (starterType && starterType !== "none" && s._bricksfly_anim_editor_enabled) {
      iframe.contentWindow.postMessage(
        {
          type: "aab-play-starter-animation",
          elementId: elementId,
          settings: clone(bricksToStarterAnimSettings(s)),
        },
        "*",
      );
      dispatched.push("starter");
    } else if (
      containerType &&
      containerType !== "none" &&
      s._bricksfly_anim_editor_enabled_container
    ) {
      iframe.contentWindow.postMessage(
        {
          type: "aab-play-starter-animation",
          elementId: elementId,
          settings: clone(bricksToStarterAnimSettings(s)),
        },
        "*",
      );
      dispatched.push("starter-container");
    }
  }

  window.bricksflyPlayTextAnimation = playActiveAnimation;

  window.bricksflyDebugBricksState = function () {
    var gp = getBricksGlobalProperties();
    console.log("[AAB debug] globalProperties:", gp);
    console.log("[AAB debug] $_state:", gp && gp.$_state);
    console.log(
      "[AAB debug] $_state.activeElement:",
      gp && gp.$_state && gp.$_state.activeElement,
    );
    console.log("[AAB debug] $_data:", gp && gp.$_data);
    console.log("[AAB debug] $_activeElement:", gp && gp.$_activeElement);
    console.log(
      "[AAB debug] window.bricksData.activeId:",
      window.bricksData && window.bricksData.activeId,
    );
    console.log("[AAB debug] lastClickedElementId:", lastClickedElementId);
    var iframe = document.getElementById("bricks-builder-iframe");
    console.log("[AAB debug] iframe:", iframe);
    try {
      var doc =
        iframe && (iframe.contentDocument || iframe.contentWindow.document);
      console.log(
        "[AAB debug] iframe .brxe-active:",
        doc && doc.querySelector(".brxe-active"),
      );
      console.log(
        "[AAB debug] iframe [data-text_animation]:",
        doc && doc.querySelectorAll("[data-text_animation]").length,
      );
    } catch (e) {
      console.log("[AAB debug] iframe access error:", e);
    }
    console.log("[AAB debug] resolved active id:", getActiveElementId());
  };

  document.addEventListener("click", function (e) {
    var btn =
      e.target &&
      e.target.closest &&
      e.target.closest(".aab-free-play-animation");
    if (!btn) return;
    e.preventDefault();
    playActiveAnimation();
  });

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", attachIframeClickTracker);
  } else {
    attachIframeClickTracker();
  }
})();
