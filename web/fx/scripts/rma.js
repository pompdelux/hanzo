var rma = (function($) {
  var pub = {};

  pub.init = function() {

    $('.rma-activitycode').on('change', function(e) {
        $select = $(this);
        if($select.val()) {
            $select.parent().next('label').slideDown('fast');
        } else {
            $select.parent().next('label').slideUp('fast');
        }
    });

    $('#rma-submit').on('click', function(event) {
        $(this).attr('disabled', 'disabled');
        dialoug.loading($(this), Translator.get('js:please.wait') );
        var products = [];
        $('.rma-activitycode').each(function(index, el) {
            var id = $(this).data('id');
            if ($(el).val()) {
                products.push({
                    'id' : id,
                    'rma_activitycode' : $(el).val(),
                    'rma_description' : $('#description-productid-' + id).val()
                });
            }
        });
        if(products.length > 0) {
            // Add a hidden form to send.
            $form = $('<form></form>')
                .attr('method', 'GET')
                .append($('<input type="hidden" name="order_id">').val(rma_order_id))
                .append($('<input type="hidden" name="products">').val(JSON.stringify(products)))
                .appendTo('body')
                .submit()
            ;
        }
        dialoug.stopLoading();
        $(this).removeAttr('disabled');
    });
  };

  return pub;
})(jQuery);

if($('#body-rma').length){
    rma.init();
}