$(function() {
  // only apply flowplayer if container is found
  if ($('div#flowplayer-container').length) {
    // add pop player button

    $('a#flowplayer').parent().before('<button href="#" rel="div.overlay" id="pop-flowplayer"> <img src="" alt="" class="thumb" /> <img src="/shared-fx/images/famfamfam/control_play.png" alt="" class="play" /> video </button>');

    // attach main image as movie teaser image
    $('#pop-flowplayer img.thumb').attr('src', $('.productimage-large a.cloud-zoom').attr('rev'));

    // setup player settings
    var player = $f("flowplayer", '/templates/pompdelux/scripts/flowplayer/flowplayer.commercial-3.2.5.swf', {
      wmode: 'opaque',
      key : [ '#@abc69980d87e1bdf4c0', '#@7420e35e69f7d145217', '#@c653c42454bc68842d6', '#@bed07fa279e220368ee' ]
    });
    // setup overlay
    var position = $('div.productimage-large').position();
    $("button[rel]").overlay({
      effect: 'apple',
      top: position.top - 22,
      left: position.left - 22,
      mask : {loadSpeed : 'fast', opacity : 0.9},
      onLoad: function() {
        player.load();
        player.setVolume(100);
      },
      onClose: function() {
        player.unload();
      }
    });
  }

  // catch any flv movies embeded in cms pages
  $('div#cms-page a[href*=".flv"]').each(function() {
    $f(this, {src : '/templates/pompdelux/scripts/flowplayer/flowplayer.commercial-3.2.5.swf'}, {
      wmode: 'opaque',
      key : [ '#@abc69980d87e1bdf4c0', '#@7420e35e69f7d145217', '#@c653c42454bc68842d6', '#@bed07fa279e220368ee' ],
      clip : {
        url : this.href,
        autoPlay : true
      },
      onLoad: function() {
        this.setVolume(100);
      }
    });
  });
});