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
/*!********************************************************************!*\
  !*** ./inc/AtomicWidgets/Widgets/Countdown/assets/js/countdown.js ***!
  \********************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _elementor_frontend_handlers__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/frontend-handlers */ "@elementor/frontend-handlers");
/* harmony import */ var _elementor_frontend_handlers__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_frontend_handlers__WEBPACK_IMPORTED_MODULE_0__);

const UNIT_TYPES = ['days', 'hours', 'minutes', 'seconds'];
const MS_PER_SECOND = 1000;
const MS_PER_MINUTE = 60 * MS_PER_SECOND;
const MS_PER_HOUR = 60 * MS_PER_MINUTE;
const MS_PER_DAY = 24 * MS_PER_HOUR;
const pad = value => String(Math.max(0, value)).padStart(2, '0');
const computeFragments = distanceMs => {
  if (distanceMs <= 0) {
    return {
      days: 0,
      hours: 0,
      minutes: 0,
      seconds: 0
    };
  }
  return {
    days: Math.floor(distanceMs / MS_PER_DAY),
    hours: Math.floor(distanceMs % MS_PER_DAY / MS_PER_HOUR),
    minutes: Math.floor(distanceMs % MS_PER_HOUR / MS_PER_MINUTE),
    seconds: Math.floor(distanceMs % MS_PER_MINUTE / MS_PER_SECOND)
  };
};
const applyLayout = container => {
  container.style.flexDirection = container.dataset.layout === 'vertical' ? 'column' : 'row';
};

// Track active intervals so re-init clears the previous one.
const activeIntervals = new WeakMap();
const initCountdown = container => {
  // Clear any existing interval from a previous init (e.g. editor re-render).
  if (activeIntervals.has(container)) {
    window.clearInterval(activeIntervals.get(container));
    activeIntervals.delete(container);
  }
  applyLayout(container);
  const dueDateRaw = container.dataset.dueDate || '';
  const dueDateMs = new Date(dueDateRaw.replace(' ', 'T')).getTime();
  const digitNodes = {};
  UNIT_TYPES.forEach(unitType => {
    const unit = container.querySelector(`[data-unit-type="${unitType}"]`);
    digitNodes[unitType] = unit ? unit.querySelector('.aae-a-countdown-unit-count') : null;
  });
  const setExpired = expired => {
    if (expired) {
      container.setAttribute('data-expired', 'true');
    } else {
      container.removeAttribute('data-expired');
    }
  };
  const tick = () => {
    if (!Number.isFinite(dueDateMs)) {
      setExpired(true);
      return;
    }
    const distance = dueDateMs - Date.now();
    const fragments = computeFragments(distance);
    UNIT_TYPES.forEach(unitType => {
      const node = digitNodes[unitType];
      if (node) {
        node.textContent = pad(fragments[unitType]);
      }
    });
    if (distance <= 0) {
      setExpired(true);
      const id = activeIntervals.get(container);
      if (id) {
        window.clearInterval(id);
        activeIntervals.delete(container);
      }
    } else {
      setExpired(false);
    }
  };
  tick();

  // In the editor, stop after one tick — the interval would cause DOM
  // mutations every second which trigger Elementor's MutationObserver
  // and cause an infinite re-render loop.
  const isEditMode = typeof elementorFrontend !== 'undefined' && elementorFrontend.isEditMode();
  if (!isEditMode) {
    const intervalId = window.setInterval(tick, MS_PER_SECOND);
    activeIntervals.set(container, intervalId);
  }
};
(0,_elementor_frontend_handlers__WEBPACK_IMPORTED_MODULE_0__.register)({
  elementType: 'e-aae-a-countdown',
  id: 'e-aae-a-countdown-handler',
  callback: ({
    element
  }) => {
    initCountdown(element);
  }
});
}();
/******/ })()
;
//# sourceMappingURL=countdown.js.map