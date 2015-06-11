var rma = (function ($) {
    var pub = {};

    pub.init = function () {

        $('.rma-activitycode').on('change', function (e) {
            var $select = $(this);
            var $context = $select.parent().parent();
            $('.rma-cause, .rma-description, .rma-productreplacement', $context).hide();

            if ($select.val()) {
                // Show the appopriate dropdown for causes.
                $context.find('.rma-' + $select.val() + '-causes').slideDown('fast').css('display', 'inline-block');
                $context.find('.rma-description').slideDown('fast').css('display', 'inline-block');

                if ($select.val() === 'replacement') {
                    $context.find('.rma-productreplacement').slideDown('fast');
                }
            }
        });

        $('.rma-submit').on('click', function (event) {
            event.preventDefault();
            generatePdf();
        });

        $('.rma-form').on('submit', function (event) {
            event.preventDefault();
            generatePdf();
        });

        $('#rma-return-all-order').on('change', function (e) {
            if ($(this).attr('checked')) {
                // Return all products.
                // 1. Hide products
                // 2. Set all product to return activity
                // 3. Set all causes to Fortrudt Køb

                $('.rma-form .rma-cause, .rma-form .rma-description, .rma-form .rma-productreplacement').hide();
                $('.rma-form .rma-activitycode').val('return');
                $('.rma-form .rma-return-causes')
                    .slideDown('fast')
                    .css('display', 'inline-block')
                    .find('select')
                    .val(Translator.trans('rma.return.all.text'));
            }
        });

    };

    pub.uploadInit = function() {
      var $form = $("form#rma_claims");
      $form.on('submit',(function(e) {
        e.preventDefault();

        // Override entire page with answer if success
        var $page = $form.parent(),
        $message  = $(".message"),
        url       = $(this).attr('action');

        $.ajax({
          url:          url,
          type:         "POST",
          data:         new FormData(this),
          contentType:  false, // The content type used when sending data to the server. Default is: "application/x-www-form-urlencoded"
          cache:        false,
          processData:  false  // To send DOMDocument or non processed data file it is set to false (i.e. data should not be in the form of string)
        }).done(function(data) {
          if (data.error === true) {
            $message.html(data.error_msg).addClass('error').removeClass('hidden');
          }
          else {
            $page.html(data.msg).addClass("message");
          }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            $message.html("An unexpected error occurred:<br>"+textStatus+"<br>"+errorThrown).addClass('error').removeClass('hidden');
        }).always(function() {
            $('html,body').animate({scrollTop: 0});
        });
      }));

      // Show next element if file has been selected
      $("input[type='file']", $form).change(function() {
        $(this).next().removeClass('hidden');
      });

      // Show input fields if radio button selected
      $("input[name='contact']", $form).change(function() {
        var name = $(this).val();

        $(".contact_methods input[type='text']", $form).addClass('hidden');
        $("input[name='"+name+"_value']", $form).removeClass('hidden');
      });
    };

    function generatePdf() {

        var has_errors = false;
        $('.rma-activitycode').each(function (index, el) {
            var id = $(this).data('id');
            if ($('#replacement-master-lineid-' + id).val() &&
                (!$('#replacement-size-lineid-' + id).val() || !$('#replacement-color-lineid-' + id).val())) {
                dialoug.notice(Translator.trans('rma.product.not.filled.correctly'), 'error', 4000, $('#replacement-master-lineid-' + id).closest('.quickorder'));
                has_errors = true;
            }
        });

        if (has_errors) {
            return;
        }

        var $submit_button = $('.rma-submit');
        $submit_button.attr('disabled', 'disabled');
        dialoug.loading($submit_button, Translator.trans('please.wait'));
        var products = [];

        $('.rma-activitycode').each(function (index, el) {
            var id = $(this).data('id');

            if ($(el).val()) {
                products.push({
                    'id': id,
                    'rma_activitycode': $(el).val(),
                    'rma_cause': $('#' + $(el).val() + '-cause-lineid-' + id).val(),
                    'rma_description': $('#description-lineid-' + id).val(),
                    'rma_replacement': {
                        'master': $('#replacement-master-lineid-' + id).val(),
                        'size': $('#replacement-size-lineid-' + id).val(),
                        'color': $('#replacement-color-lineid-' + id).val()
                    }
                });
            }
        });

        if (products.length > 0) {
            // Force PDF download trough an POST form. Products variable can be
            // extremely long.
            $('<form></form>')
                .attr('method', 'POST')
                .append($('<input type="hidden" name="products">').val(JSON.stringify(products)))
                .appendTo('body')
                .submit();

                window.setTimeout(function(){
                    $('.cached-version').show();
                }, 3000);
            // Force download of return label with an iframe delayed a couple of
            // seconds.
            if (-1 == jQuery.inArray($('html').data('domainkey'), ['nl', 'salesnl', 'com'])) {
                window.setTimeout(function(){
                    $('<iframe src="' + base_url + 'account/consignor/return-label/' + rma_order_id + '" width="1" height="0" class="off" frameborder="0"></iframe>')
                        .appendTo('body');
                }, 3000);
            } else {
                $('.cached-version a.returnlabel').hide();
            }
        }

        dialoug.stopLoading();
        $submit_button.removeAttr('disabled');
    }

    return pub;
})(jQuery);

if ($('#body-rma').length) {
    rma.init();
}
if ($("form#rma_claims").length) {
  rma.uploadInit();
}
