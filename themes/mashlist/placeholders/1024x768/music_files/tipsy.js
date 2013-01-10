/*

@file tipsy.js
@description jQuery plugin that adds a tooltip to selected elements
@author Ryan Bailey <rbailey@nerdery.com>

*/
(function($) {

    var defaults = {
        tip: undefined,
        text: undefined,
        textCallback: undefined,
        delay: 1000,
        fadeIn: 100,
        followCursor: false,
        left: undefined,
        top: undefined,
        right: undefined,
        bottom: undefined,
        middle: undefined,
        center: undefined,
        delegate: undefined
    };

    $.fn.tipsy = function(options){
        var $tip;
        var $text;
        var selector = this.selector;
        var settings = {};
        $.extend(settings, defaults);
        $.extend(settings, options);
        
        $tip = $(settings.tip).eq(0);
        $tip.css({
            position: 'absolute',
            display: 'none'
        });
        $tip.appendTo($('body'));
        $tip.hide();
        
        if (settings.text) {
            $text = $tip.find($(settings.text));
        } else {
            $text = $tip;
        }

        this.each(function(index, element) {
            var $el = $(element);
            var timer;
            if (isset(settings.delegate)) {
                $(settings.delegate).delegate(selector, 'mouseover', hoverStart);
                $(settings.delegate).delegate(selector, 'mouseout', hoverEnd);
            } else {
                $el.hover(hoverStart, hoverEnd);
            }
            function hoverStart(e) {
                if (isset(settings.textCallback)) {
                    $text.html(settings.textCallback($el));
                }
                // mouseover
                if (settings.followCursor) {
                    $('body').bind('mousemove', mousemoveHandler);
                }
                timer = setTimeout(
                    function() {
                        if (!settings.followCursor) {
                            var offset = calculateOffset($el, settings);
                            $tip.css('top', offset.top);
                            $tip.css('left', offset.left);
                        }
                        $tip.fadeIn(settings.fadeIn);
                    },
                    settings.delay
                );
                //
            };
            function hoverEnd(e) {
                //if (e.relatedTarget != $tip.get(0)) {
                    clearTimeout(timer);
                    $tip.hide();
                    $el.unbind('mousemove', mousemoveHandler);
                //}
                //
            };
        });

        //
        // Helpers
        //

        function mousemoveHandler(e) {
            $tip.css('left', e.pageX + 10).css('top', e.pageY + 10);
        }

        function calculateOffset($element, options) {
            var offset = $element.offset();
            
            // Calculate horizontal offset
            if (isset(options.left)) {
                offset.left -= $tip.width();
                offset.left += options.left;
            } else if (isset(options.center)) {
                offset.left += (($element.width()
                            -    $tip.outerWidth())
                            /    2)
                            +    options.center;
            } else if (isset(options.right)) {
                offset.left += $element.width();
                offset.left += options.right; 
            }
            
            // Calculate vertical offset
            if (isset(options.top)) {
                offset.top -= $tip.height();
                offset.top += options.top;
            } else if (isset(options.middle)) {
                offset.top += (($element.height()
                           -    $tip.outerHeight())
                           /    2)
                           +    options.middle;
            } else if (isset(options.bottom)) {
                offset.top += $element.height();
                offset.top += options.bottom;
            }
            
            return offset;

        }

    }
    
    function isset(thing) {
        return (typeof thing != 'undefined');
    }
    
})(jQuery);

