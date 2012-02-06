var maps = (function($) {
  var pub = {};

  pub.initZip = function() {
    $("#geo-zipcode-form").submit(function(e) {
      e.preventDefault();

      dialoug.loading('#near-you-container', i18n.t('Loading ...'), 'append');
      var url = base_url + "rest/v1/gm/proxy/" + $("#geo-zipcode-container #geo-zipcode").val() + "/" + geo_zipcode_params.country;

      $.get(url, function(response) {
        var params = {
          type : geo_zipcode_params.type,
          lat  : response.data.Placemark[0].Point.coordinates[1],
          lon  : response.data.Placemark[0].Point.coordinates[0]
        };

        $('body').trigger('near-you-container.loaded');
        dialoug.stopLoading();
      }, "json");
      $("#geo-zipcode-container #geo-zipcode").val("");
    });

  };

  pub.initContainer = function() {
    yatzy.compile('consultantItem');
    dialoug.loading('#near-you-container', i18n.t('Loading ...'), 'append');

    var req = '';
    for (var key in near_you_params) {
      req = req + '/' + near_you_params[key];
    }

    $.getJSON(base_url + 'rest/v1/gm/near_you' + req, function(result) {
      $('#near-you-container').append(yatzy.render('consultantItem', result.data));
      dialoug.stopLoading();
      $('body').trigger('near-you-container.loaded');
    });
  };

  return pub;
})(jQuery);

/**
 * auto init some of the maps methods.
 */
if ($("#geo-zipcode-form").length) {
  maps.initZip();
}
if ($('#near-you-container').length) {
  maps.initContainer();
}
