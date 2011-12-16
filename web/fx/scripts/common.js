/**
 * pseudo sleep function
 *
 * @param delay miliceconds to sleep
 */
function sleep(delay)
{
  var start = new Date().getTime();
  while (new Date().getTime() < start + delay);
}

/**
 * add a notice slide box message to the top right of the screen.
 *
 * @param message the message to display
 * @param duration the number of miliseconds the message should be displayed
 */
function slideNotice(message, duration) {
  $('body').prepend('<div id="slide-notice-box">' + message + '</div>');
  var $slide = $('#slide-notice-box');
  var slideWidth = $slide.outerWidth();
  var docWidth = $(document).width();

  $slide.css({
    left : docWidth + 'px',
    width : slideWidth
  });

  $slide.animate({
    left : '-='+(slideWidth + 10 )+'px'
  }, {
    complete : function() {
      sleep(duration);
      $slide.animate({left : docWidth}, {
        complete : function() {
          $slide.remove();
        }
      });
    }
  });
}

function changeColorSetEventHandler()
{
  $('.color').change(function(e) {
    var p_id = $(this).attr('rel');
    if ($('#quantitybox_' + p_id) && $('quantitybox_' + p_id).html() != '')
    {
      $('#quantitybox_' + p_id).remove();
    }
    if ($(this).val() != -1)
    {
      $('#thequan_' + p_id).append('\
        <div class="quantity_set" id="quantitybox_' + p_id + '">\
          <label id="quantitylabel_' + p_id +'">'+i18n.qty+':</label><br />\
          <select name="cart_quantity[]" id="quantity_' + p_id + '" class="quantitys_set"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option></select>      \
        </div>'
      );

      $('#quantity').focus();
    }
    else
    {
      $('#submit').html('');
    }
  });
}

function addSubmitAndQuantity(e)
{
  $(document).unbind('close.facebox', addSubmitAndQuantity);

  if ($('#quantitybox').length == 0) {
    $('#submit').append('<div id="quantitybox" class="f-left"><label>'+i18n.qty+':</label><select name="quantity" id="quantity" title=""><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option></select></div><input type="button" value="'+i18n.addToBasket+'" name="submit" id="btn-submit" class="button" onclick="addToBasket()" />');
  }

  if (typeof e == 'object' && e.data.date) {
    $('#quantity').attr('title', e.data.date);
  }
}

function changeColorEventHandler()
{
  $('#color').change(function() {
    if ($('#quantitybox') && $('quantitybox').html() != '')
    {
      $('#quantitybox').remove();
    }

    if ($('#color').val() != -1)
    {
      var params = {
        id : $('#color option:selected').val(),
        type : 'getStockLocation',
        qty: 1
      };
      $.getJSON('/ajax.php', params, function (data) {
        var date = $('#color option:selected').attr('lang');
        if (date)
        {
          fbStockNotice(date);
        }
        else
        {
          addSubmitAndQuantity(1);
        }
      });
    }
    else
    {
      $('#submit').html('');
    }
  });
}

function fbStockNotice(date)
{
  var notice = formatStockNotice(date);
  $('body').addClass('xXx');

  jQuery.facebox(notice);
  $(document).bind('close.facebox', {
    date: date
  }, addSubmitAndQuantity);
}

function formatStockNotice(date)
{
  var notice = i18n.lateArrival;
  notice = notice.replace('{product}', $('h1').text() + ' ' + $('#product_size option:selected').text() + ' ' + $('#color option:selected').text());
  notice = notice.replace('{date}', date);
  return notice;
}

/**
 * Fetch colors based on size product id
 */
function fetchColors(productSize, productID)
{
  return function() {
    var product_size = (productSize == undefined) ? $(this).val() : productSize;
    var product_id   = (productID == undefined) ? $('#productsListing').val() : productID;

    if (product_id != -1)
    {
      $.getJSON('/ajax.php?type=color&pid=' + product_id + '&ps=' + product_size, function(data) {

        var paragraph = document.getElementById('colors');
        var sizeselect = document.createElement('select');

        // Set some styles..
        with (sizeselect.style)
        {
          padding = '0';
          margin = '0 5px 0 0';
          }

        sizeselect.setAttribute('name', 'products_id');
        sizeselect.setAttribute('id', 'colorselect');

        /* create the default 'choose' option */
        var option = document.createElement('option');
        var text   = document.createTextNode(i18n.size);
        option.setAttribute('value', -1);
        option.appendChild(text);
        sizeselect.appendChild(option);

        // alert

        $.each(data, function(i, item) {
          var option = document.createElement('option');
          var text = document.createTextNode(item);
          option.setAttribute('value', i);
          option.appendChild(text);
          sizeselect.appendChild(option);
        });

        paragraph.appendChild(sizeselect);
      });
    }
  }
}

