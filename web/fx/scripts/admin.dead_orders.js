var adminDeadOrders = (function($) {
    var pub = {};

    pub.init = function() {
        attachEvents();
    };

    function attachEvents() {
        $("#dead-orders-form a.delete-order, #failed-orders-form a.delete-order").click(function(e) {
            e.preventDefault();
            var orderId = $(this).data('order-id');
            dialoug.confirm( Translator.get('js:notice'), $(this).data('confirm-message'), function(choise) {
                if (choise === 'ok') {
                    $.ajax({
                        url: base_url+'orders/delete/'+orderId,
                        dataType: 'json',
                        async : false,
                        success: function(data) {
                            if (response.status) {
                              $a.parent().parent().fadeOut(function() {
                                $(this).remove();
                              });

                              window.scrollTo(window.scrollMinX, window.scrollMinY);
                              dialoug.slideNotice(response.message);
                            }
                        }
                    });
                }
            });
        });


        $("a.delete-order-log").on('click', function(event) {
            event.preventDefault();
            var $element = $(this).closest('tr');
            var href = this.href;
            dialoug.confirm(Translator.get('js:notice'), $(this).data('confirm-message'), function(choise) {
                if (choise !== 'ok') {
                    return;
                }

                var xhr = $.ajax({
                    url: href,
                    dataType: 'json',
                    async : false
                });

                xhr.done(function(response) {
                    if (!response.status) {
                        return;
                    }

                    $element.fadeOut(function() {
                        $element.remove();
                    });
                });
            });
        });

        // preview and re-send orders
        $('a.preview-order, a.resend-order').on('click', function(event) {
            event.preventDefault();

            var $a = $(this);
            $a.parent().find('.loader').show();
            var $row = $a.closest('tr');
            var oid = $a.closest('form').find('input').val();

            if (undefined === oid) {
              oid = '';
            } else {
              oid = '/'+oid;
            }

            $.getJSON(this.href+oid, function(result) {
              if (result.status) {
                if (result.message) {
                  dialoug.info(Translator.get('js:notice'), result.message);
                  $row.fadeOut();
                } else {
                  $.colorbox({html: result.data.html});
                }
              } else {
                dialoug.alert(Translator.get('js:notice'), result.message);
              }
              $a.parent().find('.loader').hide();
            });
        });

        //
        $('#dead-orders-form tbody tr').each(function() {
            var $row = $(this);
            var oid = $(this).data('id');
            $.ajax({
                url: base_url+'orders/dead/check/'+oid,
                type: 'post',
                dataType: 'json',
                success: function(result) {
                    if (result.status) {
                        if (result.message) {
                            dialoug.info(Translator.get('js:notice'), result.message);
                            $row.fadeOut();
                        }
                    } else {
                        $row.find('td.error-msg').text(result.data.error_msg + ' ');
                    }
                }
            });
        });
    }

    return pub;
})(jQuery);
