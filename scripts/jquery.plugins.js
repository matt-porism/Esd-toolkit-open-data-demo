// wraps the jquery UI autocomplete
$.fn.selectAutoComplete = function (options) {
    var settings = $.extend({
        "unselected-class": "dull"
    }, options);

    var unselectedClass = settings["unselected-class"];
    var unselectedLabel = settings["unselected-label"];

    var selectHandler = options.select;

    var $select = this;

    if (!unselectedLabel) {
        unselectedLabel = $select.find("option:first").text();
    }

    var $input = $("<input type='text' />");
    $input.insertBefore($select);

    // set the initial value
    if ($select.val() !== "") {
        var text = $("option:selected", this).text();
        $input.val(text);
    }

    $select.hide();
    var $options = $("option", this);

    var soruce = [];
    for (var i = 0; i < $options.length; i++) {
        var option = $options[i];
        if (option.value === "")
            continue;
        soruce.push({ value: option.value, label: option.text });
    }

    function getByLabel(label) {
        label = label.toLowerCase();
        for (var i = 0; i < $options.length; i++) {
            var option = $options[i];
            if (option.text.toLowerCase() === label) {
                return option.value;
            }
        }
        return "";
    }


    $input.autocomplete({
        source: soruce,
        select: function (event, ui) {
            $(this).val(ui.item.label);
            $select.val(ui.item.value);
            if (selectHandler) {
                selectHandler(event, ui);
            }
            return false;
        },
        focus: function (event, ui) {
            $(this).val(ui.item.label);
            $select.val(ui.item.value);
            return false;
        }
    }).focus(function () {
        if ($(this).hasClass(unselectedClass)) {
            $(this).removeClass(unselectedClass);
            $(this).val("");
        }
    }).blur(function () {
        var label = $(this).val();
        var value = getByLabel(label);
        $select.val(value);
        if ($select.val() === "") {
            $(this).addClass(unselectedClass).val(unselectedLabel);
        }
    }).blur();
};


(function($){
    var methods = {
        init: function (options) {    
            var settings = $.extend({
                "loading-class": "loading"
            }, options);

            var loadingClass = settings["loading-class"];

            return this.each(function(){
                var $this = $(this);

                var data = $this.data('loadingDivData');

                if (!data) {
                    var $loadingDiv = $("<div />", { "class": loadingClass, css: { "display": "none" } })
                        .appendTo(document.body);

                    $this.data("loadingDivData", {
                        target: $this,
                        loadingDiv: $loadingDiv
                    });
                }
            });
        },
        show: function () {
            return this.each(function(){
                var data = $(this).data('loadingDivData');
                if (!data) { return this; }

                var $target = data.target;
                var offset = $target.closest(":visible").offset();

                var $loadingDiv = data.loadingDiv;
                $loadingDiv.show().offset(offset);
            });
        },
        hide: function () {
            return this.each(function(){
                var data = $(this).data('loadingDivData');
                if (!data) { return this; }

                data.loadingDiv.hide();
            });
        }
    };

    $.fn.loadingSign = function (methodOrOptions) {
        if (methods[methodOrOptions]) {
            var args = Array.prototype.slice.call( arguments, 1 );
            return methods[ methodOrOptions ].apply(this, args);
        }
        else if (typeof methodOrOptions === 'object' || !methodOrOptions) {
            return methods.init.apply( this, arguments );
        } 
        else {
            $.error('Method ' + methodOrOptions + ' does not exist on jQuery.loadingSign' );
        }                
    };
})(jQuery);