function fetchCategoryChildren(cat_id, appto)
{
  if (cat_id != 'empty')
  {
    $.getJSON('/ajax.php?type=getCategoryChildren&id=' + cat_id, function(data) {

      if (data != null)
      {
        var html = '';
        var first = $('#top_categories_id option:first').html();
        html = '<option value="empty">' + first + '</option>';

        $.each(data, function(i, item)
        {
          html += '<option value="'+i+'">' + item + '</option>';
        });

        $(appto).html('');
        $(appto).append(html);
        $(appto).show();
        $(appto).focus();
      }
    });
  }
  else
  {
    $(appto).html('');
    $(appto).hide();
  }
}

$(function() {
  $('#product_set_info #submit').click(function() {
    if ($('.quantitys_set').size() == 0)
    {
      alert(i18n.chooseSizeAndColor);
      return false;
    }

    return true;
  });

  $("#select-domain a.open-menu").click(function(e) {
    e.preventDefault();
    $("#select-domain div").toggle();
  });

  $(".cart_delete_action a").click(function(e){
    e.preventDefault();
    var pid = $(this).attr('href');
    if (confirm(i18n.confirmDeleteFromBasket)) {
      $(this).parent().parent().parent().fadeOut('slow', function() {
        var params = {
          id : pid,
        basket : true
        };
        $.post('/ajax.php?type=removeFromBasket', params, function(data) {
          if (data.basket && data.total) {
            $('#mini-basket a').html(data.basket);
            $('td.total').html(data.total);
          }
        }, 'json');
      });
    }
    else
    {
      // handle cancel clicks
      this.checked = false;
      this.blur();
    }
  });

  $(".cart_edit_action a").click(function(e){
    e.preventDefault();
    var pid = $(this).attr('href');
    jQuery.facebox({ajax: '/ajax.php?type=edit_product_in_cart&pid='+pid});
  });

  /* Check in checkout shipping page, if delivery address country is other than private address*/
  $('#checkout_shipping_form input[type=submit]').click(function() {
    if ($('#deliver_address_country').val() != $('#private_address_country').val())
    {
      alert(i18n.countryAlert);
      return false;
    }
    return true;
  });

  /* Advanced search extra -->> */
  $('#advanced_search_extra #top_categories_id').change(function() {
    var cat_id = $('#top_categories_id option:selected').val();
    fetchCategoryChildren(cat_id,'#sub_categories_id');
    $('#categories_id').hide();
    $('#size').hide();
    $('#hidesearch').hide();
  });

  $('#advanced_search_extra #sub_categories_id').change(function() {
    var cat_id = $('#sub_categories_id option:selected').val();
    fetchCategoryChildren(cat_id,'#categories_id');
    $('#categories_id').hide();
    $('#size').hide();
    $('#hidesearch').hide();
  });

  //$('#advanced_search_extra #categories_id').change(function() {
  $('#advanced_search_extra #sub_categories_id').change(function() {
    $('#size').show();
  });

  /*
   * if size changed, we display the search button
   */
  $('#advanced_search_extra #attribute_1').change(function() {
    $('#hidesearch').show();
  });

  /* <<-- Advanced search extra */

  /**
   * Fetch sizes based on product id
   */
  $('#productsListing').change(function() {
    var product_id = $(this).val();
    if (product_id != -1)
    {
      $.getJSON('/ajax.php?type=size&pid=' + product_id, function(data) {
        var paragraph = document.getElementById('sizes');
        var sizeselect = document.createElement('select');

        // Set some styles..
        with (sizeselect.style)
        {
          padding = '0';
          margin = '0 5px 0 0';
        }

        sizeselect.setAttribute('name', 'sizeselect_product_id');
        sizeselect.setAttribute('id', 'sizeselect');

        /* create the default 'choose' option */
        var option = document.createElement('option');
        var text   = document.createTextNode(i18n.size);
        option.setAttribute('value', -1);
        option.appendChild(text);
        sizeselect.appendChild(option);

        // alert

        $.each(data, function(i, item) {
          var option = document.createElement('option');
          var text = document.createTextNode(item);
          option.setAttribute('value', item);
          option.appendChild(text);
          sizeselect.appendChild(option);
        });

        paragraph.appendChild(sizeselect);

        $('#sizeselect').change( fetchColors() );
      });
    }
  });

  /* stuff happens when choosing an element in the size dropdown */
  $('.product_set_size').change(function() {
    var product_size = $(this).val();
    var product_id   = $(this).attr('rel');

    if (product_size != -1)
    {
      $('#colors').html('');
      $('#submit').html('');
      $.getJSON('/ajax.php?type=color&pid=' + product_id + '&ps=' + product_size, function(data) {

        if ($('#color_' + product_id))
        {
          $('#color_' + product_id).remove();
          $('#colorlabel_' + product_id).remove();
          $('#quantity_' + product_id).remove();
          $('#quantitylabel_' + product_id).remove();
        }
        /* create the select tag */
        var colorselect = '<select name="products_id[]" rel="'+product_id+'" class="color" id="color_' + product_id + '">';

        /* create the default 'choose' option */
        var option = '<option value="-1">' + i18n.choose + '</option>';

        /* loop through all the elements returned from the ajax call */
        $.each(data, function(i, item) {
          option += '<option value="' + i + '">' + item.color + '</option>';
        });

        $('#colors_' + product_id).prepend('<label id="colorlabel_' + product_id +'">'+i18n.color+':</label><br />').append(colorselect + option + '</select>');
        $('#indicator').remove();


        /* stuff happens when choosing an element in the color dropdown */
        changeColorSetEventHandler();
      });
    }
    else
    {
      $('#colors').html('');
      $('#submit').html('');
    }
  });

  $('#product_size').change(function() {
    /* set some variables */
    var product_size = $(this).val();
    var product_id   = $('#product_id').val();
    $('#product_size').after('<img id="indicator" src="/templates/pompdelux/images/indicator.gif" />');
    /* erase the colors field */
    $('#colors').html('');

    /* call and get a Json object of colors */
    if (product_size != -1)
    {
      $('#colors').html('');
      $('#submit').html('');
      $.getJSON('/ajax.php?type=color&pid=' + product_id + '&ps=' + product_size, function(data) {
        var colorselect = '<select name="products_id" id="color">';
        var option = '<option value="-1">' + i18n.choose + '</option>';

        $.each(data, function(i, item) {
          option += '<option value="' + i + '" lang="' + item.fbmsg + '">' + item.color + '</option>';
        });
        $('#colors').prepend('<label>'+i18n.color+':</label>').append(colorselect + option + '</select>');
        $('#indicator').remove();
        /* stuff happens when choosing an element in the color dropdown */
        changeColorEventHandler();
      });
    }
    else
    {
      $('#colors').html('');
      $('#submit').html('');
    }
  });

//  if ($(":input[type!='hidden']")[0] && $('input:visible')[0]) {
//    $('input:visible')[0].focus();
//  }

  try {
    $('a[rel*=facebox]').facebox({opacity : 0.7});

    $(document).bind('reveal.facebox', function() {
      $('#facebox .body .content h2.mskema span').text($('h1').text());
    });

    /* set all a tags with rel="lightbox" to use the lightBox script */
    $('a[rel*=lightbox]').lightBox({
    //$('a.lightbox').lightBox({
      imageLoading : '/templates/pompdelux/scripts/images/lightbox-ico-loading.gif',
      imageBtnPrev : '/templates/pompdelux/scripts/images/lightbox-btn-prev.gif',
      imageBtnNext : '/templates/pompdelux/scripts/images/lightbox-btn-next.gif',
      imageBtnClose : '/templates/pompdelux/scripts/images/lightbox-btn-close.gif',
      imageBlank : '/templates/pompdelux/scripts/images/lightbox-blank.gif'
    });
  } catch (e) {}

//  $('div.mousetrap').live('click', function () {
//    $('a.cloud-zoom').click();
//  });

  /**
   * auto complete city names when entering zip codes.
   * only works for se/no/dk so if the request is made via .com we skip this step
   */
  var tld = document.location.hostname.match(new RegExp("\.([a-z,A-Z]{2,6})$"));
  try {
    if (tld[1] != 'com' && tld[1] != 'nl') {
      $('input[name="city"]').attr('readonly', 'readonly');

      // set city to readonly
      $('input[name="city"]').focus(function () {
        this.value = '';
        if ($('input[name="postcode"]').val() == '') {
          $('input[name="postcode"]')
            .css('border-color', '#a10000')
            .fadeOut(100).fadeIn(100)
            .fadeOut(100).fadeIn(100)
            .fadeOut(100).fadeIn(100)
            .focus();
          return;
        }
        var params = {
          type : 'getCityFromZip',
          q    : $('input[name="postcode"]').val()
        };
        $.getJSON('/ajax.php', params, function(data) {
          if (data && data.city) {
            $('input[name="city"]').val(data.city);
            $('input[name="postcode"]').css('border-color', '#444345');
            try {
              $('input[name="telephone"]').focus();
            } catch (e) {}
          }
          else {
            $('input[name="postcode"]').css('border-color', '#a10000').fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100).focus();
          }
        });
      });
    }
  } catch (e) {}

  $('.paymentClass').click(function () {
    $('.paymentType').each(function () {
      this.checked = false;
    });
    if ($(this).hasClass('dibs')) {
      $('#paymentSelected').val('dibs');
    } else {
      $('#paymentSelected').val(this.value);
    }
  });

  $("#checkout_payment_form input[type='submit']").click(function () {
      var n = $("#checkout_payment_form input:checked").length;
      if ( n == 0 ) {
        alert( i18n.selectPaymentCard );
        return false;
      }
  });
  /* <<-- hf@bellcom.dk: used for dibs card selection */

  $('div.invite-item a.closer').click(function () {
    $(this).parent().fadeOut("fast").remove();
  });
  $('a#trigger').click(function () {
    $('div.invite-item.hidden').clone().appendTo('#invite-container').hide().fadeIn("fast").removeClass('hidden');
    $('div.invite-item:last a.closer').click(function () {
      $(this).parent().fadeOut("fast").remove();
    });
  });

  /**
   * hf@bellcom.dk, 18-jan-2011: edit of products in the shopping cart -->>
   */
  $("body").delegate('#edit_product_in_cart #product_size', 'change', changeSizeEventHandler2());

  $("body").delegate('#edit_product_in_cart #quantity', 'change', function() {
    if ($('#submit_button').length !== 0) {
      $('#submit_button').remove();
    }
    $('#cancel').before('<div id="submit_button"><input type="button" class="button" value="'+i18n.updateBasket+'" name="submit" id="btn-cart-edit-update" onclick="updateBasket()" /></div>');
  });

  $("body").delegate('#edit_product_in_cart #color', 'change', function() {
    if ($('#quantitybox').length)
    {
      $('#quantitybox').remove();
      $('#submit_button').remove();
    }

    if ($('#color').val() != -1)
    {
      var params = {
        id : $('#color option:selected').val(),
        type : 'getStockLocation',
        qty: 1
      };
      $.getJSON('/ajax.php', params, function (data) {
        var date = $('#color option:selected').attr('lang');
        if (date)
        {
          var notice = formatStockNotice(date);
          $('body').addClass('xXx');
          $('#edit_product_in_cart .warning').html(notice);
        }
        $(document).unbind('close.facebox', addSubmitAndQuantity);

        if ($('#quantitybox').length === 0) {
          $('#cancel').before('<div id="quantitybox"><label for="quantity">'+i18n.qty+':</label><select name="quantity" id="quantity" title=""><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option></select></div><div id="submit_button"><input type="button" class="button" value="'+i18n.updateBasket+'" name="submit" id="btn-cart-edit-update" onclick="updateBasket()" /></div>');
        }

        if (typeof e == 'object' && e.data.date) {
          $('#quantity').attr('title', e.data.date);
        }
        $('#submit').data('isNewProduct',true);
      });

    }
    else
    {
      $('#submit').html('');
    }
  });
  /**
   * <<-- hf@bellcom.dk, 11-jan-2011: edit of products in the shopping cart
   */

  if ($('#body-checkout-shipping input[type="radio"]').length > 2) {
    $('#c-continue-block').hide();
  }

  var normalAddress;
  $('#body-checkout-shipping input[type="radio"]').click(function () {    
    // re insert "normal" address if detached by the overnight method
    if (normalAddress) {
      $('#ts-shipping-address h2').after(normalAddress);
      $("#overnightbox-address-display").hide();
      $('#c-continue-block p input.button').show();
    }
    
    var hrefs = $('#ts-shipping-address a').attr('href').split('?');
    $('#ts-shipping-address a').attr('href', hrefs[0] + '?type=' + $(this).attr('rel'));
    $('#c-continue-block').show();

    // no is allowed to not set company name
    var dom = /no$/g;
    if (dom.test(hrefs[0].split('/')[2])) {
      $('#c-continue-block p').show();
      return;
    }
    
    if (($(this).attr('rel') == 'company') &&
        ($('#is-company-address').val() == 0)
    ) {
      $('#c-continue-block p').hide();
      jQuery.facebox('<div class="error">' + i18n.chooseOrCreateCompanyAddress + '</div>');
    }
    else if ($(this).attr('rel') == 'overnightbox')
    {
      // Kill normal address display, show overnightbox fields
      normalAddress = $("#normal-address-display").detach();
      $("#overnightbox-address-display").show();
      if ($('#overnightbox-address-display div.error').length) {
        $('#c-continue-block p input.button').hide();
      }
    }
    else
    {
      $('#c-continue-block p').show();
    }
  });

  $('#f-new-address input[type="text"]').change(function() {
    $('#f-new-address input[type="radio"]:checked').attr('checked', false);
  });

  if ($('#c-continue-block').length) {
    var url = document.location.href.split('?');
    switch (url[1]) {
      case 'c=private':
        $('#body-checkout-shipping input[rel="private"]').click();
        break;
      case 'c=company':
        $('#body-checkout-shipping input[rel="company"]').click();
        break;
      case 'c=overnightbox':
        $('#body-checkout-shipping input[rel="overnightbox"]').click();
        break;
    }
  }

  /**
   * note, this is a really simple validation routine.
   */
  $('form').submit(function() {
    switch (this.id) {
      case 'f-new-address':
        if ($('#f-new-address input[type="radio"]:checked').length) {
          return;
        }
        $('body').data('error', '');
        $('#f-new-address input[rel="required"]').each(function(){
          if (this.value == '') {
            var txt = $(this).parent().text();
            txt = txt.split(':');
            $('body').data('error', $('body').data('error') + '<li>' + txt[0]  + '</li>');
          }
        });
        if ($('body').data('error').length) {
          var notice = '<ul>' + $('body').data('error') + '</ul>';
          $('body').removeData('error');
          jQuery.facebox('<div class="error">'+i18n.missingRequirements + notice + '</div>');
          return false;
        }
        break;
    }
  });

  $('table.hostess-paticipantlist tbody td.delete-trigger').click(function (){
    var participant = this.id.split('-')[2];
    if (false == isNaN(participant)) {
      if (confirm(i18n.confirmDeleteParticipant)) {
        var params = {
          type : 'deleteParticipant',
          id : participant
        };
        $.post('ajax.php', params);
        $('#participant-id-'+participant).parent().fadeOut();
      }
    }
  });


  $('input[type="radio"]').addClass('noborder');
  $('input[type="checkbox"]').addClass('noborder');


  // added funky version of the extended search form
  $('a.trigger-extended-search').click(function(){
    $(this).hide();
    $('form[name="advanced_search"]').hide();
    $('div#ze-extended-container').slideDown('fast', function() {
      $('div#ze-extended-container .step-1 a').click(function() {
        // reset selection
        $('div#ze-extended-container .step-1 a').removeClass('selected');
        $('div#ze-extended-container .step-2 a').removeClass('selected');
        $(this).addClass('selected');
        $(this).addClass('ac_loading');
        this.blur();

        // reset hidden vals
        var cat = this.href.split('#')[1];
        $('form[name="advanced_search"] input[name="categories_id"]').val(cat);
        $('form[name="advanced_search"] input[name="attribute_1"]').val('');

        // reset form
        $('form[name="advanced_search"]').hide();

        $.get('/ajax.php', {type : 'getCategorySizes', cid : cat}, function(result) {
          $('div#ze-extended-container .step-2 ul li').remove();
          for (i=0; i<result.length; i++) {
            $('div#ze-extended-container .step-2 ul').append('<li><a href="#'+result[i]+'">'+result[i]+'</a></li>');
          }
          $('div#ze-extended-container li a').removeClass('ac_loading');

          $('div#ze-extended-container .step-2').slideDown('fast', function() {
            $('div#ze-extended-container .step-2 a').click(function() {
              $('div#ze-extended-container .step-2 a').removeClass('selected');
              $(this).addClass('selected');
              $(this).addClass('ac_loading');
              this.blur();
              $('form[name="advanced_search"] input[name="attribute_1"]').val(this.href.split('#')[1]);
              if (!$('form[name="advanced_search"] div.final').length) {
                $('form[name="advanced_search"]').prepend('<div class="final"><h4>Indtast søgeord</h4></div>');
                $('form[name="advanced_search"]').append('\
                  <input type="hidden" value="1" name="inc_subcat"/>\
                  <input type="hidden" value="1" name="search_in_attributes" />\
                  <input type="hidden" value="=" name="attribute_1_match_type" />\
                  <input type="hidden" value="off" name="cms_search" />'
                );
              }
              $('form[name="advanced_search"]').attr('action', $('a.trigger-extended-search').attr('href'));
              $('form[name="advanced_search"]').submit();
              return false;
            });
          });

        }, 'json');

        return false;
      });
    });
    return false;
  });

  // zoom on product images
  initCloudZoom();

  $('.style-guide .element').hide();

  $('.productimage-small a').click(function(e) {
    var from = {
      small  : $(this).find('img').attr('src'),
      medium : this.rev,
      large  : this.href,
      id     : this.id
    }
    var to = {
      small  : $('.productimage-large a').attr('rev'),
      medium : $('.productimage-large a img').attr('src'),
      large  : $('.productimage-large a').attr('href'),
      id     : $('.productimage-large a').attr('id')
    }

    $('.productimage-large a').attr('rev', from.small);
    $('.productimage-large a').attr('href', from.large);
    $('.productimage-large a').attr('id', from.id);
    $('.productimage-large a img').attr('src', from.medium);

    this.rev = to.medium;
    this.href = to.large;
    $(this).find('img').attr('src', to.small);
    this.id = to.id;

    $('.style-guide .element').hide();
    $('.style-guide .' + from.id).show();

    initCloudZoom();
    e.preventDefault();
  });

  if (document.location.href.indexOf('#img') != -1) {
    var id = document.location.href.split('#img')[1];
    if ($('.productimage-large a#image-id-' + id).length) {
      if ($('.style-guide .image-id-' + id).length) {
        $('.style-guide .image-id-' + id).show();
      }
    } else {
      $('#image-id-' + id).click();
    }
  }
  // tabs
  $("ul.tabs").tabs("div.panes > div");

  // frontpage count down
  $('td.countdown strong').countdown({
    timezone : +1,
    until: i18n.countdownTo,
    layout:'<strong>' + i18n.countdownFormat + '</strong>'
  });

  // frontpage box
  if ($('html').hasClass('mobile') == false) {
    $('#body-frontpage #main a.button').wrap('<div class="btn"></div>');
    $('#body-frontpage #main a.button').click(function(event) {
      event.preventDefault();
      var $a = $(this);
      var $this = $a.parent();
      var $parent = $this.parent();
      var $child = $parent.children('div').first();

      var maxWidth = $('#container').width() - ($('nav.main').width() + $('#teasers').width() + 100);
      if (maxWidth > 670) {
        maxWidth = 670;
      }

      if ($child.height() == '500') {
        $a.text($a.data('text'));
        $this.animate({width : '350px'});
        $parent.animate({marginTop : '440px', width : '350px'});
        $child.animate({height : '100px', width : '350px'});
      } else {
        $a.data('text', $this.text());
        $a.text(i18n.close);
        $this.animate({width : maxWidth + 'px'});
        $parent.animate({marginTop : '75px', width : maxWidth + 'px'});
        $child.animate({height : '500px', width : maxWidth + 'px'});
      }
    });

    $(document).ready(function() {
      ajustMainContentOffset(1);
    });
  }

  // pop message
  try {
    if (js_alert !== undefined) {
      dialoug.alert(i18n.alertTitle, js_alert);
    }
  } catch (e) {}

  // open facebook in a new window
  $('li.facebook a').click(function(event) {
    event.preventDefault();
    window.open(this.href);
  });

  // ios class added to body
  switch (navigator.platform) {
    case 'iPad':
    case 'iPhone':
    case 'iPod':
      $('html').addClass('ios');
      break;
  }

  $('a[rel="slideshow"]').colorbox({
    rel: 'slideshow',
    close: i18n.close,
    previous : "«",
    next : "»",
    current : ""
  });

  $('input[name="handling-fee-3"]').hide();

  $('.productimage-large img.loupe').click(function() {
    var elm = $(this).closest('div').find('a.cloud-zoom');
    cloudZoomClick(elm);
  });

});

