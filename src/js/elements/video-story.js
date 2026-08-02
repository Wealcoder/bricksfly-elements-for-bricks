import "../../scss/elements/video-story.scss";

/**
 * Video Story - Bricks Element Script
 *
 * Handles:
 * - Video duration display from metadata
 * - Hover play/pause on article
 * - Click to activate: unmute, show controls, mark active
 */
function bricksflyVideoStory( el ) {
	if ( ! el ) return;

	var root = el;

	// Cleanup previous init
	if ( root._aabVS ) {
		root._aabVS.destroy();
		root._aabVS = null;
	}

	var videos = root.querySelectorAll( '.aab--video-story .thumb video' );

	if ( ! videos.length ) {
		// Re-check: root itself might be .aab--video-story
		videos = root.querySelectorAll( '.thumb video' );
	}

	if ( ! videos.length ) return;

	var cleanups = [];

	videos.forEach( function( video ) {
		var article = video.closest( 'article.aab--post' );
		if ( ! article ) return;

		var clicked = false;

		// Show duration when metadata loaded
		function onLoadedMetadata() {
			var duration = video.duration;
			if ( isNaN( duration ) ) return;
			var minutes = Math.floor( duration / 60 );
			var seconds = Math.floor( duration % 60 );
			var formatted = minutes + ':' + ( seconds < 10 ? '0' + seconds : seconds );
			var durationEl = article.querySelector( '.duration' );
			if ( durationEl ) durationEl.textContent = formatted;
		}
		video.addEventListener( 'loadedmetadata', onLoadedMetadata );
		cleanups.push( function() { video.removeEventListener( 'loadedmetadata', onLoadedMetadata ); } );

		// If metadata already loaded (cached video)
		if ( video.readyState >= 1 ) {
			onLoadedMetadata();
		}

		// Hover on article
		function onMouseEnter() {
			if ( ! clicked ) video.play();
		}
		function onMouseLeave() {
			if ( ! clicked ) {
				video.pause();
				video.currentTime = 0;
			}
		}
		article.addEventListener( 'mouseenter', onMouseEnter );
		article.addEventListener( 'mouseleave', onMouseLeave );
		cleanups.push( function() {
			article.removeEventListener( 'mouseenter', onMouseEnter );
			article.removeEventListener( 'mouseleave', onMouseLeave );
		} );

		// Click to activate
		function onClick() {
			// Pause all other videos
			var allVideos = root.querySelectorAll( '.thumb video' );
			allVideos.forEach( function( other ) {
				if ( other !== video ) {
					other.pause();
					other.currentTime = 0;
					other.muted = true;
					other.removeAttribute( 'controls' );
					var otherArticle = other.closest( 'article.aab--post' );
					if ( otherArticle ) otherArticle.classList.remove( 'active' );
				}
			} );

			if ( ! clicked ) {
				video.currentTime = 0;
				video.play();
			}

			video.muted = false;
			video.setAttribute( 'controls', 'controls' );
			clicked = true;

			// Active state
			root.querySelectorAll( '.aab--post' ).forEach( function( el ) {
				el.classList.remove( 'active' );
			} );
			article.classList.add( 'active' );
		}
		article.addEventListener( 'click', onClick );
		cleanups.push( function() { article.removeEventListener( 'click', onClick ); } );
	} );

	// Store cleanup reference
	root._aabVS = {
		destroy: function() {
			cleanups.forEach( function( fn ) { fn(); } );
			cleanups = [];
		}
	};
}

if (typeof window !== 'undefined') { window.bricksflyVideoStory = bricksflyVideoStory; }
