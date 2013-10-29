webshims.register("form-shim-extend2",function(e,t,i,n,a,r){"use strict";var o=e([]),s=function(t){t=e(t);var i,a,r=o;return"radio"==t[0].type&&(a=t.prop("form"),i=t[0].name,r=i?a?e(a[i]):e(n.getElementsByName(i)).filter(function(){return!e.prop(this,"form")}):t,r=r.filter('[type="radio"]')),r},u=!("submitBubbles"in e.support)||e.support.submitBubbles,l=function(t){u||!t||"object"!=typeof t||t._submit_attached||(e.event.add(t,"submit._submit",function(e){e._submit_bubble=!0}),t._submit_attached=!0)};if(!u&&e.event.special.submit&&(e.event.special.submit.setup=function(){return e.nodeName(this,"form")?!1:(e.event.add(this,"click._submit keypress._submit",function(t){var i=t.target,n=e.nodeName(i,"input")||e.nodeName(i,"button")?e.prop(i,"form"):a;l(n)}),a)}),t.defineNodeNamesBooleanProperty(["input","textarea","select"],"required",{set:function(t){e(this).getShadowFocusElement().attr("aria-required",!!t+"")},initAttr:Modernizr.localstorage}),t.reflectProperties(["input"],["pattern"]),!("maxLength"in n.createElement("textarea"))){var c=function(){var t,i=0,n=e([]),a=1e9,r=function(){var e=n.prop("value"),t=e.length;t>i&&t>a&&(t=Math.max(i,a),n.prop("value",e.substr(0,t))),i=t},o=function(){clearTimeout(t),n.unbind(".maxlengthconstraint")};return function(s,u){o(),u>-1&&(a=u,i=e.prop(s,"value").length,n=e(s),n.on({"keydown.maxlengthconstraint keypress.maxlengthconstraint paste.maxlengthconstraint cut.maxlengthconstraint":function(){setTimeout(r,0)},"keyup.maxlengthconstraint":r,"blur.maxlengthconstraint":o}),t=setInterval(r,200))}}();c.update=function(t,i){e(t).is(":focus")&&(i||(i=e.prop(t,"maxlength")),c(t,i))},e(n).on("focusin",function(t){var i;"TEXTAREA"==t.target.nodeName&&(i=e.prop(t.target,"maxlength"))>-1&&c(t.target,i)}),t.defineNodeNameProperty("textarea","maxlength",{attr:{set:function(e){this.setAttribute("maxlength",""+e),c.update(this)},get:function(){var e=this.getAttribute("maxlength");return null==e?a:e}},prop:{set:function(e){if(isNumber(e)){if(0>e)throw"INDEX_SIZE_ERR";return e=parseInt(e,10),this.setAttribute("maxlength",e),c.update(this,e),a}this.setAttribute("maxlength","0"),c.update(this,0)},get:function(){var e=this.getAttribute("maxlength");return isNumber(e)&&e>=0?parseInt(e,10):-1}}}),t.defineNodeNameProperty("textarea","maxLength",{prop:{set:function(t){e.prop(this,"maxlength",t)},get:function(){return e.prop(this,"maxlength")}}})}e.support.getSetAttribute||null!=e("<form novalidate></form>").attr("novalidate")||t.defineNodeNameProperty("form","novalidate",{attr:{set:function(e){this.setAttribute("novalidate",""+e)},get:function(){var e=this.getAttribute("novalidate");return null==e?a:e}}}),Modernizr.formattribute!==!1&&Modernizr.fieldsetdisabled||function(){(function(t,i){e.prop=function(a,r,o){var s;return a&&1==a.nodeType&&o===i&&e.nodeName(a,"form")&&a.id&&(s=n.getElementsByName(r),s&&s.length||(s=n.getElementById(r)),s&&(s=e(s).filter(function(){return e.prop(this,"form")==a}).get(),s.length))?1==s.length?s[0]:s:t.apply(this,arguments)}})(e.prop,a);var i=function(t){var i=e.data(t,"webshimsAddedElements");i&&(i.remove(),e.removeData(t,"webshimsAddedElements"))};if(Modernizr.formattribute||(t.defineNodeNamesProperty(["input","textarea","select","button","fieldset"],"form",{prop:{get:function(){var i=t.contentAttr(this,"form");return i&&(i=n.getElementById(i),i&&!e.nodeName(i,"form")&&(i=null)),i||this.form},writeable:!1}}),t.defineNodeNamesProperty(["form"],"elements",{prop:{get:function(){var t=this.id,i=e.makeArray(this.elements);return t&&(i=e(i).add('input[form="'+t+'"], select[form="'+t+'"], textarea[form="'+t+'"], button[form="'+t+'"], fieldset[form="'+t+'"]').not(".webshims-visual-hide > *").get()),i},writeable:!1}}),e(function(){var t=function(e){e.stopPropagation()},a={image:1,submit:1};e(n).on("submit",function(t){if(!t.isDefaultPrevented()){var n,a=t.target,r=a.id;r&&(i(a),n=e('input[form="'+r+'"], select[form="'+r+'"], textarea[form="'+r+'"]').filter(function(){return!this.disabled&&this.name&&this.form!=a}).clone(),n.length&&(e.data(a,"webshimsAddedElements",e('<div class="webshims-visual-hide" />').append(n).appendTo(a)),setTimeout(function(){i(a)},9)),n=null)}}),e(n).on("click",function(i){if(a[i.target.type]&&!i.isDefaultPrevented()&&e(i.target).is("input[form], button[form]")){var n,r=e.prop(i.target,"form"),o=i.target.form;r&&r!=o&&(n=e(i.target).clone().removeAttr("form").addClass("webshims-visual-hide").on("click",t).appendTo(r),o&&i.preventDefault(),l(r),n.trigger("click"),setTimeout(function(){n.remove(),n=null},9))}})})),Modernizr.fieldsetdisabled||t.defineNodeNamesProperty(["fieldset"],"elements",{prop:{get:function(){return e("input, select, textarea, button, fieldset",this).get()||[]},writeable:!1}}),!e.fn.finish&&1.9>parseFloat(e.fn.jquery,10)){var r=/\r?\n/g,o=/^(?:color|date|datetime|datetime-local|email|hidden|month|number|password|range|search|tel|text|time|url|week)$/i,s=/^(?:select|textarea)/i;e.fn.serializeArray=function(){return this.map(function(){var t=e.prop(this,"elements");return t?e.makeArray(t):this}).filter(function(){return this.name&&!this.disabled&&(this.checked||s.test(this.nodeName)||o.test(this.type))}).map(function(t,i){var n=e(this).val();return null==n?null:e.isArray(n)?e.map(n,function(e){return{name:i.name,value:e.replace(r,"\r\n")}}):{name:i.name,value:n.replace(r,"\r\n")}}).get()}}}(),null==e("<input />").prop("labels")&&t.defineNodeNamesProperty("button, input, keygen, meter, output, progress, select, textarea","labels",{prop:{get:function(){if("hidden"==this.type)return null;var t=this.id,i=e(this).closest("label").filter(function(){var e=this.attributes["for"]||{};return!e.specified||e.value==t});return t&&(i=i.add('label[for="'+t+'"]')),i.get()},writeable:!1}}),"value"in n.createElement("progress")||function(){var i=parseInt("NaN",10),n=function(t){var i;i=e.prop(t,"position"),e.attr(t,"data-position",i),e("> span",t).css({width:(0>i?100:100*i)+"%"})},a={position:{prop:{get:function(){var t,a=this.getAttribute("value"),r=-1;return a=a?1*a:i,isNaN(a)?n.isInChange&&e(this).removeAttr("aria-valuenow"):(t=e.prop(this,"max"),r=Math.max(Math.min(a/t,1),0),n.isInChange&&(e.attr(this,"aria-valuenow",100*r),"max"==n.isInChange&&e.attr(this,"aria-valuemax",t))),r},writeable:!1}}};e.each({value:0,max:1},function(i,r){var o="value"==i&&!e.fn.finish;a[i]={attr:{set:function(e){var t=a[i].attr._supset.call(this,e);return n.isInChange=i,n(this),n.isInChange=!1,t}},removeAttr:{value:function(){if(this.removeAttribute(i),o)try{delete this.value}catch(e){}n.isInChange=i,n(this),n.isInChange=!1}},prop:{get:function(){var t=1*a[i].attr.get.call(this);return 0>t||isNaN(t)?t=r:"value"==i?t=Math.min(t,e.prop(this,"max")):0===t&&(t=r),t},set:function(e){return e=1*e,isNaN(e)&&t.error("Floating-point value is not finite."),a[i].attr.set.call(this,e)}}}}),t.createElement("progress",function(){var i=e(this).attr({role:"progressbar","aria-valuemin":"0"}).html('<span class="progress-value" />').jProp("labels").map(function(){return t.getID(this)}).get();i.length?e.attr(this,"aria-labelledby",i.join(" ")):t.info("you should use label elements for your prgogress elements"),n.isInChange="max",n(this),n.isInChange=!1},a)}();try{n.querySelector(":checked")}catch(p){(function(){e("html").addClass("no-csschecked");var i={radio:1,checkbox:1},a=function(){var t,i,n,a=this.options||[];for(t=0,i=a.length;i>t;t++)n=e(a[t]),n[e.prop(a[t],"selected")?"addClass":"removeClass"]("prop-checked")},r=function(){var t,i=e.prop(this,"checked")?"addClass":"removeClass",n=this.className||"";-1==n.indexOf("prop-checked")==("addClass"==i)&&(e(this)[i]("prop-checked"),(t=this.parentNode)&&(t.className=t.className))};t.onNodeNamesPropertyModify("select","value",a),t.onNodeNamesPropertyModify("select","selectedIndex",a),t.onNodeNamesPropertyModify("option","selected",function(){e(this).closest("select").each(a)}),t.onNodeNamesPropertyModify("input","checked",function(t,n){var a=this.type;"radio"==a&&n?s(this).each(r):i[a]&&e(this).each(r)}),e(n).on("change",function(t){i[t.target.type]?"radio"==t.target.type?s(t.target).each(r):e(t.target)[e.prop(t.target,"checked")?"addClass":"removeClass"]("prop-checked"):"select"==t.target.nodeName.toLowerCase()&&e(t.target).each(a)}),t.addReady(function(t,n){e("option, input",t).add(n.filter("option, input")).each(function(){var t;i[this.type]?t="checked":"option"==this.nodeName.toLowerCase()&&(t="selected"),t&&e(this)[e.prop(this,t)?"addClass":"removeClass"]("prop-checked")})})})()}(function(){var n;if(Modernizr.textareaPlaceholder=!!("placeholder"in e("<textarea />")[0]),Modernizr.input.placeholder&&r.overridePlaceholder&&(n=!0),Modernizr.input.placeholder&&Modernizr.textareaPlaceholder&&!n)return function(){var t=navigator.userAgent;-1!=t.indexOf("Mobile")&&-1!=t.indexOf("Safari")&&e(i).on("orientationchange",function(){var t,i=function(e,t){return t},n=function(){e("input[placeholder], textarea[placeholder]").attr("placeholder",i)};return function(){clearTimeout(t),t=setTimeout(n,9)}}())}(),a;var o="over"==t.cfg.forms.placeholderType,s=t.cfg.forms.responsivePlaceholder,u=["textarea"];t.debug!==!1,(!Modernizr.input.placeholder||n)&&u.push("input");var l=function(e){try{if(e.setSelectionRange)return e.setSelectionRange(0,0),!0;if(e.createTextRange){var t=e.createTextRange();return t.collapse(!0),t.moveEnd("character",0),t.moveStart("character",0),t.select(),!0}}catch(i){}},c=function(t,i,n,r){if(n===!1&&(n=e.prop(t,"value")),o||"password"==t.type){if(!n&&r)return e(t).off(".placeholderremove").on({"keydown.placeholderremove keypress.placeholderremove paste.placeholderremove input.placeholderremove":function(n){(!n||17!=n.keyCode&&16!=n.keyCode)&&(i.box.removeClass("placeholder-visible"),e(t).unbind(".placeholderremove"))},"blur.placeholderremove":function(){e(t).unbind(".placeholderremove")}}),a}else{if(!n&&r&&l(t)){var s=setTimeout(function(){l(t)},9);return e(t).off(".placeholderremove").on({"keydown.placeholderremove keypress.placeholderremove paste.placeholderremove input.placeholderremove":function(n){(!n||17!=n.keyCode&&16!=n.keyCode)&&(t.value=e.prop(t,"value"),i.box.removeClass("placeholder-visible"),clearTimeout(s),e(t).unbind(".placeholderremove"))},"mousedown.placeholderremove drag.placeholderremove select.placeholderremove":function(){l(t),clearTimeout(s),s=setTimeout(function(){l(t)},9)},"blur.placeholderremove":function(){clearTimeout(s),e(t).unbind(".placeholderremove")}}),a}r||n||!t.value||(t.value=n)}i.box.removeClass("placeholder-visible")},p=function(t,i,n){n===!1&&(n=e.prop(t,"placeholder")),o||"password"==t.type||(t.value=n),i.box.addClass("placeholder-visible")},d=function(t,i,n,r,s){if(r||(r=e.data(t,"placeHolder"))){var u=e(t).hasClass("placeholder-visible");return n===!1&&(n=e.attr(t,"placeholder")||""),e(t).unbind(".placeholderremove"),i===!1&&(i=e.prop(t,"value")),i||"focus"!=s&&(s||!e(t).is(":focus"))?i?(c(t,r,i),a):(n&&!i?p(t,r,n):c(t,r,i),a):(("password"==t.type||o||u)&&c(t,r,"",!0),a)}},f=function(t){return t=e(t),!!(t.prop("title")||t.attr("aria-labelledby")||t.attr("aria-label")||t.jProp("labels").length)},h=function(t){return t=e(t),e(f(t)?'<span class="placeholder-text"></span>':'<label for="'+t.prop("id")+'" class="placeholder-text"></label>')},m=function(){var n={text:1,search:1,url:1,email:1,password:1,tel:1,number:1};return t.modules["form-number-date-ui"].loaded&&delete n.number,{create:function(t){var n,a,r=e.data(t,"placeHolder");if(r)return r;if(r=e.data(t,"placeHolder",{}),e(t).on("focus.placeholder blur.placeholder",function(e){d(this,!1,!1,r,e.type),r.box["focus"==e.type?"addClass":"removeClass"]("placeholder-focused")}),(n=e.prop(t,"form"))&&e(t).onWSOff("reset.placeholder",function(e){setTimeout(function(){d(t,!1,!1,r,e.type)},0)},!1,n),"password"==t.type||o)r.text=h(t),s||e(t).is(".responsive-width")||-1!=(t.currentStyle||{width:""}).width.indexOf("%")?(a=!0,r.box=r.text):r.box=e(t).wrap('<span class="placeholder-box placeholder-box-'+(t.nodeName||"").toLowerCase()+" placeholder-box-"+e.css(t,"float")+'" />').parent(),r.text.insertAfter(t).on("mousedown.placeholder",function(){d(this,!1,!1,r,"focus");try{setTimeout(function(){t.focus()},0)}catch(e){}return!1}),e.each(["lineHeight","fontSize","fontFamily","fontWeight"],function(i,n){var a=e.css(t,n);r.text.css(n)!=a&&r.text.css(n,a)}),e.each(["Left","Top"],function(i,n){var a=(parseInt(e.css(t,"padding"+n),10)||0)+Math.max(parseInt(e.css(t,"margin"+n),10)||0,0)+(parseInt(e.css(t,"border"+n+"Width"),10)||0);r.text.css("padding"+n,a)}),e(t).onWSOff("updateshadowdom",function(){var i,n;((n=t.offsetWidth)||(i=t.offsetHeight))&&r.text.css({width:n,height:i}).css(e(t).position())},!0);else{var u=function(i){e(t).hasClass("placeholder-visible")&&(c(t,r,""),setTimeout(function(){(!i||"submit"!=i.type||i.isDefaultPrevented())&&d(t,!1,!1,r)},9))};e(t).onWSOff("beforeunload",u,!1,i),r.box=e(t),n&&e(t).onWSOff("submit",u,!1,n)}return r},update:function(i,r){var o=(e.attr(i,"type")||e.prop(i,"type")||"").toLowerCase();if(!n[o]&&!e.nodeName(i,"textarea"))return t.warn('placeholder not allowed on input[type="'+o+'"], but it is a good fallback :-)'),a;var s=m.create(i);s.text&&s.text.text(r),d(i,!1,r,s)}}}();e.webshims.publicMethods={pHolder:m},u.forEach(function(e){t.defineNodeNameProperty(e,"placeholder",{attr:{set:function(e){var i=this;n?(t.data(i,"bustedPlaceholder",e),i.placeholder=""):t.contentAttr(i,"placeholder",e),m.update(i,e)},get:function(){var e;return n&&(e=t.data(this,"bustedPlaceholder")),e||t.contentAttr(this,"placeholder")}},reflect:!0,initAttr:!0})}),u.forEach(function(i){var a,r={};["attr","prop"].forEach(function(i){r[i]={set:function(r){var o,s=this;n&&(o=t.data(s,"bustedPlaceholder")),o||(o=t.contentAttr(s,"placeholder")),e.removeData(s,"cachedValidity");var u=a[i]._supset.call(s,r);return o&&"value"in s&&d(s,r,o),u},get:function(){var t=this;return e(t).hasClass("placeholder-visible")?"":a[i]._supget.call(t)}}}),a=t.defineNodeNameProperty(i,"value",r)})})(),function(){var i=n;if(!("value"in n.createElement("output"))){t.defineNodeNameProperty("output","value",{prop:{set:function(t){var i=e.data(this,"outputShim");i||(i=a(this)),i(t)},get:function(){return t.contentAttr(this,"value")||e(this).text()||""}}}),t.onNodeNamesPropertyModify("input","value",function(t,i,n){if("removeAttr"!=n){var a=e.data(this,"outputShim");a&&a(t)}});var a=function(a){if(!a.getAttribute("aria-live")){a=e(a);var r=(a.text()||"").trim(),o=a.prop("id"),s=a.attr("for"),u=e('<input class="output-shim" type="text" disabled name="'+(a.attr("name")||"")+'" value="'+r+'" style="display: none !important;" />').insertAfter(a);u[0].form||i;var l=function(e){u[0].value=e,e=u[0].value,a.text(e),t.contentAttr(a[0],"value",e)};return a[0].defaultValue=r,t.contentAttr(a[0],"value",r),a.attr({"aria-live":"polite"}),o&&(u.attr("id",o),a.attr("aria-labelledby",a.jProp("labels").map(function(){return t.getID(this)}).get().join(" "))),s&&(o=t.getID(a),s.split(" ").forEach(function(e){e=n.getElementById(e),e&&e.setAttribute("aria-controls",o)})),a.data("outputShim",l),u.data("outputShim",l),l}};t.addReady(function(t,i){e("output",t).add(i.filter("output")).each(function(){a(this)})}),function(){var n={updateInput:1,input:1},a={radio:1,checkbox:1,submit:1,button:1,image:1,reset:1,file:1,color:1},r=function(e){var i,a,r=e.prop("value"),o=function(i){if(e){var a=e.prop("value");a!==r&&(r=a,i&&n[i.type]||t.triggerInlineForm&&t.triggerInlineForm(e[0],"input"))}},s=function(){clearTimeout(a),a=setTimeout(o,9)},u=function(){e.unbind("focusout",u).unbind("keyup keypress keydown paste cut",s).unbind("input change updateInput",o),clearInterval(i),setTimeout(function(){o(),e=null},1)};clearInterval(i),i=setInterval(o,200),s(),e.on({"keyup keypress keydown paste cut":s,focusout:u,"input updateInput change":o})};e(i).on("focusin",function(i){!i.target||i.target.readOnly||i.target.disabled||"input"!=(i.target.nodeName||"").toLowerCase()||a[i.target.type]||(t.data(i.target,"implemented")||{}).inputwidgets||r(e(i.target))})}()}}()});