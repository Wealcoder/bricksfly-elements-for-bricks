import "../../scss/elements/post-social-share.scss";

(function () {
  'use strict';

  function initElement(el) {
    var links = el.querySelectorAll('.aab-share-list a[data-type]');
    if (!links.length) return;

    links.forEach(function (link) {
      link.addEventListener('click', function () {
        var type = link.getAttribute('data-type');
        if (!type) return;

        // AJAX share count tracking
        if (typeof THEBRBRE_ADDONS_JS !== 'undefined' && THEBRBRE_ADDONS_JS.post_id) {
          var xhr = new XMLHttpRequest();
          xhr.open('POST', THEBRBRE_ADDONS_JS.ajaxUrl, true);
          xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

          xhr.onload = function () {
            if (xhr.status !== 200) return;

            try {
              var response = JSON.parse(xhr.responseText);
              if (response && response.success && response.data && response.data.post_shares) {
                var counts = el.querySelectorAll('.aab-share-count');
                counts.forEach(function (countEl) {
                  var countType = countEl.getAttribute('data-type');
                  if (countType && response.data.post_shares[countType] !== undefined) {
                    countEl.textContent = response.data.post_shares[countType];
                  }
                });
              }
            } catch (e) {
              // Silent fail
            }
          };

          xhr.send(
            'action=thebrbre_post_shares' +
            '&post_id=' + encodeURIComponent(THEBRBRE_ADDONS_JS.post_id) +
            '&nonce=' + encodeURIComponent(THEBRBRE_ADDONS_JS._wpnonce || '') +
            '&social=' + encodeURIComponent(type)
          );
        }
      });
    });
  }

  function initAll(el) {
    if (el) {
      initElement(el);
      return;
    }
    document.querySelectorAll('.aab-social-share').forEach(function (root) {
      initElement(root);
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    initAll();
  });

  // Bricks builder calls window.thebrbrePostSocialShare() with NO arguments
  // on re-render, so the global must be a no-arg-safe entrypoint (matching
  // the convention used by every other element in this plugin, e.g.
  // counter.js/video-box.js's initAll) rather than initElement directly,
  // which assumes its argument is always a valid element.
  window.thebrbrePostSocialShare = initAll;
})();
