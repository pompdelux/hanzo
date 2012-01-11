$(document).ready(function() {
  var s_root = $("#chained").scrollable({circular : true, speed : 800}).autoscroll({
    autoplay : true,
    interval : 5000
  });
  $('#chained').hover(
    function() {
      var api = s_root.data("scrollable");
      api.pause();
    },
    function() {
      var api = s_root.data("scrollable");
      api.play();
    }
  );

  $('#info-box div b').countdown({
    until: new Date(2011, 3, 8, 10, 0, 0),
    layout:'<b>{d<}{dn} {dl} {d>} {hn} {hl}<br />{mn} {ml} {sn} {sl}</b>'
  });

  $("#chained a").click(function(e) {
    e.preventDefault();

    var src = $(this).find('img').attr('src');
    src = src.replace('120x240', '0x700');
    $.colorbox({
      html : '<img src="' + src + '" alt="" />',
      height : '780px',
      width : '540px'
    });
  });
});
