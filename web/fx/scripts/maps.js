var maps = (function($) {
  var pub = {};

  pub.initZip = function() {
    $("#geo-zipcode-form").submit(function(e) {
      e.preventDefault();

      dialoug.loading('#near-you-container', 'Loading ...', 'append');
      var url = base_url + "rest/v1/gm/proxy/" + $("#geo-zipcode-container #geo-zipcode").val() + "/" + geo_zipcode_params.country;

      $.get(url, function(response) {
        var params = {
          type : geo_zipcode_params.type,
          lat  : response.data.Placemark[0].Point.coordinates[1],
          lon  : response.data.Placemark[0].Point.coordinates[0]
        };
        // $("#near-you-container").load("/ajax.php", params, function() {
        //   $('body').trigger('near-you-container.loaded');
        // });
        dialoug.stopLoading();
      }, "json");
      $("#geo-zipcode-container #geo-zipcode").val("");
    });

  };

  pub.initContainer = function() {
    if ($('#near-you-container').length) {
      $("#near-you-container").load("/ajax.php", near_you_params, function() {
        $('body').trigger('near-you-container.loaded');
      });
    }
  };

  return pub;
})(jQuery);

/**
 * auto init some of the maps methods.
 */
if ($("#geo-zipcode-form").length) {
  maps.initZip();
}
if ($('#maps-container').length) {
  maps.initContainer();
}
