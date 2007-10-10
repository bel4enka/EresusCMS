/**
 * Скрипт определения браузера
 * 
 * Система управления контентом Eresus™ 2
 * © 2007, Eresus Group, http://eresus.ru/
 * 
 * Скрипт написан на основе
 * http://mozilla.org/docs/web-developer/sniffer/browser_type.html
 * TODO: Дописать на сонове Мозилловского скрипта
 *  
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 */

var UA = {ID: navigator.userAgent.toLowerCase()}

// *** BROWSER VERSION ***

// Note: On IE5, these return 4, so use is_ie5up to detect IE5.
UA.major = parseInt(navigator.appVersion);
UA.minor = parseFloat(navigator.appVersion);

// Microsoft Internet Explorer
UA.MSIE   = (UA.ID.indexOf('msie') != -1) && (UA.ID.indexOf('opera') == -1) && (UA.ID.indexOf('webtv') == -1);
// Gecko (Mozilla, FireFox, etc)
UA.Gecko  = UA.ID.indexOf('gecko') != -1;
// Opera
UA.Opera  = UA.ID.indexOf('opera') != -1;
// Safari
UA.Safari = UA.ID.indexOf('safari') != -1;
// Konqueror
UA.ID.Konqueror = UA.ID.indexOf('konqueror') != -1;

