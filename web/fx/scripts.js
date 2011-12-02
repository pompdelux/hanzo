/*!
 * jQuery Tools dev - The missing UI library for the Web
 * 
 * dateinput/dateinput.js
 * overlay/overlay.js
 * overlay/overlay.apple.js
 * rangeinput/rangeinput.js
 * scrollable/scrollable.js
 * scrollable/scrollable.autoscroll.js
 * scrollable/scrollable.navigator.js
 * tabs/tabs.js
 * tabs/tabs.slideshow.js
 * toolbox/toolbox.expose.js
 * toolbox/toolbox.flashembed.js
 * toolbox/toolbox.history.js
 * toolbox/toolbox.mousewheel.js
 * tooltip/tooltip.js
 * tooltip/tooltip.dynamic.js
 * tooltip/tooltip.slide.js
 * validator/validator.js
 * 
 * NO COPYRIGHTS OR LICENSES. DO WHAT YOU LIKE.
 * 
 * http://flowplayer.org/tools/
 * 
 * jquery.event.wheel.js - rev 1 
 * Copyright (c) 2008, Three Dub Media (http://threedubmedia.com)
 * Liscensed under the MIT License (MIT-LICENSE.txt)
 * http://www.opensource.org/licenses/mit-license.php
 * Created: 2008-07-01 | Updated: 2008-07-14
 * 
 * -----
 * 
 */