function ajustMainContentOffset() {
  var $main = $('#main');
  var $nav = $('nav.main');
  var navWidth = $nav.width();
  var offset = $main.offset();

  if (navWidth > offset.left) {
    $main.css({marginLeft : navWidth + 'px'});
  }
}

// triggers
$(document).bind('cbox_closed', function(){
  initCloudZoom();
  $('body').click();
});

function changeSizeEventHandler()
{
  return function()
  {
    var productSize = $(this).val();
    var productID = $("#product_id").val();
    $('#product_size').after('<img id="indicator" src="/templates/pompdelux/images/indicator.gif" />');

    if (productSize != -1)
    {
      $('#colors').html('');
      $('#submit').html('');
      $("#quantit").html('');
      $.getJSON('/ajax.php?type=color&pid=' + productID + '&ps=' + productSize, function(data) {
        var colorselect = '<select name="products_id" id="color">';
        var option = '<option value="-1">' + i18n.choose + '</option>';

        $.each(data, function(i, item) {
          option += '<option value="' + i + '" lang="' + item.fbmsg + '">' + item.color + '</option>';
        });
        $('#colors').prepend('<label>'+i18n.color+':</label>').append(colorselect + option + '</select>');
        $('#indicator').remove();
        $('#submit').data('isNewProduct',true);
      });
    }
    else
    {
      $('#colors').html('');
      $('#submit').html('');
      $("#quantity").html('');
    }
  }
}

