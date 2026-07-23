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
        if (typeof AAB_ADDONS_JS !== 'undefined' && AAB_ADDONS_JS.post_id) {
          var xhr = new XMLHttpRequest();
          xhr.open('POST', AAB_ADDONS_JS.ajaxUrl, true);
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
            '&post_id=' + encodeURIComponent(AAB_ADDONS_JS.post_id) +
            '&nonce=' + encodeURIComponent(AAB_ADDONS_JS._wpnonce || '') +
            '&social=' + encodeURIComponent(type)
          );
        }
      });
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.aab-social-share').forEach(function (el) {
      initElement(el);
    });
  });

  // Expose for Bricks $scripts re-init
  window.aabPostSocialShare = initElement;
})();
