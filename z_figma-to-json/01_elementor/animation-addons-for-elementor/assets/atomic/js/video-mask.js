/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./inc/AtomicWidgets/Widgets/VideoMask/assets/scss/video-mask.scss":
/*!*************************************************************************!*\
  !*** ./inc/AtomicWidgets/Widgets/VideoMask/assets/scss/video-mask.scss ***!
  \*************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

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
/*!*********************************************************************!*\
  !*** ./inc/AtomicWidgets/Widgets/VideoMask/assets/js/video-mask.js ***!
  \*********************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _elementor_frontend_handlers__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/frontend-handlers */ "@elementor/frontend-handlers");
/* harmony import */ var _elementor_frontend_handlers__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_frontend_handlers__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _scss_video_mask_scss__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../scss/video-mask.scss */ "./inc/AtomicWidgets/Widgets/VideoMask/assets/scss/video-mask.scss");



// Resolve Elementor's assets base URL so the mask-shape SVG path can be built
// without hard-coding an absolute URL (which varies per installation).
const getElementorAssetsUrl = () => {
  var _ref, _window$elementorFron;
  return (_ref = (_window$elementorFron = window.elementorFrontend?.config?.urls?.assets) !== null && _window$elementorFron !== void 0 ? _window$elementorFron : window.elementorCommon?.config?.urls?.assets) !== null && _ref !== void 0 ? _ref : '';
};

// Set only the dynamic mask-image URL via inline style.
// mask-size / mask-position / mask-repeat are owned by video-mask.scss so
// they remain overridable from the Elementor Style panel.
const applyMaskShape = container => {
  const wrapper = container.querySelector('.vm-video-wrapper');
  if (!wrapper) return;
  const shape = wrapper.dataset.shape || 'circle';
  const baseUrl = getElementorAssetsUrl();
  if (!baseUrl) return;
  const url = `${baseUrl}mask-shapes/${shape}.svg`;
  wrapper.style.webkitMaskImage = `url(${url})`;
  wrapper.style.maskImage = `url(${url})`;
};
const initVideoMask = (container, signal) => {
  // The click-trigger is the inner AAE_A_Video_Mask_Btn atomic element.
  // Using data-element_type to find it is robust against class-name changes.
  const btn = container.querySelector('[data-element_type="e-aae-a-video-mask-btn"]');
  const video = container.querySelector('video');
  if (!btn || !video) return;

  // Idle state: apply the shape mask — circle appears centered over the button.
  applyMaskShape(container);
  const opts = signal ? {
    signal
  } : {};
  btn.addEventListener('click', () => {
    const isOpen = container.classList.toggle('mask-open');
    if (isOpen) {
      // CSS removes mask via .mask-open .vm-video-wrapper { mask-image: none !important }
      // → video expands to its full rectangular (tetragon) area.
      video.play().catch(() => {});
    } else {
      // Removing .mask-open lets the JS inline mask-image take effect again
      // → restores the idle circle shape.
      video.pause();
      video.currentTime = 0;
    }
  }, opts);

  // When a non-looping video ends, restore the idle masked state automatically.
  video.addEventListener('ended', () => {
    container.classList.remove('mask-open');
  }, opts);
};
(0,_elementor_frontend_handlers__WEBPACK_IMPORTED_MODULE_0__.register)({
  elementType: 'e-aae-a-video-mask',
  id: 'aae-a-video-mask-handler',
  callback: ({
    element,
    signal
  }) => {
    const container = element.classList.contains('aae-a-video-mask') ? element : element.querySelector('.aae-a-video-mask');
    if (container) initVideoMask(container, signal);
  }
});
}();
/******/ })()
;
//# sourceMappingURL=video-mask.js.map