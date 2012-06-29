/* http://keith-wood.name/countdown.html
   Danish initialisation for the jQuery countdown extension
   Written by Buch (admin@buch90.dk).
*/
(function($) {
	$.countdown.regional['dk'] = {
		labels: ['år', 'måneder', 'uger', 'dage', 'timer', 'min', 'sek'],
		labels1: ['år', 'månad', 'uge', 'dag', 'time', 'min', 'sek'],
		compactLabels: ['Å', 'M', 'U', 'D'],
		whichLabels: null,
		timeSeparator: ':', isRTL: false
  };
	$.countdown.setDefaults($.countdown.regional['dk']);
})(jQuery);
