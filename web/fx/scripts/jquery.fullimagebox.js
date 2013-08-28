(function( $ ){

    var wrapper,
        $wrapper,
        image,
        $image,
        tempImage,
        list = [],
        index = 0,
        x = 0,
        y = 0;

    var defaults = {
          'id' : 'full-image-wrapper',
          'selector' : 'a[rel=full-image]'
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

            list = []; // Reset the list of images and create a new one
            $(settings.selector).each(function(i){
                list.push({
                    'src' : $(this).attr('href')
                });
            });

            if(!$('#'+settings.id).length){
                $image.hide();
                wrapper = document.createElement('div');
                $wrapper = $(wrapper).attr({'id':settings.id, 'class':'full-image-box'})
                    .css({
                        'height': $(window).outerHeight()
                    }).append('<div class="loader"></div><a href="#" class="close"></a><a href="#" class="prev" style="display:none;"></a><a href="#" class="next" style="display:none;"></a>')
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
                $(document, $wrapper).keydown(function(e){
                    if (e.keyCode == 37) {
                        _next( index - 1 );
                    }else if (e.keyCode == 39) {
                        _next();
                    }
                });
            }else{
                $wrapper = $('div#'+settings.id);
            }

            // Calculate the position of the image dependent of the mouse
            $wrapper.mousemove(function(e){
                var $this = $(this),
                    ratio = ($this.height() - (e.pageY - $this.offset().top))/$this.height();

                y = ratio * ($(image).height() - $this.height());
            });

            // Set the image to the right position
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


            // When a new image is loaded into the temporary image, switch it to the main image.
            $(tempImage).bind('load', function (e){
                //alert('loaded '+image.src===tempImage.src);
                if(image.src !== tempImage.src){ // Workaround. fade was firing multiple times.

                    $image.animate({opacity: 'hide'}, 400, function() {
                        if ($('html').hasClass('ie'))
                            this.style.removeAttribute('filter');

                        image.src = tempImage.src;
                        _renderButtons();

                        $image.animate({opacity: 'show'}, 400, function() {
                            if ($('html').hasClass('ie'))
                                this.style.removeAttribute('filter');
                            $('.loader').animate({opacity: 'hide'}, 240);
                        });
                    });

                }else{
                    $('.loader').hide();
                }
            });
            // Handle if the image couldnt get loaded. This will trigger a next() but can only increment
            // .on('error', function(){
            //     _next();
            // });

            $('.close, img.full-image', $wrapper).on('click', function(e){
                e.preventDefault();
                $wrapper.fadeOut(400);
            });

            $image.show();
            $wrapper.appendTo($('body'));
        },
        next : function( listIndex ){
            _next( listIndex );
        },
        prev : function( ){
            _next( index - 1 );
        },
        open : function( ){
            _next( _getIndex( this ) );
            $wrapper.fadeIn(100);
        }
    };
    function _next ( listIndex ) {
        if (undefined !== listIndex) {
            if(list.length < listIndex || listIndex === -1){
                index = 0;
            }else{
                index = listIndex;
            }
        }else{
            index++;
        }
        $('.loader', $wrapper).show();

        if(list[index].src !== ''){
            tempImage.src = list[index].src;
        }else{
            _next();
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

    // Searches all images in the list for the same source.
    // @return int index of image in list
    function _getIndex( element ){
        for (var i = list.length - 1; i >= 0; i--) {
            if(list[i].src === $(element[0]).attr('href'))
                return i;
        }
        return -1;
    }

    $.fn.fullImageBox = function( method ) {
        // Method calling logic
        if ( methods[method] ) {
            return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.fullImageBox' );
        }
    };

})( jQuery );
