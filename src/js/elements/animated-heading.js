import "../../scss/elements/animated-heading.scss";

/**
 * Animated Heading - Bricks Element Script (No GSAP)
 *
 * Pure CSS + vanilla JS implementation.
 *
 * @param {HTMLElement} el - The Bricks element wrapper
 */
function bricksflyAnimatedHeading( el ) {
	if ( ! el ) return;

	var heading = el.querySelector( '.bricksfly-animated-heading' );
	if ( ! heading ) return;

	// Parse settings
	var raw = heading.getAttribute( 'data-bricksfly-anim' );
	if ( ! raw ) return;

	var settings;
	try {
		var tmp = document.createElement( 'textarea' );
		tmp.innerHTML = raw;
		settings = JSON.parse( tmp.value );
	} catch ( e ) {
		return;
	}

	// Cleanup previous init
	if ( heading._aabCleanup ) {
		heading._aabCleanup();
		heading._aabCleanup = null;
	}

	var extras = heading.querySelectorAll( '.bricksfly-anim-cursor, .bricksfly-anim-mask' );
	for ( var i = 0; i < extras.length; i++ ) extras[i].remove();

	// Restore original text if split previously
	if ( heading._aabOriginalHTML ) {
		var link = heading.querySelector( 'a' );
		if ( link ) {
			link.innerHTML = heading._aabOriginalHTML;
		} else {
			heading.innerHTML = heading._aabOriginalHTML;
		}
	}

	heading.removeAttribute( 'style' );

	var type     = settings.type || 'reveal';
	if ( type === 'none' ) return;

	var duration = Math.max( 0.1, Math.min( 10, parseFloat( settings.duration ) || 1 ) );
	var delay    = Math.max( 0, Math.min( 10, parseFloat( settings.delay ) || 0 ) );
	var stagger  = Math.max( 0, Math.min( 1, parseFloat( settings.stagger ) || 0.02 ) );
	var trigger  = settings.trigger || 'on_scroll';
	var trigSel  = settings.triggerSelector || null;

	switch ( type ) {
		case 'reveal':
			aabAH_simple( heading, {
				from: 'translate3d(0,60px,0)', to: 'translate3d(0,0,0)',
				opFrom: 0, opTo: 1
			}, duration, delay, trigger, trigSel );
			break;

		case 'scale':
			aabAH_simple( heading, {
				from: 'scale(0.7)', to: 'scale(1)',
				opFrom: 0, opTo: 1
			}, duration, delay, trigger, trigSel );
			break;

		case 'slide':
			aabAH_simple( heading, {
				from: 'translate3d(-100px,0,0)', to: 'translate3d(0,0,0)',
				opFrom: 0, opTo: 1
			}, duration, delay, trigger, trigSel );
			break;

		case 'skew_reveal':
			aabAH_simple( heading, {
				from: 'skewX(-20deg) translate3d(100px,0,0)', to: 'skewX(0deg) translate3d(0,0,0)',
				opFrom: 0, opTo: 1
			}, duration, delay, trigger, trigSel );
			break;

		case 'glow_pulse':
			aabAH_glowPulse( heading, duration, delay, trigger, trigSel );
			break;

		case 'typewriter':
			aabAH_typewriter( heading, duration, delay, stagger, trigger, trigSel );
			break;

		case 'mask_wipe':
			aabAH_maskWipe( heading, duration, delay, trigger, trigSel );
			break;

		case 'water_wave':
			aabAH_charAnim( heading, 'water_wave', duration, delay, stagger, trigger, trigSel );
			break;

		case 'background_clip':
			aabAH_backgroundClip( heading, duration, delay, trigger, trigSel );
			break;

		case 'character':
			aabAH_charAnim( heading, 'character', duration, delay, stagger, trigger, trigSel );
			break;
	}
}


/* ===========================
   TRIGGER HELPER
=========================== */

