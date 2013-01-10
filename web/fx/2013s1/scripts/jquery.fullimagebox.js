(function( $ ){

    var $wrapper,
        image,
        $image,
        tempImage,
        list = [],
        index = 0,
        x = 0,
        y = 0;

    var defaults = {
          'id' : 'full-image-wrapper'
        };

    var methods = {
        init : function( options ) {

            var settings = $.extend( defaults, options);

            if(!$(image).length){
                image = document.createElement('img');
            }
            if(!$(tempImage).length){
                tempImage = document.createElement('img');
            }else{
                tempImage.src = '';
            }
            $image = $(image).addClass('full-image');
            $('img[rel=full-image]').each   (function(i){
                list.push($(this));
            });

            if(!$('#'+settings.id).length){
                $image.hide();
                $wrapper = $('<div/>', {
                        id: settings.id,
                        class: 'full-image-box'
                    }).css({
                        'height': $(window).outerHeight()
                    }).append('<div class="loader"></div><a href="#" class="close"></div><a href="#" class="prev" style="display:none;"></a><a href="#" class="next" style="display:none;"></a>')
                    .append($image)
                    .hide()
                ;
                $('.prev', $wrapper).on('click' , function (e) {
                    e.preventDefault();
                    _next(index - 1);
                });

                $('.next', $wrapper).on('click' , function (e) {
                    e.preventDefault();
                    _next();
                });
            }else{
                $wrapper = $('div#'+settings.id);
            }

            $wrapper.mousemove(function(e){
                var $this = $(this),
                    ratio = ($this.height() - (e.pageY - $this.offset().top))/$this.height();

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

            $('.close, img.full-image', $wrapper).on('click', function(e){
                e.preventDefault();
                $wrapper.fadeOut(400, function(e){
                    $wrapper.remove();
                    list = [];
                });
            });

            $image.show();
            $wrapper.appendTo($('body')).fadeIn(100);

            _next(0); // Set the initial picture in the box.
            _renderButtons();
        },
        next : function( listIndex ){
            _next( listIndex );
        },
        prev : function( ){
            _next( index - 1 );
        }
    };
    function _next ( listIndex ) {
        if (undefined !== listIndex) {
            if(undefined === list[listIndex]){
                return;
            }
            index = listIndex;
        }else{
            index++;
        }
        $('.loader', $wrapper).show();

        // Load the new image into an temporary image, until loaded
        $(tempImage).on('load', function (e){
            if(image.src !== tempImage.src){ // Workaround. fade was firing multiple times.
                $(image).fadeOut('300', function(){
                    image.src = $(tempImage).attr('src');
                    console.log(image.src);
                    _renderButtons();
                    $('.loader').hide();
                    $(this).fadeIn('300');
                });
            }else{
                $('.loader').hide();
            }
        })
        .on('error', function(){
            _next();
        });

        if($(list[index]).data('high-res')){
            tempImage.src = $(list[index]).data('high-res');
        }else{
            tempImage.src = $(list[index]).attr('src');
        }
    }
    function _renderButtons(){
        if(index > 0){
            $('.prev', $wrapper).fadeIn('100');
        }else{
            $('.prev', $wrapper).fadeOut('100');
        }
        if(index !== list.length-1){
            $('.next', $wrapper).fadeIn('100');
        }else{
            $('.next', $wrapper).fadeOut('100');
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