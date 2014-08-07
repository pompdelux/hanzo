$(function() {
  $('div.tx-irfaq-pi1 dt').click(function() {
    var parents = $(this).parents();
    var max = parents.length;
    for (var i=0; i<max; i++) {
      if ($(parents[i]).get(0).tagName == 'DL') {
        $(parents[i]).find('dd').toggle();
        $(parents[i]).find('img').toggleClass('active');

        if ($(parents[i]).find('img').hasClass('active')) {
          $(parents[i]).find('img').attr('src', cdn_url+'fx/images/faqminus.gif');
        } else {
          $(parents[i]).find('img').attr('src', cdn_url+'fx/images/faqplus.gif');
        }
      }
    }
  });

  $('div.tx-irfaq-pi1 a.toggleAll').click(function() {
    $(this).parent().find('dd').toggle();
    $(this).toggleClass('active');

    if ($(this).hasClass('active')) {
      $(this).text(Translator.trans('hide.all'));
      $(this).parent().find('img').each(function(x) {
        $(this).attr('src', cdn_url+'fx/images/faqminus.gif');
      });
    } else {
      $(this).text(Translator.trans('show.all'));
      $(this).parent().find('img').each(function(x) {
        $(this).attr('src', cdn_url+'fx/images/faqplus.gif');
      });
    }
  });
});