function aabAH_onTrigger( el, trigSel, trigger, callback ) {
	var triggerEl = el;
	var cleanups = [];

	if ( trigSel ) {
		var foundEl = document.querySelector( trigSel );
		if ( foundEl ) {
			triggerEl = foundEl;
		} else {
			console.warn( 'Animated Heading: trigger selector "' + trigSel + '" not found, using element itself' );
		}
	}

	if ( trigger === 'on_page_load' ) {
		callback();

	} else if ( trigger === 'on_scroll' ) {
		var observer = new IntersectionObserver( function( entries ) {
			entries.forEach( function( entry ) {
				if ( entry.isIntersecting ) {
					callback();
					observer.disconnect();
				}
			} );
		}, { threshold: 0.15 } );
		observer.observe( triggerEl );
		cleanups.push( function() { observer.disconnect(); } );

	} else if ( trigger === 'play_with_scroll' ) {
		var started = false;
		function onScroll() {
			var rect = triggerEl.getBoundingClientRect();
			var winH = window.innerHeight;
			var start = winH * 0.85;
			var end = winH * 0.2;
			var progress = ( start - rect.top ) / ( start - end );
			progress = Math.max( 0, Math.min( 1, progress ) );
			if ( ! started && progress > 0 ) { started = true; }
			if ( started ) callback( progress );
		}
		window.addEventListener( 'scroll', onScroll, { passive: true } );
		onScroll();
		cleanups.push( function() { window.removeEventListener( 'scroll', onScroll ); } );

	} else if ( trigger === 'mouseover' ) {
		var handler = function() { callback(); };
		triggerEl.addEventListener( 'mouseenter', handler );
		cleanups.push( function() { triggerEl.removeEventListener( 'mouseenter', handler ); } );

	} else if ( trigger === 'click' ) {
		var clickHandler = function() { callback(); };
		triggerEl.addEventListener( 'click', clickHandler );
		cleanups.push( function() { triggerEl.removeEventListener( 'click', clickHandler ); } );
	}

	return function() {
		cleanups.forEach( function( fn ) { fn(); } );
	};
}


/* ===========================
   SPLIT TEXT HELPER
=========================== */

function aabAH_splitChars( el ) {
	var container = el.querySelector( 'a' ) || el;
	var text = container.textContent;
	el._aabOriginalHTML = text;

	container.innerHTML = '';
	var spans = [];

	for ( var i = 0; i < text.length; i++ ) {
		var span = document.createElement( 'span' );
		span.style.display = 'inline-block';
		span.textContent = text[i] === ' ' ? '\u00A0' : text[i];
		container.appendChild( span );
		spans.push( span );
	}

	return spans;
}


/* ===========================
   SIMPLE TRANSFORM ANIMATIONS
   (reveal, scale, slide, skew_reveal)
=========================== */

function aabAH_simple( el, anim, duration, delay, trigger, trigSel ) {
	// Set initial hidden state
	el.style.transform = anim.from;
	el.style.opacity = anim.opFrom;
	el.style.transition = 'none';

	if ( trigger === 'play_with_scroll' ) {
		var cleanup = aabAH_onTrigger( el, trigSel, trigger, function( progress ) {
			el.style.transition = 'none';
			el.style.opacity = anim.opFrom + ( anim.opTo - anim.opFrom ) * progress;
			el.style.transform = aabAH_lerpTransform( anim.from, anim.to, progress );
		} );
		el._aabCleanup = cleanup;
		return;
	}

	if ( trigger === 'mouseover' || trigger === 'click' ) {
		// Start visible for hover/click
		el.style.transform = anim.to;
		el.style.opacity = anim.opTo;
	}

	function play() {
		el.style.transform = anim.from;
		el.style.opacity = anim.opFrom;
		// Force reflow
		void el.offsetWidth;
		el.style.transition = 'transform ' + duration + 's cubic-bezier(0.16,1,0.3,1), opacity ' + duration + 's cubic-bezier(0.16,1,0.3,1)';
		el.style.transitionDelay = delay + 's';
		el.style.transform = anim.to;
		el.style.opacity = anim.opTo;
	}

	var cleanup = aabAH_onTrigger( el, trigSel, trigger, function() {
		if ( trigger === 'on_page_load' || trigger === 'on_scroll' ) {
			// Single fire: apply with delay
			void el.offsetWidth;
			el.style.transition = 'transform ' + duration + 's cubic-bezier(0.16,1,0.3,1), opacity ' + duration + 's cubic-bezier(0.16,1,0.3,1)';
			el.style.transitionDelay = delay + 's';
			el.style.transform = anim.to;
			el.style.opacity = anim.opTo;
		} else {
			play();
		}
	} );

	el._aabCleanup = cleanup;
}


/* ===========================
   LERP TRANSFORM FOR SCRUB
=========================== */

