/**
 * yatzy is a jquery template plugin, simple but powerfull
 * thanks to mbj
 *
 * example:
 * js:
 *    var params = {title: 'test', loop: [1,2,3,4]}
 *    console.log(yatzy.render('templateId', params));
 *
 *  html:
 *    <script type="text/html" id="templateId">
 *      <h1><?= title ?></h1>
 *      <ul>
 *      <? for(i=0,l=data.loop.length; i<l; i++) { ?>
 *        <li><?= data.loop[i] ?></li>
 *      <? } ?>
 *      </ul>
 *    <script>
 *
 *  to precompile the template, first call the compile() method, then render() :
 *
 * yatzy.compile('templateId');
 * yatzy.render('templateId', params);
 */
var yatzy = (function(window, $, undefined) {

  var pub = {}, templates = {};

  pub.compile = function(id) {
    if (undefined === templates[id]) {
      var t = document.getElementById(id);
      if (t) {
        templates[id] = t.innerHTML;
      }
      else {
        if (window.console) {
          console.error('[tpl 13] id: ' + id + ' not found!');
        }
      }

      if ((undefined !== templates[id]) && typeof (templates[id] != 'function')) {
        var code = "try { var p=[], template_id='" + id + "'; p.push('" +
          templates[id]
            .replace(/[\r\t\n]/g, " ")
            .replace(/<\?/g, "\t")
            .replace(/((^|\?>)[^\t]*)'/g, "$1\r")
            .replace(/\t=(.*?)\?>/g, "',$1,'")
            .replace(/\t/g, "');")
            .replace(/\?>/g, "p.push('")
            .replace(/\r/g, "\\'") + "');return p.join(''); } catch(ex) { if(window.console) { console.error('[tpl 28] ' + template_id + ' Exception:', ex); } }";

        templates[id] = new Function("data", code);
      }
    }
  };

  pub.render = function(id, data) {
    if (undefined === templates[id]) {
      yatzy.compile(id);
    }

    return jQuery.trim(templates[id](data));
  };

  return pub;
})(this, jQuery);
