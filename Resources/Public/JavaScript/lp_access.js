var LpAccess_ClickHandler;

(function ($) {
    'use strict';

    var LpAccess_ClickHandler = function () {
        var $this = $(this),
            $root = $this.closest(".tx-lpaccess"),
            $input = $root.find("input"),
            activeValues = Array();

        if ($this.hasClass("active")) {
            $this.removeClass("active");
        } else {
            $this.addClass("active");
        }

        if (!$this.hasClass("changed")) {
            $this.addClass("changed");
        }

        var $activeTds = $root.find("td.active");
        $.each($activeTds, function() {
            activeValues.push($(this).data("value"));
        });

        $input.val(activeValues.join(","));
        $input.trigger('onchange');
    };

    // DOM ready
    $(function () {
        $(".tx-lpaccess td").click(LpAccess_ClickHandler);
    });
})(TYPO3.jQuery);