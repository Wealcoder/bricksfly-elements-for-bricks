import "../../scss/elements/draggable-items.scss";

(function ($) {
    'use strict';

    var STAGGER_MS = 150;
    var SETTLE_BUFFER_MS = 1000;
    var VISIBILITY_THRESHOLD = 0.2;

    function enableDraggable($items) {
        if (!$items.length || typeof $items.draggable !== 'function') return;

        $items.each(function () {
            var $item = $(this);
            if ($item.data('ui-draggable')) {
                $item.draggable('destroy');
            }
            $item.draggable({ containment: 'parent' });
        });
    }

    function playDropIn($wrapper) {
        if ($wrapper.data('aab-drag-played')) return;
        $wrapper.data('aab-drag-played', true);

        var $items = $wrapper.find('.drag--item');
        if (!$items.length) return;

        $items.removeClass('is-loaded');

        $items.each(function (index) {
            var $item = $(this);
            setTimeout(function () {
                $item.addClass('is-loaded');
            }, index * STAGGER_MS);
        });

        var totalDelay = $items.length * STAGGER_MS + SETTLE_BUFFER_MS;
        setTimeout(function () {
            enableDraggable($items);
        }, totalDelay);
    }

    function observeWrapper($wrapper) {
        var el = $wrapper.get(0);
        if (!el) return;

        // Fallback: no IntersectionObserver support → play immediately.
        if (typeof window.IntersectionObserver !== 'function') {
            playDropIn($wrapper);
            return;
        }

        var observer = new IntersectionObserver(function (entries, obs) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting && entry.intersectionRatio >= VISIBILITY_THRESHOLD) {
                    playDropIn($wrapper);
                    obs.disconnect();
                }
            });
        }, {
            threshold: [0, VISIBILITY_THRESHOLD, 0.5, 1]
        });

        observer.observe(el);
    }

    function initDraggableItems(el) {
        if (!el || typeof $ !== 'function') return;

        var $el = $(el);
        var $wrappers = $el.hasClass('draggable--items')
            ? $el
            : $el.find('.draggable--items');

        if (!$wrappers.length) return;

        $wrappers.each(function () {
            var $wrapper = $(this);
            if ($wrapper.data('aab-drag-initialized')) return;
            $wrapper.data('aab-drag-initialized', true);

            observeWrapper($wrapper);
        });
    }

    $(function () {
        $('.draggable--items').each(function () {
            initDraggableItems(this);
        });
    });

    window.bricksflyDraggableItems = function (el) {
        if (el) {
            var $root = $(el);
            var $wrappers = $root.hasClass('draggable--items') ? $root : $root.find('.draggable--items');
            $wrappers.removeData('aab-drag-initialized');
            $wrappers.removeData('aab-drag-played');
            $wrappers.find('.drag--item').each(function () {
                var $item = $(this);
                if ($item.data('ui-draggable')) {
                    $item.draggable('destroy');
                }
                $item.css({ left: '', top: '', position: '' });
            });
            initDraggableItems(el);
        } else {
            $('.draggable--items').each(function () {
                initDraggableItems(this);
            });
        }
    };
})(jQuery);
