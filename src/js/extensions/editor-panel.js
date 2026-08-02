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

  function bricksToTextSettings(s) {
    if (!s || typeof s !== "object") return null;
    function g(key, def) {
      var v = s[key];
      return v === undefined || v === null || v === "" ? def : v;
    }
    return {
      type: g("aab_text_animation_type", "none"),
      editor_view: g("aab_enable_editor_view", false),
      trigger: g("aab_text_trigger", "on_scroll"),
      triggerTarget: g("aab_text_trigger_selector", null),
      wrapperMode: g("aab_text_wrapper_mode", "default"),
      startTrigger: g("aab_text_start_trigger", ".start_area"),
      endTrigger: g("aab_text_end_trigger", ".end_area"),
      start: g("aab_scroll_anim_start", "top top"),
      startCustom: g("aab_scroll_anim_start_cust", "top top"),
      end: g("aab_scroll_anim_end", "bottom top"),
      endCustom: g("aab_scroll_anim_end_cust", "bottom top"),
      scrub: g("aab_scroll_scrub", 0),
      markers: g("aae_anim_txt_markers", "false"),
      delay: g("aab_text_delay", 0.15),
      duration: g("aab_text_duration", 1),
      stagger: g("aab_text_stagger", 0.02),
      x: g("aab_translate_x", 20),
      y: g("aab_translate_y", 0),
      rotationDir: g("aab_text_rotation_dir", "x"),
      rotation: g("aab_text_rotation", -80),
      transformOrigin: g("aab_text_transform_origin", "top center -50"),
      scale: g("aab_text_scale", 1.5),
      scaleEase: g("aab_scale_text_ease", "power2.out"),
      scaleBreak: g("aab_text_scale_break", "lines"),
      spinColor: g("aab_spin_text_color", ""),
      spinStart: g("aab_spin_text_start", "top 50%"),
      spinEnd: g("aab_spin_text_end", "bottom 30%"),
      spinScrub: g("aab_spin_text_scrub", false),
      spinToggleActions: g(
        "aab_spin_text_toggle_action",
        "play none none reverse",
      ),
      invertStart: g("aab_anim_invert_start", "top 85%"),
      invertEnd: g("aab_anim_invert_end", "bottom center"),
    };
  }

  function bricksToAdvancedSettings(s) {
    if (!s || typeof s !== "object") return null;
    function g(key, def) {
      var v = s[key];
      return v === undefined || v === null || v === "" ? def : v;
    }
    return {
      type: g("aab_animation", "none"),
      method: g("aab_method", "from"),
      trigger: g("aab_trigger", "on_scroll"),
      triggerTarget: g("aab_trigger_text_selector", null),
      wrapperMode: g("aab_anim_wrapper", "default"),
      startTrigger: g("aab_anim_start_trigger", ".start_area"),
      endTrigger: g("aab_anim_end_trigger", ".end_area"),
      start: g("aab_anim_start", "top top"),
      startCustom: g("aab_anim_start_custom", ""),
      end: g("aab_anim_end", "bottom top"),
      endCustom: g("aab_anim_end_custom", ""),
      markers: g("aab_anim_markers", false),
      delay: g("aab_delay", 0.15),
      duration: g("aab_duration", 1.5),
      ease: g("aab_ease", "power2.out"),
      fadeFrom: g("fade_from", "bottom"),
      fadeOffset: g("aab_fade_offset", 50),
      startScale: g("aab_start_scale", 0.7),
      rotationDir: g("aab_rotation_direction", "x"),
      rotation: g("aab_rotation_value", -80),
      transformOrigin: g("aab_transform_origin", "top center -50"),
      customProps: g("aab_ani_custom_props", []),
      enableInEditor: !!s.aab_enable_animation_editor,
    };
  }

  function bricksToImageSettings(s) {
    if (!s || typeof s !== "object") return null;
    function g(key, def) {
      var v = s[key];
      return v === undefined || v === null || v === "" ? def : v;
    }
    return {
      image_animation_type: g("aab_image_animation", "none"),
      animation_to: g("aab_animation_to", "right"),
      scale_start: g("aab_scale_start", 0.5),
      scale_end: g("aab_scale_end", 1),
      animation_start: g("aab_animation_start", "top center"),
      animation_custom_start: g("aab_animation_custom_start", "top 90%"),
      image_ease: g("aab_image_ease", "power2.out"),
      img_animation_editor: !!s.aab_img_animation_editor,
    };
  }

  function bricksToMouseMoveSettings(s) {
    if (!s || typeof s !== "object") return null;
    function g(key, def) {
      var v = s[key];
      return v === undefined || v === null || v === "" ? def : v;
    }
    return {
      enabled: !!s.aab_enable_mouse_move_effect,
      move_x: g("aab_mouse_move_x", 70),
      move_y: g("aab_mouse_move_y", 70),
      duration: g("aab_mouse_move_duration", 0.5),
      custom: g("aab_mouse_move_custom", ""),
    };
  }

  function bricksToImageRevealSettings(s) {
    if (!s || typeof s !== "object") return null;
    function g(key, def) {
      var v = s[key];
      return v === undefined || v === null || v === "" ? def : v;
    }
    var img = s["aab_reveal_image"];
    var imgUrl = img && img.url ? img.url : "";
    return {
      enabled: !!s["aab_image_reveal_enable"],
      enabled_editor: !!s["aab_image_reveal_editor"],
      image: imgUrl,
      width: g("aab_reveal_image_width", 250),
      height: g("aab_reveal_image_height", 250),
      top: g("aab_reveal_image_top", 0),
      left: g("aab_reveal_image_left", 0),
      zindex: g("aab_reveal_image_zindex", 9999),
    };
  }

  // Build a sparse responsive map for a Bricks setting. Bricks stores
  // per-breakpoint variants as `<key>:<bp_key>` siblings; the live data
  // attribute the iframe consumes is responsive (PHP normalize+compact),
  // but settings sent over postMessage from the panel were previously
  // flattened to the base-only value, which made the preview always show
  // the desktop text regardless of the active device. Returns a scalar
  // when only the base is set so the iframe-side `pickResponsive` can
  // short-circuit; otherwise returns a {bp_key: value} map that the
  // shared `bricksflyResponsive.resolveResponsive` can cascade.
  function bricksToResponsive(s, baseKey, def) {
    var bps =
      window.bricksflyBreakpoints && window.bricksflyBreakpoints.length
        ? window.bricksflyBreakpoints
        : null;

    var rawBase = s[baseKey];
    var baseVal =
      rawBase === undefined || rawBase === null || rawBase === ""
        ? def
        : rawBase;

    if (!bps) return baseVal;

    var baseBpKey = null;
    for (var i = 0; i < bps.length; i++) {
      if (bps[i].base) {
        baseBpKey = bps[i].key;
        break;
      }
    }
    if (!baseBpKey) return baseVal;

    var map = {};
    map[baseBpKey] = baseVal;
    var hasOverride = false;
    for (var j = 0; j < bps.length; j++) {
      var k = bps[j].key;
      if (k === baseBpKey) continue;
      var rk = baseKey + ":" + k;
      if (
        Object.prototype.hasOwnProperty.call(s, rk) &&
        s[rk] !== null &&
        s[rk] !== ""
      ) {
        map[k] = s[rk];
        hasOverride = true;
      }
    }
    return hasOverride ? map : baseVal;
  }

  function bricksToCursorHoverSettings(s) {
    if (!s || typeof s !== "object") return null;
    function g(key, def) {
      var v = s[key];
      return v === undefined || v === null || v === "" ? def : v;
    }
    return {
      enabled: !!s.zz3_che5x_enabled,
      editor_enabled: !!s.zz3_che5x_enabled_editor,
      text: bricksToResponsive(s, "zz3_che5x_text", "View"),
      typography: s.zz3_che5x_cursor_typography || null,
      background: s.zz3_che5x_cursor_background || null,
      width: g("zz3_che5x_cursor_width", 80),
      height: g("zz3_che5x_cursor_height", 80),
      border: s.zz3_che5x_cursor_border || null,
    };
  }

  function bricksToTiltSettings(s) {
    if (!s || typeof s !== "object") return null;
    function g(key, def) {
      var v = s[key];
      return v === undefined || v === null || v === "" ? def : v;
    }
    return {
      enabled: !!s.aab_enable_tilt,
      max_tilt: g("aab_max_tilt", 20),
      perspective: g("aab_tilt_perspective", 1000),
      scale: g("aab_tilt_scale", 1.2),
      duration: g("aab_tilt_duration", 0.4),
    };
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
