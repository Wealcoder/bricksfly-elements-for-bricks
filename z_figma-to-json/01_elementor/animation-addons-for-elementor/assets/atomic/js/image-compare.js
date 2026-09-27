/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "@elementor/frontend-handlers":
/*!***************************************************!*\
  !*** external ["elementorV2","frontendHandlers"] ***!
  \***************************************************/
/***/ (function(module) {

module.exports = elementorV2.frontendHandlers;

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Check if module exists (development only)
/******/ 		if (__webpack_modules__[moduleId] === undefined) {
/******/ 			var e = new Error("Cannot find module '" + moduleId + "'");
/******/ 			e.code = 'MODULE_NOT_FOUND';
/******/ 			throw e;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
!function() {
/*!***************************************************************************!*\
  !*** ./inc/AtomicWidgets/Widgets/ImageCompare/assets/js/image-compare.js ***!
  \***************************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _elementor_frontend_handlers__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/frontend-handlers */ "@elementor/frontend-handlers");
/* harmony import */ var _elementor_frontend_handlers__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_frontend_handlers__WEBPACK_IMPORTED_MODULE_0__);


/**
 * AAE Atomic Image Compare — frontend handler.
 *
 * The native <input type="range"> handles drag + touch + keyboard for free
 * in both axes (writing-mode: vertical-lr rotates the control for vertical
 * mode without changing its value semantics — top = min, bottom = max).
 *
 * This handler only:
 *   1. Mirrors the range value into the `--aae-image-compare-position`
 *      CSS variable. Everything visual — divider/thumb position AND the
 *      clip-path direction on the Before image + Before label — reads
 *      that variable through CSS rules scoped by `[data-direction]`, so
 *      this handler never has to know which axis is active.
 *   2. Toggles `data-{before,after}-label-hidden` when the handle reaches
 *      the edges so labels fade out gracefully.
 *   3. Suppresses track-clicks when `data-enable-click-move="no"` (drag
 *      only — clicks revert to the pre-click value), using the right
 *      axis for the drag-distance check.
 *   4. Stops pointerdown from bubbling so the v4 editor's element-drag
 *      layer doesn't intercept the slider drag inside the editor.
 */
const CLICK_VS_DRAG_THRESHOLD_PX = 4;
const initImageCompare = container => {
  if (container.dataset.aaeCompareReady === '1') return;
  container.dataset.aaeCompareReady = '1';
  const range = container.querySelector('[data-aae-compare-range]');
  if (!range) return;
  const isVertical = () => container.dataset.direction === 'vertical';
  const getDefaultPosition = () => {
    const fallback = Number(container.dataset.defaultPosition);
    return Number.isFinite(fallback) ? fallback : 50;
  };
  const update = () => {
    const rawValue = range.value === '' ? getDefaultPosition() : Number(range.value);
    const value = Math.min(100, Math.max(0, Number.isFinite(rawValue) ? rawValue : getDefaultPosition()));
    range.value = value;
    container.style.setProperty('--aae-image-compare-position', value + '%');
    container.dataset.beforeLabelHidden = value < 12 ? 'true' : 'false';
    container.dataset.afterLabelHidden = value > 88 ? 'true' : 'false';
  };
  range.addEventListener('input', update);

  /* Click-to-move suppression. Native range fires `input` on both drag
     AND click — distinguish by tracking pointer movement on the relevant
     axis (X for horizontal, Y for vertical) between pointerdown and
     pointerup. Movement under the threshold → click → revert. */
  let pointerStartCoord = null;
  let pointerStartValue = null;
  let pointerMoved = false;
  const getAxisCoord = event => isVertical() ? event.clientY : event.clientX;
  range.addEventListener('pointerdown', event => {
    pointerStartCoord = getAxisCoord(event);
    pointerStartValue = range.value;
    pointerMoved = false;
  });
  range.addEventListener('pointermove', event => {
    if (pointerStartCoord === null) return;
    if (Math.abs(getAxisCoord(event) - pointerStartCoord) > CLICK_VS_DRAG_THRESHOLD_PX) {
      pointerMoved = true;
    }
  });
  const pointerEnd = () => {
    if (pointerStartCoord === null) return;
    const clickMoveDisabled = container.dataset.enableClickMove === 'no';
    if (clickMoveDisabled && !pointerMoved) {
      range.value = pointerStartValue;
      update();
    }
    pointerStartCoord = null;
    pointerStartValue = null;
    pointerMoved = false;
  };
  range.addEventListener('pointerup', pointerEnd);
  range.addEventListener('pointercancel', pointerEnd);

  /* Editor preview: the v4 editor wraps every element with a drag layer
     that captures pointerdown to move the widget around the canvas. That
     layer eats the range input's drag, so the user ends up moving the
     whole widget instead of the slider. Stop pointer events from
     bubbling up — the range input still receives them, so drag / touch /
     keyboard interaction stays intact. */
  const swallow = event => event.stopPropagation();
  ['pointerdown', 'mousedown', 'touchstart', 'dragstart'].forEach(eventName => {
    range.addEventListener(eventName, swallow);
  });

  /* Run once on load. */
  update();
};
(0,_elementor_frontend_handlers__WEBPACK_IMPORTED_MODULE_0__.register)({
  elementType: 'e-aae-a-image-compare',
  id: 'e-aae-a-image-compare-handler',
  callback: ({
    element
  }) => {
    element.removeAttribute('data-aae-compare-ready');
    initImageCompare(element);
  }
});
}();
/******/ })()
;
//# sourceMappingURL=image-compare.js.map