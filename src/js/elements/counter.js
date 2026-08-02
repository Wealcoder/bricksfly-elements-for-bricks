import "../../scss/elements/counter.scss";

/**
 * Counter — Bricks Element Script (No GSAP)
 *
 * Self-initializing: scans the DOM for [data-aab-counter] roots and
 * starts each one. Bricks calls window.bricksflyCounter() (with or without
 * the wrapper) on builder re-render and on frontend init — both paths
 * end up calling initAll().
 */
( function () {
	'use strict';

	function parseSettings( raw ) {
		if ( ! raw ) return null;
		try {
			var tmp = document.createElement( 'textarea' );
			tmp.innerHTML = raw;
			return JSON.parse( tmp.value );
		} catch ( e ) {
			return null;
		}
	}

	function formatNumber( number, decimals, separator ) {
		var sepChar = '';
		var decPoint = '.';

		switch ( separator ) {
			case 'comma': sepChar = ','; break;
			case 'dot':   sepChar = '.'; decPoint = ','; break;
			case 'space': sepChar = ' '; break;
		}

		var fixed = number.toFixed( decimals );
		var parts = fixed.split( '.' );
		var intPart = parts[ 0 ];
		var decPart = parts[ 1 ] || '';

		if ( sepChar ) {
			var isNeg = intPart[ 0 ] === '-';
			if ( isNeg ) intPart = intPart.substring( 1 );

			var result = '';
			var len = intPart.length;
			for ( var i = 0; i < len; i++ ) {
				if ( i > 0 && ( len - i ) % 3 === 0 ) {
					result += sepChar;
				}
				result += intPart[ i ];
			}
			intPart = ( isNeg ? '-' : '' ) + result;
		}

		if ( decimals > 0 && decPart ) {
			return intPart + decPoint + decPart;
		}

		return intPart;
	}

	function destroyInstance( root ) {
		if ( root._aabCounter ) {
			try { root._aabCounter.destroy(); } catch ( e ) {}
			root._aabCounter = null;
		}
	}

	function initInstance( root ) {
		if ( ! root ) return;

		var numberEl = root.querySelector( '.aab-counter__number' );
		if ( ! numberEl ) return;

		var settings = parseSettings( root.getAttribute( 'data-aab-counter' ) );
		if ( ! settings ) return;

		destroyInstance( root );

		var start     = parseFloat( settings.start );
		var end       = parseFloat( settings.end );
		var duration  = parseInt( settings.duration, 10 );
		var separator = settings.separator || 'none';
		var decimals  = parseInt( settings.decimals, 10 );
		var trigger   = settings.trigger || 'on_scroll';

		if ( isNaN( start ) ) start = 0;
		if ( isNaN( end ) ) end = 100;
		if ( isNaN( duration ) || duration < 1 ) duration = 2000;
		if ( isNaN( decimals ) || decimals < 0 ) decimals = 0;

		numberEl.textContent = formatNumber( start, decimals, separator );

		var hasRun = false;
		var animId = null;
		var observer = null;

		function runCount() {
			if ( hasRun ) return;
			hasRun = true;

			if ( start === end ) {
				numberEl.textContent = formatNumber( end, decimals, separator );
				return;
			}

			var startTime = null;

			function step( timestamp ) {
				if ( ! startTime ) startTime = timestamp;
				var elapsed = timestamp - startTime;
				var progress = Math.min( elapsed / duration, 1 );
				var eased = 1 - Math.pow( 1 - progress, 3 );
				var current = start + ( end - start ) * eased;

				numberEl.textContent = formatNumber( current, decimals, separator );

				if ( progress < 1 ) {
					animId = requestAnimationFrame( step );
				} else {
					numberEl.textContent = formatNumber( end, decimals, separator );
				}
			}

			animId = requestAnimationFrame( step );
		}

		if ( trigger === 'on_page_load' ) {
			runCount();
		} else {
			observer = new IntersectionObserver( function ( entries ) {
				for ( var i = 0; i < entries.length; i++ ) {
					if ( entries[ i ].isIntersecting ) {
						runCount();
						observer.disconnect();
						observer = null;
						break;
					}
				}
			}, { threshold: 0.2 } );
			observer.observe( root );
		}

		root._aabCounter = {
			destroy: function () {
				if ( animId ) cancelAnimationFrame( animId );
				if ( observer ) observer.disconnect();
				hasRun = false;
				animId = null;
				observer = null;
			}
		};
	}

	function initAll() {
		var roots = document.querySelectorAll( '[data-aab-counter]' );
		for ( var i = 0; i < roots.length; i++ ) {
			initInstance( roots[ i ] );
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initAll );
	} else {
		initAll();
	}

	window.bricksflyCounter = initAll;
} )();
