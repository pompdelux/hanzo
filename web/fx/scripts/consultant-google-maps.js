$(function() {
  try {
    if (gm_settings !== undefined) {
      var latlng = new google.maps.LatLng(gm_settings.lat, gm_settings.lng);
      var myOptions = {
        zoom: gm_settings.zoom,
        center: latlng,
        disableDefaultUI: true,
        scaleControl: true,
        navigationControl: true,
        mapTypeId: google.maps.MapTypeId.ROADMAP
      };
      var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

      $.ajax({
        type: 'GET',
        url: '/ajax.php?type=googlemaps',
        async: 'false',
        dataType: 'json',
        success: function(data) {
          $.each(data, function(i,item) {
            var text = Translator.trans('consultant') + '<br />' + item.fullname + '<br />' + item.postcode + ' '+ item.city + '<br /><br /><p>' + Translator.trans('phone') + ': ' + item.phone + '<br />' + Translator.trans('email') + ': <a href="mailto:' + item.email + '">' + item.email + '</a><br /><br />' + item.notes;
            var point = new google.maps.LatLng(item.latitude, item.longitude);
            var infowindow = new google.maps.InfoWindow({ content: text });
            var markerParams = {
              position: point,
              map: map,
              icon : cdn_url+'/images/POMPdeLUX_map_logo.png'
            };
            var marker = new google.maps.Marker(markerParams);
            google.maps.event.addListener(marker, 'click', function() {
              infowindow.open(map,marker);
            });
          });
        }
      });
    }
  }
  catch(e){}
});
