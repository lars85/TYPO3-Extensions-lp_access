/*jslint browser:true, plusplus: true*/
/*global TYPO3*/

(function ($) {
    'use strict';

    var LpAccessClickHandler = function () {
        var $this = $(this),
            $root = $this.closest(".tx-lpaccess"),
            $input = $root.find("input"),
            activeValues = [],
            $activeTds;

        if ($this.hasClass("active")) {
            $this.removeClass("active");
        } else {
            $this.addClass("active");
        }

        if (!$this.hasClass("changed")) {
            $this.addClass("changed");
        }

        $activeTds = $root.find("td.active");
        $.each($activeTds, function () {
            activeValues.push($(this).data("value"));
        });

        $input.val(activeValues.join(","));
        $input.trigger("onchange");
    };

    $(function () {
        var $elements = $(".tx-lpaccess td");
        if ($elements.length) {
            $elements.click(LpAccessClickHandler);
        }
    });
}(TYPO3.jQuery));