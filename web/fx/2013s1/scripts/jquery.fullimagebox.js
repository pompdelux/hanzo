(function( $ ){

    var $wrapper,
        image,
        list = [],
        index = 0,
        x = 0,
        y = 0;

    var defaults = {
          'id' : 'full-image-wrapper',
          'loader-src' : '/fx/images/ajax-loader.gif'
        };

    var methods = {
        init : function( options ) {

            var settings = $.extend( defaults, options);

            $('img[rel=zoom]').each(function(i){
                list.push($(this));
            });

            if(!$('#'+settings.id).length){
                $wrapper = $('<div/>', {
                        id: settings.id
                    }).css({
                        'position' : 'absolute',
                        'z-index' : '300',
                        'top' : '0',
                        'left' : '0'
                    }).append('<div class="loader"></div><a class="prev"></a><a class="next"></a>')
                    .hide()
                ;
            }else{
                $wrapper = $('div#'+settings.id);
            }
            _renderButtons();

            image = document.createElement('img');
            $(image).on('load', function(e){

                $(this).css({'width':'100%', 'top':'0', 'left':'0', 'position': 'absolute'});

                $wrapper.css({'width' : '100%', 'height': $(window).outerHeight()}).append(image).find('.loader').hide();
            });
            _changeImage($(this));
            $wrapper.mousemove(function(e){
                var $this = $(this),
                    ratio = ($this.height() - e.pageY)/$this.height();

                y = ratio * ($(image).height() - $this.height());
            });

            $wrapper.hover(
                function(){
                    var $this = $(this);
                    var scroller = setInterval( function(){
                        if(y > 0){
                            $(image).css('top',-y+'px');
                        }
                    }, 30 );
                    $(this).data('scroller', scroller);
                },
                function(){
                    var scroller = $(this).data('scroller');
                    clearInterval( scroller );
                }
            );

            $wrapper.on('click', function(e){
                $(this).fadeOut(400, function(e){
                    $(this).hide();
                });
            });

            $wrapper.appendTo($('body')).fadeIn(2000);

        },
        next : function( listIndex ){
            if (undefined === listIndex) {
              index = listIndex;
            }
            var tempImage = document.createElement('img');
            tempImage.src = list[index].src;
            $(tempImage).on('load', function (e){
                $(image).hide('300', function(){
                    image.src = list[index].src;
                    _renderButtons();
                    $(this).show('300');
                });
            });
        }
    };

    function _renderButtons(){
        if(index > 0){
            $('.prev', $wrapper).show();
        }else{
            $('.prev', $wrapper).hide();
        }
        if(index !== list.length-1){
            $('.next', $wrapper).show();
        }else{
            $('.next', $wrapper).hide();
        }
    }

    function _changeImage( item ){
        if(item.data('high-res')){
            image.src = item.data('high-res');
        }else{
            image.src = item.attr('src');
        }
    }

    $.fn.fullImageBox = function( method ) {
        // Method calling logic
        if ( methods[method] ) {
            return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.tooltip' );
        }
    };

})( jQuery );