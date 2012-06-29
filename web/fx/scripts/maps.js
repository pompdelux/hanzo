var maps = (function($) {
  var pub = {};

  pub.initZip = function() {
    $("#geo-zipcode-form").submit(function(e) {
      e.preventDefault();

      dialoug.loading('#near-you-container', ExposeTranslation.get('js:loading.std'), 'prepend');
      var url = base_url + "rest/v1/gm/proxy/" + encodeURI($("#geo-zipcode-container #geo-zipcode").val()) + "/" + geo_zipcode_params.country;

      $.getJSON(url, function(response) {
        var req = '/' + geo_zipcode_params.type + '/' + response.data.Placemark[0].Point.coordinates[1] + '/' + response.data.Placemark[0].Point.coordinates[0];
        $.getJSON(base_url + 'rest/v1/gm/near_you' + req, function(result) {
          dataToContainer(result.data);
        });

      });
      $("#geo-zipcode-container #geo-zipcode").val("");
    });

  };

  pub.initContainer = function() {
    dialoug.loading('#near-you-container', ExposeTranslation.get('js:loading.std'), 'prepend');

    var req = '';
    for (var key in near_you_params) {
      req = req + '/' + near_you_params[key];
    }

    $.getJSON(base_url + 'rest/v1/gm/near_you' + req, function(result) {
      dataToContainer(result.data);
    });
  };

  pub.initConsultantsmap = function() {
    var latlng = new google.maps.LatLng(gm_settings.lat, gm_settings.lng);

    var myOptions = {
      zoom: gm_settings.zoom,
      center: latlng,
      disableDefaultUI: true,
      scaleControl: true,
      navigationControl: true,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    var map = new google.maps.Map(document.getElementById("consultants-map-canvas"), myOptions);

    $.getJSON(base_url + 'rest/v1/gm/consultants', function(result) {
      $.each(result.data, function(i,item) {
        var text = ExposeTranslation.get('js:consultant') + '<br>' + item.fullname + '<br>' + item.postcode + ' '+ item.city + '<br><br><p>' + ExposeTranslation.get('js:phone') + ': ' + item.phone + '<br>' + ExposeTranslation.get('js:email') + ': <a href="mailto:' + item.email + '">' + item.email + '</a><br><br>' + item.notes;
        var point = new google.maps.LatLng(item.latitude, item.longitude);
        var infowindow = new google.maps.InfoWindow({ content: text });

        var markerParams = {
          position: point,
          map: map,
          icon : cdn_url + 'fx/images/POMPdeLUX_map_logo.png'
        };

        var marker = new google.maps.Marker(markerParams);
        google.maps.event.addListener(marker, 'click', function() {
          infowindow.open(map,marker);
        });
      });
    });
  };

  dataToContainer = function(data) {
    $('#near-you-container div:not(.dialoug-loading), #near-you-container hr').remove();
    $('#near-you-container').append(yatzy.render('consultantItem', data));

    dialoug.stopLoading();
    $('body').trigger('near-you-container.loaded');
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
if ($('#consultants-map-canvas').length) {
  maps.initConsultantsmap();
}
