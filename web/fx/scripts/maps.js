/* global yatzy:true, dialoug:true, Translator:true, base_url:true, near_you_params:true, geo_zipcode_params:true, google:true, gm_settings:true */
var maps = (function ($) {
    'use strict';
    var pub = {};

    /**
     * Handle zip code lookup
     * - Listen for submit event
     * - Perform geo location lookup
     * - Load consultants/open house events
     * - Display on map
     */
    pub.initZip = function () {
        var $geoZipForm = $("#geo-zipcode-form");
        if ($geoZipForm.length === 0) {
          return;
        }

        $geoZipForm.submit(function (event) {
            var $this = $(this);
            event.preventDefault();

            dialoug.loading('#near-you-container', Translator.trans('loading.std'), 'prepend');
            var geoRequest = getGeocodeForZip($("#geo-zipcode", $this).val());

            geoRequest.done(function (response) {
                if (false === response.status || typeof response.data.postcodes === 'undefined' || response.data.postcodes.length === 0) {
                    dialoug.stopLoading();
                    return;
                }

                // handle more than one result, not just using the first.
                if (response.data.postcodes.length > 1) {
                    dialoug.notice(Translator.trans('maps.choose.correct.location'), 'info', 8000, '#geo-zipcode-form p');
                    var html = '<select id="geo-zipcode">';

                    $.each(response.data.postcodes, function (i, city) {
                        html += '<option value="' + city.zip_code + ',' + city.city + '">' + city.zip_code + ' - ' + city.city + '</option>';
                    });

                    html += '</select>';
                    $("#geo-zipcode", $this).replaceWith(html);
                    dialoug.stopLoading();

                    return;
                } else {
                    if ($('#geo-zipcode', $this).is('select')) {
                        $("#geo-zipcode", $this).replaceWith('<input type="text" id="geo-zipcode">');
                    }
                }

                var nearYouRequest = getNearYou(geo_zipcode_params.type, response.data.postcodes[0].lat, response.data.postcodes[0].lng);

                nearYouRequest.done(function(result) {
                    dataToContainer(result.data);

                    if (typeof gm_settings === 'undefined') {
                        return;
                    }

                    var mapContainer = '#consultants-map-canvas';

                    if (result.data.length) {
                        populateMap(mapContainer, result.data, true);
                        $(mapContainer).show();
                        google.maps.event.trigger($(mapContainer)[0], 'resize');
                    } else {
                        $(mapContainer).hide();
                    }
                });
            })

            $("#geo-zipcode", $this).val("");
        });

        $geoZipForm.on('change', 'select', function () {
            $geoZipForm.submit();
        });
    };

    /**
     * Load data defined in near_you_params.type
     * - Display on map
     *
     */
    pub.initContainer = function () {
        if (!($('#near-you-container').length && typeof near_you_params.auto_load_results !== 'undefined' && near_you_params.auto_load_results === true)) {
          return;
        }

        dialoug.loading('#near-you-container', Translator.trans('loading.std'), 'prepend');

        var mapContainer = '#consultants-map-canvas';
        var nearYouRequest = getNearYou(near_you_params.type);

        nearYouRequest.done(function(result) {
          dataToContainer(result.data);

          if (typeof gm_settings === 'undefined') {
            return;
          }

          // If no result: load all consultants
          if (result.data.length) {
            populateMap(mapContainer, result.data, true);
            $(mapContainer).show();
            google.maps.event.trigger($(mapContainer)[0], 'resize');
          } else {
            var consultantsRequest = getConsultants();
            consultantsRequest.done(function(result) {
              populateMap(mapContainer, result.data, true);
            });
          }
        });
    };

    /**
     * Load Consultantants
     * - Display on map
     *
     */
    pub.initConsultantsmap = function () {
        var mapContainer = '#consultants-map-canvas';
        if (!($(mapContainer).length && typeof near_you_params.auto_load_results !== 'undefined' && near_you_params.auto_load_results === true)) {
          return;
        }
        if (typeof gm_settings === 'undefined') {
          return;
        }
        if ($('body').hasClass('is-mobile')) {
          gm_settings.zoom = gm_settings.zoom - 1;
          $(mapContainer).width('100%');
        }
        var consultantsRequest = getConsultants();
        consultantsRequest.done(function(result) {
          populateMap(mapContainer, result.data, true);
        });
    };

    var getGeocodeForZip = function(zip) {
      return $.getJSON(base_url + "muneris/gpc/" + encodeURI(zip));
    };

    var getConsultants = function() {
      return $.getJSON(base_url + 'events/advisor/consultants');
    };

    var getNearYou = function(type, lat, lng) {
      lat = lat || 0;
      lng = lng || 0;
      var showAll = ((undefined != near_you_params.all) && near_you_params.all) ? '/1' : '';
      var req = '/' + type + '/' + lat + '/' + lng + showAll;
      return $.getJSON(base_url + 'events/advisor/near_you' + req);
    };

    var getMap = function(id) {
        var zc = (undefined === gm_settings.zoomControl) ? true : gm_settings.zoomControl;
        return new google.maps.Map($(id)[0], {
            zoom: gm_settings.zoom,
            center: new google.maps.LatLng(gm_settings.lat, gm_settings.lng),
            disableDefaultUI: true,
            disableDoubleClickZoom: zc ? false : true,
            scrollwheel: zc,
            zoomControl: zc,
            scaleControl: true,
            navigationControl: true,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });
    };

    var populateMap = function(mapContainer, data, fit) {
        var map = getMap(mapContainer);
        var bounds = new google.maps.LatLngBounds();
        $.each(data, function (i, item) {
            var text = Translator.trans('consultant') + '<br>' + item.name + '<br>' + item.zip + ' ' + item.city + '<br><br><p>' + Translator.trans('phone') + ': ' + item.phone + '<br>' + Translator.trans('email') + ': <a href="mailto:' + item.email + '">' + item.email + '</a><br><br>' + item.info;
            var point = new google.maps.LatLng(item.latitude, item.longitude);
            bounds.extend(point);

            var infowindow = new google.maps.InfoWindow({content: text});

            var markerParams = {
                position: point,
                map: map,
                icon: cdn_url + 'fx/images/POMPdeLUX_map_logo.png'
            };

            var marker = new google.maps.Marker(markerParams);
            google.maps.event.addListener(marker, 'click', function () {
                infowindow.open(map, marker);
            });
        });

        if ((undefined !== fit) && (fit === true)) {
            map.fitBounds(bounds);
            if (1 === data.length) {
                map.setZoom(map.getZoom() - 7);
            }
        }
    };

    var dataToContainer = function (data) {
        $('#near-you-container div:not(.dialoug-loading), #near-you-container hr').remove();
        $('#near-you-container').append(yatzy.render('consultantItem', data));

        dialoug.stopLoading();
        $('body').trigger('near-you-container.loaded');
    };

    return pub;
})(jQuery);

maps.initZip();
maps.initContainer();
// maps.initConsultantsmap();
