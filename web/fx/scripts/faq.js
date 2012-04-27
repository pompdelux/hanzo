$(function()
{
  $('div.tx-irfaq-pi1 dt').click(function()
  {
    var parents = $(this).parents();
    for (i=0, max=parents.length; i<max; i++)
    {
      if ($(parents[i]).get(0).tagName == 'DL')
      {
        $(parents[i]).find('dd').toggle();
        $(parents[i]).find('img').toggleClass('active');

        if ($(parents[i]).find('img').hasClass('active'))
        {
          $(parents[i]).find('img').attr('src', '/templates/pompdelux/images/faqminus.gif');
        }
        else
        {
          $(parents[i]).find('img').attr('src', '/templates/pompdelux/images/faqplus.gif');
        }
      }
    }
  });

  $('div.tx-irfaq-pi1 a.toggleAll').click(function()
  {
    $(this).parent().find('dd').toggle();
    $(this).toggleClass('active');

    if ($(this).hasClass('active'))
    {
      $(this).text(ExposeTranslation.get('js:hide.all');
      $(this).parent().find('img').each(function(x)
      {
        $(this).attr('src', '/templates/pompdelux/images/faqminus.gif');
      });

    }
    else
    {
      $(this).text(ExposeTranslation.get('js:show.all');
      $(this).parent().find('img').each(function(x)
      {
        $(this).attr('src', '/templates/pompdelux/images/faqplus.gif');
      });
    }
  });
});
