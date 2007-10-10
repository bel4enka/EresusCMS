/**
 * AJAX-запросы
 * 
 * Система управления контентом Eresus™ 2
 * © 2007, Eresus Group, http://eresus.ru/
 * 
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 */

/**
 * Выполняет AJAX-запрос
 * 
 * @param string   url
 * @param function handler
 * 
 * @return XMLHttpRequest
 */
function ajaxCall(url, handler) 
{
	var req;

  if (window.XMLHttpRequest) req = new XMLHttpRequest();
	else if (window.ActiveXObject) req = new ActiveXObject('Microsoft.XMLHTTP');

  req.onreadystatechange = handler;
  req.open('GET', url, true);
  req.send(null);
	return req;
}        