/* FIXME */
function changeSizeEventHandler2()
{
  return function()
  {
    var productSize = $(this).val();
    var productID = $("#product_id").val();
    $('#product_size').after('<img id="indicator" src="/templates/pompdelux/images/indicator.gif" />');

    if (productSize != -1)
    {
      $('#colors').html('');
      $('#submit_button').html('');
      $("#quantitybox").html('');
      $.getJSON('/ajax.php?type=color&pid=' + productID + '&ps=' + productSize, function(data) {
        var colorselect = '<select name="products_id" id="color">';
        var option = '<option value="-1">' + i18n.choose + '</option>';

        $.each(data, function(i, item) {
          option += '<option value="' + i + '" lang="' + item.fbmsg + '">' + item.color + '</option>';
        });
        $('#colors').prepend('<label>'+i18n.color+':</label>').append(colorselect + option + '</select>');
        $('#indicator').remove();
        $('#submit').data('isNewProduct',true);
      });
    }
    else
    {
      $('#colors').html('');
      $('#submit_button').html('');
      $("#quantitybox").html('');
    }
  }
}

function initCloudZoom() {
  if ($('.productimage-large div.mousetrap').length) {
    $('.productimage-large div.mousetrap').remove();
  }
  var params = {
    zoomWidth: 165,
    zoomHeight: 330,
    showTitle: false,
    adjustX: 8,
    click : function(a) {
      cloudZoomClick(a);
    }
  }
  $('.cloud-zoom, .cloud-zoom-gallery').CloudZoom(params);
}

