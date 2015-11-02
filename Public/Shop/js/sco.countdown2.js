/* ==========================================================
 * sco.countdown.js
 * http://github.com/terebentina/sco.js
 * ==========================================================
 * Copyright 2013 Dan Caragea.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */

/*jshint laxcomma:true, sub:true, browser:true, jquery:true, devel:true */

;(function($, undefined) {
    "use strict";

    var pluginName = 'scojs_countdown';

    function doit($elem, data, until) {
        var str = ''
            ,started = false
            ,left = {d: 0, h: 0, m: 0, s: 0}
            ,js_current = Math.round((new Date()).getTime() / 1000)
            ;

        left.s = until - js_current;
        if (left.s <= -2) {
            return;
        }


        if (Math.floor(left.s / 86400) > 0) {
            left.d = Math.floor(left.s / 86400);
            left.s = left.s % 86400;
            left.d=left.d>9?left.d:("0"+left.d);
            str += '<font class="font_time_style">'+left.d+"</font>" + data.strings.d;
            started = true;
        }
        if (Math.floor(left.s / 3600) > 0) {
            left.h = Math.floor(left.s / 3600);
            left.s = left.s % 3600;
            started = true;
        }
        if (started) {
            left.h=left.h>9?left.h:("0"+left.h);
            str += '<font class="font_time_style">' + left.h+"</font>" + data.strings.h;
            started = true;
        }
        if (Math.floor(left.s / 60) > 0) {
            left.m = Math.floor(left.s / 60);
            left.s = left.s % 60;
            started = true;
        }
        if (started) {
            left.m=left.m>9?left.m:("0"+left.m);
            str += '<font class="font_time_style">' + left.m+"</font>" + data.strings.m;
            started = true;
        }
        if (left.s > 0 || left.s > -1) {
            started = true;
        }

        if (started) {
            left.s=left.s>9?left.s:("0"+left.s);
            str += '<font class="font_time_style">' + left.s+"</font>" + data.strings.s;
            started = true;
        }
        //alert(str);
        $elem.html(str);
        setTimeout(function() {doit($elem, data, until);}, 500);
    }

    $.fn[pluginName] = function(options) {
        var $this = $(this)
            ,data = $this.data()
            ,js_current
            ;

        data = $.extend({}, $.fn[pluginName].defaults, options, data);

        if (!data.until) {
            return;
        }

        js_current = Math.round((new Date()).getTime() / 1000);
        if (!data.current) {
            data.current = js_current;
        }

        data.until -= (js_current - data.current);

        doit($this, data, data.until);
    };

    $.fn[pluginName].defaults = {
        strings: {d: '天', h: '时', m: '分', s: '秒'}
    };
})(jQuery);
