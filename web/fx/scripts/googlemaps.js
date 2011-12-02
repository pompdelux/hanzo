$(function(){
  $("#geo-zipcode-form").submit(function(e) {
    e.preventDefault();
    $("#near-you-container").html("<div class=\"ac_loading\" style=\"height:20px;background-position:top left;padding-left:20px;\">loading...</div>");
    $.get("/ajax.php", {type : "proxy-gm", q : $("#geo-zipcode-container #geo-zipcode").val(), country : geo_zipcode_params.country }, function(data) {
      var params = {
        type : geo_zipcode_params.type,
        lat  : data.Placemark[0].Point.coordinates[1],
        lon  : data.Placemark[0].Point.coordinates[0]
      };
      $("#near-you-container").load("/ajax.php", params, function() {
        $('body').trigger('near-you-container.loaded');
      });
    }, "json");
    $("#geo-zipcode-container #geo-zipcode").val("");
  });

  if ($('#near-you-container').length) {
    $("#near-you-container").load("/ajax.php", near_you_params, function() {
      $('body').trigger('near-you-container.loaded');
    });
  }
});