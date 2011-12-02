$(function() {
  $('form.newsletter-subscription-form .button').click(function() {    
    var $this = $(this);
    //$('#newsletter-sub-action').val($this.data('action'));
    $this.closest('form').submit();
  });
  
  // use inline labels if the "real" labels are hidden
  if ($('form.newsletter-subscription-form label').is(':hidden')) {
    $('form.newsletter-subscription-form input[title]').each(function() {
      if (this.value == '') {
        this.value = this.title;
      }
      $(this).focus(function() {
        if (this.value == this.title) {
          $(this).val('').addClass('focused');
        }
      });
      $(this).blur(function() {
        if (this.value == '') {
          $(this).val($(this).attr('title')).removeClass('focused');
        }
      });    
    });
  }
});
