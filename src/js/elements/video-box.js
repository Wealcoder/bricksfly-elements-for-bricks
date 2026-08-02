import "../../scss/elements/video-box.scss";

/**
 * Video Box — Bricks Element Script
 *
 * - Hover-autoplay for video thumbnails (mirrors the Elementor behaviour)
 * - Delegates popup open/close to bricksflyVideoPopup
 *
 * Bricks builder calls window.bricksflyVideoBox() with NO arguments on every
 * element re-render, so the global is a no-arg function that re-scans
 * the DOM and re-inits each .aab-video-box instance idempotently.
 */
( function () {
	'use strict';

	function destroyInstance( root ) {
		if ( root._aabVideoBoxHandlers ) {
			root.removeEventListener( 'mouseenter', root._aabVideoBoxHandlers.enter );
			root.removeEventListener( 'mouseleave', root._aabVideoBoxHandlers.leave );
			root._aabVideoBoxHandlers = null;
		}
	}

	function initInstance( root ) {
		if ( ! root ) return;

		destroyInstance( root );

		// Hover-autoplay: only when thumb is a <video>
		var thumbVideo = root.querySelector( '.thumb video' );
		if ( thumbVideo ) {
			var enter = function () {
				var p = thumbVideo.play();
				// .play() returns a Promise in modern browsers; swallow autoplay
				// rejections so they don't show in the console.
				if ( p && typeof p.catch === 'function' ) {
					p.catch( function () {} );
				}
			};
			var leave = function () {
				thumbVideo.pause();
				try { thumbVideo.currentTime = 0; } catch ( e ) {}
			};
			root.addEventListener( 'mouseenter', enter );
			root.addEventListener( 'mouseleave', leave );
			root._aabVideoBoxHandlers = { enter: enter, leave: leave };
		}

		// Delegate popup wiring to bricksflyVideoPopup (which expects an element)
		if ( typeof window.bricksflyVideoPopup === 'function' ) {
			window.bricksflyVideoPopup( root );
		}
	}

	function initAll() {
		document.querySelectorAll( '.aab-video-box' ).forEach( function ( root ) {
			initInstance( root );
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initAll );
	} else {
		initAll();
	}

	// Bricks builder calls this with NO arguments on each re-render.
	window.bricksflyVideoBox = initAll;
} )();