function aabAH_lerpTransform( from, to, progress ) {
	var fromVals = aabAH_parseTransformValues( from );
	var toVals = aabAH_parseTransformValues( to );
	var result = [];

	for ( var key in toVals ) {
		var f = fromVals[key] || { value: 0, unit: toVals[key].unit };
		var t = toVals[key];
		var val = f.value + ( t.value - f.value ) * progress;
		result.push( key + '(' + val + t.unit + ')' );
	}

	return result.join( ' ' );
}

function aabAH_parseTransformValues( str ) {
	var result = {};
	var regex = /(\w+)\(([^)]+)\)/g;
	var match;
	while ( ( match = regex.exec( str ) ) !== null ) {
		var name = match[1];
		var args = match[2].split( ',' );
		// Handle translate3d specially
		if ( name === 'translate3d' ) {
			var parts = args.map( function( s ) { return s.trim(); } );
			result['translateX'] = aabAH_parseValue( parts[0] );
			result['translateY'] = aabAH_parseValue( parts[1] );
			result['translateZ'] = aabAH_parseValue( parts[2] );
		} else {
			result[name] = aabAH_parseValue( args[0].trim() );
		}
	}
	return result;
}

function aabAH_parseValue( str ) {
	var num = parseFloat( str ) || 0;
	var unit = str.replace( /[0-9.\-]/g, '' ) || '';
	return { value: num, unit: unit };
}


/* ===========================
   CHARACTER ANIMATIONS
   (water_wave, character)
=========================== */

function aabAH_charAnim( el, type, duration, delay, stagger, trigger, trigSel ) {
	var chars = aabAH_splitChars( el );
	if ( ! chars.length ) return;

	// Set initial state
	chars.forEach( function( span ) {
		span.style.transition = 'none';
		if ( type === 'water_wave' ) {
			span.style.transform = 'translate3d(0,30px,0)';
			span.style.opacity = '0';
		} else {
			span.style.transform = 'translate3d(0,40px,0) rotateX(-90deg)';
			span.style.opacity = '0';
			span.style.transformOrigin = 'center bottom';
		}
	} );

	function playChars() {
		chars.forEach( function( span, idx ) {
			var charDelay = delay + ( idx * stagger );
			span.style.transition = 'transform ' + duration + 's cubic-bezier(0.16,1,0.3,1), opacity ' + duration + 's cubic-bezier(0.16,1,0.3,1)';
			span.style.transitionDelay = charDelay + 's';
			span.style.transform = 'translate3d(0,0,0) rotateX(0deg)';
			span.style.opacity = '1';
		} );
	}

	function resetChars() {
		chars.forEach( function( span ) {
			span.style.transition = 'none';
			if ( type === 'water_wave' ) {
				span.style.transform = 'translate3d(0,30px,0)';
				span.style.opacity = '0';
			} else {
				span.style.transform = 'translate3d(0,40px,0) rotateX(-90deg)';
				span.style.opacity = '0';
			}
		} );
	}

	if ( trigger === 'play_with_scroll' ) {
		var cleanup = aabAH_onTrigger( el, trigSel, trigger, function( progress ) {
			chars.forEach( function( span, idx ) {
				var charStart = ( idx * stagger ) / ( ( chars.length * stagger ) + duration );
				var charEnd = charStart + ( duration / ( ( chars.length * stagger ) + duration ) );
				var charProgress = Math.max( 0, Math.min( 1, ( progress - charStart ) / ( charEnd - charStart ) ) );

				span.style.transition = 'none';
				span.style.opacity = charProgress;
				if ( type === 'water_wave' ) {
					span.style.transform = 'translate3d(0,' + ( 30 * ( 1 - charProgress ) ) + 'px,0)';
				} else {
					span.style.transform = 'translate3d(0,' + ( 40 * ( 1 - charProgress ) ) + 'px,0) rotateX(' + ( -90 * ( 1 - charProgress ) ) + 'deg)';
				}
			} );
		} );
		el._aabCleanup = cleanup;
		return;
	}

	if ( trigger === 'mouseover' || trigger === 'click' ) {
		// Start visible
		chars.forEach( function( span ) {
			span.style.transform = 'translate3d(0,0,0) rotateX(0deg)';
			span.style.opacity = '1';
			span.style.transition = 'none';
		} );
	}

	var cleanup = aabAH_onTrigger( el, trigSel, trigger, function() {
		if ( trigger === 'mouseover' || trigger === 'click' ) {
			resetChars();
			void el.offsetWidth;
		}
		playChars();
	} );

	el._aabCleanup = cleanup;
}


/* ===========================
   GLOW PULSE
=========================== */

