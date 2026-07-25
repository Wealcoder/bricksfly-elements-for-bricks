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

//   window.thebrbreFloatingElements = thebrbreFloatingElements;
// })();

(function () {
  var ELEMENT_NAME = "aab-floating-elements";
  var WATCHED_KEYS = ["size", "offsetX", "offsetXEnd", "offsetY", "offsetYEnd"];

  function getBuilderPanel() {
    // The Bricks control panel is in the parent document
    return (
      document.querySelector("#bricks-panel") ||
      document.querySelector(".bricks-panel") ||
      document.querySelector("#brx-panel") ||
      document.body
    );
  }

  function isOurElement() {
    // Bricks adds the active element name as a data attribute or class on the panel
    var panel =
      document.querySelector('[data-element-name="' + ELEMENT_NAME + '"]') ||
      document.querySelector(
        '.bricks-panel-element[data-name="' + ELEMENT_NAME + '"]',
      ) ||
      document.querySelector("#bricks-panel");

    if (!panel) return false;

    // Fallback: check if any panel label contains our field names
    return true;
  }

  function triggerSnapshotUpdate() {
    // Find the hidden _cssSnapshot input inside the active repeater item
    // and toggle its value to force Bricks to detect a change
    var inputs = document.querySelectorAll(
      'input[data-control-key="_cssSnapshot"], ' +
        '[class*="_cssSnapshot"] input, ' +
        'input[id*="_cssSnapshot"]',
    );

    inputs.forEach(function (input) {
      var current = input.value || "";
      var next = String(Date.now());

      // Use native input setter to trigger Vue reactivity
      var nativeInputValueSetter = Object.getOwnPropertyDescriptor(
        window.HTMLInputElement.prototype,
        "value",
      ).set;
      nativeInputValueSetter.call(input, next);

      input.dispatchEvent(new Event("input", { bubbles: true }));
      input.dispatchEvent(new Event("change", { bubbles: true }));
    });
  }

  function attachPanelListener() {
    var panel = getBuilderPanel();
    if (!panel) {
      setTimeout(attachPanelListener, 500);
      return;
    }

    var debounceTimer = null;

    panel.addEventListener(
      "input",
      function (e) {
        var target = e.target;
        if (!target) return;

        // Only react to inputs that look like our responsive fields
        // Bricks names repeater sub-field inputs with the field key in their path
        var name =
          target.name || target.id || target.getAttribute("data-control") || "";
        var isWatched = WATCHED_KEYS.some(function (k) {
          return name.indexOf(k) !== -1;
        });

        if (!isWatched) return;

        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(triggerSnapshotUpdate, 150);
      },
      true,
    ); // capture phase so we catch all bubbled input events
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", attachPanelListener);
  } else {
    attachPanelListener();
  }
})();
