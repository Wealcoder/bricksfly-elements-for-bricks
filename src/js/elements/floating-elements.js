import "../../scss/elements/floating-elements.scss";

// (function () {
//   function aabFloatingElements() {
//     var WATCHED = [
//       "size",
//       "offsetX",
//       "offsetXEnd",
//       "offsetY",
//       "offsetYEnd",
//       "horizontalOrientation",
//       "verticalOrientation",
//     ];

//     // Snapshot all responsive variants of watched fields for all repeater items
//     function snapshot(items) {
//       if (!Array.isArray(items)) return "";
//       return JSON.stringify(
//         items.map(function (item) {
//           var out = {};
//           Object.keys(item).forEach(function (k) {
//             WATCHED.forEach(function (f) {
//               if (k === f || k.startsWith(f + ":")) out[k] = item[k];
//             });
//           });
//           return out;
//         }),
//       );
//     }

//     function init() {
//       // bricksData is the global Vuex store state proxy exposed by Bricks
//       var bd = window.bricksData;
//       if (!bd || !bd.elements) {
//         setTimeout(init, 500);
//         return;
//       }

//       var lastSnap = {};

//       setInterval(function () {
//         // Find all aab-floating-elements elements on the page
//         Object.values(bd.elements).forEach(function (el) {
//           if (!el || el.name !== "aab-floating-elements") return;
//           if (!el.settings || !el.settings.floating_items) return;

//           var id = el.id;
//           var items = el.settings.floating_items;
//           var snap = snapshot(items);

//           console.log("dhoeiur");

//           if (lastSnap[id] === snap) return; // nothing changed
//           lastSnap[id] = snap;

//           // Stamp the snapshot hash into each repeater item's _cssSnapshot field.
//           // This mutates bricksData.elements directly — Bricks' Vue reactivity
//           // picks it up and triggers a PHP rerender of the element.
//           var hash =
//             snap.length +
//             "-" +
//             snap.split("").reduce(function (a, c) {
//               return ((a << 5) - a + c.charCodeAt(0)) | 0;
//             }, 0);

//           items.forEach(function (item) {
//             item._cssSnapshot = hash;
//           });
//         });
//       }, 300);
//     }

//     if (document.readyState === "loading") {
//       document.addEventListener("DOMContentLoaded", init);
//     } else {
//       init();
//     }
//   }

//   window.bricksflyFloatingElements = bricksflyFloatingElements;
// })();

(function () {
  var WATCHED_KEYS = ["size", "offsetX", "offsetXEnd", "offsetY", "offsetYEnd"];

  // This script is enqueued on the element (bricks_is_builder() gate in
  // enqueue_scripts()), so it runs inside the CANVAS IFRAME's own document.
  // The Bricks settings panel is rendered in the top-level builder window,
  // not the iframe — querying/listening on the bare `document` here can
  // never see it. Everything below has to go through `window.parent.document`
  // instead (same-origin with the canvas iframe, so this is safe).
  function getPanelDocument() {
    try {
      if (window.parent && window.parent.document) {
        return window.parent.document;
      }
    } catch (e) {
      // Cross-origin (shouldn't happen for the Bricks canvas) — bail quietly.
    }
    return null;
  }

  function triggerSnapshotUpdate() {
    var panelDoc = getPanelDocument();
    if (!panelDoc) return;

    // Bricks puts the field key in `data-control-key` on the *wrapper* div
    // around each repeater sub-field (see main.min.js: `data-control-key: r`
    // on the ".repeater-item-inner" div) — the actual <input> rendered
    // inside it carries no such attribute. So the selector has to look for
    // an input *descending from* that wrapper, not carrying the attribute
    // itself.
    var inputs = panelDoc.querySelectorAll(
      '[data-control-key="_cssSnapshot"] input, ' +
        '[data-control-key="_cssSnapshot"] textarea',
    );

    inputs.forEach(function (input) {
      var next = String(Date.now());

      // Use the native setter (from the panel's own window) so React/Vue's
      // patched `value` property doesn't swallow the change silently.
      var nativeInputValueSetter = Object.getOwnPropertyDescriptor(
        window.parent.HTMLInputElement.prototype,
        "value",
      ).set;
      nativeInputValueSetter.call(input, next);

      input.dispatchEvent(new Event("input", { bubbles: true }));
      input.dispatchEvent(new Event("change", { bubbles: true }));
    });
  }

  // _cssSnapshot is a real (but purely internal) Bricks control so Bricks'
  // reactivity can see it — it has no reason to ever be user-facing. Best
  // effort visual hide: walk up one level from the input to its control
  // row and hide that, re-run whenever the panel changes since Bricks
  // mounts/unmounts repeater item controls dynamically.
  function hideSnapshotControls() {
    var panelDoc = getPanelDocument();
    if (!panelDoc) return;

    var wrappers = panelDoc.querySelectorAll('[data-control-key="_cssSnapshot"]');
    wrappers.forEach(function (wrapper) {
      wrapper.style.display = "none";
    });
  }

  function attachPanelListener() {
    var panelDoc = getPanelDocument();
    if (!panelDoc) {
      setTimeout(attachPanelListener, 500);
      return;
    }

    var debounceTimer = null;

    panelDoc.addEventListener(
      "input",
      function (e) {
        hideSnapshotControls();

        var target = e.target;
        if (!target) return;

        // The field key lives in `data-control-key` on the wrapper div
        // around each repeater sub-field, not on the <input> that actually
        // fired this event — walk up to find it.
        var wrapper = target.closest
          ? target.closest("[data-control-key]")
          : null;
        var key = wrapper ? wrapper.getAttribute("data-control-key") || "" : "";
        var isWatched = WATCHED_KEYS.some(function (k) {
          return key.indexOf(k) !== -1;
        });

        if (!isWatched) return;

        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(triggerSnapshotUpdate, 150);
      },
      true,
    ); // capture phase so we catch all bubbled input events

    // Repeater items mount their controls lazily (opening/reordering items),
    // so also sweep periodically rather than only reacting to input events.
    setInterval(hideSnapshotControls, 1000);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", attachPanelListener);
  } else {
    attachPanelListener();
  }
})();