function aabAH_glowPulse( el, duration, delay, trigger, trigSel ) {
	el.style.opacity = '0';
	el.style.transition = 'none';

	function playGlow() {
		el.style.transition = 'opacity ' + duration + 's ease-out';
		el.style.opacity = '1';
		// Start pulse after fade-in
		setTimeout( function() {
			el.style.animation = 'bricksfly-glow-pulse 2s ease-in-out infinite alternate';
		}, duration * 1000 );
	}

	if ( trigger === 'play_with_scroll' ) {
		el.style.opacity = '1';
		var cleanup = aabAH_onTrigger( el, trigSel, trigger, function( progress ) {
			var shadow = 20 * progress;
			var shadow2 = 40 * progress;
			var alpha1 = 0.8 * progress;
			var alpha2 = 0.4 * progress;
			el.style.textShadow = '0 0 ' + shadow + 'px rgba(255,255,255,' + alpha1 + '), 0 0 ' + shadow2 + 'px rgba(255,255,255,' + alpha2 + ')';
		} );
		el._aabCleanup = cleanup;
		return;
	}

	if ( trigger === 'mouseover' ) {
		el.style.opacity = '1';
		var te = ( trigSel ? document.querySelector( trigSel ) : null ) || el;
		var enterH = function() {
			el.style.transition = 'text-shadow 0.5s ease-out';
			el.style.textShadow = '0 0 20px rgba(255,255,255,0.8), 0 0 40px rgba(255,255,255,0.4)';
		};
		var leaveH = function() {
			el.style.transition = 'text-shadow 0.5s ease-out';
			el.style.textShadow = 'none';
		};
		te.addEventListener( 'mouseenter', enterH );
		te.addEventListener( 'mouseleave', leaveH );
		el._aabCleanup = function() {
			te.removeEventListener( 'mouseenter', enterH );
			te.removeEventListener( 'mouseleave', leaveH );
		};
		return;
	}

	if ( trigger === 'click' ) {
		el.style.opacity = '1';
		var tc = ( trigSel ? document.querySelector( trigSel ) : null ) || el;
		var clickH = function() {
			el.style.animation = 'none';
			void el.offsetWidth;
			playGlow();
		};
		tc.addEventListener( 'click', clickH );
		el._aabCleanup = function() { tc.removeEventListener( 'click', clickH ); };
		return;
	}

	var cleanup = aabAH_onTrigger( el, trigSel, trigger, function() {
		setTimeout( function() { playGlow(); }, delay * 1000 );
	} );
	el._aabCleanup = cleanup;
}


/* ===========================
   TYPEWRITER
=========================== */

function aabAH_typewriter( el, _duration, delay, stagger, trigger, trigSel ) {
	var chars = aabAH_splitChars( el );
	if ( ! chars.length ) return;

	chars.forEach( function( span ) {
		span.style.opacity = '0';
		span.style.transition = 'none';
	} );

	// Cursor
	var cursor = document.createElement( 'span' );
	cursor.className = 'bricksfly-anim-cursor';
	cursor.textContent = '|';
	cursor.style.opacity = '0';
	var container = el.querySelector( 'a' ) || el;
	container.appendChild( cursor );

	var timer = null;

	function playTypewriter() {
		cursor.style.opacity = '1';
		var idx = 0;
		function showNext() {
			if ( idx < chars.length ) {
				chars[idx].style.opacity = '1';
				idx++;
				timer = setTimeout( showNext, stagger * 1000 );
			}
		}
		showNext();
	}

	function resetTypewriter() {
		if ( timer ) clearTimeout( timer );
		chars.forEach( function( span ) { span.style.opacity = '0'; } );
	}

	if ( trigger === 'play_with_scroll' ) {
		cursor.style.opacity = '1';
		var cleanup = aabAH_onTrigger( el, trigSel, trigger, function( progress ) {
			var total = chars.length;
			var visible = Math.round( progress * total );
			chars.forEach( function( span, i ) {
				span.style.opacity = i < visible ? '1' : '0';
			} );
		} );
		el._aabCleanup = function() { if ( timer ) clearTimeout( timer ); cleanup(); };
		return;
	}

	if ( trigger === 'mouseover' || trigger === 'click' ) {
		// Don't start visible for typewriter - characters should type on trigger
	}

	var cleanup = aabAH_onTrigger( el, trigSel, trigger, function() {
		resetTypewriter();
		void el.offsetWidth;
		if ( trigger === 'on_page_load' ) {
			setTimeout( playTypewriter, delay * 1000 );
		} else {
			playTypewriter();
		}
	} );

	el._aabCleanup = function() { if ( timer ) clearTimeout( timer ); cleanup(); };
}


