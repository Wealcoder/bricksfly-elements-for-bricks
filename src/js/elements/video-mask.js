import "../../scss/elements/video-mask.scss";

(function () {
  'use strict';

  function initElement(el) {
    var root = el.querySelector('.aab-video-mask') || el;
    if (!root) return;

    var btns = root.querySelectorAll('.video--btn');

    btns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        // Toggle mask-open class
        root.classList.toggle('mask-open');

        // Toggle open/close titles
        var openTitle = root.querySelector('.open-title');
        var closeTitle = root.querySelector('.close-title');
        if (openTitle) openTitle.classList.toggle('hidden');
        if (closeTitle) closeTitle.classList.toggle('hidden');

        // Apply content color to parent section
        var contentColor = root.getAttribute('data-content-color');
        if (contentColor) {
          var parent = root.closest('.aab-video-mask-content');
          if (parent) {
            if (root.classList.contains('mask-open')) {
              parent.style.cssText = 'color: ' + contentColor + ' !important; fill: ' + contentColor + ' !important;';
            } else {
              parent.style.cssText = '';
            }
          }
        }

        // Handle video play/pause
        var videos = root.querySelectorAll('video');
        videos.forEach(function (video) {
          if (video.autoplay) return;
          if (video.paused) {
            video.play();
          } else {
            video.pause();
          }
        });
      });
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.aab-video-mask').forEach(function (root) {
      var wrapper = root.closest('[id^="brxe-"]') || root.parentElement;
      initElement(wrapper);
    });
  });

  // Expose for Bricks $scripts re-init
  window.aabVideoMask = initElement;
})();