function cloudZoomClick(a) {
  $(a).unwrap();
  $.colorbox({
    html: '<img src="'+$(a).attr('href')+'" alt="" />',
    close: i18n.close
  });
}

function selectCardAction( card )
{
  $('#ze-buy-form').append('<input type="hidden" value="'+card+'" name="paytype"/>');
  $('#ze-buy-form').submit();
}

/**
* hf@bellcom.dk, 18-jan-2011: edit of products in the shopping cart, 99% copy of addToBasket -->>
*/
function updateBasket()
{
  var data = {
    type : 'getStockLocation',
    qty  : $('#quantity').val(),
    id   : $('select#color option:selected').val()
  };
  $.getJSON('/ajax.php', data, function(result) {
    var date = $('#quantity').attr('title');
    var sbm = true;
    var pop = true;

    if ($('body').hasClass('xXx') && date == result.date) {
      pop = false;
    }

    if (pop && result.date) {
      var notice = i18n.lateArrivalAlert;
      notice = notice.replace('{product}', $('h1').text() + ' ' + $('#product_size option:selected').text() + ' ' + $('#color option:selected').text());
      notice = notice.replace('{date}', result.date);

      if (!confirm (notice)) {
        sbm = false;
      } else {
        date = result.date;
      }
    }

    if (sbm) {
      if ( $('#submit').data('isNewProduct') === true )
      {
        //$.post('/ajax.php?type=removeFromBasket', {id : $('#product_id').val()}, function(data) {}, 'json');
        $.ajax({
            type: 'POST',
            url: '/ajax.php?type=removeFromBasket',
            data: {id : $('#product_id').val()},
            dataType: 'json',
            async: false
            });
      }

      var params = {
        type     : 'addToBasket',
        qty      : $('#quantity').val(),
        qtyTotal : true,
        id       : $('select#color option:selected').val(),
        date     : date,
        dd       : true
      };

      $.post('/ajax.php', params, function (data)
      {
        $('div#quantitybox p.a-notice').remove();
        $('#quantity').css({
          borderColor : '#444345',
          borderWidth : '1px'
        });
        if (data.notice)
        {
          $('div#quantitybox').append('<p class="a-notice">'+data.notice+'</p>');
          $('#quantity').css({
            borderColor : '#a10000',
            borderWidth : '2px'
          });
        }
        else
        {
          $.facebox.close();
          // Alternative nice style could be: old product: .slideUp() and then .slideDown() with the new product
          // var oldProduct = $("#shopping_cart_product_table").find("input[name='products_id[]']").val();
          window.location.reload();
        }
      }, 'json');
    }
  });
}
/**
 * <<-- hf@bellcom.dk, 18-jan-2011: edit of products in the shopping cart
 */