/* ===========================
   MASK WIPE
=========================== */

function aabAH_maskWipe( el, duration, delay, trigger, trigSel ) {
	el.style.position = 'relative';
	el.style.overflow = 'hidden';

	var mask = document.createElement( 'div' );
	mask.className = 'bricksfly-anim-mask';
	mask.style.cssText = 'position:absolute;top:0;left:0;width:100%;height:100%;background:currentColor;z-index:2;pointer-events:none;';
	mask.style.transform = 'translate3d(-101%,0,0)';
	mask.style.transition = 'none';
	el.appendChild( mask );

	var inner = el.querySelector( 'a' ) || el;
	if ( inner !== el ) inner.style.opacity = '0';
	else {
		// Wrap text content for opacity control
		var children = Array.prototype.slice.call( el.childNodes );
		var wrapper = document.createElement( 'span' );
		wrapper.className = 'bricksfly-mask-inner';
		children.forEach( function( child ) {
			if ( child !== mask ) wrapper.appendChild( child );
		} );
		el.insertBefore( wrapper, mask );
		inner = wrapper;
		inner.style.opacity = '0';
	}

	var halfDur = duration * 0.4;

	function playMask() {
		mask.style.transition = 'none';
		mask.style.transform = 'translate3d(-101%,0,0)';
		inner.style.opacity = '0';
		if ( ! mask.parentNode ) el.appendChild( mask );
		void mask.offsetWidth;

		// Slide mask in
		mask.style.transition = 'transform ' + halfDur + 's cubic-bezier(0.65,0,0.35,1)';
		mask.style.transform = 'translate3d(0,0,0)';

		setTimeout( function() {
			// Show text, slide mask out
			inner.style.opacity = '1';
			mask.style.transition = 'transform ' + halfDur + 's cubic-bezier(0.65,0,0.35,1)';
			mask.style.transform = 'translate3d(101%,0,0)';

			setTimeout( function() {
				if ( mask.parentNode ) mask.remove();
			}, halfDur * 1000 );
		}, halfDur * 1000 );
	}

	if ( trigger === 'play_with_scroll' ) {
		var cleanup = aabAH_onTrigger( el, trigSel, trigger, function( progress ) {
			mask.style.transition = 'none';
			if ( progress < 0.5 ) {
				// Mask sliding in
				var p = progress / 0.5;
				inner.style.opacity = '0';
				mask.style.transform = 'translate3d(' + ( -101 + 101 * p ) + '%,0,0)';
				if ( ! mask.parentNode ) el.appendChild( mask );
			} else {
				// Mask sliding out
				var p2 = ( progress - 0.5 ) / 0.5;
				inner.style.opacity = '1';
				mask.style.transform = 'translate3d(' + ( 101 * p2 ) + '%,0,0)';
				if ( p2 >= 1 && mask.parentNode ) mask.remove();
			}
		} );
		el._aabCleanup = cleanup;
		return;
	}

	var cleanup = aabAH_onTrigger( el, trigSel, trigger, function() {
		if ( trigger === 'on_page_load' ) {
			setTimeout( playMask, delay * 1000 );
		} else {
			playMask();
		}
	} );

	el._aabCleanup = cleanup;
}


/* ===========================
   BACKGROUND CLIP
=========================== */

function aabAH_backgroundClip( el, duration, delay, trigger, trigSel ) {
	if ( ! el.style.background || el.style.background === '' ) {
		el.style.background = 'linear-gradient(45deg, #6366f1, #ec4899, #f59e0b, #10b981)';
	}
	el.style.backgroundSize = '400% 400%';
	el.style.webkitBackgroundClip = 'text';
	el.style.backgroundClip = 'text';
	el.style.webkitTextFillColor = 'transparent';
	el.style.color = 'transparent';
	el.style.opacity = '0';

	function playClip() {
		el.style.transition = 'opacity 0.5s ease';
		el.style.opacity = '1';
		el.style.animation = 'bricksfly-bg-shift ' + ( duration * 2 ) + 's ease infinite alternate';
	}

	if ( trigger === 'play_with_scroll' ) {
		el.style.opacity = '1';
		var cleanup = aabAH_onTrigger( el, trigSel, trigger, function( progress ) {
			el.style.backgroundPosition = ( progress * 100 ) + '% 50%';
		} );
		el._aabCleanup = cleanup;
		return;
	}

	if ( trigger === 'mouseover' || trigger === 'click' ) {
		el.style.opacity = '1';
	}

	var cleanup = aabAH_onTrigger( el, trigSel, trigger, function() {
		if ( trigger === 'on_page_load' ) {
			setTimeout( playClip, delay * 1000 );
		} else {
			playClip();
		}
	} );

	el._aabCleanup = cleanup;
}

