import "../../scss/elements/image-hotspot.scss";

/**
 * Image Hotspot - Bricks Element Script
 *
 * Click-triggered tooltips are handled with one delegated listener, bound once,
 * so builder re-renders (which call bricksflyImageHotspot again) never stack
 * listeners and never leave a handler pointing at a removed node.
 */
( function () {
	'use strict';

	var bound = false;

	function closeAll( except ) {
		var open = document.querySelectorAll( '.aab-hotspot__tooltip.active' );
		for ( var i = 0; i < open.length; i++ ) {
			if ( open[ i ] !== except ) {
				open[ i ].classList.remove( 'active' );
			}
		}
	}

	function onClick( e ) {
		var icon = e.target.closest( '.aab-hotspot--click .aab-hotspot__icon' );

		if ( icon ) {
			var item    = icon.closest( '.aab-hotspot--click' );
			var tooltip = item ? item.querySelector( '.aab-hotspot__tooltip' ) : null;

			closeAll( tooltip );

			if ( tooltip ) {
				tooltip.classList.toggle( 'active' );
			}
			return;
		}

		// Click outside any click-triggered hotspot closes every open tooltip.
		if ( ! e.target.closest( '.aab-hotspot--click' ) ) {
			closeAll( null );
		}
	}

	function bricksflyImageHotspot() {
		if ( bound ) {
			return;
		}
		bound = true;
		document.addEventListener( 'click', onClick );
	}

	if ( typeof window !== 'undefined' ) {
		window.bricksflyImageHotspot = bricksflyImageHotspot;
		bricksflyImageHotspot();
	}
} )();
