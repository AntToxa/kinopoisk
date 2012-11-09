$(document).ready(function () {
    $(window).resize(function () {
        $.footer();
    });
    $.footer();
    $('#jQueryTpl-table-rows').template('jQueryTpl-table-rows');

    $(".input-field").datepicker({
        regional:"ru",
        dateFormat:"dd.mm.yy",
        onSelect:function (dateText, inst) {
            var form = $('#form-data');
            var url= form.attr("action");
            $.ajax({
                url: url,
                type: "POST",
                data: form.serialize(),
                success: function(data) {
                    if(data.responseCode==200 ){
                        $('#tbl-show-film-info').find('tr').each(
                            function(){
                                if(!$(this).hasClass('no-delete')){
                                    $(this).remove();
                                }
                            }
                        );
                        if(data.film_info.length){
                            var tpl = $.tmpl('jQueryTpl-table-rows', data.film_info);
                            $('#no-films').hide();
                        }else{
                           $('#no-films').show();
                        }
                        tpl.insertAfter(
                            '#header-film'
                        );
                        $.footer();
                    } else if(data.responseCode==400){
                        alert(data.film_info_error);
                    }
                    else{
                        alert("Упс! Ошибочка вышла. Попробуйте снова и будет Вам счастье.");

                    }
                }
            });
        }
    });
});


$.footer = function () {
    $('#main,#background-glare').height('auto');
    $('#main,#background-glare').height($(document).height() - $('#footer').height());
}


jQuery.fn.liScroll = function (settings) {
    settings = jQuery.extend({
        travelocity:0.07
    }, settings);
    return this.each(function () {
        var $strip = jQuery(this);
        $strip.addClass("newsticker")
        var stripWidth = 1;
        $strip.find("li").each(function (i) {
            stripWidth += jQuery(this, i).outerWidth(true);
        });
        var $mask = $strip.wrap("<div class='mask'></div>");
        var $tickercontainer = $strip.parent().wrap("<div class='tickercontainer'></div>");
        var containerWidth = $strip.parent().parent().width();
        $strip.width(stripWidth);
        var totalTravel = stripWidth + containerWidth;
        var defTiming = totalTravel / settings.travelocity;

        function scrollnews(spazio, tempo) {
            $strip.animate({left:'-=' + spazio}, tempo, "linear", function () {
                $strip.css("left", containerWidth);
                scrollnews(totalTravel, defTiming);
            });
        }

        scrollnews(totalTravel, defTiming);

    });
};


(function (jq) {
    jq.autoScroll = function (ops) {
        ops = ops || {};
        ops.styleClass = ops.styleClass || 'scroll-to-top-button';
        var t = jq('<div class="' + ops.styleClass + '"></div>'),
            d = jq(ops.target || document);
        jq(ops.container || 'body').append(t);

        t.css({
            opacity:0,
            position:'absolute',
            top:0,
            right:0
        }).click(function () {
                jq('html,body').animate({
                    scrollTop:0
                }, ops.scrollDuration || 1000);
            });

        d.scroll(function () {
            var sv = d.scrollTop();
            if (sv < 10) {
                t.clearQueue().fadeOut(ops.hideDuration || 200);
                return;
            }

            t.css('display', '').clearQueue().animate({
                top:sv,
                opacity:0.8
            }, ops.showDuration || 500);
        });
    };
})(jQuery);