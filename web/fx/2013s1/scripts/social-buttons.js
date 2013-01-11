(function(document, $) {


  var social = (function($) {
    var pub = {};

    pub.init = function() {
      $('a.social-button').on('click', function(e){
        e.preventDefault();
        window.open($(this).attr('href'), 'Share - POMPdeLUX', 'width=728,height=500');
      });
    };

    return pub;
  })(jQuery);

  social.init();

})(document, jQuery);
