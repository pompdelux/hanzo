var checkout = (function($) {
  var pub = {};
  var blocks = {};

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
      success: function(data) {
        if ( !data.status )
        {
          dialoug.alert( i18n.t('Notice!'), data.message);
        }
      }
    });
  }

  function Block(blockName, blockSelector) {
    this.data = {
      name: blockName,
      selector: blockSelector,
      state: false,
    };

    this.update = function() {
      //console.log( "Updating: "+ this.data.name +" "+ (this.data.state ? 'true' : 'false'));
      checkoutUpdate( this.data.name, this.data.state );
      updateServerState( this.data.name, this.data.state, this.data );
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

      $(this.data.selector+' form input').on('click', function(event) {
        self.data.state = true; // If the user has selected a shipping method, everything is ok
        self.data.selectedMethod = $(this).val();
        self.update();
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
        console.log($(this).data('type'));
        self.data.addresses.push($(this).data('type'));
      });
    };
    blocks.address.reveal = function() {
      this.data.state = false;
      $(this.data.selector).slideDown();
      var shippingMethod = blocks.shipping.data.selectedMethod;

      switch(shippingMethod) {
        case '10':
          this.data.state = true;
          this.getData();
          this.update();
          $(this.data.selector +" "+ ".checkout-block-type-shipping").show();
          $(this.data.selector +" "+ ".checkout-block-type-overnightbox").hide();
        break;
        case '11':
          $(this.data.selector +" "+ ".checkout-block-type-shipping").show();
          $(this.data.selector +" "+ ".checkout-block-type-overnightbox").hide();
          var hasCompanyAddress = false;
          $(this.data.selector).find(".account-address-block").each(function() {
            if ( $(this).data('allowedDest') === 'company' )
            {
              hasCompanyAddress = true;
            }
          });

          if ( !hasCompanyAddress )
          {
            this.data.state = false;
            this.update();
            dialoug.alert( i18n.t('Notice!'), i18n.t('checkout.shipping_business') );
          }
          else
          {
            this.data.state = true;
            this.update();
          }
          // look for normalAddress in common.js on old shop
          // if not ok hide payment
          break;
        case '12':
          this.data.state = false;
          this.update();
          $(this.data.selector +" "+ ".checkout-block-type-shipping").hide();
          $(this.data.selector +" "+ ".checkout-block-type-overnightbox").show();
          // if not ok hide payment
        break;
      }
    };
     
    /**
     * Payment block
     */
    blocks.payment = new Block( 'payment', '#checkout-block-payment');
    blocks.payment.init = function() {
      var self = this;
      $(this.data.selector+' form input[type=radio]').on('click', function(event) {
        //console.log('---------------------------------------------------');
        var $paytypeSelector      = $(this).closest('form');
        self.data.selectedMethod  = $paytypeSelector.attr('id');
        self.data.selectedPaytype = $(this).val();
        self.data.state           = true;
        self.update();

        var $selectedInput = $(this);
        $(self.data.selector+' form input[type=radio]').each(function(item) {
          if ( this !== $selectedInput.get(0) )
          {
            $(this).prop('checked',false);
          }
        });
      });
    };

    /**
     * Summery block
     */
    blocks.summery = new Block( 'summery', '#checkout-block-summery');
    blocks.summery.reveal = function() {
      var self = this;
      $(this.data.selector).slideDown();
      self.data.state = true;
      self.update();
    };

    /**
     * Confirm block
     */
    blocks.confirm = new Block( 'confirm', '#checkout-block-confirm' );
    blocks.confirm.init = function() {
      var self = this;

      $("#checkout-execute").on('click', function(event) { // When the user clicks the link, check if everything is as it should be
        event.preventDefault();

        $.each(blocks, function(item) {
          if ( this.data.state !== true )
          {
            //console.log(this.data.name +" does not have a ok state");
            this.setMessage( i18n.t('Not filled correctly'), 'error' );
            this.error();
            self.data.state = false;
            self.update();
            return false;
          }
        });

        self.data.state = true;
        self.update();

        $.ajax({
          url: base_url+'checkout/validate',
          type: 'post',
          dataType: 'json',
          success: function(data) {
            if ( data.status )
            {
              $.each(blocks, function(item) {
                this.hide();
              });

              blocks.payment.execute();
            }
            else
            {
              $.each(blocks, function(item) {
                if ( this.data.name === data.data.name )
                {
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
      //console.log('confirm reveal');
      var self = this;
      $.each(blocks, function(item) {
        if ( this.data.name !== self.data.name && this.data.state !== true )
        {
          //console.log(this.data.name +" does not have a ok state");
          this.setMessage( i18n.t('Not filled correctly'), 'error' );
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

  /**
   * Main method, called on checkout page 
   */
  pub.init = function() {
    blockInit();

    $.each(blocks, function(item) {
      this.init();
    });
  };

  return pub;
})(jQuery);
