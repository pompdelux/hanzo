/* http://keith-wood.name/countdown.html
   Swedish initialisation for the jQuery countdown extension
   Written by Carl (carl@nordenfelt.com).
*/
(function($) {
	$.countdown.regional['se'] = {
		labels: ['år', 'månader', 'veckor', 'dagar', 'timmar', 'minuter', 'sekunder'],
		labels1: ['år', 'månad', 'vecka', 'dag', 'timme', 'minut', 'sekund'],
		compactLabels: ['Å', 'M', 'V', 'D'],
		whichLabels: null,
		timeSeparator: ':', isRTL: false
  };
	$.countdown.setDefaults($.countdown.regional['se']);
})(jQuery);
