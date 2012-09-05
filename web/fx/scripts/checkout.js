var checkout = (function($) {
  var pub = {};
  var blocks = {};
  var order = {};

  /*
   * This controlles the flow between the states
   * It should be called from the blocks when they change state
   */
  function checkoutUpdate( name, state ) {
    switch(name) {
      case 'shipping':
        if (state === true)
        {
          blocks.address.reveal();
        }
        else
        {
          blocks.address.hide();
          blocks.payment.hide();
          blocks.summery.hide();
          blocks.confirm.hide();
        }
      break;
      case 'address':
        if (state === true)
        {
          blocks.payment.reveal();
          if (blocks.payment.data.state) {
            checkoutUpdate( blocks.payment.data.name, blocks.payment.data.state );
          }
        }
        else
        {
          blocks.payment.hide();
          blocks.summery.hide();
          blocks.confirm.hide();
        }
      break;
      case 'payment':
        if (state === true)
        {
          blocks.summery.reveal();
        }
        else
        {
          blocks.summery.hide();
          blocks.confirm.hide();
        }
      break;
      case 'summery':
        if (state === true)
        {
          blocks.confirm.reveal();
        }
        else
        {
          blocks.confirm.hide();
        }
      break;
    }
  }

  /* Displays an message in a block */
  function setBlockMessage( selector, msg, type ) {
    $(selector).addClass(type);
    $(selector).html(msg);
    $(selector).show();
  }

  function updateServerState( block, state, data ) {
    $.ajax({
      url: base_url+'checkout/update/'+block+'/'+state,
      type: 'post',
      dataType: 'json',
      data: {data: data},
      async: false,
      cache: false,
      success: function(data) {
        if ( !data.status )
        {
          dialoug.alert(ExposeTranslation.get('js:notice'), data.message);
        }
      }
    });
  }

  function Block(blockName, blockSelector) {
    this.data = {
      name: blockName,
      selector: blockSelector,
      state: false
    };

    /**
     * Sequence is very important, server must be in uptodate state before updating what the user sees
     */
    this.update = function() {
      updateServerState( this.data.name, this.data.state, this.data );
      checkoutUpdate( this.data.name, this.data.state );
    };

    this.reset = function() {
      $(this.data.selector+' input').prop('checked',false);
    };

    this.init = function() {
      return true;
    };

    this.reveal = function() {
      $(this.data.selector).slideDown();
      return true;
    };

    this.error = function() {
      this.state = false;
      this.reset();
      this.update();
    };

    this.hide = function() {
      $(this.data.selector).slideUp();
      return true;
    };

    this.setMessage = function( msg, type ) {
      setBlockMessage( this.data.selector+' .msg', msg, type );
    };

    this.execute = function() {
      return true;
    };
  }

  function blockInit() {
   /**
    * Shipping block
    */
    blocks.shipping = new Block( 'shipping', '#checkout-block-shipping' );
    blocks.shipping.init = function() {
      $(this.data.selector+' form input').each( function(item) {
        $(this).prop('disabled',false);
      });

      var self = this;
      var pos = $('#checkout-block-address').offset();

      self.data.selectedMethod = order.DeliveryMethod || undefined;

      if ( self.data.selectedMethod !== undefined ) {
        self.data.state = true;
        $( '#shipping_method_'+self.data.selectedMethod ).prop('checked',true);
        self.update();
        $('html,body').animate({scrollTop : parseInt( pos.top,10 )});
      }

      if ( $( this.data.selector+' form input').length === 1 ) {
        self.data.state = true; // Only one shipping method exist, check it and go
        self.data.selectedMethod = $( this.data.selector+' form input').val();
        $( '#shipping_method_'+self.data.selectedMethod ).prop('checked',true);
        self.update();
        $('html,body').animate({scrollTop : parseInt( pos.top,10 )});
        return; // skip setting click event on input
      }

      $(this.data.selector+' form input').on('click', function(event) {
        self.data.state = true; // If the user has selected a shipping method, everything is ok
        self.data.selectedMethod = $(this).val();
        self.update();
        $('html,body').animate({scrollTop : parseInt( pos.top,10 )});
      });
    };

    /**
     * Address block
     */
    blocks.address = new Block( 'address', '#checkout-block-address');
    blocks.address.getData = function() {
      var self = this;
      this.data.addresses = [];
      $(this.data.selector).find(".account-address-block").each(function() {
        if ($(this).data('type')) {
          self.data.addresses.push($(this).data('type'));
        }
      });
    };
    blocks.address.reveal = function() {
      var self = this;
      this.data.state = false;
      $(this.data.selector).slideDown();
      var shippingMethod = blocks.shipping.data.selectedMethod;

      if ( $(".account-address-block").length < 2 ) { /* There are missing addresses */
        this.data.state = false;
        this.update();
        return;
      }

      this.getData();
      // Same switch is in blocks.summery to display/hide addresses, please update both
      switch(shippingMethod) {
        case 'S': // .no
        case '20': // .com
        case '30': // .se
        case '60': // .nl
        case '10': // .dk privat
        case '500': // .fi
          this.data.state = true;
          this.update();
          $(this.data.selector +" "+ ".checkout-block-type-shipping").show();
          $(this.data.selector +" "+ ".checkout-block-type-overnightbox").hide();
        break;
        case '11': // .dk erhverv
          $(this.data.selector +" "+ ".checkout-block-type-shipping").show();
          $(this.data.selector +" "+ ".checkout-block-type-overnightbox").hide();
          var hasCompanyAddress = false;
          $(this.data.selector).find(".account-address-block").each(function() {
            if ( $(this).data('allowedDest') === 'company' ) {
              hasCompanyAddress = true;
            }
          });

          if ( !hasCompanyAddress ) {
            this.data.state = false;
            this.update();
            dialoug.alert(ExposeTranslation.get('js:notice'), ExposeTranslation.get('js:checkout.shipping_business'));
          }
          else {
            this.data.state = true;
            this.update();
          }
          // look for normalAddress in common.js on old shop
          // if not ok hide payment
        break;
        case '12': // .dk døgnpost
          $(this.data.selector +" "+ ".checkout-block-type-shipping").hide();
          $(this.data.selector +" "+ ".checkout-block-type-overnightbox").show();
          var hasOvernightboxAddress = false;
          $(this.data.selector).find(".account-address-block").each(function() {
            if ( $(this).data('allowedDest') === 'overnightbox' ) {
              hasOvernightboxAddress = true;
            }
          });

          if ( !hasOvernightboxAddress ) {
            this.data.state = false;
            this.update();
            dialoug.alert(ExposeTranslation.get('js:notice'), ExposeTranslation.get('js:checkout.shipping_overnightbox'));
          }
          else {
            this.data.state = true;
            this.update();
          }
        break;
      }

      $('#checkout-block-address a').on('click', function(event) {
        dialoug.loading($(this));

        var shipping_id = $('input[name="shipping"]:checked').val();
        var url = this.href+'/'+shipping_id;
        var $target = $(this).closest('td');
        event.preventDefault();

        $.getJSON(url, function(result) {
          $.colorbox({
            // 'overlayClose' : false,
            'escKey' : false,
            'close' : '',
            'html': result.data.html
          });

          dialoug.stopLoading();

          var $form = $('#cboxLoadedContent form');

          $form.on('click', 'input.button.left', function(event) {
            event.preventDefault();
            $.colorbox.close();
          });

          // autofill the city in dk/no/se
          if ($form.data('callback') == 'postal') {
            var $city = $('input#form_city', $form);
            $city.attr('readonly', 'readonly');

            var last_zip = '';
            $form.on('keyup', 'input#form_postal_code', function(event) {
              if (this.value.length > 2 && last_zip != this.value) {
                last_zip = this.value;
                var url = base_url+'service/get-city-from-zip/'+this.value;
                $.getJSON(url, function(result) {
                  if (result.status) {
                    $city.val(result.data.city);
                  }
                });
              }
            });
          }

          $form.on('submit', function(event) {
            event.preventDefault();
            $('input', $form).removeClass('error');
            $('div.error', $form).remove();

            $.post($form.attr('action'), $form.serialize(), function(result) {
              if (result.status) {

                var tpl = {
                  10 : '%first_name% %last_name%<br>%postal_code% %city%<br>%address_line_1%<br>%country%',
                  20 : '%first_name% %last_name%<br>%postal_code% %city%<br>%address_line_1%<br>%country%',
                  30 : '%first_name% %last_name%<br>%postal_code% %city%<br>%address_line_1%<br>%country%',
                  60 : '%first_name% %last_name%<br>%postal_code% %city%<br>%address_line_1%<br>%country%',
                  'S' : '%first_name% %last_name%<br>%postal_code% %city%<br>%address_line_1%<br>%country%',
                  500 : '%first_name% %last_name%<br>%postal_code% %city%<br>%address_line_1%<br>%country%',
                  11 : '%company_name%<br>Att: %first_name% %last_name%<br>%postal_code% %city%<br>%address_line_1%<br>%country%',
                  12 : '%first_name% %last_name%<br>%postal_code% %city%<br>Box: %address_line_1%'
                };

                var address = tpl[shipping_id]
                  .replace('%company_name%', result.data.address.company_name)
                  .replace('%first_name%', result.data.address.first_name)
                  .replace('%last_name%', result.data.address.last_name)
                  .replace('%postal_code%', result.data.address.postal_code)
                  .replace('%city%', result.data.address.city)
                  .replace('%address_line_1%', result.data.address.address_line_1)
                  .replace('%country%', result.data.address.country)
                ;

                $('div.account-address-block', $target).html(address);
                $.colorbox.close();

                self.data.state = true;
                self.update();
              } else {
                $form.prepend('<div class="error">'+result.message+'</div>');
                $.each(result.data, function(index, data) {
                  $('input#form_'+index, $form).addClass('error');
                });
                $.colorbox.resize();
              }
            }, 'json');
          });
        });
      });
    };

    /**
     * Payment block
     */
    blocks.payment = new Block( 'payment', '#checkout-block-payment');
    blocks.payment.init = function() {
      var self = this;

      self.data.selectedMethod  = order.BillingMethod || undefined;
      self.data.selectedPaytype = order.PaymentMethod || undefined;

      if ( self.data.selectedMethod !== undefined && self.data.selectedPaytype !== undefined ) {
        $("ul#payment-method-"+self.data.selectedMethod+" input[type=radio]").each(function() {
          if ( $(this).val() == self.data.selectedPaytype ) {
            $(this).prop('checked',true);
          }
        });
        self.data.state = true;
        self.update();
      }

      $(this.data.selector+' form input[type=radio]').on('click', function(event) {
        var $paytypeSelector      = $(this).closest('form');
        self.data.selectedMethod  = $paytypeSelector.attr('id');
        self.data.selectedPaytype = $(this).val();
        self.data.state           = true;
        self.update();

        dialoug.loading($(this).parent(), '', 'append');

        var $selectedInput = $(this);
        $(self.data.selector+' form input[type=radio]').each(function(item) {
          if ( this !== $selectedInput.get(0) )
          {
            $(this).prop('checked',false);
          }
        });
      });
    };

    blocks.payment.reveal = function() {
      var selector = this.data.selector;

      if ( $("form#dibs").length > 0 ) {
        var fieldsToUpdate = [
          'orderid',
          'amount',
          'md5key',
          'accepturl',
          'delivery01.Firstname',
          'delivery02.Lastname',
          'delivery03.Company',
          'delivery04.Address1',
          'delivery05.Address2',
          'delivery06.City',
          'delivery07.Postcode',
          'delivery08.StateProvince',
          'delivery09.Country',
          'delivery10.Telephone',
          'delivery11.Email',
          'delivery12.OrderId',
        ];
        // Update dibs form, should do nothing to other payment methods
        $.ajax({
          url: base_url + 'payment/dibs/formupdate',
          type: 'post',
          dataType: 'json',
          async: false,
          cache: false,
          success: function(data) {
            $.each(fieldsToUpdate, function(index,value) {
              $("form#dibs input[name='"+value+"']").val(data.fields[value]);
            });

            $(selector).slideDown();
            return true;
          }
        });
      }
      else {
        $(selector).slideDown();
        return true;
      }
    };

    blocks.payment.execute = function() {
      var ok = true;
      $.each(blocks, function(index, item) {
        if (item.data.state !== true) { ok = false; }
      });

      if (ok) {
        var $form = $('#'+blocks.payment.data.selectedMethod);
        console.log(new Error().lineNumber);
        if ($form.attr('action') !== '') {
          console.log(new Error().lineNumber);
          $form.submit();
        }
      }

      return true;
    };

    /**
     * Summery block
     */
    blocks.summery = new Block( 'summery', '#checkout-block-summery');
    blocks.summery.reveal = function() {
      var self = this;
      var $container = $(this.data.selector);
      $.getJSON(base_url + 'checkout/summery', function(result) {

        dialoug.stopLoading();

        if($container.length) {
          $container.replaceWith(result.data);
        } else {
          $('#main #checkout-block-confirm').before(result.data);
        }

        $.each(blocks, function(index, item) {
          if (item.data.name !== 'summery' && item.data.name !== 'confirm') {
            $(item.data.selector).slideUp();
          }
        });

        $('#continue-button').hide();

        // Hide/Show addresses, see blocks.address, please update both
        var shippingMethod = blocks.shipping.data.selectedMethod;
        switch(shippingMethod) {
          case 'S': // .no
          case '20': // .com
          case '30': // .se
          case '60': // .nl
          case '10': // .dk privat
          case '500': // .fi
            $(blocks.address.data.selector +" "+ ".checkout-block-type-shipping").show();
            $(blocks.address.data.selector +" "+ ".checkout-block-type-overnightbox").hide();
          break;
          case '11':
            $(blocks.address.data.selector +" "+ ".checkout-block-type-shipping").show();
            $(blocks.address.data.selector +" "+ ".checkout-block-type-overnightbox").hide();
          break;
          case '12':
            $(blocks.address.data.selector +" "+ ".checkout-block-type-shipping").hide();
            $(blocks.address.data.selector +" "+ ".checkout-block-type-overnightbox").show();
          break;
        }

        $(self.data.selector).slideDown();
        self.data.state = true;
        self.update();
      });
    };

    /**
     * Confirm block
     */
    blocks.confirm = new Block( 'confirm', '#checkout-block-confirm' );
    blocks.confirm.init = function() {
      var self = this;

      $('#checkout-block-confirm a.edit').on('click', function(event) {
        event.preventDefault();
        $.each(blocks, function(index, item) {
          if (item.data.name !== 'summery' && item.data.name !== 'confirm') {
            $(item.data.selector).slideDown();
          } else {
            $(item.data.selector).slideUp();
          }
        });

        var $button = $('#continue-button');
        if ($button.length) {
          $button.show();
        } else {
          $('#main').append('<div id="continue-button" class="checkout-block"><a href="" class="button right">'+ExposeTranslation.get('js:forward')+'</a></div>');
          $('#continue-button a').on('click', function(event) {
            event.preventDefault();
            blocks.summery.reveal();
          });
        }
      });

      // When the user clicks the link, check if everything is as it should be
      $("#checkout-execute").on('click', function(event) {
        event.preventDefault();
        var dataToVerify = [];

        $.each(blocks, function(item) {
          if ( this.data.state !== true ) {
            this.setMessage(ExposeTranslation.get('js:not.filled.correctly'), 'error' );
            this.error();
            self.data.state = false;
            self.update();
            return false;
          }

          dataToVerify.push( this.data );
        });

        self.data.state = true;
        self.update();

        $.ajax({
          url: base_url+'checkout/validate',
          type: 'post',
          dataType: 'json',
          async: false,
          cache: false,
          data: {data: dataToVerify},
          success: function(data) {
            if ( data.status === true ) {
              $.each(blocks, function(item) {
                this.hide();
              });

              blocks.payment.execute();
            } else {
              $.each(blocks, function(item) {
                if ( this.data.name === data.data.name ) {
                  this.setMessage( data.message, 'error' );
                  this.error();
                  self.data.state = false;
                  self.update();
                }
              });
            }
          }
        });
      });
    };

    blocks.confirm.reveal = function() {
      $(this.data.selector).slideDown();
      var self = this;
      $.each(blocks, function(item) {
        if ( this.data.name !== self.data.name && this.data.state !== true ) {
          this.setMessage(ExposeTranslation.get('js:not.filled.correctly'), 'error');
          this.error();
          self.data.state = false;
          self.update();
          return false;
        }
      });

      self.data.state = true;
      self.update();
    };
  }

  function populateOrder() {
    $.ajax({
      url: base_url+'checkout/populate_order',
      type: 'post',
      dataType: 'json',
      cache: false,
      async: false,
      success: function(data) {
        if (data.error) {
          order = {};
        }
        else {
          order = data.order;
        }
      }
    });
  }

  /**
   * Main method, called on checkout page
   */
  pub.init = function() {
    populateOrder();
    blockInit();

    $.each(blocks, function(item) {
      this.init();
    });
  };

  return pub;
})(jQuery);
