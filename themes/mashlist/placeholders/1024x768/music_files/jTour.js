/*
 * Copyright (c) 2012 
 * ==================================
 * 
 *
 * This is part of an item on themeforest
 * You can check out the screenshots and purchase it on themeforest:
 * http://rxa.li/tour
 * 
 * 
 * ===========================================
 * original filename: jTour.min.js
 * filesize: 27627 Bytes
 * last modified: Tue, 27 Mar 2012 14:21:53 +0200
 *
 */
window.jTour=function(j,M){function U(){E=(window.innerWidth||document.documentElement.clientWidth)/u;F=(window.innerHeight||document.documentElement.clientHeight)/u}function N(b){switch(b.keyCode){case 37:"keyup"==b.type&&0<d?G():b.preventDefault();break;case 39:"keyup"==b.type?v():b.preventDefault();break;case 38:"keyup"==b.type?r(0.25):b.preventDefault();break;case 40:"keyup"==b.type?r(-0.25):b.preventDefault();break;case 32:manualskip=!0;"keyup"==b.type?y(!0):b.preventDefault();break;case 27:"keyup"==
b.type?B():b.preventDefault()}}function C(b){b||(b=d);H=H||$(window).scrollTop();I=I||$(window).scrollLeft();s.show().fadeIn(function(){c.pauseOnHover&&e.bind({"mouseenter.jTour":y,"mouseleave.jTour":z});c.keyboardNav&&$(document).bind({"keyup.jTour":N,"keydown.jTour":N});w=!1;d=b;c.onStart.call(h,d);J(b)});c.showControls&&(n.find(".play").hide(),n.find(".pause").show())}function B(){busy||(clearTimeout(t),p.clearQueue().stop(),s.hide(),c.pauseOnHover&&e.unbind(".jTour"),c.keyboardNav&&$(document).unbind(".jTour"),
c.scrollBack?K(I,H,c.scrollDuration,function(){c.onStop.call(h,d)}):c.onStop.call(h,d),i&&i.exposeElement&&i.exposeElement.css(i.exposeElement.data("jTour")).removeData("jTour").removeClass(l+"exposed"),O(),d=0,c.showControls&&(n.find(".play").show(),n.find(".pause").hide()))}function y(b,a){if(w){if(!0===b){manualskip=!manualskip;z();return}if(!manualskip)return}!1!==a&&c.onPause.call(h,d);clearTimeout(t);p.clearQueue().stop();c.showControls&&manualskip&&(n.find(".play").show(),n.find(".pause").hide());
w=!0}function z(b){if(w&&!manualskip){!1!==b&&c.onPlay.call(h,d);clearTimeout(t);var b=p.width()/(A.width()/100)/100,a=j[d].live||P.live,a=a-a*b;p.clearQueue().stop().animate({width:"100%"},{duration:a*(1/c.speed),easing:"linear",step:D,complete:v});c.showControls&&(n.find(".play").hide(),n.find(".pause").show());w=!1}}function v(){if(!busy){q&&j[d].steps&&(j[d].onStep&&j[d].onStep.call(h,i,100),$.each(q,function(b,a){j[d].steps[q[b]].call(h,i,a)}),q=null);if(j[d].goTo)return window.location=j[d].goTo+
"#jTour="+(d+1),setTimeout(function(){location.reload()},1E3),!1;d+1<L?(clearTimeout(t),p.clearQueue().stop(),J(++d)):(B(),c.onFinish.call(h,d))}}function G(){!busy&&0<d&&(clearTimeout(t),p.clearQueue().stop(),J(--d))}function J(b){var a=$.extend({},P,j[b]),m=a.element=a.element?"string"==typeof a.element?$(a.element):a.element:0;if(m.length){if("auto"===a.live){var d=$("<div>").html(a.html).text().length;a.live=j[b].live=Math.max(2500,2500*Math.log(d/10)+1E3)}a.live=Math.abs(a.live);a.isArea="area"==
a.element[0].nodeName.toLowerCase();i?i.onBeforeHide.call(h,i.element):(a.delayOut=0,a.animationOut="hide");busy=!0;e[a.animationOut](a.delayOut,function(){e.css({left:0,top:0,"min-width":0});i&&i.onHide.call(h,i.element);t=setTimeout(function(){var g,d,o,j,f;A.html(a.html);c.onChange.call(h,b);if(m[0]===x[0])g=E*u,d=F*u/2-e.outerHeight()/2,o=0,j=0;else if(a.isArea){j=m[0].coords.split(",");g=a.exposeElement=$("img[usemap=#"+m.parent().attr("name")+"]");var k=g.offset(),V=parseInt(g.css("paddingTop"),
10);o=parseInt(g.css("paddingLeft"),10);var r=parseInt(g.css("borderTopWidth"),10),t=parseInt(g.css("borderLeftWidth"),10);g=j[2]-j[0];d=j[3]-j[1];o=parseInt(j[0],10)+k.left+o+t;j=parseInt(j[1],10)+k.top+V+r}else g=m.outerWidth(),d=m.outerHeight(),o=m.offset().left,j=m.offset().top;f={left:o,top:j};k={x:0,y:0};switch(a.position){case "ne":if(!isNaN(a.offset))a.offset={x:0,y:a.offset};f.top-=e.outerHeight()+a.offset.y;f.left=o+g-e.outerWidth()+a.offset.x;k.x=g/2-+e.outerWidth()/2+a.offset.x;k.y=-d/
2-e.outerHeight()/2-a.offset.y;break;case "nw":if(!isNaN(a.offset))a.offset={x:0,y:a.offset};f.top-=e.outerHeight()+a.offset.y;f.left=o-a.offset.x;k.x=-g/2+e.outerWidth()/2-a.offset.x;k.y=-d/2-e.outerHeight()/2-a.offset.y;break;case "n":if(!isNaN(a.offset))a.offset={x:0,y:a.offset};f.top-=e.outerHeight()+a.offset.y;f.left+=(g-e.outerWidth())/2+a.offset.x;k.x=a.offset.x;k.y=-d/2-e.outerHeight()/2-a.offset.y;break;case "se":if(!isNaN(a.offset))a.offset={x:0,y:a.offset};f.top+=d+a.offset.y;f.left=o+
g-e.outerWidth()+a.offset.x;k.x=g/2-e.outerWidth()/2+a.offset.x;k.y=d/2+e.outerHeight()/2+a.offset.y;break;case "sw":if(!isNaN(a.offset))a.offset={x:0,y:a.offset};f.top+=d+a.offset.y;f.left=o-a.offset.x;k.x=-g/2+e.outerWidth()/2-a.offset.x;k.y=d/2+e.outerHeight()/2+a.offset.y;break;case "s":if(!isNaN(a.offset))a.offset={x:0,y:a.offset};f.top+=d+a.offset.y;f.left+=(g-e.outerWidth())/2+a.offset.x;k.x=a.offset.x;k.y=d/2+e.outerHeight()/2+a.offset.y;break;case "w":if(!isNaN(a.offset))a.offset={x:a.offset,
y:0};f.top-=e.outerHeight()/2-d/2-a.offset.y;f.left-=e.outerWidth()+a.offset.x;k.x=-g/2+e.outerWidth()/2-a.offset.x;k.y=-a.offset.y;break;case "e":if(!isNaN(a.offset))a.offset={x:a.offset,y:0};f.top-=e.outerHeight()/2-d/2-a.offset.y;f.left+=g+a.offset.x;k.x=g/2-e.outerWidth()/2+a.offset.x;k.y=-a.offset.y;break;case "c":if(!isNaN(a.offset))a.offset={x:0,y:a.offset};f.top-=e.outerHeight()/2-d/2-a.offset.y;f.left+=(g-e.outerWidth())/2+a.offset.x;k.x=a.offset.x;k.y=a.offset.y}scrolltopos={x:Math.max(0,
o-(E*u/2-g/2)+k.x),y:Math.max(0,j-(F*u/2-d/2)+k.y)};i&&i.exposeElement&&!a.isArea&&i.exposeElement.css(i.exposeElement.data("jTour")).removeData("jTour").removeClass(l+"exposed");K(scrolltopos.x,scrolltopos.y,c.scrollDuration,function(){Q.removeAttr("class").addClass(l+"arrow "+a.position);a.steps?(q=[],$.each(a.steps,function(a){q.push(a)}),D=function(b){a.onStep.call(h,m,b);var c=q.length;if(c)for(var d=0;d<c;d++)b>=q[d]&&(a.steps[q[d]].call(h,m),q.shift())}):D=function(b){a.onStep.call(h,m,b)};
c.showControls&&(0==b?n.find("a.prev").hide():b==L-1?n.find("a.next").hide():n.find("a.next, a.prev").show());a.onBeforeShow.call(h,m);p.clearQueue().stop().css({width:$.browser.opera?"1%":"0%"});f["min-width"]=e.width();if(a.expose&&m!=x){var d={position:m.css("position"),zIndex:m.css("zIndex")},g={position:"relative",zIndex:W+1};a.exposeElement=a.exposeElement||m;"object"===typeof a.expose&&$.each(a.expose,function(a,b){d[a]=m.css(a);g[a]=b});a.exposeElement.data("jTour",d).css(g).addClass(l+"exposed")}i?
i.overlayOpacity!=a.overlayOpacity&&s.fadeTo(2*a.delayIn,a.overlayOpacity):s.css({opacity:a.overlayOpacity});e.css(f).attr("class",R+" step-"+b)[a.animationIn](a.delayIn,function(){busy=!1;a.onShow.call(h,m);i=a;c.autoplay&&!w&&(manualskip=!1,p.stop().animate({width:"100%"},{duration:a.live*(1/c.speed),easing:"linear",step:D,complete:v}))})})},a.wait)})}else console&&m&&console.log('Element $("'+m.selector+"\") doesn't exist!"),L--,v()}function O(){i&&(i.onBeforeHide.call(h,i.element),p.clearQueue().stop(),
e.stop(),e[i.animationOut](i.delayOut,function(){A.empty();i.onHide.call(h,i.element);i=null}))}function r(b){c.speed=Math.max(0.1,c.speed+b);busy||(y(null,!1),z(!1))}function S(b,a,c,d){if("object"==typeof b)d=$.isFunction(a)?a:$.isFunction(c)?c:!1,c=!isNaN(a)?a:0,a=b.offset().top,b=b.offset().left;$.isFunction(c)&&(d=c,c=!1);var g={};null!=b&&(g.left=b);null!=a&&(g.top=a);e.animate(g,{duration:!1===c?0:c,complete:d&&function(){d.call(h)}})}function K(b,a,d,e){x.scrollTop()==a&&x.scrollLeft()==b&&
(d=1);var g={};$.each(c.axis,function(c,d){g[X[d]]="x"==d?b:a});x.animate(g,{duration:d||c.scrollDuration,complete:e&&function(){e.call(h)},queue:!0,easing:c.easing})}if(this===window)return new jTour(j,M);if("function"!==typeof jQuery||171>jQuery.fn.jquery.replace(/\./g,""))return alert("jQuery >=1.7.1 is required for jTour!"),!1;$.isArray(j)||$.error("tourdata must be a valid array");var e,A,p,Q,n,s,l="jTour_",x=$.browser.webkit?$("body"):$("html"),d=0,i,L=j.length,t,q,D,w=manualskip=busy=!1,H,
I,E,F,W=2E4,u=2.5,R,X={x:"scrollLeft",y:"scrollTop"},T={speed:1,axis:"xy",autostart:!1,autoplay:!0,pauseOnHover:!0,keyboardNav:!0,showProgress:!0,showControls:!0,scrollBack:!1,scrollDuration:300,easing:"swing",onStart:function(){},onStop:function(){},onPause:function(){},onPlay:function(){},onChange:function(){},onFinish:function(){},position:"c",live:"auto",offset:0,wait:0,expose:!1,overlayOpacity:0.2,delayIn:200,delayOut:100,animationIn:"fadeIn",animationOut:"fadeOut",onBeforeShow:function(){},
onShow:function(){},onBeforeHide:function(){},onHide:function(){},onStep:function(){}};"boolean"!=typeof rx&&(T={});var c=$.extend({},T,M),P={position:c.position,live:c.live,offset:c.offset,wait:c.wait,expose:c.expose,overlayOpacity:c.overlayOpacity,delayIn:c.delayIn,delayOut:c.delayOut,animationIn:c.animationIn,animationOut:c.animationOut,onBeforeShow:c.onBeforeShow,onShow:c.onShow,onBeforeHide:c.onBeforeHide,onHide:c.onHide,onStep:c.onStep,element:x,goTo:null},h={start:function(b){C(b)},restart:function(b){O();
d=b||0;C(b)},pause:function(b){manualskip=!0;y(b)},play:function(){manualskip=!1;z()},stop:function(){B()},next:function(){v()},prev:function(){G()},faster:function(b){r(b||0.25)},slower:function(b){r(b||-0.25)},moveTo:function(b,a,c,d){S(b,a,c,d)},offset:function(b,a,c,d){S("+="+b,"+="+a,c,d)},scroll:function(b,a,c,d){K(b,a,c,d)}};h.box=h.content=h.overlay=null;h.tourdata=j;(function(){e=$("<div/>",{"class":l+"box"}).hide().appendTo("body");Q=$("<div/>",{"class":l+"arrow"}).appendTo(e);A=$("<div/>",
{"class":l+"content"}).appendTo(e);$("<div/>",{"class":l+"progress"}).html('<div class="'+l+'progress_bar"></div>').appendTo(e);p=e.find("."+l+"progress_bar");c.showControls&&(n=$("<ul/>",{"class":l+"nav"}).html('<li><a class="'+l+'nav_btn prev" title="previous" data-role="prev">&nbsp;</a></li><li><a class="'+l+'nav_btn play" title="play" data-role="play">&nbsp;</a></li><li><a class="'+l+'nav_btn pause" title="pause" data-role="pause">&nbsp;</a></li><li><a class="'+l+'nav_btn stop" title="stop" data-role="stop">&nbsp;</a></li><li><a class="'+
l+'nav_btn next" title="next" data-role="next">&nbsp;</a></li><li><a class="'+l+'nav_btn slower" title="slower" data-role="slower">&nbsp;</a></li><li><a class="'+l+'nav_btn faster" title="faster" data-role="faster">&nbsp;</a></li>').appendTo(e).delegate("a","click.jTour",function(){manualskip=!0;switch($(this).data("role")){case "next":v();break;case "prev":G();break;case "slower":manualskip=!1;r(-0.25);break;case "faster":manualskip=!1;r(0.25);break;case "pause":y();break;case "play":manualskip=
!1;z();e.trigger("mouseleave");break;case "stop":B()}}),e.addClass("has-controls"));s=$("<div/>",{"class":l+"overlay"}).css("opacity",c.overlayOpacity).hide();c.showProgress||e.find("."+l+"progress").hide();!1!==c.overlayOpacity?s.appendTo("body").css("height",$(document).height()):s.remove();R=e.attr("class");$(window).unbind("resize.jTour").bind("resize.jTour",U).resize();h.box=e;h.content=A;h.overlay=s;c.axis=c.axis.split("");if(location.hash){var b=location.hash.split("=");if(b[0].match(/jTour/))return location.hash=
"",C(parseInt(b[1],10)),!1}c.autostart&&C()})();return h};