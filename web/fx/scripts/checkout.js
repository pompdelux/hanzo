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
          blocks.payment.reveal();
        }
        else
        {
          blocks.payment.hide();
          blocks.summery.hide();
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
        }
      break;
      case 'summery':
        if (state === true)
        {
          $("#checkout-execute").removeClass('hidden');
        }
      break;
    } 
  }

  /* Displays an message in a block */
  function setBlockMessage( selector, msg, type ) {
    console.log(selector+' '+msg+' '+type); 
    $(selector).addClass(type);
    $(selector).html(msg);
  }

  /**
   * Shipping block
   */
  blocks.shipping = (function() {
    var name     = 'shipping',
        selector = '#checkout-block-shipping',
        state    = false;

    function update(event) {
      // If the user has selected a shipping method, everything is ok
      checkoutUpdate( name, state );
    };

    function reset() {
      $(selector+' input').prop('checked',false)
    };

    return { 
      init : function() {
        $(selector+' form input').on('click', function(event) {
          state = true;
          update(event);
        });
      },
      getName : function() {
        return name;
      },
      reveal: function() {
        // This is the first block shown and it is not hidden
        return true;
      },
      error: function() {
        console.log('shipping marked as error');
        state = false;
        reset();
        update(false);
      },
      hide: function() {
        // This can't be hidden as it is the first block
        return false;
      },
      setMessage: function( msg, type ) {
        setBlockMessage( selector+' .msg', msg, type );
      },
      execute: function() {
        // What should we do here? in payment it makes sense, because we submit the form
      }
    };
  }());

  /**
   * Payment block
   */
  blocks.payment = (function() {
    var name     = 'payment',
        selector = '#checkout-block-payment',
        $paytypeSelector = '',
        state    = false;

    function update(event) {
      checkoutUpdate( name, state );
    };

    function reset() {
      // FIXME: does not work
      $(selector+' input').prop('checked',false)
    };

    return { 
      init : function() {
        $(selector+' form input').on('click', function(event) {
          state = true;
          $paytypeSelector = $(this).closest('form');
          // FIXME: clear other form on click
          update(event);
        });
      },
      getName : function() {
        return name;
      },
      reveal: function() {
        $(selector).slideDown();
      },
      error: function() {
        state = false;
        reset();
        update(false);
      },
      hide: function() {
        console.log('payment hide');
        $(selector).slideUp();
      },
      setMessage: function( msg, type ) {
        setBlockMessage( selector, msg, type );
      },
      execute: function() {
        console.log('Executing selected payment method');
        console.log( $paytypeSelector.attr('name') );
      }
    };
  }());

  /**
   * Summery block
   */
  blocks.summery = (function() {
    var name     = 'summery',
        selector = '#checkout-block-summery',
        state    = false;

    function update() {
      checkoutUpdate( name, state );
    };

    function reset() {
    
    };

    return { 
      init : function() {
      },
      getName : function() {
        return name;
      },
      reveal: function() {
        $(selector).slideDown();
        // FIXME: delay update until slideDown is done
        state = true;
        update();
      },
      error: function() {
        state = false;
        update(false);
      },
      hide: function() {
        $(selector).slideUp();
      },
      setMessage: function( msg, type ) {
        setBlockMessage( selector, msg, type );
      },
      execute: function() {
      }
    };
  }());

  /**
   * Main method, called on checkout page 
   */
  pub.init = function() {
    $.each(blocks, function(item) {
      this.init();
    });

    // When the user clicks the link, check if everything is as it should be
    $("#checkout-execute").on('click', function(event) {
      event.preventDefault();
      $.ajax({
        url: base_url+'checkout/state',
        type: 'post',
        dataType: 'json',
        success: function(data) {
          if ( data.status )
          {
            blocks.payment.execute();
          }
          else
          {
            $.each(blocks, function(item) {
              if ( this.getName() === data.data.name )
              {
                this.setMessage( data.message, 'error' );
                this.error();
              }
            });
          }
        }
      });
    });
  };

  return pub;
})(jQuery);