function addToBasket()
{
  var data = {
    type : 'getStockLocation',
    qty  : $('#quantity').val(),
    id   : $('select#color option:selected').val()
  };
  $.getJSON('/ajax.php', data, function(result) {
    var date = $('#quantity').attr('title');
    var sbm = true;
    var pop = true;

    if ($('body').hasClass('xXx') && date == result.date) {
      pop = false;
    }

    if (pop && result.date) {
      var notice = i18n.lateArrivalAlert;
      notice = notice.replace('{product}', $('h1').text() + ' ' + $('#product_size option:selected').text() + ' ' + $('#color option:selected').text());
      notice = notice.replace('{date}', result.date);

      if (!confirm (notice)) {
        sbm = false;
      } else {
        date = result.date;
      }
    }

    if (sbm) {
      var params = {
        type : 'addToBasket',
        qty  : $('#quantity').val(),
        id   : $('select#color option:selected').val(),
        date : date,
        dd   : true
      };
      $.post('/ajax.php', params, function (data)
      {
        $('div#quantitybox p.a-notice').remove();
        $('#quantity').css({
          borderColor : '#444345',
          borderWidth : '1px'
        });
        if (data.notice)
        {
          $('div#quantitybox').append('<p class="a-notice">'+data.notice+'</p>');
          $('#quantity').css({
            borderColor : '#a10000',
            borderWidth : '2px'
          });
        }
        else
        {
          window.scrollTo(window.scrollMinX, window.scrollMinY);
          $('#mini-basket a').html(data.basket);

          // notification
          slideNotice('"' + $('h1').text() + '" just added to your basket', 2000);

          $('div#colors').html('');
          $('div#submit').html('');
          if (data.sizes)
          {
            $('select#product_size').html(data.sizes);
          }
          else
          {
            $('select#product_size').hide();
            $("label[for='product_size']").replaceWith(data.out_of_stock);
          }
        }
      }, 'json');
    }
  });
}

