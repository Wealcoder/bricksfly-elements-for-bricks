import "../../scss/elements/toggle-switch.scss";

(function () {
    'use strict';

    function initInstance(wrapper) {
        if (!wrapper) return;
        if (wrapper.dataset.toggleInit === '1') return;
        wrapper.dataset.toggleInit = '1';

        var inputs = wrapper.querySelectorAll('input');
        var panes  = wrapper.querySelectorAll('.toggle-pane');
        var labels = wrapper.querySelectorAll('.before_label, .after_label');

        inputs.forEach(function (input) {
            input.addEventListener('change', function () {
                panes.forEach(function (pane) {
                    pane.classList.toggle('show');
                });
                labels.forEach(function (label) {
                    label.classList.toggle('active');
                });
            });
        });
    }

    function initAll() {
        document.querySelectorAll('.aae-toggle-switch-wrapper').forEach(function (wrapper) {
            initInstance(wrapper);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

    // Bricks builder calls window.aaeToggleSwitch() with no args on each re-render.
    window.aaeToggleSwitch = initAll;
})();
