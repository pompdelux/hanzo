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
          $("#checkout-execute").addClass('hidden');
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
          $("#checkout-execute").addClass('hidden');
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
    $(selector).addClass(type);
    $(selector).html(msg);
  }

  function updateServerState( block, state, data )
  {
    $.ajax({
      url: base_url+'checkout/update/'+block+'/'+state,
      type: 'post',
      dataType: 'json',
      data: {data: data},
      success: function(data) {
      }
    });
  }

  /**
   * Shipping block
   */
  blocks.shipping = (function() {
    var name     = 'shipping',
        selector = '#checkout-block-shipping',
        selectedMethod = '',
        state    = false;

    function update(event) {
      checkoutUpdate( name, state );
      updateServerState(name,state,{selected_method:selectedMethod});
    };

    function reset() {
      $(selector+' input').prop('checked',false)
    };

    return { 
      init: function() {
        $(selector+' form input').each( function(item) {
          $(this).prop('disabled',false);
        });
        $(selector+' form input').on('click', function(event) {
          // If the user has selected a shipping method, everything is ok
          state = true;
          selectedMethod = $(this).val();
          update(event);
        });
      },
      getName: function() {
        return name;
      },
      getState: function() {
        return state;
      },
      reveal: function() {
        // This is the first block shown and it is not hidden
        return true;
      },
      error: function() {
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
    var name             = 'payment',
        selector         = '#checkout-block-payment',
        selectedMethod   = '',
        selectedPaytype  = '',
        $paytypeSelector = '',
        state            = false;

    function update(event) {
      checkoutUpdate( name, state );
      updateServerState(name,state,{selected_method:selectedMethod,selected_paytype:selectedPaytype});
    };

    function reset() {
      $(selector+' input').prop('checked',false);
    };

    return { 
      init: function() {
        $(selector+' form input[type=radio]').on('click', function(event) {
          $paytypeSelector = $(this).closest('form');
          selectedMethod   = $paytypeSelector.attr('id');
          selectedPaytype  = $(this).val();
          state            = true;

          var $selectedInput = $(this);
          $(selector+' form input[type=radio]').each(function(item) {
            if ( this !== $selectedInput.get(0) )
            {
              $(this).prop('checked',false);
            }
          });
          update(event);
        });
      },
      getName: function() {
        return name;
      },
      getState: function() {
        return state;
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
        $(selector).slideUp();
      },
      setMessage: function( msg, type ) {
        setBlockMessage( selector, msg, type );
      },
      execute: function() {
        $paytypeSelector.submit();
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
      init: function() {
      },
      getName: function() {
        return name;
      },
      getState: function() {
        return state;
      },
      reveal: function() {
        // FIXME: should block contens come from ajax for js template?
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

  function handleCheckoutExecute()
  {
    // When the user clicks the link, check if everything is as it should be
    $("#checkout-execute").on('click', function(event) {
      event.preventDefault();

      $.each(blocks, function(item) {
        if ( this.getState() !== true )
        {
          // FIXME: hardcoded text
          this.setMessage( 'Not filled correctly', 'error' );
          this.error();
          return false;
        }
      });

      $.ajax({
        url: base_url+'checkout/validate',
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
  }

  /**
   * Main method, called on checkout page 
   */
  pub.init = function() {
    $.each(blocks, function(item) {
      this.init();
    });

    handleCheckoutExecute();
  };

  return pub;
})(jQuery);