if (typeof window !== 'undefined') {
	window.bricksflyAnimatedHeading = bricksflyAnimatedHeading;
	// Add a refresh function for editor integration
	window.bricksflyRefreshAnimatedHeadings = function() {
		// Reset all initialized flags
		var elements = document.querySelectorAll('.bricksfly-animated-heading');
		elements.forEach(function(el) {
			var wrapper = el.parentElement;
			if (wrapper) {
				wrapper._aabInitialized = false;
				// Clean up any existing animations
				if (el._aabCleanup) {
					el._aabCleanup();
					el._aabCleanup = null;
				}
			}
		});
		// Re-initialize everything
		initAnimatedHeadings();
	};
}

/* ===========================
   INITIALIZATION
=========================== */

(function() {
	// Initialize when DOM is ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initAnimatedHeadings);
	} else {
		initAnimatedHeadings();
	}

	// Bricks Builder integration for live preview
	if (window.Bricks && Bricks.builder) {
		// Re-initialize when settings change in the builder
		Bricks.builder.on('elementSettingsChange', function(data) {
			if (data && data.elementId) {
				// Find all animated headings and re-initialize
				setTimeout(function() {
					var elements = document.querySelectorAll('.bricksfly-animated-heading[data-bricksfly-anim]');
					elements.forEach(function(el) {
						var wrapper = el.parentElement;
						if (wrapper) {
							// Clean up previous animation
							if (wrapper._aabCleanup) {
								wrapper._aabCleanup();
								wrapper._aabCleanup = null;
							}
							wrapper._aabInitialized = false;
							// Re-initialize with new settings
							window.bricksflyAnimatedHeading(wrapper);
						}
					});
				}, 100);
			}
		});
	}

	// Also initialize after Bricks AJAX updates
	if (window.Bricks && Bricks.ajax) {
		Bricks.ajax.on('success', initAnimatedHeadings);
	}

	// MutationObserver for dynamic content and editor changes
	var observer = new MutationObserver(function(mutations) {
		// Check if any mutations involve our animated headings
		var hasRelevantChanges = mutations.some(function(mutation) {
			if (mutation.type === 'childList') {
				return Array.from(mutation.addedNodes).some(function(node) {
					return node.nodeType === 1 && (
						node.classList && node.classList.contains('bricksfly-animated-heading') ||
						(node.querySelector && node.querySelector('.bricksfly-animated-heading'))
					);
				});
			}
			if (mutation.type === 'attributes') {
				return mutation.target.classList && mutation.target.classList.contains('bricksfly-animated-heading');
			}
			return false;
		});

		if (hasRelevantChanges) {
			initAnimatedHeadings();
		}
	});

	// Start observing the document for changes
	observer.observe(document.body || document.documentElement, {
		childList: true,
		subtree: true,
		attributes: true,
		attributeFilter: ['data-bricksfly-anim', 'class']
	});

	function initAnimatedHeadings() {
		var elements = document.querySelectorAll('.bricksfly-animated-heading[data-bricksfly-anim]');
		elements.forEach(function(el) {
			// Get the parent wrapper element (the div that wraps the heading tag)
			var wrapper = el.parentElement;
			if (wrapper && !wrapper._aabInitialized) {
				// Force animation replay in editor mode
				var isEditor = window.Bricks && Bricks.builder;
				window.bricksflyAnimatedHeading(wrapper);
				wrapper._aabInitialized = true;

				// In editor, immediately replay animation for preview
				if (isEditor) {
					setTimeout(function() {
						// Trigger the animation again for preview
						var heading = wrapper.querySelector('.bricksfly-animated-heading');
						if (heading && heading._aabCleanup) {
							var cleanup = heading._aabCleanup;
							heading._aabCleanup = null;
							cleanup();
							// Re-initialize
							wrapper._aabInitialized = false;
							window.bricksflyAnimatedHeading(wrapper);
							wrapper._aabInitialized = true;
						}
					}, 50);
				}
			}
		});
	}
})();