(function(a){a.tools=a.tools||{version:"dev"};var b=[],c,d=[75,76,38,39,74,72,40,37],e={};c=a.tools.dateinput={conf:{format:"mm/dd/yy",selectors:!1,yearRange:[-5,5],lang:"en",offset:[0,0],speed:0,firstDay:0,min:undefined,max:undefined,trigger:0,editable:0,css:{prefix:"cal",input:"date",root:0,head:0,title:0,prev:0,next:0,month:0,year:0,days:0,body:0,weeks:0,today:0,current:0,week:0,off:0,sunday:0,focus:0,disabled:0,trigger:0}},localize:function(b,c){a.each(c,function(a,b){c[a]=b.split(",")}),e[b]=c}},c.localize("en",{months:"January,February,March,April,May,June,July,August,September,October,November,December",shortMonths:"Jan,Feb,Mar,Apr,May,Jun,Jul,Aug,Sep,Oct,Nov,Dec",days:"Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday",shortDays:"Sun,Mon,Tue,Wed,Thu,Fri,Sat"});function f(a,b){return 32-(new Date(a,b,32)).getDate()}function g(a,b){a=""+a,b=b||2;while(a.length<b)a="0"+a;return a}var h=/d{1,4}|m{1,4}|yy(?:yy)?|"[^"]*"|'[^']*'/g,i=a("<a/>");function j(a,b,c){var d=a.getDate(),f=a.getDay(),j=a.getMonth(),k=a.getFullYear(),l={d:d,dd:g(d),ddd:e[c].shortDays[f],dddd:e[c].days[f],m:j+1,mm:g(j+1),mmm:e[c].shortMonths[j],mmmm:e[c].months[j],yy:String(k).slice(2),yyyy:k},m=b.replace(h,function(a){return a in l?l[a]:a.slice(1,a.length-1)});return i.html(m).html()}function k(a){return parseInt(a,10)}function l(a,b){return a.getFullYear()===b.getFullYear()&&a.getMonth()==b.getMonth()&&a.getDate()==b.getDate()}function m(a){if(a!==undefined){if(a.constructor==Date)return a;if(typeof a=="string"){var b=a.split("-");if(b.length==3)return new Date(k(b[0]),k(b[1])-1,k(b[2]));if(!/^-?\d+$/.test(a))return;a=k(a)}var c=new Date;c.setDate(c.getDate()+a);return c}}function n(c,g){var h=this,i=new Date,n=i.getFullYear(),o=g.css,p=e[g.lang],q=a("#"+o.root),r=q.find("#"+o.title),s,t,u,v,w,x,y=c.attr("data-value")||g.value||c.val(),z=c.attr("min")||g.min,A=c.attr("max")||g.max,B,C;z===0&&(z="0"),y=m(y)||i,z=m(z||new Date(n+g.yearRange[0],1,1)),A=m(A||new Date(n+g.yearRange[1]+1,1,-1));if(!p)throw"Dateinput: invalid language: "+g.lang;if(c.attr("type")=="date"){var C=c.clone(),D=C.wrap("<div/>").parent().html(),E=a(D.replace(/type/i,"type=text data-orig-type"));E.val(g.value),c.replaceWith(E),c=E}c.addClass(o.input);var F=c.add(h);if(!q.length){q=a("<div><div><a/><div/><a/></div><div><div/><div/></div></div>").hide().css({position:"absolute"}).attr("id",o.root),q.children().eq(0).attr("id",o.head).end().eq(1).attr("id",o.body).children().eq(0).attr("id",o.days).end().eq(1).attr("id",o.weeks).end().end().end().find("a").eq(0).attr("id",o.prev).end().eq(1).attr("id",o.next),r=q.find("#"+o.head).find("div").attr("id",o.title);if(g.selectors){var G=a("<select/>").attr("id",o.month),H=a("<select/>").attr("id",o.year);r.html(G.add(H))}var I=q.find("#"+o.days);for(var J=0;J<7;J++)I.append(a("<span/>").text(p.shortDays[(J+g.firstDay)%7]));a("body").append(q)}g.trigger&&(s=a("<a/>").attr("href","#").addClass(o.trigger).click(function(a){h.show();return a.preventDefault()}).insertAfter(c));var K=q.find("#"+o.weeks);H=q.find("#"+o.year),G=q.find("#"+o.month);function L(b,d,e){y=b,v=b.getFullYear(),w=b.getMonth(),x=b.getDate(),e=e||a.Event("api"),e.type="change",F.trigger(e,[b]);e.isDefaultPrevented()||(c.val(j(b,d.format,d.lang)),c.data("date",b),h.hide(e))}function M(b){b.type="onShow",F.trigger(b),a(document).bind("keydown.d",function(b){if(b.ctrlKey)return!0;var e=b.keyCode;if(e==8){c.val("");return h.hide(b)}if(e==27||e==9)return h.hide(b);if(a(d).index(e)>=0){if(!B){h.show(b);return b.preventDefault()}var f=a("#"+o.weeks+" a"),g=a("."+o.focus),i=f.index(g);g.removeClass(o.focus);if(e==74||e==40)i+=7;else if(e==75||e==38)i-=7;else if(e==76||e==39)i+=1;else if(e==72||e==37)i-=1;i>41?(h.addMonth(),g=a("#"+o.weeks+" a:eq("+(i-42)+")")):i<0?(h.addMonth(-1),g=a("#"+o.weeks+" a:eq("+(i+42)+")")):g=f.eq(i),g.addClass(o.focus);return b.preventDefault()}if(e==34)return h.addMonth();if(e==33)return h.addMonth(-1);if(e==36)return h.today();e==13&&(a(b.target).is("select")||a("."+o.focus).click());return a([16,17,18,9]).index(e)>=0}),a(document).bind("click.d",function(b){var d=b.target;!a(d).parents("#"+o.root).length&&d!=c[0]&&(!s||d!=s[0])&&h.hide(b)})}a.extend(h,{show:function(d){if(!(c.attr("readonly")||c.attr("disabled")||B)){d=d||a.Event(),d.type="onBeforeShow",F.trigger(d);if(d.isDefaultPrevented())return;a.each(b,function(){this.hide()}),B=!0,G.unbind("change").change(function(){h.setValue(H.val(),a(this).val())}),H.unbind("change").change(function(){h.setValue(a(this).val(),G.val())}),t=q.find("#"+o.prev).unbind("click").click(function(a){t.hasClass(o.disabled)||h.addMonth(-1);return!1}),u=q.find("#"+o.next).unbind("click").click(function(a){u.hasClass(o.disabled)||h.addMonth();return!1}),h.setValue(y);var e=c.offset();/iPad/i.test(navigator.userAgent)&&(e.top-=a(window).scrollTop()),q.css({top:e.top+c.outerHeight({margins:!0})+g.offset[0],left:e.left+g.offset[1]}),g.speed?q.show(g.speed,function(){M(d)}):(q.show(),M(d));return h}},setValue:function(b,c,d){var e=k(c)>=-1?new Date(k(b),k(c),k(d||1)):b||y;e<z?e=z:e>A&&(e=A),typeof b=="string"&&(e=m(b)),b=e.getFullYear(),c=e.getMonth(),d=e.getDate(),c==-1?(c=11,b--):c==12&&(c=0,b++);if(!B){L(e,g);return h}w=c,v=b;var j=new Date(b,c,1-g.firstDay),n=j.getDay(),q=f(b,c),s=f(b,c-1),x;if(g.selectors){G.empty(),a.each(p.months,function(c,d){z<new Date(b,c+1,-1)&&A>new Date(b,c,0)&&G.append(a("<option/>").html(d).attr("value",c))}),H.empty();var C=i.getFullYear();for(var D=C+g.yearRange[0];D<C+g.yearRange[1];D++)z<=new Date(D+1,-1,1)&&A>new Date(D,0,0)&&H.append(a("<option/>").text(D));G.val(c),H.val(b)}else r.html(p.months[c]+" "+b);K.empty(),t.add(u).removeClass(o.disabled);for(var E=n?0:-7,F,I;E<(n?42:35);E++)F=a("<a/>"),E%7===0&&(x=a("<div/>").addClass(o.week),K.append(x)),E<n?(F.addClass(o.off),I=s-n+E+1,e=new Date(b,c-1,I)):E<n+q?(I=E-n+1,e=new Date(b,c,I),l(y,e)?F.attr("id",o.current).addClass(o.focus):l(i,e)&&F.attr("id",o.today)):(F.addClass(o.off),I=E-q-n+1,e=new Date(b,c+1,I)),z&&e<z&&F.add(t).addClass(o.disabled),A&&e>A&&F.add(u).addClass(o.disabled),F.attr("href","#"+I).text(I).data("date",e),x.append(F);K.find("a").click(function(b){var c=a(this);c.hasClass(o.disabled)||(a("#"+o.current).removeAttr("id"),c.attr("id",o.current),L(c.data("date"),g,b));return!1}),o.sunday&&K.find(o.week).each(function(){var b=g.firstDay?7-g.firstDay:0;a(this).children().slice(b,b+1).addClass(o.sunday)});return h},setMin:function(a,b){z=m(a),b&&y<z&&h.setValue(z);return h},setMax:function(a,b){A=m(a),b&&y>A&&h.setValue(A);return h},today:function(){return h.setValue(i)},addDay:function(a){return this.setValue(v,w,x+(a||1))},addMonth:function(a){return this.setValue(v,w+(a||1),x)},addYear:function(a){return this.setValue(v+(a||1),w,x)},destroy:function(){c.add(document).unbind("click.d").unbind("keydown.d"),q.add(s).remove(),c.removeData("dateinput").removeClass(o.input),C&&c.replaceWith(C)},hide:function(b){if(B){b=a.Event(),b.type="onHide",F.trigger(b),a(document).unbind("click.d").unbind("keydown.d");if(b.isDefaultPrevented())return;q.hide(),B=!1}return h},getConf:function(){return g},getInput:function(){return c},getCalendar:function(){return q},getValue:function(a){return a?j(y,a,g.lang):y},isOpen:function(){return B}}),a.each(["onBeforeShow","onShow","change","onHide"],function(b,c){a.isFunction(g[c])&&a(h).bind(c,g[c]),h[c]=function(b){b&&a(h).bind(c,b);return h}}),g.editable||c.bind("focus.d click.d",h.show).keydown(function(b){var c=b.keyCode;if(!B&&a(d).index(c)>=0){h.show(b);return b.preventDefault()}return b.shiftKey||b.ctrlKey||b.altKey||c==9?!0:b.preventDefault()}),m(c.val())&&L(y,g)}a.expr[":"].date=function(b){var c=b.getAttribute("type");return c&&c=="date"||a(b).data("dateinput")},a.fn.dateinput=function(d){if(this.data("dateinput"))return this;d=a.extend(!0,{},c.conf,d),a.each(d.css,function(a,b){!b&&a!="prefix"&&(d.css[a]=(d.css.prefix||"")+(b||a))});var e;this.each(function(){var c=new n(a(this),d);b.push(c);var f=c.getInput().data("dateinput",c);e=e?e.add(f):f});return e?e:this}})(jQuery);
(function(a){a.tools=a.tools||{version:"dev"},a.tools.overlay={addEffect:function(a,b,d){c[a]=[b,d]},conf:{close:null,closeOnClick:!0,closeOnEsc:!0,closeSpeed:"fast",effect:"default",fixed:!a.browser.msie||a.browser.version>6,left:"center",load:!1,mask:null,oneInstance:!0,speed:"normal",target:null,top:"10%"}};var b=[],c={};a.tools.overlay.addEffect("default",function(b,c){var d=this.getConf(),e=a(window);d.fixed||(b.top+=e.scrollTop(),b.left+=e.scrollLeft()),b.position=d.fixed?"fixed":"absolute",this.getOverlay().css(b).fadeIn(d.speed,c)},function(a){this.getOverlay().fadeOut(this.getConf().closeSpeed,a)});function d(d,e){var f=this,g=d.add(f),h=a(window),i,j,k,l=a.tools.expose&&(e.mask||e.expose),m=Math.random().toString().slice(10);l&&(typeof l=="string"&&(l={color:l}),l.closeOnClick=l.closeOnEsc=!1);var n=e.target||d.attr("rel");j=n?a(n):null||d;if(!j.length)throw"Could not find Overlay: "+n;d&&d.index(j)==-1&&d.click(function(a){f.load(a);return a.preventDefault()}),a.extend(f,{load:function(d){if(f.isOpened())return f;var i=c[e.effect];if(!i)throw"Overlay: cannot find effect : \""+e.effect+"\"";e.oneInstance&&a.each(b,function(){this.close(d)}),d=d||a.Event(),d.type="onBeforeLoad",g.trigger(d);if(d.isDefaultPrevented())return f;k=!0,l&&a(j).expose(l);var n=e.top,o=e.left,p=j.outerWidth({margin:!0}),q=j.outerHeight({margin:!0});typeof n=="string"&&(n=n=="center"?Math.max((h.height()-q)/2,0):parseInt(n,10)/100*h.height()),o=="center"&&(o=Math.max((h.width()-p)/2,0)),i[0].call(f,{top:n,left:o},function(){k&&(d.type="onLoad",g.trigger(d))}),l&&e.closeOnClick&&a.mask.getMask().one("click",f.close),e.closeOnClick&&a(document).bind("click."+m,function(b){a(b.target).parents(j).length||f.close(b)}),e.closeOnEsc&&a(document).bind("keydown."+m,function(a){a.keyCode==27&&f.close(a)});return f},close:function(b){if(!f.isOpened())return f;b=b||a.Event(),b.type="onBeforeClose",g.trigger(b);if(!b.isDefaultPrevented()){k=!1,c[e.effect][1].call(f,function(){b.type="onClose",g.trigger(b)}),a(document).unbind("click."+m).unbind("keydown."+m),l&&a.mask.close();return f}},getOverlay:function(){return j},getTrigger:function(){return d},getClosers:function(){return i},isOpened:function(){return k},getConf:function(){return e}}),a.each("onBeforeLoad,onStart,onLoad,onBeforeClose,onClose".split(","),function(b,c){a.isFunction(e[c])&&a(f).bind(c,e[c]),f[c]=function(b){b&&a(f).bind(c,b);return f}}),i=j.find(e.close||".close"),!i.length&&!e.close&&(i=a("<a class=\"close\"></a>"),j.prepend(i)),i.click(function(a){f.close(a)}),e.load&&f.load()}a.fn.overlay=function(c){var e=this.data("overlay");if(e)return e;a.isFunction(c)&&(c={onBeforeLoad:c}),c=a.extend(!0,{},a.tools.overlay.conf,c),this.each(function(){e=new d(a(this),c),b.push(e),a(this).data("overlay",e)});return c.api?e:this}})(jQuery);
(function(a){var b=a.tools.overlay,c=a(window);a.extend(b.conf,{start:{top:null,left:null},fadeInSpeed:"fast",zIndex:9999});function d(a){var b=a.offset();return{top:b.top+a.height()/2,left:b.left+a.width()/2}}var e=function(b,e){var f=this.getOverlay(),g=this.getConf(),h=this.getTrigger(),i=this,j=f.outerWidth({margin:!0}),k=f.data("img"),l=g.fixed?"fixed":"absolute";if(!k){var m=f.css("backgroundImage");if(!m)throw"background-image CSS property not set for overlay";m=m.slice(m.indexOf("(")+1,m.indexOf(")")).replace(/\"/g,""),f.css("backgroundImage","none"),k=a("<img src=\""+m+"\"/>"),k.css({border:0,display:"none"}).width(j),a("body").append(k),f.data("img",k)}var n=g.start.top||Math.round(c.height()/2),o=g.start.left||Math.round(c.width()/2);if(h){var p=d(h);n=p.top,o=p.left}g.fixed?(n-=c.scrollTop(),o-=c.scrollLeft()):(b.top+=c.scrollTop(),b.left+=c.scrollLeft()),k.css({position:"absolute",top:n,left:o,width:0,zIndex:g.zIndex}).show(),b.position=l,f.css(b),k.animate({top:f.css("top"),left:f.css("left"),width:j},g.speed,function(){f.css("zIndex",g.zIndex+1).fadeIn(g.fadeInSpeed,function(){i.isOpened()&&!a(this).index(f)?e.call():f.hide()})}).css("position",l)},f=function(b){var e=this.getOverlay().hide(),f=this.getConf(),g=this.getTrigger(),h=e.data("img"),i={top:f.start.top,left:f.start.left,width:0};g&&a.extend(i,d(g)),f.fixed&&h.css({position:"absolute"}).animate({top:"+="+c.scrollTop(),left:"+="+c.scrollLeft()},0),h.animate(i,f.closeSpeed,b)};b.addEffect("apple",e,f)})(jQuery);
(function(a){a.tools=a.tools||{version:"dev"};var b;b=a.tools.rangeinput={conf:{min:0,max:100,step:"any",steps:0,value:0,precision:undefined,vertical:0,keyboard:!0,progress:!1,speed:100,css:{input:"range",slider:"slider",progress:"progress",handle:"handle"}}};var c,d;a.fn.drag=function(b){document.ondragstart=function(){return!1},b=a.extend({x:!0,y:!0,drag:!0},b),c=c||a(document).bind("mousedown mouseup",function(e){var f=a(e.target);if(e.type=="mousedown"&&f.data("drag")){var g=f.position(),h=e.pageX-g.left,i=e.pageY-g.top,j=!0;c.bind("mousemove.drag",function(a){var c=a.pageX-h,e=a.pageY-i,g={};b.x&&(g.left=c),b.y&&(g.top=e),j&&(f.trigger("dragStart"),j=!1),b.drag&&f.css(g),f.trigger("drag",[e,c]),d=f}),e.preventDefault()}else try{d&&d.trigger("dragEnd")}finally{c.unbind("mousemove.drag"),d=null}});return this.data("drag",!0)};function e(a,b){var c=Math.pow(10,b);return Math.round(a*c)/c}function f(a,b){var c=parseInt(a.css(b),10);if(c)return c;var d=a[0].currentStyle;return d&&d.width&&parseInt(d.width,10)}function g(a){var b=a.data("events");return b&&b.onSlide}function h(b,c){var d=this,h=c.css,i=a("<div><div/><a href='#'/></div>").data("rangeinput",d),j,k,l,m,n;b.before(i);var o=i.addClass(h.slider).find("a").addClass(h.handle),p=i.find("div").addClass(h.progress);a.each("min,max,step,value".split(","),function(a,d){var e=b.attr(d);parseFloat(e)&&(c[d]=parseFloat(e,10))});var q=c.max-c.min,r=c.step=="any"?0:c.step,s=c.precision;if(s===undefined)try{s=r.toString().split(".")[1].length}catch(t){s=0}if(b.attr("type")=="range"){var u=b.clone().wrap("<div/>").parent().html(),v=a(u.replace(/type/i,"type=text data-orig-type"));v.val(c.value),b.replaceWith(v),b=v}b.addClass(h.input);var w=a(d).add(b),x=!0;function y(a,f,g,h){g===undefined?g=f/m*q:h&&(g-=c.min),r&&(g=Math.round(g/r)*r);if(f===undefined||r)f=g*m/q;if(isNaN(g))return d;f=Math.max(0,Math.min(f,m)),g=f/m*q;if(h||!j)g+=c.min;j&&(h?f=m-f:g=c.max-g),g=e(g,s);var i=a.type=="click";if(x&&k!==undefined&&!i){a.type="onSlide",w.trigger(a,[g,f]);if(a.isDefaultPrevented())return d}var l=i?c.speed:0,t=i?function(){a.type="change",w.trigger(a,[g])}:null;j?(o.animate({top:f},l,t),c.progress&&p.animate({height:m-f+o.width()/2},l)):(o.animate({left:f},l,t),c.progress&&p.animate({width:f+o.width()/2},l)),k=g,n=f,b.val(g);return d}a.extend(d,{getValue:function(){return k},setValue:function(b,c){z();return y(c||a.Event("api"),undefined,b,!0)},getConf:function(){return c},getProgress:function(){return p},getHandle:function(){return o},getInput:function(){return b},step:function(b,e){e=e||a.Event();var f=c.step=="any"?1:c.step;d.setValue(k+f*(b||1),e)},stepUp:function(a){return d.step(a||1)},stepDown:function(a){return d.step(-a||-1)}}),a.each("onSlide,change".split(","),function(b,e){a.isFunction(c[e])&&a(d).bind(e,c[e]),d[e]=function(b){b&&a(d).bind(e,b);return d}}),o.drag({drag:!1}).bind("dragStart",function(){z(),x=g(a(d))||g(b)}).bind("drag",function(a,c,d){if(b.is(":disabled"))return!1;y(a,j?c:d)}).bind("dragEnd",function(a){a.isDefaultPrevented()||(a.type="change",w.trigger(a,[k]))}).click(function(a){return a.preventDefault()}),i.click(function(a){if(b.is(":disabled")||a.target==o[0])return a.preventDefault();z();var c=o.width()/2;y(a,j?m-l-c+a.pageY:a.pageX-l-c)}),c.keyboard&&b.keydown(function(c){if(!b.attr("readonly")){var e=c.keyCode,f=a([75,76,38,33,39]).index(e)!=-1,g=a([74,72,40,34,37]).index(e)!=-1;if((f||g)&&!(c.shiftKey||c.altKey||c.ctrlKey)){f?d.step(e==33?10:1,c):g&&d.step(e==34?-10:-1,c);return c.preventDefault()}}}),b.blur(function(b){var c=a(this).val();c!==k&&d.setValue(c,b)}),a.extend(b[0],{stepUp:d.stepUp,stepDown:d.stepDown});function z(){j=c.vertical||f(i,"height")>f(i,"width"),j?(m=f(i,"height")-f(o,"height"),l=i.offset().top+m):(m=f(i,"width")-f(o,"width"),l=i.offset().left)}function A(){z(),d.setValue(c.value!==undefined?c.value:c.min)}A(),m||a(window).load(A)}a.expr[":"].range=function(b){var c=b.getAttribute("type");return c&&c=="range"||a(b).filter("input").data("rangeinput")},a.fn.rangeinput=function(c){if(this.data("rangeinput"))return this;c=a.extend(!0,{},b.conf,c);var d;this.each(function(){var b=new h(a(this),a.extend(!0,{},c)),e=b.getInput().data("rangeinput",b);d=d?d.add(e):e});return d?d:this}})(jQuery);
(function(a){a.tools=a.tools||{version:"dev"},a.tools.scrollable={conf:{activeClass:"active",circular:!1,clonedClass:"cloned",disabledClass:"disabled",easing:"swing",initialIndex:0,item:"> *",items:".items",keyboard:!0,mousewheel:!1,next:".next",prev:".prev",size:1,speed:400,vertical:!1,touch:!0,wheelSpeed:0}};function b(a,b){var c=parseInt(a.css(b),10);if(c)return c;var d=a[0].currentStyle;return d&&d.width&&parseInt(d.width,10)}function c(b,c){var d=a(c);return d.length<2?d:b.parent().find(c)}var d;function e(b,e){var f=this,g=b.add(f),h=b.children(),i=0,j=e.vertical;d||(d=f),h.length>1&&(h=a(e.items,b)),e.size>1&&(e.circular=!1),a.extend(f,{getConf:function(){return e},getIndex:function(){return i},getSize:function(){return f.getItems().size()},getNaviButtons:function(){return n.add(o)},getRoot:function(){return b},getItemWrap:function(){return h},getItems:function(){return h.find(e.item).not("."+e.clonedClass)},move:function(a,b){return f.seekTo(i+a,b)},next:function(a){return f.move(e.size,a)},prev:function(a){return f.move(-e.size,a)},begin:function(a){return f.seekTo(0,a)},end:function(a){return f.seekTo(f.getSize()-1,a)},focus:function(){d=f;return f},addItem:function(b){b=a(b),e.circular?(h.children().last().before(b),h.children().first().replaceWith(b.clone().addClass(e.clonedClass))):(h.append(b),o.removeClass("disabled")),g.trigger("onAddItem",[b]);return f},seekTo:function(b,c,k){b.jquery||(b*=1);if(e.circular&&b===0&&i==-1&&c!==0)return f;if(!e.circular&&b<0||b>f.getSize()||b<-1)return f;var l=b;b.jquery?b=f.getItems().index(b):l=f.getItems().eq(b);var m=a.Event("onBeforeSeek");if(!k){g.trigger(m,[b,c]);if(m.isDefaultPrevented()||!l.length)return f}var n=j?{top:-l.position().top}:{left:-l.position().left};i=b,d=f,c===undefined&&(c=e.speed),h.animate(n,c,e.easing,k||function(){g.trigger("onSeek",[b])});return f}}),a.each(["onBeforeSeek","onSeek","onAddItem"],function(b,c){a.isFunction(e[c])&&a(f).bind(c,e[c]),f[c]=function(b){b&&a(f).bind(c,b);return f}});if(e.circular){var k=f.getItems().slice(-1).clone().prependTo(h),l=f.getItems().eq(1).clone().appendTo(h);k.add(l).addClass(e.clonedClass),f.onBeforeSeek(function(a,b,c){if(!a.isDefaultPrevented()){if(b==-1){f.seekTo(k,c,function(){f.end(0)});return a.preventDefault()}b==f.getSize()&&f.seekTo(l,c,function(){f.begin(0)})}});var m=b.parents().add(b).filter(function(){if(a(this).css("display")==="none")return!0});m.length?(m.show(),f.seekTo(0,0,function(){}),m.hide()):f.seekTo(0,0,function(){})}var n=c(b,e.prev).click(function(){f.prev()}),o=c(b,e.next).click(function(){f.next()});e.circular||(f.onBeforeSeek(function(a,b){setTimeout(function(){a.isDefaultPrevented()||(n.toggleClass(e.disabledClass,b<=0),o.toggleClass(e.disabledClass,b>=f.getSize()-1))},1)}),e.initialIndex||n.addClass(e.disabledClass)),f.getSize()<2&&n.add(o).addClass(e.disabledClass),e.mousewheel&&a.fn.mousewheel&&b.mousewheel(function(a,b){if(e.mousewheel){f.move(b<0?1:-1,e.wheelSpeed||50);return!1}});if(e.touch){var p={};h[0].ontouchstart=function(a){var b=a.touches[0];p.x=b.clientX,p.y=b.clientY},h[0].ontouchmove=function(a){if(a.touches.length==1&&!h.is(":animated")){var b=a.touches[0],c=p.x-b.clientX,d=p.y-b.clientY;f[j&&d>0||!j&&c>0?"next":"prev"](),a.preventDefault()}}}e.keyboard&&a(document).bind("keydown.scrollable",function(b){if(!(!e.keyboard||b.altKey||b.ctrlKey||b.metaKey||a(b.target).is(":input"))){if(e.keyboard!="static"&&d!=f)return;var c=b.keyCode;if(j&&(c==38||c==40)){f.move(c==38?-1:1);return b.preventDefault()}if(!j&&(c==37||c==39)){f.move(c==37?-1:1);return b.preventDefault()}}}),e.initialIndex&&f.seekTo(e.initialIndex,0,function(){})}a.fn.scrollable=function(b){var c=this.data("scrollable");if(c)return c;b=a.extend({},a.tools.scrollable.conf,b),this.each(function(){c=new e(a(this),b),a(this).data("scrollable",c)});return b.api?c:this}})(jQuery);
(function(a){var b=a.tools.scrollable;b.autoscroll={conf:{autoplay:!0,interval:3e3,autopause:!0}},a.fn.autoscroll=function(c){typeof c=="number"&&(c={interval:c});var d=a.extend({},b.autoscroll.conf,c),e;this.each(function(){var b=a(this).data("scrollable");b&&(e=b);var c,f=!0;b.play=function(){c||(f=!1,c=setInterval(function(){b.next()},d.interval))},b.pause=function(){c=clearInterval(c)},b.stop=function(){b.pause(),f=!0},d.autopause&&b.getRoot().add(b.getNaviButtons()).hover(b.pause,b.play),d.autoplay&&b.play()});return d.api?e:this}})(jQuery);
(function(a){var b=a.tools.scrollable;b.navigator={conf:{navi:".navi",naviItem:null,activeClass:"active",indexed:!1,idPrefix:null,history:!1}};function c(b,c){var d=a(c);return d.length<2?d:b.parent().find(c)}a.fn.navigator=function(d){typeof d=="string"&&(d={navi:d}),d=a.extend({},b.navigator.conf,d);var e;this.each(function(){var b=a(this).data("scrollable"),f=d.navi.jquery?d.navi:c(b.getRoot(),d.navi),g=b.getNaviButtons(),h=d.activeClass,i=d.history&&history.pushState,j=b.getConf().size;b&&(e=b),b.getNaviButtons=function(){return g.add(f)},i&&(history.pushState({i:0}),a(window).bind("popstate",function(a){var c=a.originalEvent.state;c&&b.seekTo(c.i)}));function k(a,c,d){b.seekTo(c),d.preventDefault(),i&&history.pushState({i:c})}function l(){return f.find(d.naviItem||"> *")}function m(b){var c=a("<"+(d.naviItem||"a")+"/>").click(function(c){k(a(this),b,c)});b===0&&c.addClass(h),d.indexed&&c.text(b+1),d.idPrefix&&c.attr("id",d.idPrefix+b);return c.appendTo(f)}l().length?l().each(function(b){a(this).click(function(c){k(a(this),b,c)})}):a.each(b.getItems(),function(a){a%j==0&&m(a)}),b.onBeforeSeek(function(a,b){setTimeout(function(){if(!a.isDefaultPrevented()){var c=b/j,d=l().eq(c);d.length&&l().removeClass(h).eq(c).addClass(h)}},1)}),b.onAddItem(function(a,c){var d=b.getItems().index(c);d%j==0&&m(d)})});return d.api?e:this}})(jQuery);
(function(a){a.tools=a.tools||{version:"dev"},a.tools.tabs={conf:{tabs:"a",current:"current",onBeforeClick:null,onClick:null,effect:"default",initialIndex:0,event:"click",rotate:!1,history:!1},addEffect:function(a,c){b[a]=c}};var b={"default":function(a,b){this.getPanes().hide().eq(a).show(),b.call()},fade:function(a,b){var c=this.getConf(),d=c.fadeOutSpeed,e=this.getPanes();d?e.fadeOut(d):e.hide(),e.eq(a).fadeIn(c.fadeInSpeed,b)},slide:function(a,b){this.getPanes().slideUp(200),this.getPanes().eq(a).slideDown(400,b)},ajax:function(a,b){this.getPanes().eq(0).load(this.getTabs().eq(a).attr("href"),b)}},c;a.tools.tabs.addEffect("horizontal",function(b,d){c||(c=this.getPanes().eq(0).width()),this.getCurrentPane().animate({width:0},function(){a(this).hide()}),this.getPanes().eq(b).animate({width:c},function(){a(this).show(),d.call()})});function d(c,d,e){var f=this,g=c.add(this),h=c.find(e.tabs),i=d.jquery?d:c.children(d),j;h.length||(h=c.children()),i.length||(i=c.parent().find(d)),i.length||(i=a(d)),a.extend(this,{click:function(c,d){var i=h.eq(c);typeof c=="string"&&c.replace("#","")&&(i=h.filter("[href*="+c.replace("#","")+"]"),c=Math.max(h.index(i),0));if(e.rotate){var k=h.length-1;if(c<0)return f.click(k,d);if(c>k)return f.click(0,d)}if(!i.length){if(j>=0)return f;c=e.initialIndex,i=h.eq(c)}if(c===j)return f;d=d||a.Event(),d.type="onBeforeClick",g.trigger(d,[c]);if(!d.isDefaultPrevented()){j=c,b[e.effect].call(f,c,function(){d.type="onClick",g.trigger(d,[c])}),h.removeClass(e.current),i.addClass(e.current);return f}},getConf:function(){return e},getTabs:function(){return h},getPanes:function(){return i},getCurrentPane:function(){return i.eq(j)},getCurrentTab:function(){return h.eq(j)},getIndex:function(){return j},next:function(){return f.click(j+1)},prev:function(){return f.click(j-1)},destroy:function(){h.unbind(e.event).removeClass(e.current),i.find("a[href^=#]").unbind("click.T");return f}}),a.each("onBeforeClick,onClick".split(","),function(b,c){a.isFunction(e[c])&&a(f).bind(c,e[c]),f[c]=function(b){b&&a(f).bind(c,b);return f}}),e.history&&a.fn.history&&(a.tools.history.init(h),e.event="history"),h.each(function(b){a(this).bind(e.event,function(a){f.click(b,a);return a.preventDefault()})}),i.find("a[href^=#]").bind("click.T",function(b){f.click(a(this).attr("href"),b)}),location.hash&&e.tabs=="a"&&c.find("[href="+location.hash+"]").length?f.click(location.hash):(e.initialIndex===0||e.initialIndex>0)&&f.click(e.initialIndex)}a.fn.tabs=function(b,c){var e=this.data("tabs");e&&(e.destroy(),this.removeData("tabs")),a.isFunction(c)&&(c={onBeforeClick:c}),c=a.extend({},a.tools.tabs.conf,c),this.each(function(){e=new d(a(this),b,c),a(this).data("tabs",e)});return c.api?e:this}})(jQuery);
(function(a){var b;b=a.tools.tabs.slideshow={conf:{next:".forward",prev:".backward",disabledClass:"disabled",autoplay:!1,autopause:!0,interval:3e3,clickable:!0,api:!1}};function c(b,c){var d=this,e=b.add(this),f=b.data("tabs"),g,h=!0;function i(c){var d=a(c);return d.length<2?d:b.parent().find(c)}var j=i(c.next).click(function(){f.next()}),k=i(c.prev).click(function(){f.prev()});a.extend(d,{getTabs:function(){return f},getConf:function(){return c},play:function(){if(g)return d;var b=a.Event("onBeforePlay");e.trigger(b);if(b.isDefaultPrevented())return d;g=setInterval(f.next,c.interval),h=!1,e.trigger("onPlay");return d},pause:function(){if(!g)return d;var b=a.Event("onBeforePause");e.trigger(b);if(b.isDefaultPrevented())return d;g=clearInterval(g),e.trigger("onPause");return d},stop:function(){d.pause(),h=!0}}),a.each("onBeforePlay,onPlay,onBeforePause,onPause".split(","),function(b,e){a.isFunction(c[e])&&a(d).bind(e,c[e]),d[e]=function(b){return a(d).bind(e,b)}}),c.autopause&&f.getTabs().add(j).add(k).add(f.getPanes()).hover(d.pause,function(){h||d.play()}),c.autoplay&&d.play(),c.clickable&&f.getPanes().click(function(){f.next()});if(!f.getConf().rotate){var l=c.disabledClass;f.getIndex()||k.addClass(l),f.onBeforeClick(function(a,b){k.toggleClass(l,!b),j.toggleClass(l,b==f.getTabs().length-1)})}}a.fn.slideshow=function(d){var e=this.data("slideshow");if(e)return e;d=a.extend({},b.conf,d),this.each(function(){e=new c(a(this),d),a(this).data("slideshow",e)});return d.api?e:this}})(jQuery);
(function(a){a.tools=a.tools||{version:"dev"};var b;b=a.tools.expose={conf:{maskId:"exposeMask",loadSpeed:"slow",closeSpeed:"fast",closeOnClick:!0,closeOnEsc:!0,zIndex:9998,opacity:.8,startOpacity:0,color:"#fff",onLoad:null,onClose:null}};function c(){if(a.browser.msie){var b=a(document).height(),c=a(window).height();return[window.innerWidth||document.documentElement.clientWidth||document.body.clientWidth,b-c<20?c:b]}return[a(document).width(),a(document).height()]}function d(b){if(b)return b.call(a.mask)}var e,f,g,h,i;a.mask={load:function(j,k){if(g)return this;typeof j=="string"&&(j={color:j}),j=j||h,h=j=a.extend(a.extend({},b.conf),j),e=a("#"+j.maskId),e.length||(e=a("<div/>").attr("id",j.maskId),a("body").append(e));var l=c();e.css({position:"absolute",top:0,left:0,width:l[0],height:l[1],display:"none",opacity:j.startOpacity,zIndex:j.zIndex}),j.color&&e.css("backgroundColor",j.color);if(d(j.onBeforeLoad)===!1)return this;j.closeOnEsc&&a(document).bind("keydown.mask",function(b){b.keyCode==27&&a.mask.close(b)}),j.closeOnClick&&e.bind("click.mask",function(b){a.mask.close(b)}),a(window).bind("resize.mask",function(){a.mask.fit()}),k&&k.length&&(i=k.eq(0).css("zIndex"),a.each(k,function(){var b=a(this);/relative|absolute|fixed/i.test(b.css("position"))||b.css("position","relative")}),f=k.css({zIndex:Math.max(j.zIndex+1,i=="auto"?0:i)})),e.css({display:"block"}).fadeTo(j.loadSpeed,j.opacity,function(){a.mask.fit(),d(j.onLoad),g="full"}),g=!0;return this},close:function(){if(g){if(d(h.onBeforeClose)===!1)return this;e.fadeOut(h.closeSpeed,function(){d(h.onClose),f&&f.css({zIndex:i}),g=!1}),a(document).unbind("keydown.mask"),e.unbind("click.mask"),a(window).unbind("resize.mask")}return this},fit:function(){if(g){var a=c();e.css({width:a[0],height:a[1]})}},getMask:function(){return e},isLoaded:function(a){return a?g=="full":g},getConf:function(){return h},getExposed:function(){return f}},a.fn.mask=function(b){a.mask.load(b);return this},a.fn.expose=function(b){a.mask.load(b,this);return this}})(jQuery);
(function(){var a=document.all,b="http://www.adobe.com/go/getflashplayer",c=typeof jQuery=="function",d=/(\d+)[^\d]+(\d+)[^\d]*(\d*)/,e={width:"100%",height:"100%",id:"_"+(""+Math.random()).slice(9),allowfullscreen:!0,allowscriptaccess:"always",quality:"high",version:[3,0],onFail:null,expressInstall:null,w3c:!1,cachebusting:!1};window.attachEvent&&window.attachEvent("onbeforeunload",function(){__flash_unloadHandler=function(){},__flash_savedUnloadHandler=function(){}});function f(a,b){if(b)for(var c in b)b.hasOwnProperty(c)&&(a[c]=b[c]);return a}function g(a,b){var c=[];for(var d in a)a.hasOwnProperty(d)&&(c[d]=b(a[d]));return c}window.flashembed=function(a,b,c){typeof a=="string"&&(a=document.getElementById(a.replace("#","")));if(a){typeof b=="string"&&(b={src:b});return new j(a,f(f({},e),b),c)}};var h=f(window.flashembed,{conf:e,getVersion:function(){var a,b;try{b=navigator.plugins["Shockwave Flash"].description.slice(16)}catch(c){try{a=new ActiveXObject("ShockwaveFlash.ShockwaveFlash.7"),b=a&&a.GetVariable("$version")}catch(e){try{a=new ActiveXObject("ShockwaveFlash.ShockwaveFlash.6"),b=a&&a.GetVariable("$version")}catch(f){}}}b=d.exec(b);return b?[b[1],b[3]]:[0,0]},asString:function(a){if(a===null||a===undefined)return null;var b=typeof a;b=="object"&&a.push&&(b="array");switch(b){case"string":a=a.replace(new RegExp("([\"\\\\])","g"),"\\$1"),a=a.replace(/^\s?(\d+\.?\d+)%/,"$1pct");return"\""+a+"\"";case"array":return"["+g(a,function(a){return h.asString(a)}).join(",")+"]";case"function":return"\"function()\"";case"object":var c=[];for(var d in a)a.hasOwnProperty(d)&&c.push("\""+d+"\":"+h.asString(a[d]));return"{"+c.join(",")+"}"}return String(a).replace(/\s/g," ").replace(/\'/g,"\"")},getHTML:function(b,c){b=f({},b);var d="<object width=\""+b.width+"\" height=\""+b.height+"\" id=\""+b.id+"\" name=\""+b.id+"\"";b.cachebusting&&(b.src+=(b.src.indexOf("?")!=-1?"&":"?")+Math.random()),b.w3c||!a?d+=" data=\""+b.src+"\" type=\"application/x-shockwave-flash\"":d+=" classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\"",d+=">";if(b.w3c||a)d+="<param name=\"movie\" value=\""+b.src+"\" />";b.width=b.height=b.id=b.w3c=b.src=null,b.onFail=b.version=b.expressInstall=null;for(var e in b)b[e]&&(d+="<param name=\""+e+"\" value=\""+b[e]+"\" />");var g="";if(c){for(var i in c)if(c[i]){var j=c[i];g+=i+"="+(/function|object/.test(typeof j)?h.asString(j):j)+"&"}g=g.slice(0,-1),d+="<param name=\"flashvars\" value='"+g+"' />"}d+="</object>";return d},isSupported:function(a){return i[0]>a[0]||i[0]==a[0]&&i[1]>=a[1]}}),i=h.getVersion();function j(c,d,e){if(h.isSupported(d.version))c.innerHTML=h.getHTML(d,e);else if(d.expressInstall&&h.isSupported([6,65]))c.innerHTML=h.getHTML(f(d,{src:d.expressInstall}),{MMredirectURL:location.href,MMplayerType:"PlugIn",MMdoctitle:document.title});else{c.innerHTML.replace(/\s/g,"")||(c.innerHTML="<h2>Flash version "+d.version+" or greater is required</h2><h3>"+(i[0]>0?"Your version is "+i:"You have no flash plugin installed")+"</h3>"+(c.tagName=="A"?"<p>Click here to download latest version</p>":"<p>Download latest version from <a href='"+b+"'>here</a></p>"),c.tagName=="A"&&(c.onclick=function(){location.href=b}));if(d.onFail){var g=d.onFail.call(this);typeof g=="string"&&(c.innerHTML=g)}}a&&(window[d.id]=document.getElementById(d.id)),f(this,{getRoot:function(){return c},getOptions:function(){return d},getConf:function(){return e},getApi:function(){return c.firstChild}})}c&&(jQuery.tools=jQuery.tools||{version:"dev"},jQuery.tools.flashembed={conf:e},jQuery.fn.flashembed=function(a,b){return this.each(function(){jQuery(this).data("flashembed",flashembed(this,a,b))})})})();
(function(a){var b,c,d,e;a.tools=a.tools||{version:"dev"},a.tools.history={init:function(g){e||(a.browser.msie&&a.browser.version<"8"?c||(c=a("<iframe/>").attr("src","javascript:false;").hide().get(0),a("body").append(c),setInterval(function(){var d=c.contentWindow.document,e=d.location.hash;b!==e&&a.event.trigger("hash",e)},100),f(location.hash||"#")):setInterval(function(){var c=location.hash;c!==b&&a.event.trigger("hash",c)},100),d=d?d.add(g):g,g.click(function(b){var d=a(this).attr("href");c&&f(d);if(d.slice(0,1)!="#"){location.href="#"+d;return b.preventDefault()}}),e=!0)}};function f(a){if(a){var b=c.contentWindow.document;b.open().close(),b.location.hash=a}}a(window).bind("hash",function(c,e){e?d.filter(function(){var b=a(this).attr("href");return b==e||b==e.replace("#","")}).trigger("history",[e]):d.eq(0).trigger("history",[e]),b=e}),a.fn.history=function(b){a.tools.history.init(this);return this.bind("history",b)}})(jQuery);
(function(a){a.fn.mousewheel=function(a){return this[a?"bind":"trigger"]("wheel",a)},a.event.special.wheel={setup:function(){a.event.add(this,b,c,{})},teardown:function(){a.event.remove(this,b,c)}};var b=a.browser.mozilla?"DOMMouseScroll"+(a.browser.version<"1.9"?" mousemove":""):"mousewheel";function c(b){switch(b.type){case"mousemove":return a.extend(b.data,{clientX:b.clientX,clientY:b.clientY,pageX:b.pageX,pageY:b.pageY});case"DOMMouseScroll":a.extend(b,b.data),b.delta=-b.detail/3;break;case"mousewheel":b.delta=b.wheelDelta/120}b.type="wheel";return a.event.handle.call(this,b,b.delta)}})(jQuery);
(function(a){a.tools=a.tools||{version:"dev"},a.tools.tooltip={conf:{effect:"toggle",fadeOutSpeed:"fast",predelay:0,delay:30,opacity:1,tip:0,fadeIE:!1,position:["top","center"],offset:[0,0],relative:!1,cancelDefault:!0,events:{def:"mouseenter,mouseleave",input:"focus,blur",widget:"focus mouseenter,blur mouseleave",tooltip:"mouseenter,mouseleave"},layout:"<div/>",tipClass:"tooltip"},addEffect:function(a,c,d){b[a]=[c,d]}};var b={toggle:[function(a){var b=this.getConf(),c=this.getTip(),d=b.opacity;d<1&&c.css({opacity:d}),c.show(),a.call()},function(a){this.getTip().hide(),a.call()}],fade:[function(b){var c=this.getConf();!a.browser.msie||c.fadeIE?this.getTip().fadeTo(c.fadeInSpeed,c.opacity,b):(this.getTip().show(),b())},function(b){var c=this.getConf();!a.browser.msie||c.fadeIE?this.getTip().fadeOut(c.fadeOutSpeed,b):(this.getTip().hide(),b())}]};function c(b,c,d){var e=d.relative?b.position().top:b.offset().top,f=d.relative?b.position().left:b.offset().left,g=d.position[0];e-=c.outerHeight()-d.offset[0],f+=b.outerWidth()+d.offset[1],/iPad/i.test(navigator.userAgent)&&(e-=a(window).scrollTop());var h=c.outerHeight()+b.outerHeight();g=="center"&&(e+=h/2),g=="bottom"&&(e+=h),g=d.position[1];var i=c.outerWidth()+b.outerWidth();g=="center"&&(f-=i/2),g=="left"&&(f-=i);return{top:e,left:f}}function d(d,e){var f=this,g=d.add(f),h,i=0,j=0,k=d.attr("title"),l=d.attr("data-tooltip"),m=b[e.effect],n,o=d.is(":input"),p=o&&d.is(":checkbox, :radio, select, :button, :submit"),q=d.attr("type"),r=e.events[q]||e.events[o?p?"widget":"input":"def"];if(!m)throw"Nonexistent effect \""+e.effect+"\"";r=r.split(/,\s*/);if(r.length!=2)throw"Tooltip: bad events configuration for "+q;d.bind(r[0],function(a){clearTimeout(i),e.predelay?j=setTimeout(function(){f.show(a)},e.predelay):f.show(a)}).bind(r[1],function(a){clearTimeout(j),e.delay?i=setTimeout(function(){f.hide(a)},e.delay):f.hide(a)}),k&&e.cancelDefault&&(d.removeAttr("title"),d.data("title",k)),a.extend(f,{show:function(b){if(!h){l?h=a(l):e.tip?h=a(e.tip).eq(0):k?h=a(e.layout).addClass(e.tipClass).appendTo(document.body).hide().append(k):(h=d.next(),h.length||(h=d.parent().next()));if(!h.length)throw"Cannot find tooltip for "+d}if(f.isShown())return f;h.stop(!0,!0);var o=c(d,h,e);e.tip&&h.html(d.data("title")),b=a.Event(),b.type="onBeforeShow",g.trigger(b,[o]);if(b.isDefaultPrevented())return f;o=c(d,h,e),h.css({position:"absolute",top:o.top,left:o.left}),n=!0,m[0].call(f,function(){b.type="onShow",n="full",g.trigger(b)});var p=e.events.tooltip.split(/,\s*/);h.data("__set")||(h.unbind(p[0]).bind(p[0],function(){clearTimeout(i),clearTimeout(j)}),p[1]&&!d.is("input:not(:checkbox, :radio), textarea")&&h.unbind(p[1]).bind(p[1],function(a){a.relatedTarget!=d[0]&&d.trigger(r[1].split(" ")[0])}),e.tip||h.data("__set",!0));return f},hide:function(c){if(!h||!f.isShown())return f;c=a.Event(),c.type="onBeforeHide",g.trigger(c);if(!c.isDefaultPrevented()){n=!1,b[e.effect][1].call(f,function(){c.type="onHide",g.trigger(c)});return f}},isShown:function(a){return a?n=="full":n},getConf:function(){return e},getTip:function(){return h},getTrigger:function(){return d}}),a.each("onHide,onBeforeShow,onShow,onBeforeHide".split(","),function(b,c){a.isFunction(e[c])&&a(f).bind(c,e[c]),f[c]=function(b){b&&a(f).bind(c,b);return f}})}a.fn.tooltip=function(b){var c=this.data("tooltip");if(c)return c;b=a.extend(!0,{},a.tools.tooltip.conf,b),typeof b.position=="string"&&(b.position=b.position.split(/,?\s/)),this.each(function(){c=new d(a(this),b),a(this).data("tooltip",c)});return b.api?c:this}})(jQuery);
(function(a){var b=a.tools.tooltip;b.dynamic={conf:{classNames:"top right bottom left"}};function c(b){var c=a(window),d=c.width()+c.scrollLeft(),e=c.height()+c.scrollTop();return[b.offset().top<=c.scrollTop(),d<=b.offset().left+b.width(),e<=b.offset().top+b.height(),c.scrollLeft()>=b.offset().left]}function d(a){var b=a.length;while(b--)if(a[b])return!1;return!0}a.fn.dynamic=function(e){typeof e=="number"&&(e={speed:e}),e=a.extend({},b.dynamic.conf,e);var f=e.classNames.split(/\s/),g;this.each(function(){var b=a(this).tooltip().onBeforeShow(function(b,h){var i=this.getTip(),j=this.getConf();g||(g=[j.position[0],j.position[1],j.offset[0],j.offset[1],a.extend({},j)]),a.extend(j,g[4]),j.position=[g[0],g[1]],j.offset=[g[2],g[3]],i.css({visibility:"hidden",position:"absolute",top:h.top,left:h.left}).show();var k=c(i);if(!d(k)){k[2]&&(a.extend(j,e.top),j.position[0]="top",i.addClass(f[0])),k[3]&&(a.extend(j,e.right),j.position[1]="right",i.addClass(f[1])),k[0]&&(a.extend(j,e.bottom),j.position[0]="bottom",i.addClass(f[2])),k[1]&&(a.extend(j,e.left),j.position[1]="left",i.addClass(f[3]));if(k[0]||k[2])j.offset[0]*=-1;if(k[1]||k[3])j.offset[1]*=-1}i.css({visibility:"visible"}).hide()});b.onBeforeShow(function(){var a=this.getConf(),b=this.getTip();setTimeout(function(){a.position=[g[0],g[1]],a.offset=[g[2],g[3]]},0)}),b.onHide(function(){var a=this.getTip();a.removeClass(e.classNames)}),ret=b});return e.api?ret:this}})(jQuery);
(function(a){var b=a.tools.tooltip;a.extend(b.conf,{direction:"up",bounce:!1,slideOffset:10,slideInSpeed:200,slideOutSpeed:200,slideFade:!a.browser.msie});var c={up:["-","top"],down:["+","top"],left:["-","left"],right:["+","left"]};b.addEffect("slide",function(a){var b=this.getConf(),d=this.getTip(),e=b.slideFade?{opacity:b.opacity}:{},f=c[b.direction]||c.up;e[f[1]]=f[0]+"="+b.slideOffset,b.slideFade&&d.css({opacity:0}),d.show().animate(e,b.slideInSpeed,a)},function(b){var d=this.getConf(),e=d.slideOffset,f=d.slideFade?{opacity:0}:{},g=c[d.direction]||c.up,h=""+g[0];d.bounce&&(h=h=="+"?"-":"+"),f[g[1]]=h+"="+e,this.getTip().animate(f,d.slideOutSpeed,function(){a(this).hide(),b.call()})})})(jQuery);
(function(a){a.tools=a.tools||{version:"dev"};var b=/\[type=([a-z]+)\]/,c=/^-?[0-9]*(\.[0-9]+)?$/,d=a.tools.dateinput,e=/^([a-z0-9_\.\-\+]+)@([\da-z\.\-]+)\.([a-z\.]{2,6})$/i,f=/^(https?:\/\/)?[\da-z\.\-]+\.[a-z\.]{2,6}[#&+_\?\/\w \.\-=]*$/i,g;g=a.tools.validator={conf:{grouped:!1,effect:"default",errorClass:"invalid",inputEvent:null,errorInputEvent:"keyup",formEvent:"submit",lang:"en",message:"<div/>",messageAttr:"data-message",messageClass:"error",offset:[0,0],position:"center right",singleError:!1,speed:"normal"},messages:{"*":{en:"Please correct this value"}},localize:function(b,c){a.each(c,function(a,c){g.messages[a]=g.messages[a]||{},g.messages[a][b]=c})},localizeFn:function(b,c){g.messages[b]=g.messages[b]||{},a.extend(g.messages[b],c)},fn:function(c,d,e){a.isFunction(d)?e=d:(typeof d=="string"&&(d={en:d}),this.messages[c.key||c]=d);var f=b.exec(c);f&&(c=i(f[1])),j.push([c,e])},addEffect:function(a,b,c){k[a]=[b,c]}};function h(b,c,d){var e=b.offset().top,f=b.offset().left,g=d.position.split(/,?\s+/),h=g[0],i=g[1];e-=c.outerHeight()-d.offset[0],f+=b.outerWidth()+d.offset[1],/iPad/i.test(navigator.userAgent)&&(e-=a(window).scrollTop());var j=c.outerHeight()+b.outerHeight();h=="center"&&(e+=j/2),h=="bottom"&&(e+=j);var k=b.outerWidth();i=="center"&&(f-=(k+c.outerWidth())/2),i=="left"&&(f-=k);return{top:e,left:f}}function i(a){function b(){return this.getAttribute("type")==a}b.key="[type="+a+"]";return b}var j=[],k={"default":[function(b){var c=this.getConf();a.each(b,function(b,d){var e=d.input;e.addClass(c.errorClass);var f=e.data("msg.el");f||(f=a(c.message).addClass(c.messageClass).appendTo(document.body),e.data("msg.el",f)),f.css({visibility:"hidden"}).find("p").remove(),a.each(d.messages,function(b,c){a("<p/>").html(c).appendTo(f)}),f.outerWidth()==f.parent().width()&&f.add(f.find("p")).css({display:"inline"});var g=h(e,f,c);f.css({visibility:"visible",position:"absolute",top:g.top,left:g.left}).fadeIn(c.speed)})},function(b){var c=this.getConf();b.removeClass(c.errorClass).each(function(){var b=a(this).data("msg.el");b&&b.css({visibility:"hidden"})})}]};a.each("email,url,number".split(","),function(b,c){a.expr[":"][c]=function(a){return a.getAttribute("type")===c}}),a.fn.oninvalid=function(a){return this[a?"bind":"trigger"]("OI",a)},g.fn(":email","Please enter a valid email address",function(a,b){return!b||e.test(b)}),g.fn(":url","Please enter a valid URL",function(a,b){return!b||f.test(b)}),g.fn(":number","Please enter a numeric value.",function(a,b){return c.test(b)}),g.fn("[max]","Please enter a value smaller than $1",function(a,b){if(b===""||d&&a.is(":date"))return!0;var c=a.attr("max");return parseFloat(b)<=parseFloat(c)?!0:[c]}),g.fn("[min]","Please enter a value larger than $1",function(a,b){if(b===""||d&&a.is(":date"))return!0;var c=a.attr("min");return parseFloat(b)>=parseFloat(c)?!0:[c]}),g.fn("[required]","Please complete this mandatory field.",function(a,b){if(a.is(":checkbox"))return a.is(":checked");return b}),g.fn("[pattern]",function(a){var b=new RegExp("^"+a.attr("pattern")+"$");return b.test(a.val())});function l(b,c,e){var f=this,i=c.add(f);b=b.not(":button, :image, :reset, :submit");function l(b,c,d){if(e.grouped||!b.length){var f;if(d===!1||a.isArray(d)){f=g.messages[c.key||c]||g.messages["*"],f=f[e.lang]||g.messages["*"].en;var h=f.match(/\$\d/g);h&&a.isArray(d)&&a.each(h,function(a){f=f.replace(this,d[a])})}else f=d[e.lang]||d;b.push(f)}}a.extend(f,{getConf:function(){return e},getForm:function(){return c},getInputs:function(){return b},reflow:function(){b.each(function(){var b=a(this),c=b.data("msg.el");if(c){var d=h(b,c,e);c.css({top:d.top,left:d.left})}});return f},invalidate:function(c,d){if(!d){var g=[];a.each(c,function(a,c){var d=b.filter("[name='"+a+"']");d.length&&(d.trigger("OI",[c]),g.push({input:d,messages:[c]}))}),c=g,d=a.Event()}d.type="onFail",i.trigger(d,[c]),d.isDefaultPrevented()||k[e.effect][0].call(f,c,d);return f},reset:function(c){c=c||b,c.removeClass(e.errorClass).each(function(){var b=a(this).data("msg.el");b&&(b.remove(),a(this).data("msg.el",null))}).unbind(e.errorInputEvent||"");return f},destroy:function(){c.unbind(e.formEvent+".V").unbind("reset.V"),b.unbind(e.inputEvent+".V").unbind("change.V");return f.reset()},checkValidity:function(c,g){c=c||b,c=c.not(":disabled");if(!c.length)return!0;g=g||a.Event(),g.type="onBeforeValidate",i.trigger(g,[c]);if(g.isDefaultPrevented())return g.result;var h=[];c.not(":radio:not(:checked)").each(function(){var b=[],c=a(this).data("messages",b),k=d&&c.is(":date")?"onHide.v":e.errorInputEvent+".v";c.unbind(k),a.each(j,function(){var a=this,d=a[0];if(c.filter(d).length){var h=a[1].call(f,c,c.val());if(h!==!0){g.type="onBeforeFail",i.trigger(g,[c,d]);if(g.isDefaultPrevented())return!1;var j=c.attr(e.messageAttr);if(j){b=[j];return!1}l(b,d,h)}}}),b.length&&(h.push({input:c,messages:b}),c.trigger("OI",[b]),e.errorInputEvent&&c.bind(k,function(a){f.checkValidity(c,a)}));if(e.singleError&&h.length)return!1});var m=k[e.effect];if(!m)throw"Validator: cannot find effect \""+e.effect+"\"";if(h.length){f.invalidate(h,g);return!1}m[1].call(f,c,g),g.type="onSuccess",i.trigger(g,[c]),c.unbind(e.errorInputEvent+".v");return!0}}),a.each("onBeforeValidate,onBeforeFail,onFail,onSuccess".split(","),function(b,c){a.isFunction(e[c])&&a(f).bind(c,e[c]),f[c]=function(b){b&&a(f).bind(c,b);return f}}),e.formEvent&&c.bind(e.formEvent+".V",function(a){if(!f.checkValidity(null,a))return a.preventDefault();a.target=c,a.type=e.formEvent}),c.bind("reset.V",function(){f.reset()}),b[0]&&b[0].validity&&b.each(function(){this.oninvalid=function(){return!1}}),c[0]&&(c[0].checkValidity=f.checkValidity),e.inputEvent&&b.bind(e.inputEvent+".V",function(b){f.checkValidity(a(this),b)}),b.filter(":checkbox, select").filter("[required]").bind("change.V",function(b){var c=a(this);(this.checked||c.is("select")&&a(this).val())&&k[e.effect][1].call(f,c,b)});var m=b.filter(":radio").change(function(a){f.checkValidity(m,a)});a(window).resize(function(){f.reflow()})}a.fn.validator=function(b){var c=this.data("validator");c&&(c.destroy(),this.removeData("validator")),b=a.extend(!0,{},g.conf,b);if(this.is("form"))return this.each(function(){var d=a(this);c=new l(d.find(":input"),d,b),d.data("validator",c)});c=new l(this,this.eq(0).closest("form"),b);return this.data("validator",c)}})(jQuery);

/* 
 * flowplayer.js 3.2.4. The Flowplayer API
 * 
 * Copyright 2009 Flowplayer Oy
 * 
 * This file is part of Flowplayer.
 * 
 * Flowplayer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * Flowplayer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Flowplayer.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * Date: 2010-08-25 12:48:46 +0000 (Wed, 25 Aug 2010)
 * Revision: 551 
 */
(function(){function g(o){console.log("$f.fireEvent",[].slice.call(o))}function k(q){if(!q||typeof q!="object"){return q}var o=new q.constructor();for(var p in q){if(q.hasOwnProperty(p)){o[p]=k(q[p])}}return o}function m(t,q){if(!t){return}var o,p=0,r=t.length;if(r===undefined){for(o in t){if(q.call(t[o],o,t[o])===false){break}}}else{for(var s=t[0];p<r&&q.call(s,p,s)!==false;s=t[++p]){}}return t}function c(o){return document.getElementById(o)}function i(q,p,o){if(typeof p!="object"){return q}if(q&&p){m(p,function(r,s){if(!o||typeof s!="function"){q[r]=s}})}return q}function n(s){var q=s.indexOf(".");if(q!=-1){var p=s.slice(0,q)||"*";var o=s.slice(q+1,s.length);var r=[];m(document.getElementsByTagName(p),function(){if(this.className&&this.className.indexOf(o)!=-1){r.push(this)}});return r}}function f(o){o=o||window.event;if(o.preventDefault){o.stopPropagation();o.preventDefault()}else{o.returnValue=false;o.cancelBubble=true}return false}function j(q,o,p){q[o]=q[o]||[];q[o].push(p)}function e(){return"_"+(""+Math.random()).slice(2,10)}var h=function(t,r,s){var q=this,p={},u={};q.index=r;if(typeof t=="string"){t={url:t}}i(this,t,true);m(("Begin*,Start,Pause*,Resume*,Seek*,Stop*,Finish*,LastSecond,Update,BufferFull,BufferEmpty,BufferStop").split(","),function(){var v="on"+this;if(v.indexOf("*")!=-1){v=v.slice(0,v.length-1);var w="onBefore"+v.slice(2);q[w]=function(x){j(u,w,x);return q}}q[v]=function(x){j(u,v,x);return q};if(r==-1){if(q[w]){s[w]=q[w]}if(q[v]){s[v]=q[v]}}});i(this,{onCuepoint:function(x,w){if(arguments.length==1){p.embedded=[null,x];return q}if(typeof x=="number"){x=[x]}var v=e();p[v]=[x,w];if(s.isLoaded()){s._api().fp_addCuepoints(x,r,v)}return q},update:function(w){i(q,w);if(s.isLoaded()){s._api().fp_updateClip(w,r)}var v=s.getConfig();var x=(r==-1)?v.clip:v.playlist[r];i(x,w,true)},_fireEvent:function(v,y,w,A){if(v=="onLoad"){m(p,function(B,C){if(C[0]){s._api().fp_addCuepoints(C[0],r,B)}});return false}A=A||q;if(v=="onCuepoint"){var z=p[y];if(z){return z[1].call(s,A,w)}}if(y&&"onBeforeBegin,onMetaData,onStart,onUpdate,onResume".indexOf(v)!=-1){i(A,y);if(y.metaData){if(!A.duration){A.duration=y.metaData.duration}else{A.fullDuration=y.metaData.duration}}}var x=true;m(u[v],function(){x=this.call(s,A,y,w)});return x}});if(t.onCuepoint){var o=t.onCuepoint;q.onCuepoint.apply(q,typeof o=="function"?[o]:o);delete t.onCuepoint}m(t,function(v,w){if(typeof w=="function"){j(u,v,w);delete t[v]}});if(r==-1){s.onCuepoint=this.onCuepoint}};var l=function(p,r,q,t){var o=this,s={},u=false;if(t){i(s,t)}m(r,function(v,w){if(typeof w=="function"){s[v]=w;delete r[v]}});i(this,{animate:function(y,z,x){if(!y){return o}if(typeof z=="function"){x=z;z=500}if(typeof y=="string"){var w=y;y={};y[w]=z;z=500}if(x){var v=e();s[v]=x}if(z===undefined){z=500}r=q._api().fp_animate(p,y,z,v);return o},css:function(w,x){if(x!==undefined){var v={};v[w]=x;w=v}r=q._api().fp_css(p,w);i(o,r);return o},show:function(){this.display="block";q._api().fp_showPlugin(p);return o},hide:function(){this.display="none";q._api().fp_hidePlugin(p);return o},toggle:function(){this.display=q._api().fp_togglePlugin(p);return o},fadeTo:function(y,x,w){if(typeof x=="function"){w=x;x=500}if(w){var v=e();s[v]=w}this.display=q._api().fp_fadeTo(p,y,x,v);this.opacity=y;return o},fadeIn:function(w,v){return o.fadeTo(1,w,v)},fadeOut:function(w,v){return o.fadeTo(0,w,v)},getName:function(){return p},getPlayer:function(){return q},_fireEvent:function(w,v,x){if(w=="onUpdate"){var z=q._api().fp_getPlugin(p);if(!z){return}i(o,z);delete o.methods;if(!u){m(z.methods,function(){var B=""+this;o[B]=function(){var C=[].slice.call(arguments);var D=q._api().fp_invoke(p,B,C);return D==="undefined"||D===undefined?o:D}});u=true}}var A=s[w];if(A){var y=A.apply(o,v);if(w.slice(0,1)=="_"){delete s[w]}return y}return o}})};function b(q,G,t){var w=this,v=null,D=false,u,s,F=[],y={},x={},E,r,p,C,o,A;i(w,{id:function(){return E},isLoaded:function(){return(v!==null&&v.fp_play!==undefined&&!D)},getParent:function(){return q},hide:function(H){if(H){q.style.height="0px"}if(w.isLoaded()){v.style.height="0px"}return w},show:function(){q.style.height=A+"px";if(w.isLoaded()){v.style.height=o+"px"}return w},isHidden:function(){return w.isLoaded()&&parseInt(v.style.height,10)===0},load:function(J){if(!w.isLoaded()&&w._fireEvent("onBeforeLoad")!==false){var H=function(){u=q.innerHTML;if(u&&!flashembed.isSupported(G.version)){q.innerHTML=""}if(J){J.cached=true;j(x,"onLoad",J)}flashembed(q,G,{config:t})};var I=0;m(a,function(){this.unload(function(K){if(++I==a.length){H()}})})}return w},unload:function(J){if(this.isFullscreen()&&/WebKit/i.test(navigator.userAgent)){if(J){J(false)}return w}if(u.replace(/\s/g,"")!==""){if(w._fireEvent("onBeforeUnload")===false){if(J){J(false)}return w}D=true;try{if(v){v.fp_close();w._fireEvent("onUnload")}}catch(H){}var I=function(){v=null;q.innerHTML=u;D=false;if(J){J(true)}};setTimeout(I,50)}else{if(J){J(false)}}return w},getClip:function(H){if(H===undefined){H=C}return F[H]},getCommonClip:function(){return s},getPlaylist:function(){return F},getPlugin:function(H){var J=y[H];if(!J&&w.isLoaded()){var I=w._api().fp_getPlugin(H);if(I){J=new l(H,I,w);y[H]=J}}return J},getScreen:function(){return w.getPlugin("screen")},getControls:function(){return w.getPlugin("controls")._fireEvent("onUpdate")},getLogo:function(){try{return w.getPlugin("logo")._fireEvent("onUpdate")}catch(H){}},getPlay:function(){return w.getPlugin("play")._fireEvent("onUpdate")},getConfig:function(H){return H?k(t):t},getFlashParams:function(){return G},loadPlugin:function(K,J,M,L){if(typeof M=="function"){L=M;M={}}var I=L?e():"_";w._api().fp_loadPlugin(K,J,M,I);var H={};H[I]=L;var N=new l(K,null,w,H);y[K]=N;return N},getState:function(){return w.isLoaded()?v.fp_getState():-1},play:function(I,H){var J=function(){if(I!==undefined){w._api().fp_play(I,H)}else{w._api().fp_play()}};if(w.isLoaded()){J()}else{if(D){setTimeout(function(){w.play(I,H)},50)}else{w.load(function(){J()})}}return w},getVersion:function(){var I="flowplayer.js 3.2.4";if(w.isLoaded()){var H=v.fp_getVersion();H.push(I);return H}return I},_api:function(){if(!w.isLoaded()){throw"Flowplayer "+w.id()+" not loaded when calling an API method"}return v},setClip:function(H){w.setPlaylist([H]);return w},getIndex:function(){return p},_swfHeight:function(){return v.clientHeight}});m(("Click*,Load*,Unload*,Keypress*,Volume*,Mute*,Unmute*,PlaylistReplace,ClipAdd,Fullscreen*,FullscreenExit,Error,MouseOver,MouseOut").split(","),function(){var H="on"+this;if(H.indexOf("*")!=-1){H=H.slice(0,H.length-1);var I="onBefore"+H.slice(2);w[I]=function(J){j(x,I,J);return w}}w[H]=function(J){j(x,H,J);return w}});m(("pause,resume,mute,unmute,stop,toggle,seek,getStatus,getVolume,setVolume,getTime,isPaused,isPlaying,startBuffering,stopBuffering,isFullscreen,toggleFullscreen,reset,close,setPlaylist,addClip,playFeed,setKeyboardShortcutsEnabled,isKeyboardShortcutsEnabled").split(","),function(){var H=this;w[H]=function(J,I){if(!w.isLoaded()){return w}var K=null;if(J!==undefined&&I!==undefined){K=v["fp_"+H](J,I)}else{K=(J===undefined)?v["fp_"+H]():v["fp_"+H](J)}return K==="undefined"||K===undefined?w:K}});w._fireEvent=function(Q){if(typeof Q=="string"){Q=[Q]}var R=Q[0],O=Q[1],M=Q[2],L=Q[3],K=0;if(t.debug){g(Q)}if(!w.isLoaded()&&R=="onLoad"&&O=="player"){v=v||c(r);o=w._swfHeight();m(F,function(){this._fireEvent("onLoad")});m(y,function(S,T){T._fireEvent("onUpdate")});s._fireEvent("onLoad")}if(R=="onLoad"&&O!="player"){return}if(R=="onError"){if(typeof O=="string"||(typeof O=="number"&&typeof M=="number")){O=M;M=L}}if(R=="onContextMenu"){m(t.contextMenu[O],function(S,T){T.call(w)});return}if(R=="onPluginEvent"||R=="onBeforePluginEvent"){var H=O.name||O;var I=y[H];if(I){I._fireEvent("onUpdate",O);return I._fireEvent(M,Q.slice(3))}return}if(R=="onPlaylistReplace"){F=[];var N=0;m(O,function(){F.push(new h(this,N++,w))})}if(R=="onClipAdd"){if(O.isInStream){return}O=new h(O,M,w);F.splice(M,0,O);for(K=M+1;K<F.length;K++){F[K].index++}}var P=true;if(typeof O=="number"&&O<F.length){C=O;var J=F[O];if(J){P=J._fireEvent(R,M,L)}if(!J||P!==false){P=s._fireEvent(R,M,L,J)}}m(x[R],function(){P=this.call(w,O,M);if(this.cached){x[R].splice(K,1)}if(P===false){return false}K++});return P};function B(){if($f(q)){$f(q).getParent().innerHTML="";p=$f(q).getIndex();a[p]=w}else{a.push(w);p=a.length-1}A=parseInt(q.style.height,10)||q.clientHeight;E=q.id||"fp"+e();r=G.id||E+"_api";G.id=r;t.playerId=E;if(typeof t=="string"){t={clip:{url:t}}}if(typeof t.clip=="string"){t.clip={url:t.clip}}t.clip=t.clip||{};if(q.getAttribute("href",2)&&!t.clip.url){t.clip.url=q.getAttribute("href",2)}s=new h(t.clip,-1,w);t.playlist=t.playlist||[t.clip];var I=0;m(t.playlist,function(){var K=this;if(typeof K=="object"&&K.length){K={url:""+K}}m(t.clip,function(L,M){if(M!==undefined&&K[L]===undefined&&typeof M!="function"){K[L]=M}});t.playlist[I]=K;K=new h(K,I,w);F.push(K);I++});m(t,function(K,L){if(typeof L=="function"){if(s[K]){s[K](L)}else{j(x,K,L)}delete t[K]}});m(t.plugins,function(K,L){if(L){y[K]=new l(K,L,w)}});if(!t.plugins||t.plugins.controls===undefined){y.controls=new l("controls",null,w)}y.canvas=new l("canvas",null,w);u=q.innerHTML;function J(L){var K=w.hasiPadSupport&&w.hasiPadSupport();if(/iPad|iPhone|iPod/i.test(navigator.userAgent)&&!/.flv$/i.test(F[0].url)&&!K){return true}if(!w.isLoaded()&&w._fireEvent("onBeforeClick")!==false){w.load()}return f(L)}function H(){if(u.replace(/\s/g,"")!==""){if(q.addEventListener){q.addEventListener("click",J,false)}else{if(q.attachEvent){q.attachEvent("onclick",J)}}}else{if(q.addEventListener){q.addEventListener("click",f,false)}w.load()}}setTimeout(H,0)}if(typeof q=="string"){var z=c(q);if(!z){throw"Flowplayer cannot access element: "+q}q=z;B()}else{B()}}var a=[];function d(o){this.length=o.length;this.each=function(p){m(o,p)};this.size=function(){return o.length}}window.flowplayer=window.$f=function(){var p=null;var o=arguments[0];if(!arguments.length){m(a,function(){if(this.isLoaded()){p=this;return false}});return p||a[0]}if(arguments.length==1){if(typeof o=="number"){return a[o]}else{if(o=="*"){return new d(a)}m(a,function(){if(this.id()==o.id||this.id()==o||this.getParent()==o){p=this;return false}});return p}}if(arguments.length>1){var t=arguments[1],q=(arguments.length==3)?arguments[2]:{};if(typeof t=="string"){t={src:t}}t=i({bgcolor:"#000000",version:[9,0],expressInstall:"http://static.flowplayer.org/swf/expressinstall.swf",cachebusting:true},t);if(typeof o=="string"){if(o.indexOf(".")!=-1){var s=[];m(n(o),function(){s.push(new b(this,k(t),k(q)))});return new d(s)}else{var r=c(o);return new b(r!==null?r:o,t,q)}}else{if(o){return new b(o,t,q)}}}return null};i(window.$f,{fireEvent:function(){var o=[].slice.call(arguments);var q=$f(o[0]);return q?q._fireEvent(o.slice(1)):null},addPlugin:function(o,p){b.prototype[o]=p;return $f},each:m,extend:i});if(typeof jQuery=="function"){jQuery.fn.flowplayer=function(q,p){if(!arguments.length||typeof arguments[0]=="number"){var o=[];this.each(function(){var r=$f(this);if(r){o.push(r)}});return arguments.length?o[arguments[0]]:new d(o)}return this.each(function(){$f(this,k(q),p?k(p):{})})}}})();(function(){var h=document.all,j="http://www.adobe.com/go/getflashplayer",c=typeof jQuery=="function",e=/(\d+)[^\d]+(\d+)[^\d]*(\d*)/,b={width:"100%",height:"100%",id:"_"+(""+Math.random()).slice(9),allowfullscreen:true,allowscriptaccess:"always",quality:"high",version:[3,0],onFail:null,expressInstall:null,w3c:false,cachebusting:false};if(window.attachEvent){window.attachEvent("onbeforeunload",function(){__flash_unloadHandler=function(){};__flash_savedUnloadHandler=function(){}})}function i(m,l){if(l){for(var f in l){if(l.hasOwnProperty(f)){m[f]=l[f]}}}return m}function a(f,n){var m=[];for(var l in f){if(f.hasOwnProperty(l)){m[l]=n(f[l])}}return m}window.flashembed=function(f,m,l){if(typeof f=="string"){f=document.getElementById(f.replace("#",""))}if(!f){return}if(typeof m=="string"){m={src:m}}return new d(f,i(i({},b),m),l)};var g=i(window.flashembed,{conf:b,getVersion:function(){var m,f;try{f=navigator.plugins["Shockwave Flash"].description.slice(16)}catch(o){try{m=new ActiveXObject("ShockwaveFlash.ShockwaveFlash.7");f=m&&m.GetVariable("$version")}catch(n){try{m=new ActiveXObject("ShockwaveFlash.ShockwaveFlash.6");f=m&&m.GetVariable("$version")}catch(l){}}}f=e.exec(f);return f?[f[1],f[3]]:[0,0]},asString:function(l){if(l===null||l===undefined){return null}var f=typeof l;if(f=="object"&&l.push){f="array"}switch(f){case"string":l=l.replace(new RegExp('(["\\\\])',"g"),"\\$1");l=l.replace(/^\s?(\d+\.?\d+)%/,"$1pct");return'"'+l+'"';case"array":return"["+a(l,function(o){return g.asString(o)}).join(",")+"]";case"function":return'"function()"';case"object":var m=[];for(var n in l){if(l.hasOwnProperty(n)){m.push('"'+n+'":'+g.asString(l[n]))}}return"{"+m.join(",")+"}"}return String(l).replace(/\s/g," ").replace(/\'/g,'"')},getHTML:function(o,l){o=i({},o);var n='<object width="'+o.width+'" height="'+o.height+'" id="'+o.id+'" name="'+o.id+'"';if(o.cachebusting){o.src+=((o.src.indexOf("?")!=-1?"&":"?")+Math.random())}if(o.w3c||!h){n+=' data="'+o.src+'" type="application/x-shockwave-flash"'}else{n+=' classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"'}n+=">";if(o.w3c||h){n+='<param name="movie" value="'+o.src+'" />'}o.width=o.height=o.id=o.w3c=o.src=null;o.onFail=o.version=o.expressInstall=null;for(var m in o){if(o[m]){n+='<param name="'+m+'" value="'+o[m]+'" />'}}var p="";if(l){for(var f in l){if(l[f]){var q=l[f];p+=f+"="+(/function|object/.test(typeof q)?g.asString(q):q)+"&"}}p=p.slice(0,-1);n+='<param name="flashvars" value=\''+p+"' />"}n+="</object>";return n},isSupported:function(f){return k[0]>f[0]||k[0]==f[0]&&k[1]>=f[1]}});var k=g.getVersion();function d(f,n,m){if(g.isSupported(n.version)){f.innerHTML=g.getHTML(n,m)}else{if(n.expressInstall&&g.isSupported([6,65])){f.innerHTML=g.getHTML(i(n,{src:n.expressInstall}),{MMredirectURL:location.href,MMplayerType:"PlugIn",MMdoctitle:document.title})}else{if(!f.innerHTML.replace(/\s/g,"")){f.innerHTML="<h2>Flash version "+n.version+" or greater is required</h2><h3>"+(k[0]>0?"Your version is "+k:"You have no flash plugin installed")+"</h3>"+(f.tagName=="A"?"<p>Click here to download latest version</p>":"<p>Download latest version from <a href='"+j+"'>here</a></p>");if(f.tagName=="A"){f.onclick=function(){location.href=j}}}if(n.onFail){var l=n.onFail.call(this);if(typeof l=="string"){f.innerHTML=l}}}}if(h){window[n.id]=document.getElementById(n.id)}i(this,{getRoot:function(){return f},getOptions:function(){return n},getConf:function(){return m},getApi:function(){return f.firstChild}})}if(c){jQuery.tools=jQuery.tools||{version:"3.2.4"};jQuery.tools.flashembed={conf:b};jQuery.fn.flashembed=function(l,f){return this.each(function(){$(this).data("flashembed",flashembed(this,l,f))})}}})();
// ColorBox v1.3.17.2 - a full featured, light-weight, customizable lightbox based on jQuery 1.3+
// Copyright (c) 2011 Jack Moore - jack@colorpowered.com
// Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php

(function ($, document, window) {
	var
	// ColorBox Default Settings.	
	// See http://colorpowered.com/colorbox for details.
	defaults = {
		transition: "elastic",
		speed: 300,
		width: false,
		initialWidth: "600",
		innerWidth: false,
		maxWidth: false,
		height: false,
		initialHeight: "450",
		innerHeight: false,
		maxHeight: false,
		scalePhotos: true,
		scrolling: true,
		inline: false,
		html: false,
		iframe: false,
		fastIframe: true,
		photo: false,
		href: false,
		title: false,
		rel: false,
		opacity: 0.9,
		preloading: true,
		current: "image {current} of {total}",
		previous: "previous",
		next: "next",
		close: "close",
		open: false,
		returnFocus: true,
		loop: true,
		slideshow: false,
		slideshowAuto: true,
		slideshowSpeed: 2500,
		slideshowStart: "start slideshow",
		slideshowStop: "stop slideshow",
		onOpen: false,
		onLoad: false,
		onComplete: false,
		onCleanup: false,
		onClosed: false,
		overlayClose: true,		
		escKey: true,
		arrowKey: true,
        top: false,
        bottom: false,
        left: false,
        right: false,
        fixed: false,
        data: false
	},
	
	// Abstracting the HTML and event identifiers for easy rebranding
	colorbox = 'colorbox',
	prefix = 'cbox',
    boxElement = prefix + 'Element',
	
	// Events	
	event_open = prefix + '_open',
	event_load = prefix + '_load',
	event_complete = prefix + '_complete',
	event_cleanup = prefix + '_cleanup',
	event_closed = prefix + '_closed',
	event_purge = prefix + '_purge',
	
	// Special Handling for IE
	isIE = $.browser.msie && !$.support.opacity, // Detects IE6,7,8.  IE9 supports opacity.  Feature detection alone gave a false positive on at least one phone browser and on some development versions of Chrome, hence the user-agent test.
	isIE6 = isIE && $.browser.version < 7,
	event_ie6 = prefix + '_IE6',

	// Cached jQuery Object Variables
	$overlay,
	$box,
	$wrap,
	$content,
	$topBorder,
	$leftBorder,
	$rightBorder,
	$bottomBorder,
	$related,
	$window,
	$loaded,
	$loadingBay,
	$loadingOverlay,
	$title,
	$current,
	$slideshow,
	$next,
	$prev,
	$close,
	$groupControls,

	// Variables for cached values or use across multiple functions
	settings,
	interfaceHeight,
	interfaceWidth,
	loadedHeight,
	loadedWidth,
	element,
	index,
	photo,
	open,
	active,
	closing,
    handler,
    loadingTimer,
    publicMethod;
	
	// ****************
	// HELPER FUNCTIONS
	// ****************

	// jQuery object generator to reduce code size
	function $div(id, cssText, div) { 
		div = document.createElement('div');
		if (id) {
            div.id = prefix + id;
        }
		div.style.cssText = cssText || '';
		return $(div);
	}

	// Convert '%' and 'px' values to integers
	function setSize(size, dimension) {
		return Math.round((/%/.test(size) ? ((dimension === 'x' ? $window.width() : $window.height()) / 100) : 1) * parseInt(size, 10));
	}
	
	// Checks an href to see if it is a photo.
	// There is a force photo option (photo: true) for hrefs that cannot be matched by this regex.
	function isImage(url) {
		return settings.photo || /\.(gif|png|jpg|jpeg|bmp)(?:\?([^#]*))?(?:#(\.*))?$/i.test(url);
	}
	
	// Assigns function results to their respective settings.  This allows functions to be used as values.
	function makeSettings(i) {
        settings = $.extend({}, $.data(element, colorbox));
        
		for (i in settings) {
			if ($.isFunction(settings[i]) && i.substring(0, 2) !== 'on') { // checks to make sure the function isn't one of the callbacks, they will be handled at the appropriate time.
			    settings[i] = settings[i].call(element);
			}
		}
        
		settings.rel = settings.rel || element.rel || 'nofollow';
		settings.href = settings.href || $(element).attr('href');
		settings.title = settings.title || element.title;
        
        if (typeof settings.href === "string") {
            settings.href = $.trim(settings.href);
        }
	}

	function trigger(event, callback) {
		if (callback) {
			callback.call(element);
		}
		$.event.trigger(event);
	}

	// Slideshow functionality
	function slideshow() {
		var
		timeOut,
		className = prefix + "Slideshow_",
		click = "click." + prefix,
		start,
		stop,
		clear;
		
		if (settings.slideshow && $related[1]) {
			start = function () {
				$slideshow
					.text(settings.slideshowStop)
					.unbind(click)
					.bind(event_complete, function () {
						if (index < $related.length - 1 || settings.loop) {
							timeOut = setTimeout(publicMethod.next, settings.slideshowSpeed);
						}
					})
					.bind(event_load, function () {
						clearTimeout(timeOut);
					})
					.one(click + ' ' + event_cleanup, stop);
				$box.removeClass(className + "off").addClass(className + "on");
				timeOut = setTimeout(publicMethod.next, settings.slideshowSpeed);
			};
			
			stop = function () {
				clearTimeout(timeOut);
				$slideshow
					.text(settings.slideshowStart)
					.unbind([event_complete, event_load, event_cleanup, click].join(' '))
					.one(click, start);
				$box.removeClass(className + "on").addClass(className + "off");
			};
			
			if (settings.slideshowAuto) {
				start();
			} else {
				stop();
			}
		} else {
            $box.removeClass(className + "off " + className + "on");
        }
	}

	function launch(target) {
		if (!closing) {
			
			element = target;
			
			makeSettings();
			
			$related = $(element);
			
			index = 0;
			
			if (settings.rel !== 'nofollow') {
				$related = $('.' + boxElement).filter(function () {
					var relRelated = $.data(this, colorbox).rel || this.rel;
					return (relRelated === settings.rel);
				});
				index = $related.index(element);
				
				// Check direct calls to ColorBox.
				if (index === -1) {
					$related = $related.add(element);
					index = $related.length - 1;
				}
			}
			
			if (!open) {
				open = active = true; // Prevents the page-change action from queuing up if the visitor holds down the left or right keys.
				
				$box.show();
				
				if (settings.returnFocus) {
					try {
						element.blur();
						$(element).one(event_closed, function () {
							try {
								this.focus();
							} catch (e) {
								// do nothing
							}
						});
					} catch (e) {
						// do nothing
					}
				}
				
				// +settings.opacity avoids a problem in IE when using non-zero-prefixed-string-values, like '.5'
				$overlay.css({"opacity": +settings.opacity, "cursor": settings.overlayClose ? "pointer" : "auto"}).show();
				
				// Opens inital empty ColorBox prior to content being loaded.
				settings.w = setSize(settings.initialWidth, 'x');
				settings.h = setSize(settings.initialHeight, 'y');
				publicMethod.position();
				
				if (isIE6) {
					$window.bind('resize.' + event_ie6 + ' scroll.' + event_ie6, function () {
						$overlay.css({width: $window.width(), height: $window.height(), top: $window.scrollTop(), left: $window.scrollLeft()});
					}).trigger('resize.' + event_ie6);
				}
				
				trigger(event_open, settings.onOpen);
				
				$groupControls.add($title).hide();
				
				$close.html(settings.close).show();
			}
			
			publicMethod.load(true);
		}
	}

	// ****************
	// PUBLIC FUNCTIONS
	// Usage format: $.fn.colorbox.close();
	// Usage from within an iframe: parent.$.fn.colorbox.close();
	// ****************
	
	publicMethod = $.fn[colorbox] = $[colorbox] = function (options, callback) {
		var $this = this;
		
        options = options || {};
        
		if (!$this[0]) {
			if ($this.selector) { // if a selector was given and it didn't match any elements, go ahead and exit.
                return $this;
            }
            // if no selector was given (ie. $.colorbox()), create a temporary element to work with
			$this = $('<a/>');
			options.open = true; // assume an immediate open
		}
		
		if (callback) {
			options.onComplete = callback;
		}
		
		$this.each(function () {
			$.data(this, colorbox, $.extend({}, $.data(this, colorbox) || defaults, options));
			$(this).addClass(boxElement);
		});
		
        if (($.isFunction(options.open) && options.open.call($this)) || options.open) {
			launch($this[0]);
		}
        
		return $this;
	};

	// Initialize ColorBox: store common calculations, preload the interface graphics, append the html.
	// This preps ColorBox for a speedy open when clicked, and minimizes the burdon on the browser by only
	// having to run once, instead of each time colorbox is opened.
	publicMethod.init = function () {
		// Create & Append jQuery Objects
		$window = $(window);
		$box = $div().attr({id: colorbox, 'class': isIE ? prefix + (isIE6 ? 'IE6' : 'IE') : ''});
		$overlay = $div("Overlay", isIE6 ? 'position:absolute' : '').hide();
		
		$wrap = $div("Wrapper");
		$content = $div("Content").append(
			$loaded = $div("LoadedContent", 'width:0; height:0; overflow:hidden'),
			$loadingOverlay = $div("LoadingOverlay").add($div("LoadingGraphic")),
			$title = $div("Title"),
			$current = $div("Current"),
			$next = $div("Next"),
			$prev = $div("Previous"),
			$slideshow = $div("Slideshow").bind(event_open, slideshow),
			$close = $div("Close")
		);
		$wrap.append( // The 3x3 Grid that makes up ColorBox
			$div().append(
				$div("TopLeft"),
				$topBorder = $div("TopCenter"),
				$div("TopRight")
			),
			$div(false, 'clear:left').append(
				$leftBorder = $div("MiddleLeft"),
				$content,
				$rightBorder = $div("MiddleRight")
			),
			$div(false, 'clear:left').append(
				$div("BottomLeft"),
				$bottomBorder = $div("BottomCenter"),
				$div("BottomRight")
			)
		).children().children().css({'float': 'left'});
		
		$loadingBay = $div(false, 'position:absolute; width:9999px; visibility:hidden; display:none');
		
		$('body').prepend($overlay, $box.append($wrap, $loadingBay));
		
		$content.children()
		.hover(function () {
			$(this).addClass('hover');
		}, function () {
			$(this).removeClass('hover');
		}).addClass('hover');
		
		// Cache values needed for size calculations
		interfaceHeight = $topBorder.height() + $bottomBorder.height() + $content.outerHeight(true) - $content.height();//Subtraction needed for IE6
		interfaceWidth = $leftBorder.width() + $rightBorder.width() + $content.outerWidth(true) - $content.width();
		loadedHeight = $loaded.outerHeight(true);
		loadedWidth = $loaded.outerWidth(true);
		
		// Setting padding to remove the need to do size conversions during the animation step.
		$box.css({"padding-bottom": interfaceHeight, "padding-right": interfaceWidth}).hide();
		
        // Setup button events.
        // Anonymous functions here keep the public method from being cached, thereby allowing them to be redefined on the fly.
        $next.click(function () {
            publicMethod.next();
        });
        $prev.click(function () {
            publicMethod.prev();
        });
        $close.click(function () {
            publicMethod.close();
        });
		
		$groupControls = $next.add($prev).add($current).add($slideshow);
		
		// Adding the 'hover' class allowed the browser to load the hover-state
		// background graphics in case the images were not part of a sprite.  The class can now can be removed.
		$content.children().removeClass('hover');
		
		$overlay.click(function () {
			if (settings.overlayClose) {
				publicMethod.close();
			}
		});
		
		// Set Navigation Key Bindings
		$(document).bind('keydown.' + prefix, function (e) {
            var key = e.keyCode;
			if (open && settings.escKey && key === 27) {
				e.preventDefault();
				publicMethod.close();
			}
			if (open && settings.arrowKey && $related[1]) {
				if (key === 37) {
					e.preventDefault();
					$prev.click();
				} else if (key === 39) {
					e.preventDefault();
					$next.click();
				}
			}
		});
	};
	
	publicMethod.remove = function () {
		$box.add($overlay).remove();
		$('.' + boxElement).removeData(colorbox).removeClass(boxElement);
	};

	publicMethod.position = function (speed, loadedCallback) {
        var top = 0, left = 0;
        
        $window.unbind('resize.' + prefix);
        
        // remove the modal so that it doesn't influence the document width/height        
        $box.hide();
        
        if (settings.fixed && !isIE6) {
            $box.css({position: 'fixed'});
        } else {
            top = $window.scrollTop();
            left = $window.scrollLeft();
            $box.css({position: 'absolute'});
        }
        
		// keeps the top and left positions within the browser's viewport.
        if (settings.right !== false) {
            left += Math.max($window.width() - settings.w - loadedWidth - interfaceWidth - setSize(settings.right, 'x'), 0);
        } else if (settings.left !== false) {
            left += setSize(settings.left, 'x');
        } else {
            left += Math.round(Math.max($window.width() - settings.w - loadedWidth - interfaceWidth, 0) / 2);
        }
        
        if (settings.bottom !== false) {
            top += Math.max(document.documentElement.clientHeight - settings.h - loadedHeight - interfaceHeight - setSize(settings.bottom, 'y'), 0);
        } else if (settings.top !== false) {
            top += setSize(settings.top, 'y');
        } else {
            top += Math.round(Math.max(document.documentElement.clientHeight - settings.h - loadedHeight - interfaceHeight, 0) / 2);
        }
        
        $box.show();
        
		// setting the speed to 0 to reduce the delay between same-sized content.
		speed = ($box.width() === settings.w + loadedWidth && $box.height() === settings.h + loadedHeight) ? 0 : speed || 0;
        
		// this gives the wrapper plenty of breathing room so it's floated contents can move around smoothly,
		// but it has to be shrank down around the size of div#colorbox when it's done.  If not,
		// it can invoke an obscure IE bug when using iframes.
		$wrap[0].style.width = $wrap[0].style.height = "9999px";
		
		function modalDimensions(that) {
			// loading overlay height has to be explicitly set for IE6.
			$topBorder[0].style.width = $bottomBorder[0].style.width = $content[0].style.width = that.style.width;
			$loadingOverlay[0].style.height = $loadingOverlay[1].style.height = $content[0].style.height = $leftBorder[0].style.height = $rightBorder[0].style.height = that.style.height;
		}
		
		$box.dequeue().animate({width: settings.w + loadedWidth, height: settings.h + loadedHeight, top: top, left: left}, {
			duration: speed,
			complete: function () {
				modalDimensions(this);
				
				active = false;
				
				// shrink the wrapper down to exactly the size of colorbox to avoid a bug in IE's iframe implementation.
				$wrap[0].style.width = (settings.w + loadedWidth + interfaceWidth) + "px";
				$wrap[0].style.height = (settings.h + loadedHeight + interfaceHeight) + "px";
				
				if (loadedCallback) {
					loadedCallback();
				}
                
                setTimeout(function(){  // small delay before binding onresize due to an IE8 bug.
                    $window.bind('resize.' + prefix, publicMethod.position);
                }, 1);
			},
			step: function () {
				modalDimensions(this);
			}
		});
	};

	publicMethod.resize = function (options) {
		if (open) {
			options = options || {};
			
			if (options.width) {
				settings.w = setSize(options.width, 'x') - loadedWidth - interfaceWidth;
			}
			if (options.innerWidth) {
				settings.w = setSize(options.innerWidth, 'x');
			}
			$loaded.css({width: settings.w});
			
			if (options.height) {
				settings.h = setSize(options.height, 'y') - loadedHeight - interfaceHeight;
			}
			if (options.innerHeight) {
				settings.h = setSize(options.innerHeight, 'y');
			}
			if (!options.innerHeight && !options.height) {				
				var $child = $loaded.wrapInner("<div style='overflow:auto'></div>").children(); // temporary wrapper to get an accurate estimate of just how high the total content should be.
				settings.h = $child.height();
				$child.replaceWith($child.children()); // ditch the temporary wrapper div used in height calculation
			}
			$loaded.css({height: settings.h});
			
			publicMethod.position(settings.transition === "none" ? 0 : settings.speed);
		}
	};

	publicMethod.prep = function (object) {
		if (!open) {
			return;
		}
		
		var callback, speed = settings.transition === "none" ? 0 : settings.speed;
		
		$loaded.remove();
		$loaded = $div('LoadedContent').append(object);
		
		function getWidth() {
			settings.w = settings.w || $loaded.width();
			settings.w = settings.mw && settings.mw < settings.w ? settings.mw : settings.w;
			return settings.w;
		}
		function getHeight() {
			settings.h = settings.h || $loaded.height();
			settings.h = settings.mh && settings.mh < settings.h ? settings.mh : settings.h;
			return settings.h;
		}
		
		$loaded.hide()
		.appendTo($loadingBay.show())// content has to be appended to the DOM for accurate size calculations.
		.css({width: getWidth(), overflow: settings.scrolling ? 'auto' : 'hidden'})
		.css({height: getHeight()})// sets the height independently from the width in case the new width influences the value of height.
		.prependTo($content);
		
		$loadingBay.hide();
		
		// floating the IMG removes the bottom line-height and fixed a problem where IE miscalculates the width of the parent element as 100% of the document width.
		//$(photo).css({'float': 'none', marginLeft: 'auto', marginRight: 'auto'});
		
        $(photo).css({'float': 'none'});
        
		// Hides SELECT elements in IE6 because they would otherwise sit on top of the overlay.
		if (isIE6) {
			$('select').not($box.find('select')).filter(function () {
				return this.style.visibility !== 'hidden';
			}).css({'visibility': 'hidden'}).one(event_cleanup, function () {
				this.style.visibility = 'inherit';
			});
		}
		
		callback = function () {
            var prev, prevSrc, next, nextSrc, total = $related.length, iframe, complete;
            
            if (!open) {
                return;
            }
            
            function removeFilter() {
                if (isIE) {
                    $box[0].style.removeAttribute('filter');
                }
            }
            
            complete = function () {
                clearTimeout(loadingTimer);
                $loadingOverlay.hide();
                trigger(event_complete, settings.onComplete);
            };
            
            if (isIE) {
                //This fadeIn helps the bicubic resampling to kick-in.
                if (photo) {
                    $loaded.fadeIn(100);
                }
            }
            
            $title.html(settings.title).add($loaded).show();
            
            if (total > 1) { // handle grouping
                if (typeof settings.current === "string") {
                    $current.html(settings.current.replace('{current}', index + 1).replace('{total}', total)).show();
                }
                
                $next[(settings.loop || index < total - 1) ? "show" : "hide"]().html(settings.next);
                $prev[(settings.loop || index) ? "show" : "hide"]().html(settings.previous);
                
                prev = index ? $related[index - 1] : $related[total - 1];
                next = index < total - 1 ? $related[index + 1] : $related[0];
                
                if (settings.slideshow) {
                    $slideshow.show();
                }
                
                // Preloads images within a rel group
                if (settings.preloading) {
                    nextSrc = $.data(next, colorbox).href || next.href;
                    prevSrc = $.data(prev, colorbox).href || prev.href;
                    
                    nextSrc = $.isFunction(nextSrc) ? nextSrc.call(next) : nextSrc;
                    prevSrc = $.isFunction(prevSrc) ? prevSrc.call(prev) : prevSrc;
                    
                    if (isImage(nextSrc)) {
                        $('<img/>')[0].src = nextSrc;
                    }
                    
                    if (isImage(prevSrc)) {
                        $('<img/>')[0].src = prevSrc;
                    }
                }
            } else {
                $groupControls.hide();
            }
            
            if (settings.iframe) {
                iframe = $('<iframe/>').addClass(prefix + 'Iframe')[0];
                
                if (settings.fastIframe) {
                    complete();
                } else {
                    $(iframe).one('load', complete);
                }
                iframe.name = prefix + (+new Date());
                iframe.src = settings.href;
                
                if (!settings.scrolling) {
                    iframe.scrolling = "no";
                }
                
                if (isIE) {
                    iframe.frameBorder = 0;
                    iframe.allowTransparency = "true";
                }
                
                $(iframe).appendTo($loaded).one(event_purge, function () {
                    iframe.src = "//about:blank";
                });
            } else {
                complete();
            }
            
            if (settings.transition === 'fade') {
                $box.fadeTo(speed, 1, removeFilter);
            } else {
                removeFilter();
            }
		};
		
		if (settings.transition === 'fade') {
			$box.fadeTo(speed, 0, function () {
				publicMethod.position(0, callback);
			});
		} else {
			publicMethod.position(speed, callback);
		}
	};

	publicMethod.load = function (launched) {
		var href, setResize, prep = publicMethod.prep;
		
		active = true;
		
		photo = false;
		
		element = $related[index];
		
		if (!launched) {
			makeSettings();
		}
		
		trigger(event_purge);
		
		trigger(event_load, settings.onLoad);
		
		settings.h = settings.height ?
				setSize(settings.height, 'y') - loadedHeight - interfaceHeight :
				settings.innerHeight && setSize(settings.innerHeight, 'y');
		
		settings.w = settings.width ?
				setSize(settings.width, 'x') - loadedWidth - interfaceWidth :
				settings.innerWidth && setSize(settings.innerWidth, 'x');
		
		// Sets the minimum dimensions for use in image scaling
		settings.mw = settings.w;
		settings.mh = settings.h;
		
		// Re-evaluate the minimum width and height based on maxWidth and maxHeight values.
		// If the width or height exceed the maxWidth or maxHeight, use the maximum values instead.
		if (settings.maxWidth) {
			settings.mw = setSize(settings.maxWidth, 'x') - loadedWidth - interfaceWidth;
			settings.mw = settings.w && settings.w < settings.mw ? settings.w : settings.mw;
		}
		if (settings.maxHeight) {
			settings.mh = setSize(settings.maxHeight, 'y') - loadedHeight - interfaceHeight;
			settings.mh = settings.h && settings.h < settings.mh ? settings.h : settings.mh;
		}
		
		href = settings.href;
		
        loadingTimer = setTimeout(function () {
            $loadingOverlay.show();
        }, 100);
        
		if (settings.inline) {
			// Inserts an empty placeholder where inline content is being pulled from.
			// An event is bound to put inline content back when ColorBox closes or loads new content.
			$div().hide().insertBefore($(href)[0]).one(event_purge, function () {
				$(this).replaceWith($loaded.children());
			});
			prep($(href));
		} else if (settings.iframe) {
			// IFrame element won't be added to the DOM until it is ready to be displayed,
			// to avoid problems with DOM-ready JS that might be trying to run in that iframe.
			prep(" ");
		} else if (settings.html) {
			prep(settings.html);
		} else if (isImage(href)) {
			$(photo = new Image())
			.addClass(prefix + 'Photo')
			.error(function () {
				settings.title = false;
				prep($div('Error').text('This image could not be loaded'));
			})
			.load(function () {
				var percent;
				photo.onload = null; //stops animated gifs from firing the onload repeatedly.
				
				if (settings.scalePhotos) {
					setResize = function () {
						photo.height -= photo.height * percent;
						photo.width -= photo.width * percent;	
					};
					if (settings.mw && photo.width > settings.mw) {
						percent = (photo.width - settings.mw) / photo.width;
						setResize();
					}
					if (settings.mh && photo.height > settings.mh) {
						percent = (photo.height - settings.mh) / photo.height;
						setResize();
					}
				}
				
				if (settings.h) {
					photo.style.marginTop = Math.max(settings.h - photo.height, 0) / 2 + 'px';
				}
				
				if ($related[1] && (index < $related.length - 1 || settings.loop)) {
					photo.style.cursor = 'pointer';
					photo.onclick = function () {
                        publicMethod.next();
                    };
				}
				
				if (isIE) {
					photo.style.msInterpolationMode = 'bicubic';
				}
				
				setTimeout(function () { // A pause because Chrome will sometimes report a 0 by 0 size otherwise.
					prep(photo);
				}, 1);
			});
			
			setTimeout(function () { // A pause because Opera 10.6+ will sometimes not run the onload function otherwise.
				photo.src = href;
			}, 1);
		} else if (href) {
			$loadingBay.load(href, settings.data, function (data, status, xhr) {
				prep(status === 'error' ? $div('Error').text('Request unsuccessful: ' + xhr.statusText) : $(this).contents());
			});
		}
	};
        
	// Navigates to the next page/image in a set.
	publicMethod.next = function () {
		if (!active && $related[1] && (index < $related.length - 1 || settings.loop)) {
			index = index < $related.length - 1 ? index + 1 : 0;
			publicMethod.load();
		}
	};
	
	publicMethod.prev = function () {
		if (!active && $related[1] && (index || settings.loop)) {
			index = index ? index - 1 : $related.length - 1;
			publicMethod.load();
		}
	};

	// Note: to use this within an iframe use the following format: parent.$.fn.colorbox.close();
	publicMethod.close = function () {
		if (open && !closing) {
			
			closing = true;
			
			open = false;
			
			trigger(event_cleanup, settings.onCleanup);
			
			$window.unbind('.' + prefix + ' .' + event_ie6);
			
			$overlay.fadeTo(200, 0);
			
			$box.stop().fadeTo(300, 0, function () {
                 
				$box.add($overlay).css({'opacity': 1, cursor: 'auto'}).hide();
				
				trigger(event_purge);
				
				$loaded.remove();
				
				setTimeout(function () {
					closing = false;
					trigger(event_closed, settings.onClosed);
				}, 1);
			});
		}
	};

	// A method for fetching the current element ColorBox is referencing.
	// returns a jQuery object.
	publicMethod.element = function () {
		return $(element);
	};

	publicMethod.settings = defaults;
    
	// Bind the live event before DOM-ready for maximum performance in IE6 & 7.
    handler = function (e) {
        // checks to see if it was a non-left mouse-click and for clicks modified with ctrl, shift, or alt.
        if (!((e.button !== 0 && typeof e.button !== 'undefined') || e.ctrlKey || e.shiftKey || e.altKey)) {
            e.preventDefault();
            launch(this);
        }
    };
    
    if ($.fn.delegate) {
        $(document).delegate('.' + boxElement, 'click', handler);
    } else {
        $('.' + boxElement).live('click', handler);
    }
    
	// Initializes ColorBox when the DOM has loaded
	$(publicMethod.init);

}(jQuery, document, this));
//////////////////////////////////////////////////////////////////////////////////
// Cloud Zoom V1.0.2
// (c) 2010 by R Cecco. <http://www.professorcloud.com>
// MIT License
//
// Please retain this copyright header in all versions of the software
//////////////////////////////////////////////////////////////////////////////////
(function ($) {

  //    $(document).ready(function () {
  //        $('.cloud-zoom, .cloud-zoom-gallery').CloudZoom();
  //    });

  function format(str) {
    for (var i = 1; i < arguments.length; i++) {
      str = str.replace('%' + (i - 1), arguments[i]);
    }
    return str;
  }

  function CloudZoom(jWin, opts) {
    var sImg = $('img', jWin);
    var	img1;
    var	img2;
    var zoomDiv = null;
    var	$mouseTrap = null;
    var	lens = null;
    var	$tint = null;
    var	softFocus = null;
    var	$ie6Fix = null;
    var	zoomImage;
    var controlTimer = 0;
    var cw, ch;
    var destU = 0;
    var	destV = 0;
    var currV = 0;
    var currU = 0;
    var filesLoaded = 0;
    var mx, my;
    var ctx = this, zw;
        
    // Display an image loading message. This message gets deleted when the images have loaded and the zoom init function is called.
    // We add a small delay before the message is displayed to avoid the message flicking on then off again virtually immediately if the
    // images load really fast, e.g. from the cache.
    //var	ctx = this;
    setTimeout(function () {
      //						 <img src="/images/loading.gif"/>
      if ($mouseTrap === null) {
        var w = jWin.width();
        jWin.parent().append(format('<div style="width:%0px;position:absolute;top:75%;left:%1px;text-align:center" class="cloud-zoom-loading" >Loading...</div>', w / 3, (w / 2) - (w / 6))).find(':last').css('opacity', 0.5);
      }
    }, 200);


    var ie6FixRemove = function () {

      if ($ie6Fix !== null) {
        $ie6Fix.remove();
        $ie6Fix = null;
      }
    };

    // Removes cursor, tint layer, blur layer etc.
    this.removeBits = function () {
      //$mouseTrap.unbind();
      if (lens) {
        lens.remove();
        lens = null;
      }
      if ($tint) {
        $tint.remove();
        $tint = null;
      }
      if (softFocus) {
        softFocus.remove();
        softFocus = null;
      }
      ie6FixRemove();

      $('.cloud-zoom-loading', jWin.parent()).remove();
    };


    this.destroy = function () {
      jWin.data('zoom', null);

      if ($mouseTrap) {
        $mouseTrap.unbind();
        $mouseTrap.remove();
        $mouseTrap = null;
      }
      if (zoomDiv) {
        zoomDiv.remove();
        zoomDiv = null;
      }
      //ie6FixRemove();
      this.removeBits();
    // DON'T FORGET TO REMOVE JQUERY 'DATA' VALUES


    };


    // This is called when the zoom window has faded out so it can be removed.
    this.fadedOut = function () {
            
      if (zoomDiv) {
        zoomDiv.remove();
        zoomDiv = null;
      }
      this.removeBits();
    //ie6FixRemove();
    };

    this.controlLoop = function () {
      if (lens) {
        var x = (mx - sImg.offset().left - (cw * 0.5)) >> 0;
        var y = (my - sImg.offset().top - (ch * 0.5)) >> 0;
               
        if (x < 0) {
          x = 0;
        }
        else if (x > (sImg.outerWidth() - cw)) {
          x = (sImg.outerWidth() - cw);
        }
        if (y < 0) {
          y = 0;
        }
        else if (y > (sImg.outerHeight() - ch)) {
          y = (sImg.outerHeight() - ch);
        }

        lens.css({
          left: x,
          top: y
        });
        lens.css('background-position', (-x) + 'px ' + (-y) + 'px');

        destU = (((x) / sImg.outerWidth()) * zoomImage.width) >> 0;
        destV = (((y) / sImg.outerHeight()) * zoomImage.height) >> 0;
        currU += (destU - currU) / opts.smoothMove;
        currV += (destV - currV) / opts.smoothMove;

        zoomDiv.css('background-position', (-(currU >> 0) + 'px ') + (-(currV >> 0) + 'px'));
      }
      controlTimer = setTimeout(function () {
        ctx.controlLoop();
      }, 30);
    };

    this.init2 = function (img, id) {

      filesLoaded++;
      //console.log(img.src + ' ' + id + ' ' + img.width);
      if (id === 1) {
        zoomImage = img;
      }
      //this.images[id] = img;
      if (filesLoaded === 2) {
        this.init();
      }
    };

    /* Init function start.  */
    this.init = function () {
      // Remove loading message (if present);
      $('.cloud-zoom-loading', jWin.parent()).remove();


      /**
       * Add a box (mouseTrap) over the small image to trap mouse events.
       * It has priority over zoom window to avoid issues with inner zoom.
       * We need the dummy background image as IE does not trap mouse events on
       * transparent parts of a div.
       */
      $mouseTrap = jWin.parent().append(format("<div class='mousetrap' style='background-image:url(\".\");z-index:999;position:absolute;width:%0px;height:%1px;left:%2px;top:%3px;\'></div>", sImg.outerWidth(), sImg.outerHeight(), 0, 0)).find(':last');

      //////////////////////////////////////////////////////////////////////
      $mouseTrap.bind('click', this, function (event) {
        if (opts.click) {
          var e = event;
          var o = jWin;
          event.data.destroy();
          opts.click.call(e, o);
        }
      });
      //////////////////////////////////////////////////////////////////////
      /* Do as little as possible in mousemove event to prevent slowdown. */
      $mouseTrap.bind('mousemove', this, function (event) {
        // Just update the mouse position
        mx = event.pageX;
        my = event.pageY;
      });
      //////////////////////////////////////////////////////////////////////
      $mouseTrap.bind('mouseleave', this, function (event) {
        clearTimeout(controlTimer);
        //event.data.removeBits();
        if(lens) {
          lens.fadeOut(299);
        }
        if($tint) {
          $tint.fadeOut(299);
        }
        if(softFocus) {
          softFocus.fadeOut(299);
        }
        zoomDiv.fadeOut(300, function () {
          ctx.fadedOut();
        });
        return false;
      });
      //////////////////////////////////////////////////////////////////////
      $mouseTrap.bind('mouseenter', this, function (event) {
        mx = event.pageX;
        my = event.pageY;
        zw = event.data;
        if (zoomDiv) {
          zoomDiv.stop(true, false);
          zoomDiv.remove();
        }

        var xPos = opts.adjustX,
        yPos = opts.adjustY;
                             
        var siw = sImg.outerWidth();
        var sih = sImg.outerHeight();

        var w = opts.zoomWidth;
        var h = opts.zoomHeight;
        if (opts.zoomWidth == 'auto') {
          w = siw;
        }
        if (opts.zoomHeight == 'auto') {
          h = sih;
        }
        //$('#info').text( xPos + ' ' + yPos + ' ' + siw + ' ' + sih );
        var appendTo = jWin.parent(); // attach to the wrapper
        switch (opts.position) {
          case 'top':
            yPos -= h; // + opts.adjustY;
            break;
          case 'right':
            xPos += siw; // + opts.adjustX;
            break;
          case 'bottom':
            yPos += sih; // + opts.adjustY;
            break;
          case 'left':
            xPos -= w; // + opts.adjustX;
            break;
          case 'inside':
            w = siw;
            h = sih;
            break;
          // All other values, try and find an id in the dom to attach to.
          default:
            appendTo = $('#' + opts.position);
            // If dom element doesn't exit, just use 'right' position as default.
            if (!appendTo.length) {
              appendTo = jWin;
              xPos += siw; //+ opts.adjustX;
              yPos += sih; // + opts.adjustY;
            } else {
              w = appendTo.innerWidth();
              h = appendTo.innerHeight();
            }
        }

        zoomDiv = appendTo.append(format('<div id="cloud-zoom-big" class="cloud-zoom-big" style="display:none;position:absolute;left:%0px;top:%1px;width:%2px;height:%3px;background-image:url(\'%4\');z-index:991;"></div>', xPos, yPos, w, h, zoomImage.src)).find(':last');

        // Add the title from title tag.
        if (sImg.attr('title') && opts.showTitle) {
          zoomDiv.append(format('<div class="cloud-zoom-title">%0</div>', sImg.attr('title'))).find(':last').css('opacity', opts.titleOpacity);
        }

        // Fix ie6 select elements wrong z-index bug. Placing an iFrame over the select element solves the issue...
        if ($.browser.msie && $.browser.version < 7) {
          $ie6Fix = $('<iframe frameborder="0" src="#"></iframe>').css({
            position: "absolute",
            left: xPos,
            top: yPos,
            zIndex: 99,
            width: w,
            height: h
          }).insertBefore(zoomDiv);
        }

        zoomDiv.fadeIn(500);

        if (lens) {
          lens.remove();
          lens = null;
        } /* Work out size of cursor */
        cw = (sImg.outerWidth() / zoomImage.width) * zoomDiv.width();
        ch = (sImg.outerHeight() / zoomImage.height) * zoomDiv.height();

        // Attach mouse, initially invisible to prevent first frame glitch
        lens = jWin.append(format("<div class = 'cloud-zoom-lens' style='display:none;z-index:98;position:absolute;width:%0px;height:%1px;'></div>", cw, ch)).find(':last');

        $mouseTrap.css('cursor', lens.css('cursor'));

        var noTrans = false;

        // Init tint layer if needed. (Not relevant if using inside mode)
        if (opts.tint) {
          lens.css('background', 'url("' + sImg.attr('src') + '")');
          $tint = jWin.append(format('<div style="display:none;position:absolute; left:0px; top:0px; width:%0px; height:%1px; background-color:%2;" />', sImg.outerWidth(), sImg.outerHeight(), opts.tint)).find(':last');
          $tint.css('opacity', opts.tintOpacity);
          noTrans = true;
          $tint.fadeIn(500);

        }
        if (opts.softFocus) {
          lens.css('background', 'url("' + sImg.attr('src') + '")');
          softFocus = jWin.append(format('<div style="position:absolute;display:none;top:2px; left:2px; width:%0px; height:%1px;" />', sImg.outerWidth() - 2, sImg.outerHeight() - 2, opts.tint)).find(':last');
          softFocus.css('background', 'url("' + sImg.attr('src') + '")');
          softFocus.css('opacity', 0.5);
          noTrans = true;
          softFocus.fadeIn(500);
        }

        if (!noTrans) {
          lens.css('opacity', opts.lensOpacity);
        }
        if ( opts.position !== 'inside' ) {
          lens.fadeIn(500);
        }

        // Start processing.
        zw.controlLoop();

        return; // Don't return false here otherwise opera will not detect change of the mouse pointer type.
      });
    };

    img1 = new Image();
    $(img1).load(function () {
      ctx.init2(this, 0);
    });
    img1.src = sImg.attr('src');

    img2 = new Image();
    $(img2).load(function () {
      ctx.init2(this, 1);
    });
    img2.src = jWin.attr('href');
  }

  $.fn.CloudZoom = function (options) {
    // IE6 background image flicker fix
    try {
      document.execCommand("BackgroundImageCache", false, true);
    } catch (e) {}
    this.each(function () {
      var	relOpts, opts;
      // Hmm...eval...slap on wrist.
      
      var	a = {};

      relOpts = a;
      if ($(this).is('.cloud-zoom')) {
        $(this).css({
          'position': 'relative',
          'display': 'block'
        });
        $('img', $(this)).css({
          'display': 'block'
        });
        // Wrap an outer div around the link so we can attach things without them becoming part of the link.
        // But not if wrap already exists.
        if ($(this).parent().attr('id') != 'wrap') {
          $(this).wrap('<div id="wrap" style="top:0px;z-index:999;position:relative;"></div>');
        }
        opts = $.extend({}, $.fn.CloudZoom.defaults, options);
        opts = $.extend({}, opts, relOpts);
        $(this).data('zoom', new CloudZoom($(this), opts));

      } else if ($(this).is('.cloud-zoom-gallery')) {
        opts = $.extend({}, relOpts, options);
        $(this).data('relOpts', opts);
        $(this).bind('click', $(this), function (event) {
          var data = event.data.data('relOpts');
          // Destroy the previous zoom
          $('#' + data.useZoom).data('zoom').destroy();
          // Change the biglink to point to the new big image.
          $('#' + data.useZoom).attr('href', event.data.attr('href'));
          // Change the small image to point to the new small image.
          $('#' + data.useZoom + ' img').attr('src', event.data.data('relOpts').smallImage);
          // Init a new zoom with the new images.
          $('#' + event.data.data('relOpts').useZoom).CloudZoom();
          return false;
        });
      }
    });
    return this;
  };

  $.fn.CloudZoom.defaults = {
    zoomWidth: 'auto',
    zoomHeight: 'auto',
    position: 'right',
    tint: false,
    tintOpacity: 0.5,
    lensOpacity: 0.5,
    softFocus: false,
    smoothMove: 3,
    showTitle: true,
    titleOpacity: 0.5,
    adjustX: 0,
    adjustY: 0,
    click: false
  };

})(jQuery);

/* http://keith-wood.name/countdown.html
   Countdown for jQuery v1.5.8.
   Written by Keith Wood (kbwood{at}iinet.com.au) January 2008.
   Dual licensed under the GPL (http://dev.jquery.com/browser/trunk/jquery/GPL-LICENSE.txt) and 
   MIT (http://dev.jquery.com/browser/trunk/jquery/MIT-LICENSE.txt) licenses. 
   Please attribute the author if you use it. */

/* Display a countdown timer.
   Attach it with options like:
   $('div selector').countdown(
       {until: new Date(2009, 1 - 1, 1, 0, 0, 0), onExpiry: happyNewYear}); */

(function($) { // Hide scope, no $ conflict

/* Countdown manager. */
function Countdown() {
	this.regional = []; // Available regional settings, indexed by language code
	this.regional[''] = { // Default regional settings
		// The display texts for the counters
		labels: ['Years', 'Months', 'Weeks', 'Days', 'Hours', 'Minutes', 'Seconds'],
		// The display texts for the counters if only one
		labels1: ['Year', 'Month', 'Week', 'Day', 'Hour', 'Minute', 'Second'],
		compactLabels: ['y', 'm', 'w', 'd'], // The compact texts for the counters
		whichLabels: null, // Function to determine which labels to use
		timeSeparator: ':', // Separator for time periods
		isRTL: false // True for right-to-left languages, false for left-to-right
	};
	this._defaults = {
		until: null, // new Date(year, mth - 1, day, hr, min, sec) - date/time to count down to
			// or numeric for seconds offset, or string for unit offset(s):
			// 'Y' years, 'O' months, 'W' weeks, 'D' days, 'H' hours, 'M' minutes, 'S' seconds
		since: null, // new Date(year, mth - 1, day, hr, min, sec) - date/time to count up from
			// or numeric for seconds offset, or string for unit offset(s):
			// 'Y' years, 'O' months, 'W' weeks, 'D' days, 'H' hours, 'M' minutes, 'S' seconds
		timezone: null, // The timezone (hours or minutes from GMT) for the target times,
			// or null for client local
		serverSync: null, // A function to retrieve the current server time for synchronisation
		format: 'dHMS', // Format for display - upper case for always, lower case only if non-zero,
			// 'Y' years, 'O' months, 'W' weeks, 'D' days, 'H' hours, 'M' minutes, 'S' seconds
		layout: '', // Build your own layout for the countdown
		compact: false, // True to display in a compact format, false for an expanded one
		significant: 0, // The number of periods with values to show, zero for all
		description: '', // The description displayed for the countdown
		expiryUrl: '', // A URL to load upon expiry, replacing the current page
		expiryText: '', // Text to display upon expiry, replacing the countdown
		alwaysExpire: false, // True to trigger onExpiry even if never counted down
		onExpiry: null, // Callback when the countdown expires -
			// receives no parameters and 'this' is the containing division
		onTick: null, // Callback when the countdown is updated -
			// receives int[7] being the breakdown by period (based on format)
			// and 'this' is the containing division
		tickInterval: 1 // Interval (seconds) between onTick callbacks
	};
	$.extend(this._defaults, this.regional['']);
	this._serverSyncs = [];
}

var PROP_NAME = 'countdown';

var Y = 0; // Years
var O = 1; // Months
var W = 2; // Weeks
var D = 3; // Days
var H = 4; // Hours
var M = 5; // Minutes
var S = 6; // Seconds

$.extend(Countdown.prototype, {
	/* Class name added to elements to indicate already configured with countdown. */
	markerClassName: 'hasCountdown',
	
	/* Shared timer for all countdowns. */
	_timer: setInterval(function() { $.countdown._updateTargets(); }, 980),
	/* List of currently active countdown targets. */
	_timerTargets: [],
	
	/* Override the default settings for all instances of the countdown widget.
	   @param  options  (object) the new settings to use as defaults */
	setDefaults: function(options) {
		this._resetExtraLabels(this._defaults, options);
		extendRemove(this._defaults, options || {});
	},

	/* Convert a date/time to UTC.
	   @param  tz     (number) the hour or minute offset from GMT, e.g. +9, -360
	   @param  year   (Date) the date/time in that timezone or
	                  (number) the year in that timezone
	   @param  month  (number, optional) the month (0 - 11) (omit if year is a Date)
	   @param  day    (number, optional) the day (omit if year is a Date)
	   @param  hours  (number, optional) the hour (omit if year is a Date)
	   @param  mins   (number, optional) the minute (omit if year is a Date)
	   @param  secs   (number, optional) the second (omit if year is a Date)
	   @param  ms     (number, optional) the millisecond (omit if year is a Date)
	   @return  (Date) the equivalent UTC date/time */
	UTCDate: function(tz, year, month, day, hours, mins, secs, ms) {
		if (typeof year == 'object' && year.constructor == Date) {
			ms = year.getMilliseconds();
			secs = year.getSeconds();
			mins = year.getMinutes();
			hours = year.getHours();
			day = year.getDate();
			month = year.getMonth();
			year = year.getFullYear();
		}
		var d = new Date();
		d.setUTCFullYear(year);
		d.setUTCDate(1);
		d.setUTCMonth(month || 0);
		d.setUTCDate(day || 1);
		d.setUTCHours(hours || 0);
		d.setUTCMinutes((mins || 0) - (Math.abs(tz) < 30 ? tz * 60 : tz));
		d.setUTCSeconds(secs || 0);
		d.setUTCMilliseconds(ms || 0);
		return d;
	},

	/* Convert a set of periods into seconds.
	   Averaged for months and years.
	   @param  periods  (number[7]) the periods per year/month/week/day/hour/minute/second
	   @return  (number) the corresponding number of seconds */
	periodsToSeconds: function(periods) {
		return periods[0] * 31557600 + periods[1] * 2629800 + periods[2] * 604800 +
			periods[3] * 86400 + periods[4] * 3600 + periods[5] * 60 + periods[6];
	},

	/* Retrieve one or more settings values.
	   @param  name  (string, optional) the name of the setting to retrieve
	                 or 'all' for all instance settings or omit for all default settings
	   @return  (any) the requested setting(s) */
	_settingsCountdown: function(target, name) {
		if (!name) {
			return $.countdown._defaults;
		}
		var inst = $.data(target, PROP_NAME);
		return (name == 'all' ? inst.options : inst.options[name]);
	},

	/* Attach the countdown widget to a div.
	   @param  target   (element) the containing division
	   @param  options  (object) the initial settings for the countdown */
	_attachCountdown: function(target, options) {
		var $target = $(target);
		if ($target.hasClass(this.markerClassName)) {
			return;
		}
		$target.addClass(this.markerClassName);
		var inst = {options: $.extend({}, options),
			_periods: [0, 0, 0, 0, 0, 0, 0]};
		$.data(target, PROP_NAME, inst);
		this._changeCountdown(target);
	},

	/* Add a target to the list of active ones.
	   @param  target  (element) the countdown target */
	_addTarget: function(target) {
		if (!this._hasTarget(target)) {
			this._timerTargets.push(target);
		}
	},

	/* See if a target is in the list of active ones.
	   @param  target  (element) the countdown target
	   @return  (boolean) true if present, false if not */
	_hasTarget: function(target) {
		return ($.inArray(target, this._timerTargets) > -1);
	},

	/* Remove a target from the list of active ones.
	   @param  target  (element) the countdown target */
	_removeTarget: function(target) {
		this._timerTargets = $.map(this._timerTargets,
			function(value) { return (value == target ? null : value); }); // delete entry
	},

	/* Update each active timer target. */
	_updateTargets: function() {
		for (var i = this._timerTargets.length - 1; i >= 0; i--) {
			this._updateCountdown(this._timerTargets[i]);
		}
	},

	/* Redisplay the countdown with an updated display.
	   @param  target  (jQuery) the containing division
	   @param  inst    (object) the current settings for this instance */
	_updateCountdown: function(target, inst) {
		var $target = $(target);
		inst = inst || $.data(target, PROP_NAME);
		if (!inst) {
			return;
		}
		$target.html(this._generateHTML(inst));
		$target[(this._get(inst, 'isRTL') ? 'add' : 'remove') + 'Class']('countdown_rtl');
		var onTick = this._get(inst, 'onTick');
		if (onTick) {
			var periods = inst._hold != 'lap' ? inst._periods :
				this._calculatePeriods(inst, inst._show, this._get(inst, 'significant'), new Date());
			var tickInterval = this._get(inst, 'tickInterval');
			if (tickInterval == 1 || this.periodsToSeconds(periods) % tickInterval == 0) {
				onTick.apply(target, [periods]);
			}
		}
		var expired = inst._hold != 'pause' &&
			(inst._since ? inst._now.getTime() < inst._since.getTime() :
			inst._now.getTime() >= inst._until.getTime());
		if (expired && !inst._expiring) {
			inst._expiring = true;
			if (this._hasTarget(target) || this._get(inst, 'alwaysExpire')) {
				this._removeTarget(target);
				var onExpiry = this._get(inst, 'onExpiry');
				if (onExpiry) {
					onExpiry.apply(target, []);
				}
				var expiryText = this._get(inst, 'expiryText');
				if (expiryText) {
					var layout = this._get(inst, 'layout');
					inst.options.layout = expiryText;
					this._updateCountdown(target, inst);
					inst.options.layout = layout;
				}
				var expiryUrl = this._get(inst, 'expiryUrl');
				if (expiryUrl) {
					window.location = expiryUrl;
				}
			}
			inst._expiring = false;
		}
		else if (inst._hold == 'pause') {
			this._removeTarget(target);
		}
		$.data(target, PROP_NAME, inst);
	},

	/* Reconfigure the settings for a countdown div.
	   @param  target   (element) the containing division
	   @param  options  (object) the new settings for the countdown or
	                    (string) an individual property name
	   @param  value    (any) the individual property value
	                    (omit if options is an object) */
	_changeCountdown: function(target, options, value) {
		options = options || {};
		if (typeof options == 'string') {
			var name = options;
			options = {};
			options[name] = value;
		}
		var inst = $.data(target, PROP_NAME);
		if (inst) {
			this._resetExtraLabels(inst.options, options);
			extendRemove(inst.options, options);
			this._adjustSettings(target, inst);
			$.data(target, PROP_NAME, inst);
			var now = new Date();
			if ((inst._since && inst._since < now) ||
					(inst._until && inst._until > now)) {
				this._addTarget(target);
			}
			this._updateCountdown(target, inst);
		}
	},

	/* Reset any extra labelsn and compactLabelsn entries if changing labels.
	   @param  base     (object) the options to be updated
	   @param  options  (object) the new option values */
	_resetExtraLabels: function(base, options) {
		var changingLabels = false;
		for (var n in options) {
			if (n != 'whichLabels' && n.match(/[Ll]abels/)) {
				changingLabels = true;
				break;
			}
		}
		if (changingLabels) {
			for (var n in base) { // Remove custom numbered labels
				if (n.match(/[Ll]abels[0-9]/)) {
					base[n] = null;
				}
			}
		}
	},
	
	/* Calculate interal settings for an instance.
	   @param  target  (element) the containing division
	   @param  inst    (object) the current settings for this instance */
	_adjustSettings: function(target, inst) {
		var now;
		var serverSync = this._get(inst, 'serverSync');
		var serverOffset = 0;
		var serverEntry = null;
		for (var i = 0; i < this._serverSyncs.length; i++) {
			if (this._serverSyncs[i][0] == serverSync) {
				serverEntry = this._serverSyncs[i][1];
				break;
			}
		}
		if (serverEntry != null) {
			serverOffset = (serverSync ? serverEntry : 0);
			now = new Date();
		}
		else {
			var serverResult = (serverSync ? serverSync.apply(target, []) : null);
			now = new Date();
			serverOffset = (serverResult ? now.getTime() - serverResult.getTime() : 0);
			this._serverSyncs.push([serverSync, serverOffset]);
		}
		var timezone = this._get(inst, 'timezone');
		timezone = (timezone == null ? -now.getTimezoneOffset() : timezone);
		inst._since = this._get(inst, 'since');
		if (inst._since != null) {
			inst._since = this.UTCDate(timezone, this._determineTime(inst._since, null));
			if (inst._since && serverOffset) {
				inst._since.setMilliseconds(inst._since.getMilliseconds() + serverOffset);
			}
		}
		inst._until = this.UTCDate(timezone, this._determineTime(this._get(inst, 'until'), now));
		if (serverOffset) {
			inst._until.setMilliseconds(inst._until.getMilliseconds() + serverOffset);
		}
		inst._show = this._determineShow(inst);
	},

	/* Remove the countdown widget from a div.
	   @param  target  (element) the containing division */
	_destroyCountdown: function(target) {
		var $target = $(target);
		if (!$target.hasClass(this.markerClassName)) {
			return;
		}
		this._removeTarget(target);
		$target.removeClass(this.markerClassName).empty();
		$.removeData(target, PROP_NAME);
	},

	/* Pause a countdown widget at the current time.
	   Stop it running but remember and display the current time.
	   @param  target  (element) the containing division */
	_pauseCountdown: function(target) {
		this._hold(target, 'pause');
	},

	/* Pause a countdown widget at the current time.
	   Stop the display but keep the countdown running.
	   @param  target  (element) the containing division */
	_lapCountdown: function(target) {
		this._hold(target, 'lap');
	},

	/* Resume a paused countdown widget.
	   @param  target  (element) the containing division */
	_resumeCountdown: function(target) {
		this._hold(target, null);
	},

	/* Pause or resume a countdown widget.
	   @param  target  (element) the containing division
	   @param  hold    (string) the new hold setting */
	_hold: function(target, hold) {
		var inst = $.data(target, PROP_NAME);
		if (inst) {
			if (inst._hold == 'pause' && !hold) {
				inst._periods = inst._savePeriods;
				var sign = (inst._since ? '-' : '+');
				inst[inst._since ? '_since' : '_until'] =
					this._determineTime(sign + inst._periods[0] + 'y' +
						sign + inst._periods[1] + 'o' + sign + inst._periods[2] + 'w' +
						sign + inst._periods[3] + 'd' + sign + inst._periods[4] + 'h' + 
						sign + inst._periods[5] + 'm' + sign + inst._periods[6] + 's');
				this._addTarget(target);
			}
			inst._hold = hold;
			inst._savePeriods = (hold == 'pause' ? inst._periods : null);
			$.data(target, PROP_NAME, inst);
			this._updateCountdown(target, inst);
		}
	},

	/* Return the current time periods.
	   @param  target  (element) the containing division
	   @return  (number[7]) the current periods for the countdown */
	_getTimesCountdown: function(target) {
		var inst = $.data(target, PROP_NAME);
		return (!inst ? null : (!inst._hold ? inst._periods :
			this._calculatePeriods(inst, inst._show, this._get(inst, 'significant'), new Date())));
	},

	/* Get a setting value, defaulting if necessary.
	   @param  inst  (object) the current settings for this instance
	   @param  name  (string) the name of the required setting
	   @return  (any) the setting's value or a default if not overridden */
	_get: function(inst, name) {
		return (inst.options[name] != null ?
			inst.options[name] : $.countdown._defaults[name]);
	},

	/* A time may be specified as an exact value or a relative one.
	   @param  setting      (string or number or Date) - the date/time value
	                        as a relative or absolute value
	   @param  defaultTime  (Date) the date/time to use if no other is supplied
	   @return  (Date) the corresponding date/time */
	_determineTime: function(setting, defaultTime) {
		var offsetNumeric = function(offset) { // e.g. +300, -2
			var time = new Date();
			time.setTime(time.getTime() + offset * 1000);
			return time;
		};
		var offsetString = function(offset) { // e.g. '+2d', '-4w', '+3h +30m'
			offset = offset.toLowerCase();
			var time = new Date();
			var year = time.getFullYear();
			var month = time.getMonth();
			var day = time.getDate();
			var hour = time.getHours();
			var minute = time.getMinutes();
			var second = time.getSeconds();
			var pattern = /([+-]?[0-9]+)\s*(s|m|h|d|w|o|y)?/g;
			var matches = pattern.exec(offset);
			while (matches) {
				switch (matches[2] || 's') {
					case 's': second += parseInt(matches[1], 10); break;
					case 'm': minute += parseInt(matches[1], 10); break;
					case 'h': hour += parseInt(matches[1], 10); break;
					case 'd': day += parseInt(matches[1], 10); break;
					case 'w': day += parseInt(matches[1], 10) * 7; break;
					case 'o':
						month += parseInt(matches[1], 10); 
						day = Math.min(day, $.countdown._getDaysInMonth(year, month));
						break;
					case 'y':
						year += parseInt(matches[1], 10);
						day = Math.min(day, $.countdown._getDaysInMonth(year, month));
						break;
				}
				matches = pattern.exec(offset);
			}
			return new Date(year, month, day, hour, minute, second, 0);
		};
		var time = (setting == null ? defaultTime :
			(typeof setting == 'string' ? offsetString(setting) :
			(typeof setting == 'number' ? offsetNumeric(setting) : setting)));
		if (time) time.setMilliseconds(0);
		return time;
	},

	/* Determine the number of days in a month.
	   @param  year   (number) the year
	   @param  month  (number) the month
	   @return  (number) the days in that month */
	_getDaysInMonth: function(year, month) {
		return 32 - new Date(year, month, 32).getDate();
	},

	/* Determine which set of labels should be used for an amount.
	   @param  num  (number) the amount to be displayed
	   @return  (number) the set of labels to be used for this amount */
	_normalLabels: function(num) {
		return num;
	},

	/* Generate the HTML to display the countdown widget.
	   @param  inst  (object) the current settings for this instance
	   @return  (string) the new HTML for the countdown display */
	_generateHTML: function(inst) {
		// Determine what to show
		var significant = this._get(inst, 'significant');
		inst._periods = (inst._hold ? inst._periods :
			this._calculatePeriods(inst, inst._show, significant, new Date()));
		// Show all 'asNeeded' after first non-zero value
		var shownNonZero = false;
		var showCount = 0;
		var sigCount = significant;
		var show = $.extend({}, inst._show);
		for (var period = Y; period <= S; period++) {
			shownNonZero |= (inst._show[period] == '?' && inst._periods[period] > 0);
			show[period] = (inst._show[period] == '?' && !shownNonZero ? null : inst._show[period]);
			showCount += (show[period] ? 1 : 0);
			sigCount -= (inst._periods[period] > 0 ? 1 : 0);
		}
		var showSignificant = [false, false, false, false, false, false, false];
		for (var period = S; period >= Y; period--) { // Determine significant periods
			if (inst._show[period]) {
				if (inst._periods[period]) {
					showSignificant[period] = true;
				}
				else {
					showSignificant[period] = sigCount > 0;
					sigCount--;
				}
			}
		}
		var compact = this._get(inst, 'compact');
		var layout = this._get(inst, 'layout');
		var labels = (compact ? this._get(inst, 'compactLabels') : this._get(inst, 'labels'));
		var whichLabels = this._get(inst, 'whichLabels') || this._normalLabels;
		var timeSeparator = this._get(inst, 'timeSeparator');
		var description = this._get(inst, 'description') || '';
		var showCompact = function(period) {
			var labelsNum = $.countdown._get(inst,
				'compactLabels' + whichLabels(inst._periods[period]));
			return (show[period] ? inst._periods[period] +
				(labelsNum ? labelsNum[period] : labels[period]) + ' ' : '');
		};
		var showFull = function(period) {
			var labelsNum = $.countdown._get(inst, 'labels' + whichLabels(inst._periods[period]));
			return ((!significant && show[period]) || (significant && showSignificant[period]) ?
				'<span class="countdown_section"><span class="countdown_amount">' +
				inst._periods[period] + '</span><br/>' +
				(labelsNum ? labelsNum[period] : labels[period]) + '</span>' : '');
		};
		return (layout ? this._buildLayout(inst, show, layout, compact, significant, showSignificant) :
			((compact ? // Compact version
			'<span class="countdown_row countdown_amount' +
			(inst._hold ? ' countdown_holding' : '') + '">' + 
			showCompact(Y) + showCompact(O) + showCompact(W) + showCompact(D) + 
			(show[H] ? this._minDigits(inst._periods[H], 2) : '') +
			(show[M] ? (show[H] ? timeSeparator : '') +
			this._minDigits(inst._periods[M], 2) : '') +
			(show[S] ? (show[H] || show[M] ? timeSeparator : '') +
			this._minDigits(inst._periods[S], 2) : '') :
			// Full version
			'<span class="countdown_row countdown_show' + (significant || showCount) +
			(inst._hold ? ' countdown_holding' : '') + '">' +
			showFull(Y) + showFull(O) + showFull(W) + showFull(D) +
			showFull(H) + showFull(M) + showFull(S)) + '</span>' +
			(description ? '<span class="countdown_row countdown_descr">' + description + '</span>' : '')));
	},

	/* Construct a custom layout.
	   @param  inst             (object) the current settings for this instance
	   @param  show             (string[7]) flags indicating which periods are requested
	   @param  layout           (string) the customised layout
	   @param  compact          (boolean) true if using compact labels
	   @param  significant      (number) the number of periods with values to show, zero for all
	   @param  showSignificant  (boolean[7]) other periods to show for significance
	   @return  (string) the custom HTML */
	_buildLayout: function(inst, show, layout, compact, significant, showSignificant) {
		var labels = this._get(inst, (compact ? 'compactLabels' : 'labels'));
		var whichLabels = this._get(inst, 'whichLabels') || this._normalLabels;
		var labelFor = function(index) {
			return ($.countdown._get(inst,
				(compact ? 'compactLabels' : 'labels') + whichLabels(inst._periods[index])) ||
				labels)[index];
		};
		var digit = function(value, position) {
			return Math.floor(value / position) % 10;
		};
		var subs = {desc: this._get(inst, 'description'), sep: this._get(inst, 'timeSeparator'),
			yl: labelFor(Y), yn: inst._periods[Y], ynn: this._minDigits(inst._periods[Y], 2),
			ynnn: this._minDigits(inst._periods[Y], 3), y1: digit(inst._periods[Y], 1),
			y10: digit(inst._periods[Y], 10), y100: digit(inst._periods[Y], 100),
			y1000: digit(inst._periods[Y], 1000),
			ol: labelFor(O), on: inst._periods[O], onn: this._minDigits(inst._periods[O], 2),
			onnn: this._minDigits(inst._periods[O], 3), o1: digit(inst._periods[O], 1),
			o10: digit(inst._periods[O], 10), o100: digit(inst._periods[O], 100),
			o1000: digit(inst._periods[O], 1000),
			wl: labelFor(W), wn: inst._periods[W], wnn: this._minDigits(inst._periods[W], 2),
			wnnn: this._minDigits(inst._periods[W], 3), w1: digit(inst._periods[W], 1),
			w10: digit(inst._periods[W], 10), w100: digit(inst._periods[W], 100),
			w1000: digit(inst._periods[W], 1000),
			dl: labelFor(D), dn: inst._periods[D], dnn: this._minDigits(inst._periods[D], 2),
			dnnn: this._minDigits(inst._periods[D], 3), d1: digit(inst._periods[D], 1),
			d10: digit(inst._periods[D], 10), d100: digit(inst._periods[D], 100),
			d1000: digit(inst._periods[D], 1000),
			hl: labelFor(H), hn: inst._periods[H], hnn: this._minDigits(inst._periods[H], 2),
			hnnn: this._minDigits(inst._periods[H], 3), h1: digit(inst._periods[H], 1),
			h10: digit(inst._periods[H], 10), h100: digit(inst._periods[H], 100),
			h1000: digit(inst._periods[H], 1000),
			ml: labelFor(M), mn: inst._periods[M], mnn: this._minDigits(inst._periods[M], 2),
			mnnn: this._minDigits(inst._periods[M], 3), m1: digit(inst._periods[M], 1),
			m10: digit(inst._periods[M], 10), m100: digit(inst._periods[M], 100),
			m1000: digit(inst._periods[M], 1000),
			sl: labelFor(S), sn: inst._periods[S], snn: this._minDigits(inst._periods[S], 2),
			snnn: this._minDigits(inst._periods[S], 3), s1: digit(inst._periods[S], 1),
			s10: digit(inst._periods[S], 10), s100: digit(inst._periods[S], 100),
			s1000: digit(inst._periods[S], 1000)};
		var html = layout;
		// Replace period containers: {p<}...{p>}
		for (var i = Y; i <= S; i++) {
			var period = 'yowdhms'.charAt(i);
			var re = new RegExp('\\{' + period + '<\\}(.*)\\{' + period + '>\\}', 'g');
			html = html.replace(re, ((!significant && show[i]) ||
				(significant && showSignificant[i]) ? '$1' : ''));
		}
		// Replace period values: {pn}
		$.each(subs, function(n, v) {
			var re = new RegExp('\\{' + n + '\\}', 'g');
			html = html.replace(re, v);
		});
		return html;
	},

	/* Ensure a numeric value has at least n digits for display.
	   @param  value  (number) the value to display
	   @param  len    (number) the minimum length
	   @return  (string) the display text */
	_minDigits: function(value, len) {
		value = '' + value;
		if (value.length >= len) {
			return value;
		}
		value = '0000000000' + value;
		return value.substr(value.length - len);
	},

	/* Translate the format into flags for each period.
	   @param  inst  (object) the current settings for this instance
	   @return  (string[7]) flags indicating which periods are requested (?) or
	            required (!) by year, month, week, day, hour, minute, second */
	_determineShow: function(inst) {
		var format = this._get(inst, 'format');
		var show = [];
		show[Y] = (format.match('y') ? '?' : (format.match('Y') ? '!' : null));
		show[O] = (format.match('o') ? '?' : (format.match('O') ? '!' : null));
		show[W] = (format.match('w') ? '?' : (format.match('W') ? '!' : null));
		show[D] = (format.match('d') ? '?' : (format.match('D') ? '!' : null));
		show[H] = (format.match('h') ? '?' : (format.match('H') ? '!' : null));
		show[M] = (format.match('m') ? '?' : (format.match('M') ? '!' : null));
		show[S] = (format.match('s') ? '?' : (format.match('S') ? '!' : null));
		return show;
	},
	
	/* Calculate the requested periods between now and the target time.
	   @param  inst         (object) the current settings for this instance
	   @param  show         (string[7]) flags indicating which periods are requested/required
	   @param  significant  (number) the number of periods with values to show, zero for all
	   @param  now          (Date) the current date and time
	   @return  (number[7]) the current time periods (always positive)
	            by year, month, week, day, hour, minute, second */
	_calculatePeriods: function(inst, show, significant, now) {
		// Find endpoints
		inst._now = now;
		inst._now.setMilliseconds(0);
		var until = new Date(inst._now.getTime());
		if (inst._since) {
			if (now.getTime() < inst._since.getTime()) {
				inst._now = now = until;
			}
			else {
				now = inst._since;
			}
		}
		else {
			until.setTime(inst._until.getTime());
			if (now.getTime() > inst._until.getTime()) {
				inst._now = now = until;
			}
		}
		// Calculate differences by period
		var periods = [0, 0, 0, 0, 0, 0, 0];
		if (show[Y] || show[O]) {
			// Treat end of months as the same
			var lastNow = $.countdown._getDaysInMonth(now.getFullYear(), now.getMonth());
			var lastUntil = $.countdown._getDaysInMonth(until.getFullYear(), until.getMonth());
			var sameDay = (until.getDate() == now.getDate() ||
				(until.getDate() >= Math.min(lastNow, lastUntil) &&
				now.getDate() >= Math.min(lastNow, lastUntil)));
			var getSecs = function(date) {
				return (date.getHours() * 60 + date.getMinutes()) * 60 + date.getSeconds();
			};
			var months = Math.max(0,
				(until.getFullYear() - now.getFullYear()) * 12 + until.getMonth() - now.getMonth() +
				((until.getDate() < now.getDate() && !sameDay) ||
				(sameDay && getSecs(until) < getSecs(now)) ? -1 : 0));
			periods[Y] = (show[Y] ? Math.floor(months / 12) : 0);
			periods[O] = (show[O] ? months - periods[Y] * 12 : 0);
			// Adjust for months difference and end of month if necessary
			now = new Date(now.getTime());
			var wasLastDay = (now.getDate() == lastNow);
			var lastDay = $.countdown._getDaysInMonth(now.getFullYear() + periods[Y],
				now.getMonth() + periods[O]);
			if (now.getDate() > lastDay) {
				now.setDate(lastDay);
			}
			now.setFullYear(now.getFullYear() + periods[Y]);
			now.setMonth(now.getMonth() + periods[O]);
			if (wasLastDay) {
				now.setDate(lastDay);
			}
		}
		var diff = Math.floor((until.getTime() - now.getTime()) / 1000);
		var extractPeriod = function(period, numSecs) {
			periods[period] = (show[period] ? Math.floor(diff / numSecs) : 0);
			diff -= periods[period] * numSecs;
		};
		extractPeriod(W, 604800);
		extractPeriod(D, 86400);
		extractPeriod(H, 3600);
		extractPeriod(M, 60);
		extractPeriod(S, 1);
		if (diff > 0 && !inst._since) { // Round up if left overs
			var multiplier = [1, 12, 4.3482, 7, 24, 60, 60];
			var lastShown = S;
			var max = 1;
			for (var period = S; period >= Y; period--) {
				if (show[period]) {
					if (periods[lastShown] >= max) {
						periods[lastShown] = 0;
						diff = 1;
					}
					if (diff > 0) {
						periods[period]++;
						diff = 0;
						lastShown = period;
						max = 1;
					}
				}
				max *= multiplier[period];
			}
		}
		if (significant) { // Zero out insignificant periods
			for (var period = Y; period <= S; period++) {
				if (significant && periods[period]) {
					significant--;
				}
				else if (!significant) {
					periods[period] = 0;
				}
			}
		}
		return periods;
	}
});

/* jQuery extend now ignores nulls!
   @param  target  (object) the object to update
   @param  props   (object) the new settings
   @return  (object) the updated object */
function extendRemove(target, props) {
	$.extend(target, props);
	for (var name in props) {
		if (props[name] == null) {
			target[name] = null;
		}
	}
	return target;
}

/* Process the countdown functionality for a jQuery selection.
   @param  command  (string) the command to run (optional, default 'attach')
   @param  options  (object) the new settings to use for these countdown instances
   @return  (jQuery) for chaining further calls */
$.fn.countdown = function(options) {
	var otherArgs = Array.prototype.slice.call(arguments, 1);
	if (options == 'getTimes' || options == 'settings') {
		return $.countdown['_' + options + 'Countdown'].
			apply($.countdown, [this[0]].concat(otherArgs));
	}
	return this.each(function() {
		if (typeof options == 'string') {
			$.countdown['_' + options + 'Countdown'].apply($.countdown, [this].concat(otherArgs));
		}
		else {
			$.countdown._attachCountdown(this, options);
		}
	});
};

/* Initialise the countdown functionality. */
$.countdown = new Countdown(); // singleton instance

})(jQuery);

/* http://keith-wood.name/countdown.html
   Danish initialisation for the jQuery countdown extension
   Written by Buch (admin@buch90.dk).
*/
(function($) {
	$.countdown.regional['dk'] = {
		labels: ['år', 'måneder', 'uger', 'dage', 'timer', 'min', 'sek'],
		labels1: ['år', 'månad', 'uge', 'dag', 'time', 'min', 'sek'],
		compactLabels: ['Å', 'M', 'U', 'D'],
		whichLabels: null,
		timeSeparator: ':', isRTL: false
  };
	$.countdown.setDefaults($.countdown.regional['dk']);
})(jQuery);

/*
VideoJS - HTML5 Video Player
v2.0.2

This file is part of VideoJS. Copyright 2010 Zencoder, Inc.

VideoJS is free software: you can redistribute it and/or modify
it under the terms of the GNU Lesser General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

VideoJS is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public License
along with VideoJS.  If not, see <http://www.gnu.org/licenses/>.
*/

// Self-executing function to prevent global vars and help with minification
(function(window, undefined){
  var document = window.document;

// Using jresig's Class implementation http://ejohn.org/blog/simple-javascript-inheritance/
(function(){var initializing=false, fnTest=/xyz/.test(function(){xyz;}) ? /\b_super\b/ : /.*/; this.JRClass = function(){}; JRClass.extend = function(prop) { var _super = this.prototype; initializing = true; var prototype = new this(); initializing = false; for (var name in prop) { prototype[name] = typeof prop[name] == "function" && typeof _super[name] == "function" && fnTest.test(prop[name]) ? (function(name, fn){ return function() { var tmp = this._super; this._super = _super[name]; var ret = fn.apply(this, arguments); this._super = tmp; return ret; }; })(name, prop[name]) : prop[name]; } function JRClass() { if ( !initializing && this.init ) this.init.apply(this, arguments); } JRClass.prototype = prototype; JRClass.constructor = JRClass; JRClass.extend = arguments.callee; return JRClass;};})();

// Video JS Player Class
var VideoJS = JRClass.extend({

  // Initialize the player for the supplied video tag element
  // element: video tag
  init: function(element, setOptions){

    // Allow an ID string or an element
    if (typeof element == 'string') {
      this.video = document.getElementById(element);
    } else {
      this.video = element;
    }
    // Store reference to player on the video element.
    // So you can acess the player later: document.getElementById("video_id").player.play();
    this.video.player = this;
    this.values = {}; // Cache video values.
    this.elements = {}; // Store refs to controls elements.

    // Default Options
    this.options = {
      autoplay: false,
      preload: true,
      useBuiltInControls: false, // Use the browser's controls (iPhone)
      controlsBelow: false, // Display control bar below video vs. in front of
      controlsAtStart: false, // Make controls visible when page loads
      controlsHiding: true, // Hide controls when not over the video
      defaultVolume: 0.85, // Will be overridden by localStorage volume if available
      playerFallbackOrder: ["html5", "flash", "links"], // Players and order to use them
      flashPlayer: "htmlObject",
      flashPlayerVersion: false // Required flash version for fallback
    };
    // Override default options with global options
    if (typeof VideoJS.options == "object") { _V_.merge(this.options, VideoJS.options); }
    // Override default & global options with options specific to this player
    if (typeof setOptions == "object") { _V_.merge(this.options, setOptions); }
    // Override preload & autoplay with video attributes
    if (this.getPreloadAttribute() !== undefined) { this.options.preload = this.getPreloadAttribute(); }
    if (this.getAutoplayAttribute() !== undefined) { this.options.autoplay = this.getAutoplayAttribute(); }

    // Store reference to embed code pieces
    this.box = this.video.parentNode;
    this.linksFallback = this.getLinksFallback();
    this.hideLinksFallback(); // Will be shown again if "links" player is used

    // Loop through the player names list in options, "html5" etc.
    // For each player name, initialize the player with that name under VideoJS.players
    // If the player successfully initializes, we're done
    // If not, try the next player in the list
    this.each(this.options.playerFallbackOrder, function(playerType){
      if (this[playerType+"Supported"]()) { // Check if player type is supported
        this[playerType+"Init"](); // Initialize player type
        return true; // Stop looping though players
      }
    });

    // Start Global Listeners - API doesn't exist before now
    this.activateElement(this, "player");
    this.activateElement(this.box, "box");
  },
  /* Behaviors
  ================================================================================ */
  behaviors: {},
  newBehavior: function(name, activate, functions){
    this.behaviors[name] = activate;
    this.extend(functions);
  },
  activateElement: function(element, behavior){
    // Allow passing and ID string
    if (typeof element == "string") { element = document.getElementById(element); }
    this.behaviors[behavior].call(this, element);
  },
  /* Errors/Warnings
  ================================================================================ */
  errors: [], // Array to track errors
  warnings: [],
  warning: function(warning){
    this.warnings.push(warning);
    this.log(warning);
  },
  /* History of errors/events (not quite there yet)
  ================================================================================ */
  history: [],
  log: function(event){
    if (!event) { return; }
    if (typeof event == "string") { event = { type: event }; }
    if (event.type) { this.history.push(event.type); }
    if (this.history.length >= 50) { this.history.shift(); }
    try { console.log(event.type); } catch(e) { try { opera.postError(event.type); } catch(e){} }
  },
  /* Local Storage
  ================================================================================ */
  setLocalStorage: function(key, value){
    if (!localStorage) { return; }
    try {
      localStorage[key] = value;
    } catch(e) {
      if (e.code == 22 || e.code == 1014) { // Webkit == 22 / Firefox == 1014
        this.warning(VideoJS.warnings.localStorageFull);
      }
    }
  },
  /* Helpers
  ================================================================================ */
  getPreloadAttribute: function(){
    if (typeof this.video.hasAttribute == "function" && this.video.hasAttribute("preload")) {
      var preload = this.video.getAttribute("preload");
      // Only included the attribute, thinking it was boolean
      if (preload === "" || preload === "true") { return "auto"; }
      if (preload === "false") { return "none"; }
      return preload;
    }
  },
  getAutoplayAttribute: function(){
    if (typeof this.video.hasAttribute == "function" && this.video.hasAttribute("autoplay")) {
      var autoplay = this.video.getAttribute("autoplay");
      if (autoplay === "false") { return false; }
      return true;
    }
  },
  // Calculates amoutn of buffer is full
  bufferedPercent: function(){ return (this.duration()) ? this.buffered()[1] / this.duration() : 0; },
  // Each that maintains player as context
  // Break if true is returned
  each: function(arr, fn){
    if (!arr || arr.length === 0) { return; }
    for (var i=0,j=arr.length; i<j; i++) {
      if (fn.call(this, arr[i], i)) { break; }
    }
  },
  extend: function(obj){
    for (var attrname in obj) {
      if (obj.hasOwnProperty(attrname)) { this[attrname]=obj[attrname]; }
    }
  }
});
VideoJS.player = VideoJS.prototype;

////////////////////////////////////////////////////////////////////////////////
// Player Types
////////////////////////////////////////////////////////////////////////////////

/* Flash Object Fallback (Player Type)
================================================================================ */
VideoJS.player.extend({
  flashSupported: function(){
    if (!this.flashElement) { this.flashElement = this.getFlashElement(); }
    // Check if object exists & Flash Player version is supported
    if (this.flashElement && this.flashPlayerVersionSupported()) {
      return true;
    } else {
      return false;
    }
  },
  flashInit: function(){
    this.replaceWithFlash();
    this.element = this.flashElement;
    this.video.src = ""; // Stop video from downloading if HTML5 is still supported
    var flashPlayerType = VideoJS.flashPlayers[this.options.flashPlayer];
    this.extend(VideoJS.flashPlayers[this.options.flashPlayer].api);
    (flashPlayerType.init.context(this))();
  },
  // Get Flash Fallback object element from Embed Code
  getFlashElement: function(){
    var children = this.video.children;
    for (var i=0,j=children.length; i<j; i++) {
      if (children[i].className == "vjs-flash-fallback") {
        return children[i];
      }
    }
  },
  // Used to force a browser to fall back when it's an HTML5 browser but there's no supported sources
  replaceWithFlash: function(){
    // this.flashElement = this.video.removeChild(this.flashElement);
    if (this.flashElement) {
      this.box.insertBefore(this.flashElement, this.video);
      this.video.style.display = "none"; // Removing it was breaking later players
    }
  },
  // Check if browser can use this flash player
  flashPlayerVersionSupported: function(){
    var playerVersion = (this.options.flashPlayerVersion) ? this.options.flashPlayerVersion : VideoJS.flashPlayers[this.options.flashPlayer].flashPlayerVersion;
    return VideoJS.getFlashVersion() >= playerVersion;
  }
});
VideoJS.flashPlayers = {};
VideoJS.flashPlayers.htmlObject = {
  flashPlayerVersion: 9,
  init: function() { return true; },
  api: { // No video API available with HTML Object embed method
    width: function(width){
      if (width !== undefined) {
        this.element.width = width;
        this.box.style.width = width+"px";
        this.triggerResizeListeners();
        return this;
      }
      return this.element.width;
    },
    height: function(height){
      if (height !== undefined) {
        this.element.height = height;
        this.box.style.height = height+"px";
        this.triggerResizeListeners();
        return this;
      }
      return this.element.height;
    }
  }
};


/* Download Links Fallback (Player Type)
================================================================================ */
VideoJS.player.extend({
  linksSupported: function(){ return true; },
  linksInit: function(){
    this.showLinksFallback();
    this.element = this.video;
  },
  // Get the download links block element
  getLinksFallback: function(){ return this.box.getElementsByTagName("P")[0]; },
  // Hide no-video download paragraph
  hideLinksFallback: function(){
    if (this.linksFallback) { this.linksFallback.style.display = "none"; }
  },
  // Hide no-video download paragraph
  showLinksFallback: function(){
    if (this.linksFallback) { this.linksFallback.style.display = "block"; }
  }
});

////////////////////////////////////////////////////////////////////////////////
// Class Methods
// Functions that don't apply to individual videos.
////////////////////////////////////////////////////////////////////////////////

// Combine Objects - Use "safe" to protect from overwriting existing items
VideoJS.merge = function(obj1, obj2, safe){
  for (var attrname in obj2){
    if (obj2.hasOwnProperty(attrname) && (!safe || !obj1.hasOwnProperty(attrname))) { obj1[attrname]=obj2[attrname]; }
  }
  return obj1;
};
VideoJS.extend = function(obj){ this.merge(this, obj, true); };

VideoJS.extend({
  // Add VideoJS to all video tags with the video-js class when the DOM is ready
  setupAllWhenReady: function(options){
    // Options is stored globally, and added ot any new player on init
    VideoJS.options = options;
    VideoJS.DOMReady(VideoJS.setup);
  },

  // Run the supplied function when the DOM is ready
  DOMReady: function(fn){
    VideoJS.addToDOMReady(fn);
  },

  // Set up a specific video or array of video elements
  // "video" can be:
  //    false, undefined, or "All": set up all videos with the video-js class
  //    A video tag ID or video tag element: set up one video and return one player
  //    An array of video tag elements/IDs: set up each and return an array of players
  setup: function(videos, options){
    var returnSingular = false,
    playerList = [],
    videoElement;

    // If videos is undefined or "All", set up all videos with the video-js class
    if (!videos || videos == "All") {
      videos = VideoJS.getVideoJSTags();
    // If videos is not an array, add to an array
    } else if (typeof videos != 'object' || videos.nodeType == 1) {
      videos = [videos];
      returnSingular = true;
    }

    // Loop through videos and create players for them
    for (var i=0; i<videos.length; i++) {
      if (typeof videos[i] == 'string') {
        videoElement = document.getElementById(videos[i]);
      } else { // assume DOM object
        videoElement = videos[i];
      }
      playerList.push(new VideoJS(videoElement, options));
    }

    // Return one or all depending on what was passed in
    return (returnSingular) ? playerList[0] : playerList;
  },

  // Find video tags with the video-js class
  getVideoJSTags: function() {
    var videoTags = document.getElementsByTagName("video"),
    videoJSTags = [], videoTag;

    for (var i=0,j=videoTags.length; i<j; i++) {
      videoTag = videoTags[i];
      if (videoTag.className.indexOf("video-js") != -1) {
        videoJSTags.push(videoTag);
      }
    }
    return videoJSTags;
  },

  // Check if the browser supports video.
  browserSupportsVideo: function() {
    if (typeof VideoJS.videoSupport != "undefined") { return VideoJS.videoSupport; }
    VideoJS.videoSupport = !!document.createElement('video').canPlayType;
    return VideoJS.videoSupport;
  },

  getFlashVersion: function(){
    // Cache Version
    if (typeof VideoJS.flashVersion != "undefined") { return VideoJS.flashVersion; }
    var version = 0, desc;
    if (typeof navigator.plugins != "undefined" && typeof navigator.plugins["Shockwave Flash"] == "object") {
      desc = navigator.plugins["Shockwave Flash"].description;
      if (desc && !(typeof navigator.mimeTypes != "undefined" && navigator.mimeTypes["application/x-shockwave-flash"] && !navigator.mimeTypes["application/x-shockwave-flash"].enabledPlugin)) {
        version = parseInt(desc.match(/^.*\s+([^\s]+)\.[^\s]+\s+[^\s]+$/)[1], 10);
      }
    } else if (typeof window.ActiveXObject != "undefined") {
      try {
        var testObject = new ActiveXObject("ShockwaveFlash.ShockwaveFlash");
        if (testObject) {
          version = parseInt(testObject.GetVariable("$version").match(/^[^\s]+\s(\d+)/)[1], 10);
        }
      }
      catch(e) {}
    }
    VideoJS.flashVersion = version;
    return VideoJS.flashVersion;
  },

  // Browser & Device Checks
  isIE: function(){ return !+"\v1"; },
  isIPad: function(){ return navigator.userAgent.match(/iPad/i) !== null; },
  isIPhone: function(){ return navigator.userAgent.match(/iPhone/i) !== null; },
  isIOS: function(){ return VideoJS.isIPhone() || VideoJS.isIPad(); },
  iOSVersion: function() {
    var match = navigator.userAgent.match(/OS (\d+)_/i);
    if (match && match[1]) { return match[1]; }
  },
  isAndroid: function(){ return navigator.userAgent.match(/Android/i) !== null; },
  androidVersion: function() {
    var match = navigator.userAgent.match(/Android (\d+)\./i);
    if (match && match[1]) { return match[1]; }
  },

  warnings: {
    // Safari errors if you call functions on a video that hasn't loaded yet
    videoNotReady: "Video is not ready yet (try playing the video first).",
    // Getting a QUOTA_EXCEEDED_ERR when setting local storage occasionally
    localStorageFull: "Local Storage is Full"
  }
});

// Shim to make Video tag valid in IE
if(VideoJS.isIE()) { document.createElement("video"); }

// Expose to global
window.VideoJS = window._V_ = VideoJS;

/* HTML5 Player Type
================================================================================ */
VideoJS.player.extend({
  html5Supported: function(){
    if (VideoJS.browserSupportsVideo() && this.canPlaySource()) {
      return true;
    } else {
      return false;
    }
  },
  html5Init: function(){
    this.element = this.video;

    this.fixPreloading(); // Support old browsers that used autobuffer
    this.supportProgressEvents(); // Support browsers that don't use 'buffered'

    // Set to stored volume OR 85%
    this.volume((localStorage && localStorage.volume) || this.options.defaultVolume);

    // Update interface for device needs
    if (VideoJS.isIOS()) {
      this.options.useBuiltInControls = true;
      this.iOSInterface();
    } else if (VideoJS.isAndroid()) {
      this.options.useBuiltInControls = true;
      this.androidInterface();
    }

    // Add VideoJS Controls
    if (!this.options.useBuiltInControls) {
      this.video.controls = false;

      if (this.options.controlsBelow) { _V_.addClass(this.box, "vjs-controls-below"); }

      // Make a click on th video act as a play button
      this.activateElement(this.video, "playToggle");

      // Build Interface
      this.buildStylesCheckDiv(); // Used to check if style are loaded
      this.buildAndActivatePoster();
      this.buildBigPlayButton();
      this.buildAndActivateSpinner();
      this.buildAndActivateControlBar();
      this.loadInterface(); // Show everything once styles are loaded
      this.getSubtitles();
    }
  },
  /* Source Managemet
  ================================================================================ */
  canPlaySource: function(){
    // Cache Result
    if (this.canPlaySourceResult) { return this.canPlaySourceResult; }
    // Loop through sources and check if any can play
    var children = this.video.children;
    for (var i=0,j=children.length; i<j; i++) {
      if (children[i].tagName.toUpperCase() == "SOURCE") {
        var canPlay = this.video.canPlayType(children[i].type) || this.canPlayExt(children[i].src);
        if (canPlay == "probably" || canPlay == "maybe") {
          this.firstPlayableSource = children[i];
          this.canPlaySourceResult = true;
          return true;
        }
      }
    }
    this.canPlaySourceResult = false;
    return false;
  },
  // Check if the extention is compatible, for when type won't work
  canPlayExt: function(src){
    if (!src) { return ""; }
    var match = src.match(/\.([^\.]+)$/);
    if (match && match[1]) {
      var ext = match[1].toLowerCase();
      // Android canPlayType doesn't work
      if (VideoJS.isAndroid()) {
        if (ext == "mp4" || ext == "m4v") { return "maybe"; }
      // Allow Apple HTTP Streaming for iOS
      } else if (VideoJS.isIOS()) {
        if (ext == "m3u8") { return "maybe"; }
      }
    }
    return "";
  },
  // Force the video source - Helps fix loading bugs in a handful of devices, like the iPad/iPhone poster bug
  // And iPad/iPhone javascript include location bug. And Android type attribute bug
  forceTheSource: function(){
    this.video.src = this.firstPlayableSource.src; // From canPlaySource()
    this.video.load();
  },
  /* Device Fixes
  ================================================================================ */
  // Support older browsers that used "autobuffer"
  fixPreloading: function(){
    if (typeof this.video.hasAttribute == "function" && this.video.hasAttribute("preload") && this.video.preload != "none") {
      this.video.autobuffer = true; // Was a boolean
    } else {
      this.video.autobuffer = false;
      this.video.preload = "none";
    }
  },

  // Listen for Video Load Progress (currently does not if html file is local)
  // Buffered does't work in all browsers, so watching progress as well
  supportProgressEvents: function(e){
    _V_.addListener(this.video, 'progress', this.playerOnVideoProgress.context(this));
  },
  playerOnVideoProgress: function(event){
    this.setBufferedFromProgress(event);
  },
  setBufferedFromProgress: function(event){ // HTML5 Only
    if(event.total > 0) {
      var newBufferEnd = (event.loaded / event.total) * this.duration();
      if (newBufferEnd > this.values.bufferEnd) { this.values.bufferEnd = newBufferEnd; }
    }
  },

  iOSInterface: function(){
    if(VideoJS.iOSVersion() < 4) { this.forceTheSource(); } // Fix loading issues
    if(VideoJS.isIPad()) { // iPad could work with controlsBelow
      this.buildAndActivateSpinner(); // Spinner still works well on iPad, since iPad doesn't have one
    }
  },

  // Fix android specific quirks
  // Use built-in controls, but add the big play button, since android doesn't have one.
  androidInterface: function(){
    this.forceTheSource(); // Fix loading issues
    _V_.addListener(this.video, "click", function(){ this.play(); }); // Required to play
    this.buildBigPlayButton(); // But don't activate the normal way. Pause doesn't work right on android.
    _V_.addListener(this.bigPlayButton, "click", function(){ this.play(); }.context(this));
    this.positionBox();
    this.showBigPlayButtons();
  },
  /* Wait for styles (TODO: move to _V_)
  ================================================================================ */
  loadInterface: function(){
    if(!this.stylesHaveLoaded()) {
      // Don't want to create an endless loop either.
      if (!this.positionRetries) { this.positionRetries = 1; }
      if (this.positionRetries++ < 100) {
        setTimeout(this.loadInterface.context(this),10);
        return;
      }
    }
    this.hideStylesCheckDiv();
    this.showPoster();
    if (this.video.paused !== false) { this.showBigPlayButtons(); }
    if (this.options.controlsAtStart) { this.showControlBars(); }
    this.positionAll();
  },
  /* Control Bar
  ================================================================================ */
  buildAndActivateControlBar: function(){
    /* Creating this HTML
      <div class="vjs-controls">
        <div class="vjs-play-control">
          <span></span>
        </div>
        <div class="vjs-progress-control">
          <div class="vjs-progress-holder">
            <div class="vjs-load-progress"></div>
            <div class="vjs-play-progress"></div>
          </div>
        </div>
        <div class="vjs-time-control">
          <span class="vjs-current-time-display">00:00</span><span> / </span><span class="vjs-duration-display">00:00</span>
        </div>
        <div class="vjs-volume-control">
          <div>
            <span></span><span></span><span></span><span></span><span></span><span></span>
          </div>
        </div>
        <div class="vjs-fullscreen-control">
          <div>
            <span></span><span></span><span></span><span></span>
          </div>
        </div>
      </div>
    */

    // Create a div to hold the different controls
    this.controls = _V_.createElement("div", { className: "vjs-controls" });
    // Add the controls to the video's container
    this.box.appendChild(this.controls);
    this.activateElement(this.controls, "controlBar");
    this.activateElement(this.controls, "mouseOverVideoReporter");

    // Build the play control
    this.playControl = _V_.createElement("div", { className: "vjs-play-control", innerHTML: "<span></span>" });
    this.controls.appendChild(this.playControl);
    this.activateElement(this.playControl, "playToggle");

    // Build the progress control
    this.progressControl = _V_.createElement("div", { className: "vjs-progress-control" });
    this.controls.appendChild(this.progressControl);

    // Create a holder for the progress bars
    this.progressHolder = _V_.createElement("div", { className: "vjs-progress-holder" });
    this.progressControl.appendChild(this.progressHolder);
    this.activateElement(this.progressHolder, "currentTimeScrubber");

    // Create the loading progress display
    this.loadProgressBar = _V_.createElement("div", { className: "vjs-load-progress" });
    this.progressHolder.appendChild(this.loadProgressBar);
    this.activateElement(this.loadProgressBar, "loadProgressBar");

    // Create the playing progress display
    this.playProgressBar = _V_.createElement("div", { className: "vjs-play-progress" });
    this.progressHolder.appendChild(this.playProgressBar);
    this.activateElement(this.playProgressBar, "playProgressBar");

    // Create the progress time display (00:00 / 00:00)
    this.timeControl = _V_.createElement("div", { className: "vjs-time-control" });
    this.controls.appendChild(this.timeControl);

    // Create the current play time display
    this.currentTimeDisplay = _V_.createElement("span", { className: "vjs-current-time-display", innerHTML: "00:00" });
    this.timeControl.appendChild(this.currentTimeDisplay);
    this.activateElement(this.currentTimeDisplay, "currentTimeDisplay");

    // Add time separator
    this.timeSeparator = _V_.createElement("span", { innerHTML: " / " });
    this.timeControl.appendChild(this.timeSeparator);

    // Create the total duration display
    this.durationDisplay = _V_.createElement("span", { className: "vjs-duration-display", innerHTML: "00:00" });
    this.timeControl.appendChild(this.durationDisplay);
    this.activateElement(this.durationDisplay, "durationDisplay");

    // Create the volumne control
    this.volumeControl = _V_.createElement("div", {
      className: "vjs-volume-control",
      innerHTML: "<div><span></span><span></span><span></span><span></span><span></span><span></span></div>"
    });
    this.controls.appendChild(this.volumeControl);
    this.activateElement(this.volumeControl, "volumeScrubber");

    this.volumeDisplay = this.volumeControl.children[0];
    this.activateElement(this.volumeDisplay, "volumeDisplay");

    // Crete the fullscreen control
    this.fullscreenControl = _V_.createElement("div", {
      className: "vjs-fullscreen-control",
      innerHTML: "<div><span></span><span></span><span></span><span></span></div>"
    });
    this.controls.appendChild(this.fullscreenControl);
    this.activateElement(this.fullscreenControl, "fullscreenToggle");
  },
  /* Poster Image
  ================================================================================ */
  buildAndActivatePoster: function(){
    this.updatePosterSource();
    if (this.video.poster) {
      this.poster = document.createElement("img");
      // Add poster to video box
      this.box.appendChild(this.poster);

      // Add poster image data
      this.poster.src = this.video.poster;
      // Add poster styles
      this.poster.className = "vjs-poster";
      this.activateElement(this.poster, "poster");
    } else {
      this.poster = false;
    }
  },
  /* Big Play Button
  ================================================================================ */
  buildBigPlayButton: function(){
    /* Creating this HTML
      <div class="vjs-big-play-button"><span></span></div>
    */
    this.bigPlayButton = _V_.createElement("div", {
      className: "vjs-big-play-button",
      innerHTML: "<span></span>"
    });
    this.box.appendChild(this.bigPlayButton);
    this.activateElement(this.bigPlayButton, "bigPlayButton");
  },
  /* Spinner (Loading)
  ================================================================================ */
  buildAndActivateSpinner: function(){
    this.spinner = _V_.createElement("div", {
      className: "vjs-spinner",
      innerHTML: "<div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div>"
    });
    this.box.appendChild(this.spinner);
    this.activateElement(this.spinner, "spinner");
  },
  /* Styles Check - Check if styles are loaded (move ot _V_)
  ================================================================================ */
  // Sometimes the CSS styles haven't been applied to the controls yet
  // when we're trying to calculate the height and position them correctly.
  // This causes a flicker where the controls are out of place.
  buildStylesCheckDiv: function(){
    this.stylesCheckDiv = _V_.createElement("div", { className: "vjs-styles-check" });
    this.stylesCheckDiv.style.position = "absolute";
    this.box.appendChild(this.stylesCheckDiv);
  },
  hideStylesCheckDiv: function(){ this.stylesCheckDiv.style.display = "none"; },
  stylesHaveLoaded: function(){
    if (this.stylesCheckDiv.offsetHeight != 5) {
       return false;
    } else {
      return true;
    }
  },
  /* VideoJS Box - Holds all elements
  ================================================================================ */
  positionAll: function(){
    this.positionBox();
    this.positionControlBars();
    this.positionPoster();
  },
  positionBox: function(){
    // Set width based on fullscreen or not.
    if (this.videoIsFullScreen) {
      this.box.style.width = "";
      this.element.style.height="";
      if (this.options.controlsBelow) {
        this.box.style.height = "";
        this.element.style.height = (this.box.offsetHeight - this.controls.offsetHeight) + "px";
      }
    } else {
      this.box.style.width = this.width() + "px";
      this.element.style.height=this.height()+"px";
      if (this.options.controlsBelow) {
        this.element.style.height = "";
        // this.box.style.height = this.video.offsetHeight + this.controls.offsetHeight + "px";
      }
    }
  },
  /* Subtitles
  ================================================================================ */
  getSubtitles: function(){
    var tracks = this.video.getElementsByTagName("TRACK");
    for (var i=0,j=tracks.length; i<j; i++) {
      if (tracks[i].getAttribute("kind") == "subtitles" && tracks[i].getAttribute("src")) {
        this.subtitlesSource = tracks[i].getAttribute("src");
        this.loadSubtitles();
        this.buildSubtitles();
      }
    }
  },
  loadSubtitles: function() { _V_.get(this.subtitlesSource, this.parseSubtitles.context(this)); },
  parseSubtitles: function(subText) {
    var lines = subText.split("\n"),
        line = "",
        subtitle, time, text;
    this.subtitles = [];
    this.currentSubtitle = false;
    this.lastSubtitleIndex = 0;

    for (var i=0; i<lines.length; i++) {
      line = _V_.trim(lines[i]); // Trim whitespace and linebreaks
      if (line) { // Loop until a line with content

        // First line - Number
        subtitle = {
          id: line, // Subtitle Number
          index: this.subtitles.length // Position in Array
        };

        // Second line - Time
        line = _V_.trim(lines[++i]);
        time = line.split(" --> ");
        subtitle.start = this.parseSubtitleTime(time[0]);
        subtitle.end = this.parseSubtitleTime(time[1]);

        // Additional lines - Subtitle Text
        text = [];
        for (var j=i; j<lines.length; j++) { // Loop until a blank line or end of lines
          line = _V_.trim(lines[++i]);
          if (!line) { break; }
          text.push(line);
        }
        subtitle.text = text.join('<br/>');

        // Add this subtitle
        this.subtitles.push(subtitle);
      }
    }
  },

  parseSubtitleTime: function(timeText) {
    var parts = timeText.split(':'),
        time = 0;
    // hours => seconds
    time += parseFloat(parts[0])*60*60;
    // minutes => seconds
    time += parseFloat(parts[1])*60;
    // get seconds
    var seconds = parts[2].split(/\.|,/); // Either . or ,
    time += parseFloat(seconds[0]);
    // add miliseconds
    ms = parseFloat(seconds[1]);
    if (ms) { time += ms/1000; }
    return time;
  },

  buildSubtitles: function(){
    /* Creating this HTML
      <div class="vjs-subtitles"></div>
    */
    this.subtitlesDisplay = _V_.createElement("div", { className: 'vjs-subtitles' });
    this.box.appendChild(this.subtitlesDisplay);
    this.activateElement(this.subtitlesDisplay, "subtitlesDisplay");
  },

  /* Player API - Translate functionality from player to video
  ================================================================================ */
  addVideoListener: function(type, fn){ _V_.addListener(this.video, type, fn.rEvtContext(this)); },

  play: function(){
    this.video.play();
    return this;
  },
  onPlay: function(fn){ this.addVideoListener("play", fn); return this; },

  pause: function(){
    this.video.pause();
    return this;
  },
  onPause: function(fn){ this.addVideoListener("pause", fn); return this; },
  paused: function() { return this.video.paused; },

  currentTime: function(seconds){
    if (seconds !== undefined) {
      try { this.video.currentTime = seconds; }
      catch(e) { this.warning(VideoJS.warnings.videoNotReady); }
      this.values.currentTime = seconds;
      return this;
    }
    return this.video.currentTime;
  },
  onCurrentTimeUpdate: function(fn){
    this.currentTimeListeners.push(fn);
  },

  duration: function(){
    return this.video.duration;
  },

  buffered: function(){
    // Storing values allows them be overridden by setBufferedFromProgress
    if (this.values.bufferStart === undefined) {
      this.values.bufferStart = 0;
      this.values.bufferEnd = 0;
    }
    if (this.video.buffered && this.video.buffered.length > 0) {
      var newEnd = this.video.buffered.end(0);
      if (newEnd > this.values.bufferEnd) { this.values.bufferEnd = newEnd; }
    }
    return [this.values.bufferStart, this.values.bufferEnd];
  },

  volume: function(percentAsDecimal){
    if (percentAsDecimal !== undefined) {
      // Force value to between 0 and 1
      this.values.volume = Math.max(0, Math.min(1, parseFloat(percentAsDecimal)));
      this.video.volume = this.values.volume;
      this.setLocalStorage("volume", this.values.volume);
      return this;
    }
    if (this.values.volume) { return this.values.volume; }
    return this.video.volume;
  },
  onVolumeChange: function(fn){ _V_.addListener(this.video, 'volumechange', fn.rEvtContext(this)); },

  width: function(width){
    if (width !== undefined) {
      this.video.width = width; // Not using style so it can be overridden on fullscreen.
      this.box.style.width = width+"px";
      this.triggerResizeListeners();
      return this;
    }
    return this.video.offsetWidth;
  },
  height: function(height){
    if (height !== undefined) {
      this.video.height = height;
      this.box.style.height = height+"px";
      this.triggerResizeListeners();
      return this;
    }
    return this.video.offsetHeight;
  },

  supportsFullScreen: function(){
    if(typeof this.video.webkitEnterFullScreen == 'function') {
      // Seems to be broken in Chromium/Chrome
      if (!navigator.userAgent.match("Chrome") && !navigator.userAgent.match("Mac OS X 10.5")) {
        return true;
      }
    }
    return false;
  },

  html5EnterNativeFullScreen: function(){
    try {
      this.video.webkitEnterFullScreen();
    } catch (e) {
      if (e.code == 11) { this.warning(VideoJS.warnings.videoNotReady); }
    }
    return this;
  },

  // Turn on fullscreen (window) mode
  // Real fullscreen isn't available in browsers quite yet.
  enterFullScreen: function(){
    if (this.supportsFullScreen()) {
      this.html5EnterNativeFullScreen();
    } else {
      this.enterFullWindow();
    }
  },

  exitFullScreen: function(){
    if (this.supportsFullScreen()) {
      // Shouldn't be called
    } else {
      this.exitFullWindow();
    }
  },

  enterFullWindow: function(){
    this.videoIsFullScreen = true;
    // Storing original doc overflow value to return to when fullscreen is off
    this.docOrigOverflow = document.documentElement.style.overflow;
    // Add listener for esc key to exit fullscreen
    _V_.addListener(document, "keydown", this.fullscreenOnEscKey.rEvtContext(this));
    // Add listener for a window resize
    _V_.addListener(window, "resize", this.fullscreenOnWindowResize.rEvtContext(this));
    // Hide any scroll bars
    document.documentElement.style.overflow = 'hidden';
    // Apply fullscreen styles
    _V_.addClass(this.box, "vjs-fullscreen");
    // Resize the box, controller, and poster
    this.positionAll();
  },

  // Turn off fullscreen (window) mode
  exitFullWindow: function(){
    this.videoIsFullScreen = false;
    document.removeEventListener("keydown", this.fullscreenOnEscKey, false);
    window.removeEventListener("resize", this.fullscreenOnWindowResize, false);
    // Unhide scroll bars.
    document.documentElement.style.overflow = this.docOrigOverflow;
    // Remove fullscreen styles
    _V_.removeClass(this.box, "vjs-fullscreen");
    // Resize the box, controller, and poster to original sizes
    this.positionAll();
  },

  onError: function(fn){ this.addVideoListener("error", fn); return this; },
  onEnded: function(fn){
    this.addVideoListener("ended", fn); return this;
  }
});

////////////////////////////////////////////////////////////////////////////////
// Element Behaviors
// Tell elements how to act or react
////////////////////////////////////////////////////////////////////////////////

/* Player Behaviors - How VideoJS reacts to what the video is doing.
================================================================================ */
VideoJS.player.newBehavior("player", function(player){
    this.onError(this.playerOnVideoError);
    // Listen for when the video is played
    this.onPlay(this.playerOnVideoPlay);
    this.onPlay(this.trackCurrentTime);
    // Listen for when the video is paused
    this.onPause(this.playerOnVideoPause);
    this.onPause(this.stopTrackingCurrentTime);
    // Listen for when the video ends
    this.onEnded(this.playerOnVideoEnded);
    // Set interval for load progress using buffer watching method
    // this.trackCurrentTime();
    this.trackBuffered();
    // Buffer Full
    this.onBufferedUpdate(this.isBufferFull);
  },{
    playerOnVideoError: function(event){
      this.log(event);
      this.log(this.video.error);
    },
    playerOnVideoPlay: function(event){ this.hasPlayed = true; },
    playerOnVideoPause: function(event){},
    playerOnVideoEnded: function(event){
      this.currentTime(0);
      this.pause();
    },

    /* Load Tracking -------------------------------------------------------------- */
    // Buffer watching method for load progress.
    // Used for browsers that don't support the progress event
    trackBuffered: function(){
      this.bufferedInterval = setInterval(this.triggerBufferedListeners.context(this), 500);
    },
    stopTrackingBuffered: function(){ clearInterval(this.bufferedInterval); },
    bufferedListeners: [],
    onBufferedUpdate: function(fn){
      this.bufferedListeners.push(fn);
    },
    triggerBufferedListeners: function(){
      this.isBufferFull();
      this.each(this.bufferedListeners, function(listener){
        (listener.context(this))();
      });
    },
    isBufferFull: function(){
      if (this.bufferedPercent() == 1) { this.stopTrackingBuffered(); }
    },

    /* Time Tracking -------------------------------------------------------------- */
    trackCurrentTime: function(){
      if (this.currentTimeInterval) { clearInterval(this.currentTimeInterval); }
      this.currentTimeInterval = setInterval(this.triggerCurrentTimeListeners.context(this), 100); // 42 = 24 fps
      this.trackingCurrentTime = true;
    },
    // Turn off play progress tracking (when paused or dragging)
    stopTrackingCurrentTime: function(){
      clearInterval(this.currentTimeInterval);
      this.trackingCurrentTime = false;
    },
    currentTimeListeners: [],
    // onCurrentTimeUpdate is in API section now
    triggerCurrentTimeListeners: function(late, newTime){ // FF passes milliseconds late as the first argument
      this.each(this.currentTimeListeners, function(listener){
        (listener.context(this))(newTime || this.currentTime());
      });
    },

    /* Resize Tracking -------------------------------------------------------------- */
    resizeListeners: [],
    onResize: function(fn){
      this.resizeListeners.push(fn);
    },
    // Trigger anywhere the video/box size is changed.
    triggerResizeListeners: function(){
      this.each(this.resizeListeners, function(listener){
        (listener.context(this))();
      });
    }
  }
);
/* Mouse Over Video Reporter Behaviors - i.e. Controls hiding based on mouse location
================================================================================ */
VideoJS.player.newBehavior("mouseOverVideoReporter", function(element){
    // Listen for the mouse move the video. Used to reveal the controller.
    _V_.addListener(element, "mousemove", this.mouseOverVideoReporterOnMouseMove.context(this));
    // Listen for the mouse moving out of the video. Used to hide the controller.
    _V_.addListener(element, "mouseout", this.mouseOverVideoReporterOnMouseOut.context(this));
  },{
    mouseOverVideoReporterOnMouseMove: function(){
      this.showControlBars();
      clearInterval(this.mouseMoveTimeout);
      this.mouseMoveTimeout = setTimeout(this.hideControlBars.context(this), 4000);
    },
    mouseOverVideoReporterOnMouseOut: function(event){
      // Prevent flicker by making sure mouse hasn't left the video
      var parent = event.relatedTarget;
      while (parent && parent !== this.box) {
        parent = parent.parentNode;
      }
      if (parent !== this.box) {
        this.hideControlBars();
      }
    }
  }
);
/* Mouse Over Video Reporter Behaviors - i.e. Controls hiding based on mouse location
================================================================================ */
VideoJS.player.newBehavior("box", function(element){
    this.positionBox();
    _V_.addClass(element, "vjs-paused");
    this.activateElement(element, "mouseOverVideoReporter");
    this.onPlay(this.boxOnVideoPlay);
    this.onPause(this.boxOnVideoPause);
  },{
    boxOnVideoPlay: function(){
      _V_.removeClass(this.box, "vjs-paused");
      _V_.addClass(this.box, "vjs-playing");
    },
    boxOnVideoPause: function(){
      _V_.removeClass(this.box, "vjs-playing");
      _V_.addClass(this.box, "vjs-paused");
    }
  }
);
/* Poster Image Overlay
================================================================================ */
VideoJS.player.newBehavior("poster", function(element){
    this.activateElement(element, "mouseOverVideoReporter");
    this.activateElement(element, "playButton");
    this.onPlay(this.hidePoster);
    this.onEnded(this.showPoster);
    this.onResize(this.positionPoster);
  },{
    showPoster: function(){
      if (!this.poster) { return; }
      this.poster.style.display = "block";
      this.positionPoster();
    },
    positionPoster: function(){
      // Only if the poster is visible
      if (!this.poster || this.poster.style.display == 'none') { return; }
      this.poster.style.height = this.height() + "px"; // Need incase controlsBelow
      this.poster.style.width = this.width() + "px"; // Could probably do 100% of box
    },
    hidePoster: function(){
      if (!this.poster) { return; }
      this.poster.style.display = "none";
    },
    // Update poster source from attribute or fallback image
    // iPad breaks if you include a poster attribute, so this fixes that
    updatePosterSource: function(){
      if (!this.video.poster) {
        var images = this.video.getElementsByTagName("img");
        if (images.length > 0) { this.video.poster = images[0].src; }
      }
    }
  }
);
/* Control Bar Behaviors
================================================================================ */
VideoJS.player.newBehavior("controlBar", function(element){
    if (!this.controlBars) {
      this.controlBars = [];
      this.onResize(this.positionControlBars);
    }
    this.controlBars.push(element);
    _V_.addListener(element, "mousemove", this.onControlBarsMouseMove.context(this));
    _V_.addListener(element, "mouseout", this.onControlBarsMouseOut.context(this));
  },{
    showControlBars: function(){
      if (!this.options.controlsAtStart && !this.hasPlayed) { return; }
      this.each(this.controlBars, function(bar){
        bar.style.display = "block";
      });
    },
    // Place controller relative to the video's position (now just resizing bars)
    positionControlBars: function(){
      this.updatePlayProgressBars();
      this.updateLoadProgressBars();
    },
    hideControlBars: function(){
      if (this.options.controlsHiding && !this.mouseIsOverControls) {
        this.each(this.controlBars, function(bar){
          bar.style.display = "none";
        });
      }
    },
    // Block controls from hiding when mouse is over them.
    onControlBarsMouseMove: function(){ this.mouseIsOverControls = true; },
    onControlBarsMouseOut: function(event){
      this.mouseIsOverControls = false;
    }
  }
);
/* PlayToggle, PlayButton, PauseButton Behaviors
================================================================================ */
// Play Toggle
VideoJS.player.newBehavior("playToggle", function(element){
    if (!this.elements.playToggles) {
      this.elements.playToggles = [];
      this.onPlay(this.playTogglesOnPlay);
      this.onPause(this.playTogglesOnPause);
    }
    this.elements.playToggles.push(element);
    _V_.addListener(element, "click", this.onPlayToggleClick.context(this));
  },{
    onPlayToggleClick: function(event){
      if (this.paused()) {
        this.play();
      } else {
        this.pause();
      }
    },
    playTogglesOnPlay: function(event){
      this.each(this.elements.playToggles, function(toggle){
        _V_.removeClass(toggle, "vjs-paused");
        _V_.addClass(toggle, "vjs-playing");
      });
    },
    playTogglesOnPause: function(event){
      this.each(this.elements.playToggles, function(toggle){
        _V_.removeClass(toggle, "vjs-playing");
        _V_.addClass(toggle, "vjs-paused");
      });
    }
  }
);
// Play
VideoJS.player.newBehavior("playButton", function(element){
    _V_.addListener(element, "click", this.onPlayButtonClick.context(this));
  },{
    onPlayButtonClick: function(event){ this.play(); }
  }
);
// Pause
VideoJS.player.newBehavior("pauseButton", function(element){
    _V_.addListener(element, "click", this.onPauseButtonClick.context(this));
  },{
    onPauseButtonClick: function(event){ this.pause(); }
  }
);
/* Play Progress Bar Behaviors
================================================================================ */
VideoJS.player.newBehavior("playProgressBar", function(element){
    if (!this.playProgressBars) {
      this.playProgressBars = [];
      this.onCurrentTimeUpdate(this.updatePlayProgressBars);
    }
    this.playProgressBars.push(element);
  },{
    // Ajust the play progress bar's width based on the current play time
    updatePlayProgressBars: function(newTime){
      var progress = (newTime !== undefined) ? newTime / this.duration() : this.currentTime() / this.duration();
      if (isNaN(progress)) { progress = 0; }
      this.each(this.playProgressBars, function(bar){
        if (bar.style) { bar.style.width = _V_.round(progress * 100, 2) + "%"; }
      });
    }
  }
);
/* Load Progress Bar Behaviors
================================================================================ */
VideoJS.player.newBehavior("loadProgressBar", function(element){
    if (!this.loadProgressBars) { this.loadProgressBars = []; }
    this.loadProgressBars.push(element);
    this.onBufferedUpdate(this.updateLoadProgressBars);
  },{
    updateLoadProgressBars: function(){
      this.each(this.loadProgressBars, function(bar){
        if (bar.style) { bar.style.width = _V_.round(this.bufferedPercent() * 100, 2) + "%"; }
      });
    }
  }
);

/* Current Time Display Behaviors
================================================================================ */
VideoJS.player.newBehavior("currentTimeDisplay", function(element){
    if (!this.currentTimeDisplays) {
      this.currentTimeDisplays = [];
      this.onCurrentTimeUpdate(this.updateCurrentTimeDisplays);
    }
    this.currentTimeDisplays.push(element);
  },{
    // Update the displayed time (00:00)
    updateCurrentTimeDisplays: function(newTime){
      if (!this.currentTimeDisplays) { return; }
      // Allows for smooth scrubbing, when player can't keep up.
      var time = (newTime) ? newTime : this.currentTime();
      this.each(this.currentTimeDisplays, function(dis){
        dis.innerHTML = _V_.formatTime(time);
      });
    }
  }
);

/* Duration Display Behaviors
================================================================================ */
VideoJS.player.newBehavior("durationDisplay", function(element){
    if (!this.durationDisplays) {
      this.durationDisplays = [];
      this.onCurrentTimeUpdate(this.updateDurationDisplays);
    }
    this.durationDisplays.push(element);
  },{
    updateDurationDisplays: function(){
      if (!this.durationDisplays) { return; }
      this.each(this.durationDisplays, function(dis){
        if (this.duration()) { dis.innerHTML = _V_.formatTime(this.duration()); }
      });
    }
  }
);

/* Current Time Scrubber Behaviors
================================================================================ */
VideoJS.player.newBehavior("currentTimeScrubber", function(element){
    _V_.addListener(element, "mousedown", this.onCurrentTimeScrubberMouseDown.rEvtContext(this));
  },{
    // Adjust the play position when the user drags on the progress bar
    onCurrentTimeScrubberMouseDown: function(event, scrubber){
      event.preventDefault();
      this.currentScrubber = scrubber;

      this.stopTrackingCurrentTime(); // Allows for smooth scrubbing

      this.videoWasPlaying = !this.paused();
      this.pause();

      _V_.blockTextSelection();
      this.setCurrentTimeWithScrubber(event);
      _V_.addListener(document, "mousemove", this.onCurrentTimeScrubberMouseMove.rEvtContext(this));
      _V_.addListener(document, "mouseup", this.onCurrentTimeScrubberMouseUp.rEvtContext(this));
    },
    onCurrentTimeScrubberMouseMove: function(event){ // Removeable
      this.setCurrentTimeWithScrubber(event);
    },
    onCurrentTimeScrubberMouseUp: function(event){ // Removeable
      _V_.unblockTextSelection();
      document.removeEventListener("mousemove", this.onCurrentTimeScrubberMouseMove, false);
      document.removeEventListener("mouseup", this.onCurrentTimeScrubberMouseUp, false);
      if (this.videoWasPlaying) {
        this.play();
        this.trackCurrentTime();
      }
    },
    setCurrentTimeWithScrubber: function(event){
      var newProgress = _V_.getRelativePosition(event.pageX, this.currentScrubber);
      var newTime = newProgress * this.duration();
      this.triggerCurrentTimeListeners(0, newTime); // Allows for smooth scrubbing
      // Don't let video end while scrubbing.
      if (newTime == this.duration()) { newTime = newTime - 0.1; }
      this.currentTime(newTime);
    }
  }
);
/* Volume Display Behaviors
================================================================================ */
VideoJS.player.newBehavior("volumeDisplay", function(element){
    if (!this.volumeDisplays) {
      this.volumeDisplays = [];
      this.onVolumeChange(this.updateVolumeDisplays);
    }
    this.volumeDisplays.push(element);
    this.updateVolumeDisplay(element); // Set the display to the initial volume
  },{
    // Update the volume control display
    // Unique to these default controls. Uses borders to create the look of bars.
    updateVolumeDisplays: function(){
      if (!this.volumeDisplays) { return; }
      this.each(this.volumeDisplays, function(dis){
        this.updateVolumeDisplay(dis);
      });
    },
    updateVolumeDisplay: function(display){
      var volNum = Math.ceil(this.volume() * 6);
      this.each(display.children, function(child, num){
        if (num < volNum) {
          _V_.addClass(child, "vjs-volume-level-on");
        } else {
          _V_.removeClass(child, "vjs-volume-level-on");
        }
      });
    }
  }
);
/* Volume Scrubber Behaviors
================================================================================ */
VideoJS.player.newBehavior("volumeScrubber", function(element){
    _V_.addListener(element, "mousedown", this.onVolumeScrubberMouseDown.rEvtContext(this));
  },{
    // Adjust the volume when the user drags on the volume control
    onVolumeScrubberMouseDown: function(event, scrubber){
      // event.preventDefault();
      _V_.blockTextSelection();
      this.currentScrubber = scrubber;
      this.setVolumeWithScrubber(event);
      _V_.addListener(document, "mousemove", this.onVolumeScrubberMouseMove.rEvtContext(this));
      _V_.addListener(document, "mouseup", this.onVolumeScrubberMouseUp.rEvtContext(this));
    },
    onVolumeScrubberMouseMove: function(event){
      this.setVolumeWithScrubber(event);
    },
    onVolumeScrubberMouseUp: function(event){
      this.setVolumeWithScrubber(event);
      _V_.unblockTextSelection();
      document.removeEventListener("mousemove", this.onVolumeScrubberMouseMove, false);
      document.removeEventListener("mouseup", this.onVolumeScrubberMouseUp, false);
    },
    setVolumeWithScrubber: function(event){
      var newVol = _V_.getRelativePosition(event.pageX, this.currentScrubber);
      this.volume(newVol);
    }
  }
);
/* Fullscreen Toggle Behaviors
================================================================================ */
VideoJS.player.newBehavior("fullscreenToggle", function(element){
    _V_.addListener(element, "click", this.onFullscreenToggleClick.context(this));
  },{
    // When the user clicks on the fullscreen button, update fullscreen setting
    onFullscreenToggleClick: function(event){
      if (!this.videoIsFullScreen) {
        this.enterFullScreen();
      } else {
        this.exitFullScreen();
      }
    },

    fullscreenOnWindowResize: function(event){ // Removeable
      this.positionControlBars();
    },
    // Create listener for esc key while in full screen mode
    fullscreenOnEscKey: function(event){ // Removeable
      if (event.keyCode == 27) {
        this.exitFullScreen();
      }
    }
  }
);
/* Big Play Button Behaviors
================================================================================ */
VideoJS.player.newBehavior("bigPlayButton", function(element){
    if (!this.elements.bigPlayButtons) {
      this.elements.bigPlayButtons = [];
      this.onPlay(this.bigPlayButtonsOnPlay);
      this.onEnded(this.bigPlayButtonsOnEnded);
    }
    this.elements.bigPlayButtons.push(element);
    this.activateElement(element, "playButton");
  },{
    bigPlayButtonsOnPlay: function(event){ this.hideBigPlayButtons(); },
    bigPlayButtonsOnEnded: function(event){ this.showBigPlayButtons(); },
    showBigPlayButtons: function(){
      this.each(this.elements.bigPlayButtons, function(element){
        element.style.display = "block";
      });
    },
    hideBigPlayButtons: function(){
      this.each(this.elements.bigPlayButtons, function(element){
        element.style.display = "none";
      });
    }
  }
);
/* Spinner
================================================================================ */
VideoJS.player.newBehavior("spinner", function(element){
    if (!this.spinners) {
      this.spinners = [];
      _V_.addListener(this.video, "loadeddata", this.spinnersOnVideoLoadedData.context(this));
      _V_.addListener(this.video, "loadstart", this.spinnersOnVideoLoadStart.context(this));
      _V_.addListener(this.video, "seeking", this.spinnersOnVideoSeeking.context(this));
      _V_.addListener(this.video, "seeked", this.spinnersOnVideoSeeked.context(this));
      _V_.addListener(this.video, "canplay", this.spinnersOnVideoCanPlay.context(this));
      _V_.addListener(this.video, "canplaythrough", this.spinnersOnVideoCanPlayThrough.context(this));
      _V_.addListener(this.video, "waiting", this.spinnersOnVideoWaiting.context(this));
      _V_.addListener(this.video, "stalled", this.spinnersOnVideoStalled.context(this));
      _V_.addListener(this.video, "suspend", this.spinnersOnVideoSuspend.context(this));
      _V_.addListener(this.video, "playing", this.spinnersOnVideoPlaying.context(this));
      _V_.addListener(this.video, "timeupdate", this.spinnersOnVideoTimeUpdate.context(this));
    }
    this.spinners.push(element);
  },{
    showSpinners: function(){
      this.each(this.spinners, function(spinner){
        spinner.style.display = "block";
      });
      clearInterval(this.spinnerInterval);
      this.spinnerInterval = setInterval(this.rotateSpinners.context(this), 100);
    },
    hideSpinners: function(){
      this.each(this.spinners, function(spinner){
        spinner.style.display = "none";
      });
      clearInterval(this.spinnerInterval);
    },
    spinnersRotated: 0,
    rotateSpinners: function(){
      this.each(this.spinners, function(spinner){
        // spinner.style.transform =       'scale(0.5) rotate('+this.spinnersRotated+'deg)';
        spinner.style.WebkitTransform = 'scale(0.5) rotate('+this.spinnersRotated+'deg)';
        spinner.style.MozTransform =    'scale(0.5) rotate('+this.spinnersRotated+'deg)';
      });
      if (this.spinnersRotated == 360) { this.spinnersRotated = 0; }
      this.spinnersRotated += 45;
    },
    spinnersOnVideoLoadedData: function(event){ this.hideSpinners(); },
    spinnersOnVideoLoadStart: function(event){ this.showSpinners(); },
    spinnersOnVideoSeeking: function(event){ /* this.showSpinners(); */ },
    spinnersOnVideoSeeked: function(event){ /* this.hideSpinners(); */ },
    spinnersOnVideoCanPlay: function(event){ /* this.hideSpinners(); */ },
    spinnersOnVideoCanPlayThrough: function(event){ this.hideSpinners(); },
    spinnersOnVideoWaiting: function(event){
      // Safari sometimes triggers waiting inappropriately
      // Like after video has played, any you play again.
      this.showSpinners();
    },
    spinnersOnVideoStalled: function(event){},
    spinnersOnVideoSuspend: function(event){},
    spinnersOnVideoPlaying: function(event){ this.hideSpinners(); },
    spinnersOnVideoTimeUpdate: function(event){
      // Safari sometimes calls waiting and doesn't recover
      if(this.spinner.style.display == "block") { this.hideSpinners(); }
    }
  }
);
/* Subtitles
================================================================================ */
VideoJS.player.newBehavior("subtitlesDisplay", function(element){
    if (!this.subtitleDisplays) {
      this.subtitleDisplays = [];
      this.onCurrentTimeUpdate(this.subtitleDisplaysOnVideoTimeUpdate);
      this.onEnded(function() { this.lastSubtitleIndex = 0; }.context(this));
    }
    this.subtitleDisplays.push(element);
  },{
    subtitleDisplaysOnVideoTimeUpdate: function(time){
      // Assuming all subtitles are in order by time, and do not overlap
      if (this.subtitles) {
        // If current subtitle should stay showing, don't do anything. Otherwise, find new subtitle.
        if (!this.currentSubtitle || this.currentSubtitle.start >= time || this.currentSubtitle.end < time) {
          var newSubIndex = false,
              // Loop in reverse if lastSubtitle is after current time (optimization)
              // Meaning the user is scrubbing in reverse or rewinding
              reverse = (this.subtitles[this.lastSubtitleIndex].start > time),
              // If reverse, step back 1 becase we know it's not the lastSubtitle
              i = this.lastSubtitleIndex - (reverse) ? 1 : 0;
          while (true) { // Loop until broken
            if (reverse) { // Looping in reverse
              // Stop if no more, or this subtitle ends before the current time (no earlier subtitles should apply)
              if (i < 0 || this.subtitles[i].end < time) { break; }
              // End is greater than time, so if start is less, show this subtitle
              if (this.subtitles[i].start < time) {
                newSubIndex = i;
                break;
              }
              i--;
            } else { // Looping forward
              // Stop if no more, or this subtitle starts after time (no later subtitles should apply)
              if (i >= this.subtitles.length || this.subtitles[i].start > time) { break; }
              // Start is less than time, so if end is later, show this subtitle
              if (this.subtitles[i].end > time) {
                newSubIndex = i;
                break;
              }
              i++;
            }
          }

          // Set or clear current subtitle
          if (newSubIndex !== false) {
            this.currentSubtitle = this.subtitles[newSubIndex];
            this.lastSubtitleIndex = newSubIndex;
            this.updateSubtitleDisplays(this.currentSubtitle.text);
          } else if (this.currentSubtitle) {
            this.currentSubtitle = false;
            this.updateSubtitleDisplays("");
          }
        }
      }
    },
    updateSubtitleDisplays: function(val){
      this.each(this.subtitleDisplays, function(disp){
        disp.innerHTML = val;
      });
    }
  }
);

////////////////////////////////////////////////////////////////////////////////
// Convenience Functions (mini library)
// Functions not specific to video or VideoJS and could probably be replaced with a library like jQuery
////////////////////////////////////////////////////////////////////////////////

VideoJS.extend({

  addClass: function(element, classToAdd){
    if ((" "+element.className+" ").indexOf(" "+classToAdd+" ") == -1) {
      element.className = element.className === "" ? classToAdd : element.className + " " + classToAdd;
    }
  },
  removeClass: function(element, classToRemove){
    if (element.className.indexOf(classToRemove) == -1) { return; }
    var classNames = element.className.split(/\s+/);
    classNames.splice(classNames.lastIndexOf(classToRemove),1);
    element.className = classNames.join(" ");
  },
  createElement: function(tagName, attributes){
    return this.merge(document.createElement(tagName), attributes);
  },

  // Attempt to block the ability to select text while dragging controls
  blockTextSelection: function(){
    document.body.focus();
    document.onselectstart = function () { return false; };
  },
  // Turn off text selection blocking
  unblockTextSelection: function(){ document.onselectstart = function () { return true; }; },

  // Return seconds as MM:SS
  formatTime: function(secs) {
    var seconds = Math.round(secs);
    var minutes = Math.floor(seconds / 60);
    minutes = (minutes >= 10) ? minutes : "0" + minutes;
    seconds = Math.floor(seconds % 60);
    seconds = (seconds >= 10) ? seconds : "0" + seconds;
    return minutes + ":" + seconds;
  },

  // Return the relative horizonal position of an event as a value from 0-1
  getRelativePosition: function(x, relativeElement){
    return Math.max(0, Math.min(1, (x - this.findPosX(relativeElement)) / relativeElement.offsetWidth));
  },
  // Get an objects position on the page
  findPosX: function(obj) {
    var curleft = obj.offsetLeft;
    while(obj = obj.offsetParent) {
      curleft += obj.offsetLeft;
    }
    return curleft;
  },
  getComputedStyleValue: function(element, style){
    return window.getComputedStyle(element, null).getPropertyValue(style);
  },

  round: function(num, dec) {
    if (!dec) { dec = 0; }
    return Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
  },

  addListener: function(element, type, handler){
    if (element.addEventListener) {
      element.addEventListener(type, handler, false);
    } else if (element.attachEvent) {
      element.attachEvent("on"+type, handler);
    }
  },
  removeListener: function(element, type, handler){
    if (element.removeEventListener) {
      element.removeEventListener(type, handler, false);
    } else if (element.attachEvent) {
      element.detachEvent("on"+type, handler);
    }
  },

  get: function(url, onSuccess){
    if (typeof XMLHttpRequest == "undefined") {
      XMLHttpRequest = function () {
        try { return new ActiveXObject("Msxml2.XMLHTTP.6.0"); } catch (e) {}
        try { return new ActiveXObject("Msxml2.XMLHTTP.3.0"); } catch (f) {}
        try { return new ActiveXObject("Msxml2.XMLHTTP"); } catch (g) {}
        //Microsoft.XMLHTTP points to Msxml2.XMLHTTP.3.0 and is redundant
        throw new Error("This browser does not support XMLHttpRequest.");
      };
    }
    var request = new XMLHttpRequest();
    request.open("GET",url);
    request.onreadystatechange = function() {
      if (request.readyState == 4 && request.status == 200) {
        onSuccess(request.responseText);
      }
    }.context(this);
    request.send();
  },

  trim: function(string){ return string.toString().replace(/^\s+/, "").replace(/\s+$/, ""); },

  // DOM Ready functionality adapted from jQuery. http://jquery.com/
  bindDOMReady: function(){
    if (document.readyState === "complete") {
      return VideoJS.onDOMReady();
    }
    if (document.addEventListener) {
      document.addEventListener("DOMContentLoaded", VideoJS.DOMContentLoaded, false);
      window.addEventListener("load", VideoJS.onDOMReady, false);
    } else if (document.attachEvent) {
      document.attachEvent("onreadystatechange", VideoJS.DOMContentLoaded);
      window.attachEvent("onload", VideoJS.onDOMReady);
    }
  },

  DOMContentLoaded: function(){
    if (document.addEventListener) {
      document.removeEventListener( "DOMContentLoaded", VideoJS.DOMContentLoaded, false);
      VideoJS.onDOMReady();
    } else if ( document.attachEvent ) {
      if ( document.readyState === "complete" ) {
        document.detachEvent("onreadystatechange", VideoJS.DOMContentLoaded);
        VideoJS.onDOMReady();
      }
    }
  },

  // Functions to be run once the DOM is loaded
  DOMReadyList: [],
  addToDOMReady: function(fn){
    if (VideoJS.DOMIsReady) {
      fn.call(document);
    } else {
      VideoJS.DOMReadyList.push(fn);
    }
  },

  DOMIsReady: false,
  onDOMReady: function(){
    if (VideoJS.DOMIsReady) { return; }
    if (!document.body) { return setTimeout(VideoJS.onDOMReady, 13); }
    VideoJS.DOMIsReady = true;
    if (VideoJS.DOMReadyList) {
      for (var i=0; i<VideoJS.DOMReadyList.length; i++) {
        VideoJS.DOMReadyList[i].call(document);
      }
      VideoJS.DOMReadyList = null;
    }
  }
});
VideoJS.bindDOMReady();

// Allows for binding context to functions
// when using in event listeners and timeouts
Function.prototype.context = function(obj){
  var method = this,
  temp = function(){
    return method.apply(obj, arguments);
  };
  return temp;
};

// Like context, in that it creates a closure
// But insteaad keep "this" intact, and passes the var as the second argument of the function
// Need for event listeners where you need to know what called the event
// Only use with event callbacks
Function.prototype.evtContext = function(obj){
  var method = this,
  temp = function(){
    var origContext = this;
    return method.call(obj, arguments[0], origContext);
  };
  return temp;
};

// Removeable Event listener with Context
// Replaces the original function with a version that has context
// So it can be removed using the original function name.
// In order to work, a version of the function must already exist in the player/prototype
Function.prototype.rEvtContext = function(obj, funcParent){
  if (this.hasContext === true) { return this; }
  if (!funcParent) { funcParent = obj; }
  for (var attrname in funcParent) {
    if (funcParent[attrname] == this) {
      funcParent[attrname] = this.evtContext(obj);
      funcParent[attrname].hasContext = true;
      return funcParent[attrname];
    }
  }
  return this.evtContext(obj);
};

// jQuery Plugin
if (window.jQuery) {
  (function($) {
    $.fn.VideoJS = function(options) {
      this.each(function() {
        VideoJS.setup(this, options);
      });
      return this;
    };
    $.fn.player = function() {
      return this[0].player;
    };
  })(jQuery);
}


// Expose to global
window.VideoJS = window._V_ = VideoJS;

// End self-executing function
})(window);
// ----------------------------------------------------------------------------
// Vegas - jQuery plugin 
// Add awesome fullscreen backgrounds to your webpages.
// v 1.1 beta
// Dual licensed under the MIT and GPL licenses.
// http://vegas.jaysalvat.com/
// ----------------------------------------------------------------------------
// Copyright (C) 2011 Jay Salvat
// http://jaysalvat.com/
// ----------------------------------------------------------------------------
// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software and associated documentation files ( the "Software" ), to deal
// in the Software without restriction, including without limitation the rights
// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:
// 
// The above copyright notice and this permission notice shall be included in
// all copies or substantial portions of the Software.
// 
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
// THE SOFTWARE.
// ----------------------------------------------------------------------------
( function( $ ){
    var $background = $( '<img />' ).addClass( 'vegas-background' ),
        $overlay    = $( '<div />' ).addClass( 'vegas-overlay' ),
        $loading    = $( '<div />' ).addClass( 'vegas-loading' ),
        $current    = $(),
        paused = null,
        backgrounds = [],
        step = 0,
		delay = 5000,
        timer,
        methods = {

        // Init plugin
        init : function( settings ) {

            var options = {
                src: getBackground(),
                align: 'center',
                valign: 'center',
                fade: 0,
                loading: true,
                load: function() {},
                complete: function() {}
            }
            $.extend( options, $.vegas.defaults.background, settings );

            if ( options.loading ) {
                loading();
            }

            var $new = $background.clone();
            $new.css( {
                'position': 'fixed',
                'left': '0px',
                'top': '0px'
            })
            .imagesLoadedForVegas( function() {
                if ( $new == $current ) {
                    return;
                }
                
                $( window ).bind( 'resize.vegas', function( e ) {
                    resize( $new, options );
                });

                if ( $current.is( 'img' ) ) {

                    $current.stop();

                    $new.hide()
                        .insertAfter( $current )
                        .fadeIn( options.fade, function() {
                            $('.vegas-background')
                                .not(this)
                                    .remove();
                            $( 'body' ).trigger( 'vegascomplete', [ this, step - 1 ] );
                            options.complete.apply( $new, [ step - 1 ] );
                        });
                } else {
                    $new.hide()
                        .prependTo( 'body' )
                        .fadeIn( options.fade, function() {
                            $( 'body' ).trigger( 'vegascomplete', [ this, step - 1 ] );
                            options.complete.apply( this, [ step - 1 ] );    
                        });
                }

                $current = $new;

                resize( $current, options );

                if ( options.loading ) {
                    loaded();
                }

                $( 'body' ).trigger( 'vegasload', [ $current.get(0), step - 1 ] );
                options.load.apply( $current.get(0), [ step - 1 ] );

                if ( step ) {
                    $( 'body' ).trigger( 'vegaswalk', [ $current.get(0), step - 1 ] );
                    options.walk.apply( $current.get(0), [ step - 1 ] );
                }
            })
            .attr( 'src', options.src );

            return $.vegas;
        },

        // Destroy background and/or overlay
        destroy: function( what ) {
            if ( !what || what == 'background') {
                $( '.vegas-background, .vegas-loading' ).remove();
                $( window ).unbind( 'resize.vegas' );
                $current = null;
            }

            if ( what == 'overlay') {
                $( '.vegas-overlay' ).remove();
            }

            return $.vegas;
        },

        // Display the pattern overlay
        overlay: function( settings ) {
            var options = {
                src: null,
                opacity: null
            };
            $.extend( options, $.vegas.defaults.overlay, settings );

            $overlay.remove();

            $overlay
                .css( {
                    'margin': '0',
                    'padding': '0',
                    'position': 'fixed',
                    'left': '0px',
                    'top': '0px',
                    'width': '100%',
                    'height': '100%'
            });

            if ( options.src ) {
                $overlay.css( 'backgroundImage', 'url(' + options.src + ')' );
            }

            if ( options.opacity ) {
                $overlay.css( 'opacity', options.opacity );
            }

            $overlay.prependTo( 'body' );

            return $.vegas;
        },

        // Start/restart slideshow
        slideshow: function( settings, keepPause ) {
            var options = {
                step: step,
                delay: delay,
                preload: false,
                backgrounds: backgrounds,
                walk: function() {}
            };
            
            $.extend( options, $.vegas.defaults.slideshow, settings );
                        
            if ( options.backgrounds != backgrounds ) {
                if ( !settings.step ) {
                    options.step = 0;
                }

                if ( options.preload ) {
                    $.vegas( 'preload', options.backgrounds );
                }
            }

            backgrounds = options.backgrounds;
			delay = options.delay;
            step = options.step;

            clearInterval( timer );

            if ( !backgrounds.length ) {
                return $.vegas;
            }

            var doSlideshow = function() {
                if ( step < 0 ) {
                    step = backgrounds.length - 1;
                }

                if ( step >= backgrounds.length || !backgrounds[ step - 1 ] ) {
                    step = 0;
                }

                var settings = backgrounds[ step++ ];
                settings.walk = options.walk;

                if ( settings.fade > options.delay ) {
                    settings.fade = options.delay;
                }

                $.vegas( settings );
            }
            doSlideshow();

            if ( !keepPause ) {
                paused = false;
                
                $( 'body' ).trigger( 'vegasstart', [ $current.get(0), step - 1 ] );
            }

            if ( !paused ) {
                timer = setInterval( doSlideshow, options.delay );
            }

            return $.vegas;
        },

        // Jump to the next background in the current slideshow
        next: function() {
            var from = step;

            if ( step ) {
                $.vegas( 'slideshow', { step: step }, true );

                $( 'body' ).trigger( 'vegasnext', [ $current.get(0), step - 1, from - 1 ] );
            }

            return $.vegas;
        },

        // Jump to the previous background in the current slideshow
        previous: function() {
            var from = step;

            if ( step ) {
                $.vegas( 'slideshow', { step: step - 2 }, true );

                $( 'body' ).trigger( 'vegasprevious', [ $current.get(0), step - 1, from - 1 ] );
            }

            return $.vegas;
        },

        // Jump to a specific background in the current slideshow
        jump: function( s ) {
            var from = step;

            if ( step ) {
                $.vegas( 'slideshow', { step: s }, true );

                $( 'body' ).trigger( 'vegasjump', [ $current.get(0), step - 1, from - 1 ] );
            }

            return $.vegas;
        },

        // Stop slideshow
        stop: function() {
            var from = step;
            step = 0;
            paused = null;
            clearInterval( timer );

            $( 'body' ).trigger( 'vegasstop', [ $current.get(0), from - 1 ] );

            return $.vegas;
        },

        // Pause slideShow
        pause: function() {
            paused = true;
            clearInterval( timer );

            $( 'body' ).trigger( 'vegaspause', [ $current.get(0), step - 1 ] );

            return $.vegas;
        },

        // Get some useful values or objects
        get: function( what ) {
            if ( what == null || what == 'background' ) {
                return $current.get(0);
            }

            if ( what == 'overlay' ) {
                return $overlay.get(0);
            }

            if ( what == 'step' ) {
                return step - 1;
            }

            if ( what == 'paused' ) {
                return paused;
            }
        },
        
        // Preload an array of backgrounds
        preload: function( backgrounds ) {
            for( var i in backgrounds ) {
                if ( backgrounds[ i ].src ) {
                    $('<img src="' + backgrounds[ i ].src + '">');
                }
            }

            return $.vegas;
        }
    }

    // Resize the background
    function resize( $img, settings ) {
        var options =  {
            align: 'center',
            valign: 'center'
        }
        $.extend( options, settings );

        var ww = $( window ).width(),
            wh = $( window ).height(),
            iw = $img.width(),
            ih = $img.height(),
            rw = wh / ww,
            ri = ih / iw,
            newWidth, newHeight,
            newLeft, newTop,
            properties;

        if ( rw > ri ) {
            newWidth = wh / ri;
            newHeight = wh;
        } else {
            newWidth = ww;
            newHeight = ww * ri;
        }

        properties = {
            'width': newWidth + 'px',
            'height': newHeight + 'px',
			'top': 'auto',
			'bottom': 'auto',
			'left': 'auto',
			'right': 'auto'			
        }

        if ( !isNaN( parseInt( options.valign ) ) ) {
            properties[ 'top' ] = ( 0 - ( newHeight - wh ) / 100 * parseInt( options.valign ) ) + 'px';
        } else if ( options.valign == 'top' ) {
            properties[ 'top' ] = 0;
        } else if ( options.valign == 'bottom' ) {
            properties[ 'bottom' ] = 0;
        } else {
            properties[ 'top' ] = ( wh - newHeight ) / 2;
        } 

        if ( !isNaN( parseInt( options.align ) ) ) {
            properties[ 'left' ] = ( 0 - ( newWidth - ww ) / 100 * parseInt( options.align ) ) + 'px';
        } else if ( options.align == 'left' ) {
            properties[ 'left' ] = 0;
        } else if ( options.align == 'right' ) {
            properties[ 'right' ] = 0;
        } else {
            properties[ 'left' ] = ( ww - newWidth ) / 2 ;
        }

        $img.css( properties );
    }

    // Display the loading indicator
    function loading() {
        $loading.prependTo( 'body' ).fadeIn();
    }

    // Hide the loading indicator
    function loaded() {
        $loading.fadeOut( 'fast', function() {
            $( this ).remove();
        });
    }

    // Get the background image from the body
    function getBackground() {
        if ( $( 'body' ).css( 'backgroundImage' ) ) {
            return $( 'body' ).css( 'backgroundImage' ).replace( /url\("?(.*?)"?\)/i, '$1' );
        }
    }

    // The plugin
    $.vegas = function( method ) {
        if ( methods[ method ] ) {
            return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ) );
        } else if ( typeof method === 'object' || !method ) {
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' does not exist' );
        }
    };

    // Global parameters
    $.vegas.defaults = {
        background: {
            // src:         string
            // align:       string/int
            // valign:      string/int
            // fade:        int
            // loading      bool
            // load:        function
            // complete:    function
        },
        slideshow: {
            // step:        int
            // delay:       int
            // backgrounds: array
            // preload:     bool
            // walk:        function
        },
        overlay: {
            // src:         string
            // opacity:     float
        }
    }

    /*!
     * jQuery imagesLoaded plugin v1.0.3
     * http://github.com/desandro/imagesloaded
     *
     * MIT License. by Paul Irish et al.
     */
    $.fn.imagesLoadedForVegas = function( callback ) {
        var $this = this,
            $images = $this.find('img').add( $this.filter('img') ),
            len = $images.length,
            blank = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';

        function triggerCallback() {
          callback.call( $this, $images );
        }

        function imgLoaded() {
          if ( --len <= 0 && this.src !== blank ){
            setTimeout( triggerCallback );
            $images.unbind( 'load error', imgLoaded );
          }
        }

        if ( !len ) {
          triggerCallback();
        }

        $images.bind( 'load error',  imgLoaded ).each( function() {
          // cached images don't fire load sometimes, so we reset src.
          if (this.complete || this.complete === undefined){
            var src = this.src;
            // webkit hack from http://groups.google.com/group/jquery-dev/browse_thread/thread/eee6ab7b2da50e1f
            // data uri bypasses webkit log warning (thx doug jones)
            this.src = blank;
            // un@bellcom.dk, this generated a lot of trafic - wich we do not like..
            if (src) {
              this.src = src;              
            }
          }
        });

        return $this;
      };
})( jQuery );
/**
 * doaloug framework build around colorbox
 * 
 * 
 */
var dialoug = (function($) {
  var pub = {};

  pub.confirm = function (title, message, callback) {
    var $callback = callback;

    $.colorbox({
      'top' : '25%',
      'close' : '',
      'html': '\
        <h2>' + title + '</h2>\
        <div class="message">' + message + '</div>\
        <div class="bottons">\
          <a class="dialoug-confirm" data-case="ok" href="">' + t('Ok') + '</a>\
          <a class="dialoug-confirm" data-case="cancel" href="">' + t('Cancel') + '</a>\
        </div>\
      '
    });

    $('a.dialoug-confirm').bind('click', function(event) {
      $('a.dialoug-confirm').unbind('click');
      event.preventDefault();
      $.colorbox.close();
      if ($callback != undefined) {
        $callback($(this).data('case'));
      }
    });
  };
  
  pub.alert = function (title, message, timeout) {
    $.colorbox({
      'top' : '25%',
      'close' : i18n.close,
      'html': '<h2>' + title + '</h2><div class="message">' + message + '</div>'
    });

    if ((timeout !== undefined) && (typeof timeout == 'number')) {
      setTimeout(function() {
        $.colorbox.close();
      }, timeout);
    }
  };

  return pub;
}(jQuery));

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
            var text = i18n.consultant + '<br />' + item.fullname + '<br />' + item.postcode + ' '+ item.city + '<br /><br /><p>' + i18n.phone + ': ' + item.phone + '<br />' + i18n.email + ': <a href="mailto:' + item.email + '">' + item.email + '</a><br /><br />' + item.notes;
            var point = new google.maps.LatLng(item.latitude, item.longitude);
            var infowindow = new google.maps.InfoWindow({ content: text });
            var markerParams = {
              position: point,
              map: map,
              icon : '/templates/pompdelux/images/POMPdeLUX_map_logo.png'
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
/**
 * video.js loading
 */
VideoJS.setupAllWhenReady();

$(function() {
  /**
   * attach click events to video "poppers"
   * videos will be opened in a colorbox iframe
   * these will always have autoplay
   */
  $('a.video-popper').click(function(event) {
    event.preventDefault();
    var $this = $(this);
    var height = $this.data('height');
    var width = $this.data('width');

    // call colorbox and play the video.
    $.colorbox({
      iframe : true,
      href: '/video.php?src=' + $this.data('src') + '&height=' + height + '&width=' + width,
      innerHeight: (height + 20) + 'px',
      innerWidth: (width + 15) + 'px'
    });
  });

  /**
   * load videos and embed these in the document where the trigger is.
   */
  $('a.video-embed').each(function() {
    var $this = $(this);
    var autoplay = $this.data('autoplay');

    // setup
    var params = $this.data();
    params.embed = 1;

    // load video code and push it into the document
    $this.load('/video.php', params, function() {
      var $video = $this.find('video');
      var player = VideoJS.setup($video.attr('id'));
      
      // remove the <a> tag to prevent "clicks"
      $video.closest('div.video-js-box').unwrap();

      // if autoplay is set, start the video.
      if (autoplay && autoplay == 1) {
        player.play();

        // add tracking to analytics, but only for autoplay
        if (_gaq != undefined) {
          var src = $video.find('source').first().attr('src');
          if (src == undefined) {
            src = 'http://static.pompdelux.dk/video/' +params.src+ '.flv';
          }
          _gaq.push(['_trackPageview', src]);
        }
      }
    });
  });
});

$(function()
{
  $('div.tx-irfaq-pi1 dt').click(function()
  {
    var parents = $(this).parents();
    for (i=0, max=parents.length; i<max; i++)
    {
      if ($(parents[i]).get(0).tagName == 'DL')
      {
        $(parents[i]).find('dd').toggle();
        $(parents[i]).find('img').toggleClass('active');

        if ($(parents[i]).find('img').hasClass('active'))
        {
          $(parents[i]).find('img').attr('src', '/templates/pompdelux/images/faqminus.gif');
        }
        else
        {
          $(parents[i]).find('img').attr('src', '/templates/pompdelux/images/faqplus.gif');
        }
      }
    }
  });

  $('div.tx-irfaq-pi1 a.toggleAll').click(function()
  {
    $(this).parent().find('dd').toggle();
    $(this).toggleClass('active');

    if ($(this).hasClass('active'))
    {
      $(this).text(i18n.hideAll);
      $(this).parent().find('img').each(function(x)
      {
        $(this).attr('src', '/templates/pompdelux/images/faqminus.gif');
      });

    }
    else
    {
      $(this).text(i18n.showAll);
      $(this).parent().find('img').each(function(x)
      {
        $(this).attr('src', '/templates/pompdelux/images/faqplus.gif');
      });
    }
  });
});

$(function() {
  $('form.newsletter-subscription-form .button').click(function() {    
    var $this = $(this);
    //$('#newsletter-sub-action').val($this.data('action'));
    $this.closest('form').submit();
  });
  
  // use inline labels if the "real" labels are hidden
  if ($('form.newsletter-subscription-form label').is(':hidden')) {
    $('form.newsletter-subscription-form input[title]').each(function() {
      if (this.value == '') {
        this.value = this.title;
      }
      $(this).focus(function() {
        if (this.value == this.title) {
          $(this).val('').addClass('focused');
        }
      });
      $(this).blur(function() {
        if (this.value == '') {
          $(this).val($(this).attr('title')).removeClass('focused');
        }
      });    
    });
  }
});

/**
 * pseudo sleep function
 *
 * @param delay miliceconds to sleep
 */
function sleep(delay)
{
  var start = new Date().getTime();
  while (new Date().getTime() < start + delay);
}

/**
 * add a notice slide box message to the top right of the screen.
 *
 * @param message the message to display
 * @param duration the number of miliseconds the message should be displayed
 */
function slideNotice(message, duration) {
  $('body').prepend('<div id="slide-notice-box">' + message + '</div>');
  var $slide = $('#slide-notice-box');
  var slideWidth = $slide.outerWidth();
  var docWidth = $(document).width();

  $slide.css({
    left : docWidth + 'px',
    width : slideWidth
  });

  $slide.animate({
    left : '-='+(slideWidth + 10 )+'px'
  }, {
    complete : function() {
      sleep(duration);
      $slide.animate({left : docWidth}, {
        complete : function() {
          $slide.remove();
        }
      });
    }
  });
}

function changeColorSetEventHandler()
{
  $('.color').change(function(e) {
    var p_id = $(this).attr('rel');
    if ($('#quantitybox_' + p_id) && $('quantitybox_' + p_id).html() != '')
    {
      $('#quantitybox_' + p_id).remove();
    }
    if ($(this).val() != -1)
    {
      $('#thequan_' + p_id).append('\
        <div class="quantity_set" id="quantitybox_' + p_id + '">\
          <label id="quantitylabel_' + p_id +'">'+i18n.qty+':</label><br />\
          <select name="cart_quantity[]" id="quantity_' + p_id + '" class="quantitys_set"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option></select>      \
        </div>'
      );

      $('#quantity').focus();
    }
    else
    {
      $('#submit').html('');
    }
  });
}

function addSubmitAndQuantity(e)
{
  $(document).unbind('close.facebox', addSubmitAndQuantity);

  if ($('#quantitybox').length == 0) {
    $('#submit').append('<div id="quantitybox" class="f-left"><label>'+i18n.qty+':</label><select name="quantity" id="quantity" title=""><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option></select></div><input type="button" value="'+i18n.addToBasket+'" name="submit" id="btn-submit" class="button" onclick="addToBasket()" />');
  }

  if (typeof e == 'object' && e.data.date) {
    $('#quantity').attr('title', e.data.date);
  }
}

function changeColorEventHandler()
{
  $('#color').change(function() {
    if ($('#quantitybox') && $('quantitybox').html() != '')
    {
      $('#quantitybox').remove();
    }

    if ($('#color').val() != -1)
    {
      var params = {
        id : $('#color option:selected').val(),
        type : 'getStockLocation',
        qty: 1
      };
      $.getJSON('/ajax.php', params, function (data) {
        var date = $('#color option:selected').attr('lang');
        if (date)
        {
          fbStockNotice(date);
        }
        else
        {
          addSubmitAndQuantity(1);
        }
      });
    }
    else
    {
      $('#submit').html('');
    }
  });
}

function fbStockNotice(date)
{
  var notice = formatStockNotice(date);
  $('body').addClass('xXx');

  jQuery.facebox(notice);
  $(document).bind('close.facebox', {
    date: date
  }, addSubmitAndQuantity);
}

function formatStockNotice(date)
{
  var notice = i18n.lateArrival;
  notice = notice.replace('{product}', $('h1').text() + ' ' + $('#product_size option:selected').text() + ' ' + $('#color option:selected').text());
  notice = notice.replace('{date}', date);
  return notice;
}

/**
 * Fetch colors based on size product id
 */
function fetchColors(productSize, productID)
{
  return function() {
    var product_size = (productSize == undefined) ? $(this).val() : productSize;
    var product_id   = (productID == undefined) ? $('#productsListing').val() : productID;

    if (product_id != -1)
    {
      $.getJSON('/ajax.php?type=color&pid=' + product_id + '&ps=' + product_size, function(data) {

        var paragraph = document.getElementById('colors');
        var sizeselect = document.createElement('select');

        // Set some styles..
        with (sizeselect.style)
        {
          padding = '0';
          margin = '0 5px 0 0';
          }

        sizeselect.setAttribute('name', 'products_id');
        sizeselect.setAttribute('id', 'colorselect');

        /* create the default 'choose' option */
        var option = document.createElement('option');
        var text   = document.createTextNode(i18n.size);
        option.setAttribute('value', -1);
        option.appendChild(text);
        sizeselect.appendChild(option);

        // alert

        $.each(data, function(i, item) {
          var option = document.createElement('option');
          var text = document.createTextNode(item);
          option.setAttribute('value', i);
          option.appendChild(text);
          sizeselect.appendChild(option);
        });

        paragraph.appendChild(sizeselect);
      });
    }
  }
}

function fetchCategoryChildren(cat_id, appto)
{
  if (cat_id != 'empty')
  {
    $.getJSON('/ajax.php?type=getCategoryChildren&id=' + cat_id, function(data) {

      if (data != null)
      {
        var html = '';
        var first = $('#top_categories_id option:first').html();
        html = '<option value="empty">' + first + '</option>';

        $.each(data, function(i, item)
        {
          html += '<option value="'+i+'">' + item + '</option>';
        });

        $(appto).html('');
        $(appto).append(html);
        $(appto).show();
        $(appto).focus();
      }
    });
  }
  else
  {
    $(appto).html('');
    $(appto).hide();
  }
}

$(function() {
  $('#product_set_info #submit').click(function() {
    if ($('.quantitys_set').size() == 0)
    {
      alert(i18n.chooseSizeAndColor);
      return false;
    }

    return true;
  });

  $("#select-domain a.open-menu").click(function(e) {
    e.preventDefault();
    $("#select-domain div").toggle();
  });

  $(".cart_delete_action a").click(function(e){
    e.preventDefault();
    var pid = $(this).attr('href');
    if (confirm(i18n.confirmDeleteFromBasket)) {
      $(this).parent().parent().parent().fadeOut('slow', function() {
        var params = {
          id : pid,
        basket : true
        };
        $.post('/ajax.php?type=removeFromBasket', params, function(data) {
          if (data.basket && data.total) {
            $('#mini-basket a').html(data.basket);
            $('td.total').html(data.total);
          }
        }, 'json');
      });
    }
    else
    {
      // handle cancel clicks
      this.checked = false;
      this.blur();
    }
  });

  $(".cart_edit_action a").click(function(e){
    e.preventDefault();
    var pid = $(this).attr('href');
    jQuery.facebox({ajax: '/ajax.php?type=edit_product_in_cart&pid='+pid});
  });

  /* Check in checkout shipping page, if delivery address country is other than private address*/
  $('#checkout_shipping_form input[type=submit]').click(function() {
    if ($('#deliver_address_country').val() != $('#private_address_country').val())
    {
      alert(i18n.countryAlert);
      return false;
    }
    return true;
  });

  /* Advanced search extra -->> */
  $('#advanced_search_extra #top_categories_id').change(function() {
    var cat_id = $('#top_categories_id option:selected').val();
    fetchCategoryChildren(cat_id,'#sub_categories_id');
    $('#categories_id').hide();
    $('#size').hide();
    $('#hidesearch').hide();
  });

  $('#advanced_search_extra #sub_categories_id').change(function() {
    var cat_id = $('#sub_categories_id option:selected').val();
    fetchCategoryChildren(cat_id,'#categories_id');
    $('#categories_id').hide();
    $('#size').hide();
    $('#hidesearch').hide();
  });

  //$('#advanced_search_extra #categories_id').change(function() {
  $('#advanced_search_extra #sub_categories_id').change(function() {
    $('#size').show();
  });

  /*
   * if size changed, we display the search button
   */
  $('#advanced_search_extra #attribute_1').change(function() {
    $('#hidesearch').show();
  });

  /* <<-- Advanced search extra */

  /**
   * Fetch sizes based on product id
   */
  $('#productsListing').change(function() {
    var product_id = $(this).val();
    if (product_id != -1)
    {
      $.getJSON('/ajax.php?type=size&pid=' + product_id, function(data) {
        var paragraph = document.getElementById('sizes');
        var sizeselect = document.createElement('select');

        // Set some styles..
        with (sizeselect.style)
        {
          padding = '0';
          margin = '0 5px 0 0';
        }

        sizeselect.setAttribute('name', 'sizeselect_product_id');
        sizeselect.setAttribute('id', 'sizeselect');

        /* create the default 'choose' option */
        var option = document.createElement('option');
        var text   = document.createTextNode(i18n.size);
        option.setAttribute('value', -1);
        option.appendChild(text);
        sizeselect.appendChild(option);

        // alert

        $.each(data, function(i, item) {
          var option = document.createElement('option');
          var text = document.createTextNode(item);
          option.setAttribute('value', item);
          option.appendChild(text);
          sizeselect.appendChild(option);
        });

        paragraph.appendChild(sizeselect);

        $('#sizeselect').change( fetchColors() );
      });
    }
  });

  /* stuff happens when choosing an element in the size dropdown */
  $('.product_set_size').change(function() {
    var product_size = $(this).val();
    var product_id   = $(this).attr('rel');

    if (product_size != -1)
    {
      $('#colors').html('');
      $('#submit').html('');
      $.getJSON('/ajax.php?type=color&pid=' + product_id + '&ps=' + product_size, function(data) {

        if ($('#color_' + product_id))
        {
          $('#color_' + product_id).remove();
          $('#colorlabel_' + product_id).remove();
          $('#quantity_' + product_id).remove();
          $('#quantitylabel_' + product_id).remove();
        }
        /* create the select tag */
        var colorselect = '<select name="products_id[]" rel="'+product_id+'" class="color" id="color_' + product_id + '">';

        /* create the default 'choose' option */
        var option = '<option value="-1">' + i18n.choose + '</option>';

        /* loop through all the elements returned from the ajax call */
        $.each(data, function(i, item) {
          option += '<option value="' + i + '">' + item.color + '</option>';
        });

        $('#colors_' + product_id).prepend('<label id="colorlabel_' + product_id +'">'+i18n.color+':</label><br />').append(colorselect + option + '</select>');
        $('#indicator').remove();


        /* stuff happens when choosing an element in the color dropdown */
        changeColorSetEventHandler();
      });
    }
    else
    {
      $('#colors').html('');
      $('#submit').html('');
    }
  });

  $('#product_size').change(function() {
    /* set some variables */
    var product_size = $(this).val();
    var product_id   = $('#product_id').val();
    $('#product_size').after('<img id="indicator" src="/templates/pompdelux/images/indicator.gif" />');
    /* erase the colors field */
    $('#colors').html('');

    /* call and get a Json object of colors */
    if (product_size != -1)
    {
      $('#colors').html('');
      $('#submit').html('');
      $.getJSON('/ajax.php?type=color&pid=' + product_id + '&ps=' + product_size, function(data) {
        var colorselect = '<select name="products_id" id="color">';
        var option = '<option value="-1">' + i18n.choose + '</option>';

        $.each(data, function(i, item) {
          option += '<option value="' + i + '" lang="' + item.fbmsg + '">' + item.color + '</option>';
        });
        $('#colors').prepend('<label>'+i18n.color+':</label>').append(colorselect + option + '</select>');
        $('#indicator').remove();
        /* stuff happens when choosing an element in the color dropdown */
        changeColorEventHandler();
      });
    }
    else
    {
      $('#colors').html('');
      $('#submit').html('');
    }
  });

//  if ($(":input[type!='hidden']")[0] && $('input:visible')[0]) {
//    $('input:visible')[0].focus();
//  }

  try {
    $('a[rel*=facebox]').facebox({opacity : 0.7});

    $(document).bind('reveal.facebox', function() {
      $('#facebox .body .content h2.mskema span').text($('h1').text());
    });

    /* set all a tags with rel="lightbox" to use the lightBox script */
    $('a[rel*=lightbox]').lightBox({
    //$('a.lightbox').lightBox({
      imageLoading : '/templates/pompdelux/scripts/images/lightbox-ico-loading.gif',
      imageBtnPrev : '/templates/pompdelux/scripts/images/lightbox-btn-prev.gif',
      imageBtnNext : '/templates/pompdelux/scripts/images/lightbox-btn-next.gif',
      imageBtnClose : '/templates/pompdelux/scripts/images/lightbox-btn-close.gif',
      imageBlank : '/templates/pompdelux/scripts/images/lightbox-blank.gif'
    });
  } catch (e) {}

//  $('div.mousetrap').live('click', function () {
//    $('a.cloud-zoom').click();
//  });

  /**
   * auto complete city names when entering zip codes.
   * only works for se/no/dk so if the request is made via .com we skip this step
   */
  var tld = document.location.hostname.match(new RegExp("\.([a-z,A-Z]{2,6})$"));
  try {
    if (tld[1] != 'com' && tld[1] != 'nl') {
      $('input[name="city"]').attr('readonly', 'readonly');

      // set city to readonly
      $('input[name="city"]').focus(function () {
        this.value = '';
        if ($('input[name="postcode"]').val() == '') {
          $('input[name="postcode"]')
            .css('border-color', '#a10000')
            .fadeOut(100).fadeIn(100)
            .fadeOut(100).fadeIn(100)
            .fadeOut(100).fadeIn(100)
            .focus();
          return;
        }
        var params = {
          type : 'getCityFromZip',
          q    : $('input[name="postcode"]').val()
        };
        $.getJSON('/ajax.php', params, function(data) {
          if (data && data.city) {
            $('input[name="city"]').val(data.city);
            $('input[name="postcode"]').css('border-color', '#444345');
            try {
              $('input[name="telephone"]').focus();
            } catch (e) {}
          }
          else {
            $('input[name="postcode"]').css('border-color', '#a10000').fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100).focus();
          }
        });
      });
    }
  } catch (e) {}

  /* hf@bellcom.dk: used for dibs card selection -->>*/
//  $('.paymentType').click(function () {
//      $('.paymentClass').each(function () {
//          this.checked = false;
//        });
//      $('#paymentSelected').val('dibs');
//  });

  $('.paymentClass').click(function () {
    $('.paymentType').each(function () {
      this.checked = false;
    });
    if ($(this).hasClass('dibs')) {
      $('#paymentSelected').val('dibs');
    } else {
      $('#paymentSelected').val(this.value);
    }
  });

  $("#checkout_payment_form input[type='submit']").click(function () {
      var n = $("#checkout_payment_form input:checked").length;
      if ( n == 0 ) {
        alert( i18n.selectPaymentCard );
        return false;
      }
  });
  /* <<-- hf@bellcom.dk: used for dibs card selection */

  $('div.invite-item a.closer').click(function () {
    $(this).parent().fadeOut("fast").remove();
  });
  $('a#trigger').click(function () {
    $('div.invite-item.hidden').clone().appendTo('#invite-container').hide().fadeIn("fast").removeClass('hidden');
    $('div.invite-item:last a.closer').click(function () {
      $(this).parent().fadeOut("fast").remove();
    });
  });

  /**
   * hf@bellcom.dk, 18-jan-2011: edit of products in the shopping cart -->>
   */
  $("body").delegate('#edit_product_in_cart #product_size', 'change', changeSizeEventHandler2());

  $("body").delegate('#edit_product_in_cart #quantity', 'change', function() {
    if ($('#submit_button').length !== 0) {
      $('#submit_button').remove();
    }
    $('#cancel').before('<div id="submit_button"><input type="button" class="button" value="'+i18n.updateBasket+'" name="submit" id="btn-cart-edit-update" onclick="updateBasket()" /></div>');
  });

  $("body").delegate('#edit_product_in_cart #color', 'change', function() {
    if ($('#quantitybox').length)
    {
      $('#quantitybox').remove();
      $('#submit_button').remove();
    }

    if ($('#color').val() != -1)
    {
      var params = {
        id : $('#color option:selected').val(),
        type : 'getStockLocation',
        qty: 1
      };
      $.getJSON('/ajax.php', params, function (data) {
        var date = $('#color option:selected').attr('lang');
        if (date)
        {
          var notice = formatStockNotice(date);
          $('body').addClass('xXx');
          $('#edit_product_in_cart .warning').html(notice);
        }
        $(document).unbind('close.facebox', addSubmitAndQuantity);

        if ($('#quantitybox').length === 0) {
          $('#cancel').before('<div id="quantitybox"><label for="quantity">'+i18n.qty+':</label><select name="quantity" id="quantity" title=""><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option></select></div><div id="submit_button"><input type="button" class="button" value="'+i18n.updateBasket+'" name="submit" id="btn-cart-edit-update" onclick="updateBasket()" /></div>');
        }

        if (typeof e == 'object' && e.data.date) {
          $('#quantity').attr('title', e.data.date);
        }
        $('#submit').data('isNewProduct',true);
      });

    }
    else
    {
      $('#submit').html('');
    }
  });
  /**
   * <<-- hf@bellcom.dk, 11-jan-2011: edit of products in the shopping cart
   */

  if ($('#body-checkout-shipping input[type="radio"]').length > 2) {
    $('#c-continue-block').hide();
  }

  var normalAddress;
  $('#body-checkout-shipping input[type="radio"]').click(function () {    
    // re insert "normal" address if detached by the overnight method
    if (normalAddress) {
      $('#ts-shipping-address h2').after(normalAddress);
      $("#overnightbox-address-display").hide();
      $('#c-continue-block p input.button').show();
    }
    
    var hrefs = $('#ts-shipping-address a').attr('href').split('?');
    $('#ts-shipping-address a').attr('href', hrefs[0] + '?type=' + $(this).attr('rel'));
    $('#c-continue-block').show();

    // no is allowed to not set company name
    var dom = /no$/g;
    if (dom.test(hrefs[0].split('/')[2])) {
      $('#c-continue-block p').show();
      return;
    }
    
    if (($(this).attr('rel') == 'company') &&
        ($('#is-company-address').val() == 0)
    ) {
      $('#c-continue-block p').hide();
      jQuery.facebox('<div class="error">' + i18n.chooseOrCreateCompanyAddress + '</div>');
    }
    else if ($(this).attr('rel') == 'overnightbox')
    {
      // Kill normal address display, show overnightbox fields
      normalAddress = $("#normal-address-display").detach();
      $("#overnightbox-address-display").show();
      if ($('#overnightbox-address-display div.error').length) {
        $('#c-continue-block p input.button').hide();
      }
    }
    else
    {
      $('#c-continue-block p').show();
    }
  });

  $('#f-new-address input[type="text"]').change(function() {
    $('#f-new-address input[type="radio"]:checked').attr('checked', false);
  });

  if ($('#c-continue-block').length) {
    var url = document.location.href.split('?');
    switch (url[1]) {
      case 'c=private':
        $('#body-checkout-shipping input[rel="private"]').click();
        break;
      case 'c=company':
        $('#body-checkout-shipping input[rel="company"]').click();
        break;
      case 'c=overnightbox':
        $('#body-checkout-shipping input[rel="overnightbox"]').click();
        break;
    }
  }

  /**
   * note, this is a really simple validation routine.
   */
  $('form').submit(function() {
    switch (this.id) {
      case 'f-new-address':
        if ($('#f-new-address input[type="radio"]:checked').length) {
          return;
        }
        $('body').data('error', '');
        $('#f-new-address input[rel="required"]').each(function(){
          if (this.value == '') {
            var txt = $(this).parent().text();
            txt = txt.split(':');
            $('body').data('error', $('body').data('error') + '<li>' + txt[0]  + '</li>');
          }
        });
        if ($('body').data('error').length) {
          var notice = '<ul>' + $('body').data('error') + '</ul>';
          $('body').removeData('error');
          jQuery.facebox('<div class="error">'+i18n.missingRequirements + notice + '</div>');
          return false;
        }
        break;
    }
  });

  $('table.hostess-paticipantlist tbody td.delete-trigger').click(function (){
    var participant = this.id.split('-')[2];
    if (false == isNaN(participant)) {
      if (confirm(i18n.confirmDeleteParticipant)) {
        var params = {
          type : 'deleteParticipant',
          id : participant
        };
        $.post('ajax.php', params);
        $('#participant-id-'+participant).parent().fadeOut();
      }
    }
  });


  $('input[type="radio"]').addClass('noborder');
  $('input[type="checkbox"]').addClass('noborder');


  // added funky version of the extended search form
  $('a.trigger-extended-search').click(function(){
    $(this).hide();
    $('form[name="advanced_search"]').hide();
    $('div#ze-extended-container').slideDown('fast', function() {
      $('div#ze-extended-container .step-1 a').click(function() {
        // reset selection
        $('div#ze-extended-container .step-1 a').removeClass('selected');
        $('div#ze-extended-container .step-2 a').removeClass('selected');
        $(this).addClass('selected');
        $(this).addClass('ac_loading');
        this.blur();

        // reset hidden vals
        var cat = this.href.split('#')[1];
        $('form[name="advanced_search"] input[name="categories_id"]').val(cat);
        $('form[name="advanced_search"] input[name="attribute_1"]').val('');

        // reset form
        $('form[name="advanced_search"]').hide();

        $.get('/ajax.php', {type : 'getCategorySizes', cid : cat}, function(result) {
          $('div#ze-extended-container .step-2 ul li').remove();
          for (i=0; i<result.length; i++) {
            $('div#ze-extended-container .step-2 ul').append('<li><a href="#'+result[i]+'">'+result[i]+'</a></li>');
          }
          $('div#ze-extended-container li a').removeClass('ac_loading');

          $('div#ze-extended-container .step-2').slideDown('fast', function() {
            $('div#ze-extended-container .step-2 a').click(function() {
              $('div#ze-extended-container .step-2 a').removeClass('selected');
              $(this).addClass('selected');
              $(this).addClass('ac_loading');
              this.blur();
              $('form[name="advanced_search"] input[name="attribute_1"]').val(this.href.split('#')[1]);
              if (!$('form[name="advanced_search"] div.final').length) {
                $('form[name="advanced_search"]').prepend('<div class="final"><h4>Indtast søgeord</h4></div>');
                $('form[name="advanced_search"]').append('\
                  <input type="hidden" value="1" name="inc_subcat"/>\
                  <input type="hidden" value="1" name="search_in_attributes" />\
                  <input type="hidden" value="=" name="attribute_1_match_type" />\
                  <input type="hidden" value="off" name="cms_search" />'
                );
              }
              $('form[name="advanced_search"]').attr('action', $('a.trigger-extended-search').attr('href'));
              $('form[name="advanced_search"]').submit();
              return false;
            });
          });

        }, 'json');

        return false;
      });
    });
    return false;
  });

  // pagination call
  $('div#main .pager a').live('click', function() {
    $(this).addClass('loading');
    var req = this.href.split('?')[1] + '&type=categoryPager';
    $.get('/ajax.php', req, function(html) {
      $('div#main .page-browser').remove();
      $('div#main .product-list').replaceWith(html);
      window.scroll(0,0);
    });
    return false;
  });

  // zoom on product images
  initCloudZoom();

  // change large product image and set zoom effects.
  // un, 2011.09.13 - removed
  //if (tld[1] != 'dk' && tld[1] != 'com') {
  //  $('.style-guide').hide();
  //}
  $('.style-guide .element').hide();

  $('.productimage-small a').click(function(e) {
    var from = {
      small  : $(this).find('img').attr('src'),
      medium : this.rev,
      large  : this.href,
      id     : this.id
    }
    var to = {
      small  : $('.productimage-large a').attr('rev'),
      medium : $('.productimage-large a img').attr('src'),
      large  : $('.productimage-large a').attr('href'),
      id     : $('.productimage-large a').attr('id')
    }

    $('.productimage-large a').attr('rev', from.small);
    $('.productimage-large a').attr('href', from.large);
    $('.productimage-large a').attr('id', from.id);
    $('.productimage-large a img').attr('src', from.medium);

    this.rev = to.medium;
    this.href = to.large;
    $(this).find('img').attr('src', to.small);
    this.id = to.id;

    $('.style-guide .element').hide();
    $('.style-guide .' + from.id).show();

    initCloudZoom();
    e.preventDefault();
  });

  if (document.location.href.indexOf('#img') != -1) {
    var id = document.location.href.split('#img')[1];
    if ($('.productimage-large a#image-id-' + id).length) {
      if ($('.style-guide .image-id-' + id).length) {
        $('.style-guide .image-id-' + id).show();
      }
    } else {
      $('#image-id-' + id).click();
    }
  }
  // tabs
  $("ul.tabs").tabs("div.panes > div");

  // frontpage count down
  $('td.countdown strong').countdown({
    timezone : +1,
    until: i18n.countdownTo,
    layout:'<strong>' + i18n.countdownFormat + '</strong>'
  });

  // frontpage box
  if ($('html').hasClass('mobile') == false) {
    $('#body-index #main a.button').wrap('<div class="btn"></div>');
    $('#body-index #main a.button').click(function(event) {
      event.preventDefault();
      var $a = $(this);
      var $this = $a.parent();
      var $parent = $this.parent();
      var $child = $parent.children('div').first();

      var maxWidth = $('#container').width() - ($('nav.main').width() + $('#teasers').width() + 100);
      if (maxWidth > 670) {
        maxWidth = 670;
      }

      if ($child.height() == '500') {
        $a.text($a.data('text'));
        $this.animate({width : '350px'});
        $parent.animate({marginTop : '440px', width : '350px'});
        $child.animate({height : '100px', width : '350px'});
      } else {
        $a.data('text', $this.text());
        $a.text(i18n.close);
        $this.animate({width : maxWidth + 'px'});
        $parent.animate({marginTop : '75px', width : maxWidth + 'px'});
        $child.animate({height : '500px', width : maxWidth + 'px'});
      }
    });

    $(document).ready(function() {
      ajustMainContentOffset(1);
    });
  }

  // pop message
  try {
    if (js_alert !== undefined) {
      dialoug.alert(i18n.alertTitle, js_alert);
    }
  } catch (e) {}

  // open facebook in a new window
  $('li.facebook a').click(function(event) {
    event.preventDefault();
    window.open(this.href);
  });

  // ios class added to body
  switch (navigator.platform) {
    case 'iPad':
    case 'iPhone':
    case 'iPod':
      $('html').addClass('ios');
      break;
  }

  $('a[rel="slideshow"]').colorbox({
    rel: 'slideshow',
    close: i18n.close,
    previous : "«",
    next : "»",
    current : ""
  });

  $('input[name="handling-fee-3"]').hide();

  $('.productimage-large img.loupe').click(function() {
    var elm = $(this).closest('div').find('a.cloud-zoom');
    cloudZoomClick(elm);
  });

});

function ajustMainContentOffset() {
  var $main = $('#main');
  var $nav = $('nav.main');
  var navWidth = $nav.width();
  var offset = $main.offset();

  if (navWidth > offset.left) {
    $main.css({marginLeft : navWidth + 'px'});
  }
}

// triggers
$(document).bind('cbox_closed', function(){
  initCloudZoom();
  $('body').click();
});

function changeSizeEventHandler()
{
  return function()
  {
    var productSize = $(this).val();
    var productID = $("#product_id").val();
    $('#product_size').after('<img id="indicator" src="/templates/pompdelux/images/indicator.gif" />');

    if (productSize != -1)
    {
      $('#colors').html('');
      $('#submit').html('');
      $("#quantit").html('');
      $.getJSON('/ajax.php?type=color&pid=' + productID + '&ps=' + productSize, function(data) {
        var colorselect = '<select name="products_id" id="color">';
        var option = '<option value="-1">' + i18n.choose + '</option>';

        $.each(data, function(i, item) {
          option += '<option value="' + i + '" lang="' + item.fbmsg + '">' + item.color + '</option>';
        });
        $('#colors').prepend('<label>'+i18n.color+':</label>').append(colorselect + option + '</select>');
        $('#indicator').remove();
        $('#submit').data('isNewProduct',true);
      });
    }
    else
    {
      $('#colors').html('');
      $('#submit').html('');
      $("#quantity").html('');
    }
  }
}

/* FIXME */
function changeSizeEventHandler2()
{
  return function()
  {
    var productSize = $(this).val();
    var productID = $("#product_id").val();
    $('#product_size').after('<img id="indicator" src="/templates/pompdelux/images/indicator.gif" />');

    if (productSize != -1)
    {
      $('#colors').html('');
      $('#submit_button').html('');
      $("#quantitybox").html('');
      $.getJSON('/ajax.php?type=color&pid=' + productID + '&ps=' + productSize, function(data) {
        var colorselect = '<select name="products_id" id="color">';
        var option = '<option value="-1">' + i18n.choose + '</option>';

        $.each(data, function(i, item) {
          option += '<option value="' + i + '" lang="' + item.fbmsg + '">' + item.color + '</option>';
        });
        $('#colors').prepend('<label>'+i18n.color+':</label>').append(colorselect + option + '</select>');
        $('#indicator').remove();
        $('#submit').data('isNewProduct',true);
      });
    }
    else
    {
      $('#colors').html('');
      $('#submit_button').html('');
      $("#quantitybox").html('');
    }
  }
}

function initCloudZoom() {
  if ($('.productimage-large div.mousetrap').length) {
    $('.productimage-large div.mousetrap').remove();
  }
  var params = {
    zoomWidth: 165,
    zoomHeight: 330,
    showTitle: false,
    adjustX: 8,
    click : function(a) {
      cloudZoomClick(a);
    }
  }
  $('.cloud-zoom, .cloud-zoom-gallery').CloudZoom(params);
}

function cloudZoomClick(a) {
  $(a).unwrap();
  $.colorbox({
    html: '<img src="'+$(a).attr('href')+'" alt="" />',
    close: i18n.close
  });
}

function selectCardAction( card )
{
  $('#ze-buy-form').append('<input type="hidden" value="'+card+'" name="paytype"/>');
  $('#ze-buy-form').submit();
}

/**
* hf@bellcom.dk, 18-jan-2011: edit of products in the shopping cart, 99% copy of addToBasket -->>
*/
function updateBasket()
{
  var data = {
    type : 'getStockLocation',
    qty  : $('#quantity').val(),
    id   : $('select#color option:selected').val()
  };
  $.getJSON('/ajax.php', data, function(result) {
    var date = $('#quantity').attr('title');
    var sbm = true;
    var pop = true;

    if ($('body').hasClass('xXx') && date == result.date) {
      pop = false;
    }

    if (pop && result.date) {
      var notice = i18n.lateArrivalAlert;
      notice = notice.replace('{product}', $('h1').text() + ' ' + $('#product_size option:selected').text() + ' ' + $('#color option:selected').text());
      notice = notice.replace('{date}', result.date);

      if (!confirm (notice)) {
        sbm = false;
      } else {
        date = result.date;
      }
    }

    if (sbm) {
      if ( $('#submit').data('isNewProduct') === true )
      {
        //$.post('/ajax.php?type=removeFromBasket', {id : $('#product_id').val()}, function(data) {}, 'json');
        $.ajax({
            type: 'POST',
            url: '/ajax.php?type=removeFromBasket',
            data: {id : $('#product_id').val()},
            dataType: 'json',
            async: false
            });
      }

      var params = {
        type     : 'addToBasket',
        qty      : $('#quantity').val(),
        qtyTotal : true,
        id       : $('select#color option:selected').val(),
        date     : date,
        dd       : true
      };

      $.post('/ajax.php', params, function (data)
      {
        $('div#quantitybox p.a-notice').remove();
        $('#quantity').css({
          borderColor : '#444345',
          borderWidth : '1px'
        });
        if (data.notice)
        {
          $('div#quantitybox').append('<p class="a-notice">'+data.notice+'</p>');
          $('#quantity').css({
            borderColor : '#a10000',
            borderWidth : '2px'
          });
        }
        else
        {
          $.facebox.close();
          // Alternative nice style could be: old product: .slideUp() and then .slideDown() with the new product
          // var oldProduct = $("#shopping_cart_product_table").find("input[name='products_id[]']").val();
          window.location.reload();
        }
      }, 'json');
    }
  });
}
/**
 * <<-- hf@bellcom.dk, 18-jan-2011: edit of products in the shopping cart
 */

function addToBasket()
{
  var data = {
    type : 'getStockLocation',
    qty  : $('#quantity').val(),
    id   : $('select#color option:selected').val()
  };
  $.getJSON('/ajax.php', data, function(result) {
    var date = $('#quantity').attr('title');
    var sbm = true;
    var pop = true;

    if ($('body').hasClass('xXx') && date == result.date) {
      pop = false;
    }

    if (pop && result.date) {
      var notice = i18n.lateArrivalAlert;
      notice = notice.replace('{product}', $('h1').text() + ' ' + $('#product_size option:selected').text() + ' ' + $('#color option:selected').text());
      notice = notice.replace('{date}', result.date);

      if (!confirm (notice)) {
        sbm = false;
      } else {
        date = result.date;
      }
    }

    if (sbm) {
      var params = {
        type : 'addToBasket',
        qty  : $('#quantity').val(),
        id   : $('select#color option:selected').val(),
        date : date,
        dd   : true
      };
      $.post('/ajax.php', params, function (data)
      {
        $('div#quantitybox p.a-notice').remove();
        $('#quantity').css({
          borderColor : '#444345',
          borderWidth : '1px'
        });
        if (data.notice)
        {
          $('div#quantitybox').append('<p class="a-notice">'+data.notice+'</p>');
          $('#quantity').css({
            borderColor : '#a10000',
            borderWidth : '2px'
          });
        }
        else
        {
          window.scrollTo(window.scrollMinX, window.scrollMinY);
          $('#mini-basket a').html(data.basket);

          // notification
          slideNotice('"' + $('h1').text() + '" just added to your basket', 2000);

          $('div#colors').html('');
          $('div#submit').html('');
          if (data.sizes)
          {
            $('select#product_size').html(data.sizes);
          }
          else
          {
            $('select#product_size').hide();
            $("label[for='product_size']").replaceWith(data.out_of_stock);
          }
        }
      }, 'json');
    }
  });
}

// moved here from supersize
$(function(){
  $.getDocHeight = function(){
    var D = document;
    return Math.max(Math.max(
        document.body.scrollHeight,
        document.documentElement.scrollHeight
      ),
        Math.max(document.body.offsetHeight,
        document.documentElement.offsetHeight
      ),
        Math.max(document.body.clientHeight,
        document.documentElement.clientHeight
    ));
  };

  if (($('html').hasClass('mobile') == false) &&
      (navigator.platform == 'iPad' || navigator.platform == 'iPhone' || navigator.platform == 'iPod') &&
      (-1 == navigator.userAgent.indexOf('OS 5_'))
  ) {
    $.fn.placeFooter();
//    $("footer").css({
//      position : 'absolute',
//      height: $('footer').height() + 'px',
//      top : ($.getDocHeight() - $('footer').outerHeight(true)) + 'px'
//    });

    window.onorientationchange = function() {
      $.fn.placeFooter();
    }

    $('body').bind('near-you-container.loaded', function() {
      $.fn.placeFooter();
    });
  }
});

function alert_dialout(msg) {
  $.colorbox({
    html : msg,
    close: i18n.close
  });
}

(function($) {
  /**
   * handles placement of footer - due to iOS issues with position:fixed
   * -------------------------------------------------------------------
   */
  $.fn.placeFooter = function() {
    $("footer").css({
      position : 'absolute',
      height: $('footer').height() + 'px',
      top : ($.getDocHeight() - $('footer').outerHeight(true)) + 'px',
      width : $('body').width()
    });
  };
})(jQuery);

(function($) { 
  /**
   * animated effect on first menu level
   */
  $('nav.main > ul > li > a').hover(function() {
    var $this = $(this);
    var left = $this.data('pl-save');
    if (left == undefined) {
      left = parseInt($this.css('padding-left'));
      $this.data('plsave', left);
    }
    $this.animate({'padding-left': (left + 10) + 'px'}, 'fast');
  }, function() {
    var $this = $(this);
    $this.animate({'padding-left': $this.data('plsave') + 'px'}, 'fast');
  });
})(jQuery);

/**
 * using the background jquery plugin from:
 * http://vegas.jaysalvat.com/
 *
 * backgrounds are setup in the main template - header
 */
$(function(){
  
  if (vegas_backgrounds != undefined) {
    if (vegas_backgrounds.length > 1) {
      $.vegas('slideshow', {
        preload : true,
        backgrounds : vegas_backgrounds
      });      
    } else {
      $.vegas(vegas_backgrounds[0]);
    }
  }
  

// if you want overlays - use it like this.
//
//  $.vegas('slideshow', {
//    preload : true,
//    backgrounds : vegas_backgrounds
//  })('overlay', {
//    src:'/templates/pompdelux/scripts/vegas/overlays/01.png'
//  });
});

var i18n = {
  choose : 'Vælg',
  color : 'Color',
  qty : 'Antal',
  size : 'Size',
  chooseSizeAndColor : 'Husk at vælge størrelse, farve og antal på produkterne.',
  addToBasket: 'Læg i kurv',
  updateBasket: 'Opdater kurv',
  countryAlert : 'Der kan ikke benyttes andet land i leveringsadresse, end det der er valgt som betalingsadresse.\n\nRet venligst deres leveringsadresse.',
  hideAll : 'Skjul alle',
  showAll : 'Vis alle',
  lateArrival : '<h2 style="color:#A10000">Bemærk</h2><strong>"{product}"</strong> har vi ikke på lager i øjeblikket.<br>Hele din ordre forventes afsendt fra vores lager senest <strong>{date}</strong>.',
  confirmDeleteFromBasket : 'Er du sikker på at du vil fjerne produktet fra kurven ?',
  consultant : 'Konsulent',
  email : 'E-mail',
  phone : 'Telefon',
  lateArrivalAlert : '<h2 style="color:#A10000">Bemærk</h2>\n<strong>"{product}"</strong> har vi ikke på lager i øjeblikket.\nHele din ordre forventes afsendt fra vort lager senest <strong>{date}</strong>.',
  selectPaymentCard : 'Du skal vælge et betalingskort',
  missingRequirements : 'Du skal udfylde de påkrævede felter.',
  chooseOrCreateCompanyAddress : 'Du har valgt "Post Danmark Erhverv". Husk at ændre leveringsadressen ved at klikke på "Ret leveringsadresse" (leveringsadressen skal ved denne fragttype være en erhvervsadresse).',
  confirmDeleteParticipant : 'Er du sikker på, du vil slette denne deltager?',
  close: 'Luk',

  mannequinPrice : 'DKK :price:,00',
  mannequinEmpty : 'Der er ikke valgt produkter',

  countdownFormat : '{d<}{dn} {dl} {d>} {hn} {hl} {mn} {ml} {sn} {sl}',
  countdownTo : new Date('August 30, 2011 09:00:00'),
  alertTitle : 'Bemærk',

  pleaseWait : 'Vent venligst'
};

$(document).ready(function(){

  $("#gothia_payment_form").submit(function(e) {
    e.preventDefault();
    var formData = $(this).serialize();

    $("input[type=submit]").hide();
    $("input[type=button]").hide();
    $("div.message").addClass('wait');
    $("div.message").html( i18n.pleaseWait );
    $("div.message").show();

    $.ajax({
      type: 'POST',
      url: '/checkout_gothia.php?action=perform_flow',
      data: formData,
      dataType: 'json',
      success: function( response ) {
        if ( response.error )
        {
          $("div.message").removeClass('wait');
          $("div.message").addClass('error');
          $("div.message").html(response.error_txt);
          $("input[type=submit]").show();
          $("input[type=button]").show();
        }
        else
        {
          $("div.message").html(response.content);
          $(".help").hide();
          $("#gothia_payment_form").hide();
          if ( response.action == 'process' )
          {
            $("#checkout_process").submit();
          }
        }
      }
    });
  });

  $("#gothia_cancel_reservation").submit(function(e) {
    e.preventDefault();
    $.ajax({
      type: 'POST',
      url: '/checkout_gothia.php?action=cancel_reservation',
      dataType: 'json',
      success: function( response ) {
        if ( response.error )
        {
          $("div.message").addClass('error');
          $("div.message").html(response.error_txt);
        }
        else
        {
          $("div.message").addClass('status');
          $("div.message").html(response.content);
        }
      }
    });
  });

  $("#gothia_action_cancel_form").click(function(e) {
    e.preventDefault();
    $.ajax({
      type: 'POST',
      url: '/checkout_gothia.php?action=cancel_form',
      dataType: 'json',
      async: false,
      success: function( response ) {
        if ( response.error )
        {
          $("div.message").addClass('error');
          $("div.message").html(response.error_txt);
        }
        else
        {
          setTimeout(function(){ window.location = '/checkout_payment.php';},1000);
        }
      }
    });

  });
});