// moved here from supersize
$(function(){
  $.getDocHeight = function(){
    var D = document;
    return Math.max(Math.max(
        document.body.scrollHeight,
        document.documentElement.scrollHeight
      ),
        Math.max(document.body.offsetHeight,
        document.documentElement.offsetHeight
      ),
        Math.max(document.body.clientHeight,
        document.documentElement.clientHeight
    ));
  };

  if (($('html').hasClass('mobile') == false) &&
      (navigator.platform == 'iPad' || navigator.platform == 'iPhone' || navigator.platform == 'iPod') &&
      (-1 == navigator.userAgent.indexOf('OS 5_'))
  ) {
    $.fn.placeFooter();

    window.onorientationchange = function() {
      $.fn.placeFooter();
    }

    $('body').bind('near-you-container.loaded', function() {
      $.fn.placeFooter();
    });
  }
});

function alert_dialout(msg) {
  $.colorbox({
    html : msg,
    close: i18n.close
  });
}

(function($) {
  /**
   * handles placement of footer - due to iOS issues with position:fixed
   * -------------------------------------------------------------------
   */
  $.fn.placeFooter = function() {
    $("footer").css({
      position : 'absolute',
      height: $('footer').height() + 'px',
      top : ($.getDocHeight() - $('footer').outerHeight(true)) + 'px',
      width : $('body').width()
    });
  };
})(jQuery);

(function($) { 
  /**
   * animated effect on first menu level
   */
  $('nav.main > ul > li > a').hover(function() {
    var $this = $(this);
    var left = $this.data('pl-save');
    if (left == undefined) {
      left = parseInt($this.css('padding-left'));
      $this.data('plsave', left);
    }
    $this.animate({'padding-left': (left + 10) + 'px'}, 'fast');
  }, function() {
    var $this = $(this);
    $this.animate({'padding-left': $this.data('plsave') + 'px'}, 'fast');
  });
})(jQuery);
