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
                    .val(Translator.get('js:rma.return.all.text'));
            }
        });

    };

    function generatePdf() {

        var has_errors = false;
        $('.rma-activitycode').each(function (index, el) {
            var id = $(this).data('id');
            if ($('#replacement-master-lineid-' + id).val() &&
                (!$('#replacement-size-lineid-' + id).val() || !$('#replacement-color-lineid-' + id).val())) {
                dialoug.notice(Translator.get('js:rma.product.not.filled.correctly'), 'error', 4000, $('#replacement-master-lineid-' + id).closest('.quickorder'));
                has_errors = true;
            }
        });

        if (has_errors) {
            return;
        }

        var $submit_button = $('.rma-submit');
        $submit_button.attr('disabled', 'disabled');
        dialoug.loading($submit_button, Translator.get('js:please.wait'));
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
            // pop return lable window
            if (-1 == jQuery.inArray($('html').data('domainkey'), ['nl', 'salesnl', 'com'])) {
                window.open(base_url + 'account/consignor/return-label/' + rma_order_id);
            }

            // Add a hidden form to send.
            var $form = $('<form></form>')
                .attr('method', 'GET')
                .append($('<input type="hidden" name="order_id">').val(rma_order_id))
                .append($('<input type="hidden" name="products">').val(JSON.stringify(products)))
                .appendTo('body')
                .submit()
            ;
        }

        dialoug.stopLoading();
        $submit_button.removeAttr('disabled');
    }

    return pub;
})(jQuery);

if ($('#body-rma').length) {

    rma.init();
}
