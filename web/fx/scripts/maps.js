var maps = (function ($) {
    var pub = {};

    pub.initZip = function () {
        var $geoZipForm = $("#geo-zipcode-form");
        $geoZipForm.submit(function (event) {
            var $this = $(this);
            event.preventDefault();

            dialoug.loading('#near-you-container', Translator.trans('loading.std'), 'prepend');
            var url = base_url + "muneris/gpc/" + encodeURI($("#geo-zipcode", $this).val());

            $.getJSON(url, function (response) {
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

                var req = '/' + geo_zipcode_params.type + '/' + response.data.postcodes[0].lat + '/' + response.data.postcodes[0].lng;

                $.getJSON(base_url + 'rest/v1/gm/near_you' + req, function (result) {
                    dataToContainer(result.data);

                    if (typeof gm_settings === 'undefined') {
                        return;
                    }

                    var map = getMap('consultants-map-canvas-2');
                    if (result.data.length) {
                        populateMap(map, result.data, true);
                        $('#consultants-map-canvas-2').show();
                        google.maps.event.trigger(map, 'resize');
                    } else {
                        $('#consultants-map-canvas-2').hide();
                    }
                });

            });
            $("#geo-zipcode", $this).val("");
        });

        $geoZipForm.on('change', 'select', function () {
            $geoZipForm.submit();
        });
    };

    pub.initContainer = function () {
        dialoug.loading('#near-you-container', Translator.trans('loading.std'), 'prepend');
        $('#near-you-container').after('<div id="consultants-map-canvas-2" style="width:100%; height:300px; display:none;"></div>');

        var req = '/' + near_you_params.type + '/0/0';
        $.getJSON(base_url + 'rest/v1/gm/near_you' + req, function (result) {
            dataToContainer(result.data);

            if (typeof gm_settings === 'undefined') {
                return;
            }

            var map = getMap('consultants-map-canvas-2');
            if (result.data.length) {
                populateMap(map, result.data, true);
                $('#consultants-map-canvas-2').show();
                google.maps.event.trigger(map, 'resize');
            } else {
                $.getJSON(base_url + 'rest/v1/gm/consultants', function (consultant_result) {
                    populateMap(map, consultant_result.data, true);
                });
            }
        });
    };

    pub.initConsultantsmap = function () {
        var map = getMap('consultants-map-canvas');
        $.getJSON(base_url + 'rest/v1/gm/consultants', function (result) {
            populateMap(map, result.data);
        });
    };


    var getMap = function (id) {
        var zc = (undefined === gm_settings.zoomControl) ? true : gm_settings.zoomControl;
        return new google.maps.Map(document.getElementById(id), {
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

    var populateMap = function (map, data, fit) {
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
    if ($('body').hasClass('is-mobile')) {
        gm_settings.zoom = gm_settings.zoom - 1;
        $('#consultants-map-canvas').width('100%');
    }

    if (typeof gm_settings !== 'undefined') {
        maps.initConsultantsmap();
    }
}
