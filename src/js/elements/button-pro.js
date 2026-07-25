import "../../scss/elements/button-pro.scss";

(function () {
  "use strict";

  function bindCursorTracking(wrapper) {
    var btns = wrapper.querySelectorAll(".btn-hover");
    if (btns.length) {
      Array.prototype.forEach.call(btns, function (btn) {
        if (btn.dataset.aabBtnProBound === "1") return;
        btn.dataset.aabBtnProBound = "1";

        function updatePos(e) {
          var rect = btn.getBoundingClientRect();
          var x = e.clientX - rect.left;
          var y = e.clientY - rect.top;
          var span = btn.querySelector("span");
          if (!span) return;
          span.style.top = y + "px";
          span.style.left = x + "px";
        }

        btn.addEventListener("mouseenter", updatePos);
        btn.addEventListener("mouseleave", updatePos);
      });
    }

    bindMagnetic(wrapper);
    bindBgChange(wrapper);
  }

  function bindMagnetic(wrapper) {
    var btns = wrapper.querySelectorAll('[data-magnetic="true"]');
    Array.prototype.forEach.call(btns, function (btn) {
      if (btn.dataset.aabBtnProMagnetic === "1") return;
      btn.dataset.aabBtnProMagnetic = "1";

      btn.style.willChange = "transform";
      btn.style.transition = "transform 400ms cubic-bezier(.03,.98,.52,.99)";

      btn.addEventListener("mousemove", function (e) {
        var rect = btn.getBoundingClientRect();
        var x = e.clientX - rect.left - rect.width / 2;
        var y = e.clientY - rect.top - rect.height / 2;
        btn.style.transition = "transform 150ms ease-out";
        btn.style.transform = "translate(" + x * 0.3 + "px, " + y * 0.3 + "px)";
      });

      btn.addEventListener("mouseleave", function () {
        btn.style.transition = "transform 400ms cubic-bezier(.03,.98,.52,.99)";
        btn.style.transform = "";
      });
    });
  }

  function bindBgChange(wrapper) {
    var btns = wrapper.querySelectorAll(".btn-hover-bgchange");
    Array.prototype.forEach.call(btns, function (btn) {
      if (btn.dataset.aabBtnProBg === "1") return;
      btn.dataset.aabBtnProBg = "1";

      var span = btn.querySelector(":scope > span");
      if (!span) return;

      function updatePos(e) {
        var rect = btn.getBoundingClientRect();
        span.style.top = e.clientY - rect.top + "px";
        span.style.left = e.clientX - rect.left + "px";
      }

      btn.addEventListener("mouseenter", updatePos);
      btn.addEventListener("mouseleave", updatePos);
    });
  }

  function initButtonPro(el) {
    if (!el) return;

    var wrappers;
    if (el.classList && el.classList.contains("aae--btn-pro-wrapper")) {
      wrappers = [el];
    } else {
      wrappers = el.querySelectorAll(".aae--btn-pro-wrapper");
    }

    Array.prototype.forEach.call(wrappers, function (wrapper) {
      bindCursorTracking(wrapper);
    });
  }

  document.addEventListener("DOMContentLoaded", function () {
    document
      .querySelectorAll(".aae--btn-pro-wrapper")
      .forEach(function (wrapper) {
        bindCursorTracking(wrapper);
      });
  });

  window.thebrbreButtonPro = function (el) {
    if (el) {
      initButtonPro(el);
    } else {
      document
        .querySelectorAll(".aae--btn-pro-wrapper")
        .forEach(function (wrapper) {
          bindCursorTracking(wrapper);
        });
    }
  };
})();
