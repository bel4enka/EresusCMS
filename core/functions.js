/*-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# Система управления контентом Eresus™
# Версия 2.10
# © 2004-2007, ProCreat Systems
# © 2007, Eresus Group
# http://eresus.ru/
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# Скрипты интерфейса администратора
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-*/
var isIE = (navigator.userAgent.toLowerCase().indexOf('msie') != -1) && (navigator.userAgent.toLowerCase().indexOf('opera') == -1);
var HttpRequest = null;
var BrowseFileLast = '';

//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
function replaceMacros(sURL)
{
  var macros = new Array();
  macros['httpRoot'] = '$(httpRoot)';
  macros['httpHost'] = '$(httpHost)';
  macros['httpPath'] = '$(httpPath)';
  macros['styleRoot'] = '$(styleRoot)';
  macros['dataRoot'] = '$(dataRoot)';

  function __replace(sMatch, sMacros)
  {
    return macros[sMacros];
  }
  
  sURL = sURL.replace(/\$\(([^\)]+)\)/, __replace);
  return sURL;
}
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
function pageLeft() 
{
  return isIE ? (document.body.scrollLeft?document.body.scrollLeft:document.documentElement.scrollLeft) : window.pageXOffset;
}
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
function pageTop() 
{
  return isIE ? (document.body.scrollTop?document.body.scrollTop:document.documentElement.scrollTop) : window.pageYOffset;
}
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
function toggleMenuBrunch(Id)
{
  var brunch = document.getElementById('brunch'+Id);
  var root = document.getElementById('root'+Id);
  if (brunch.style.display == 'none') {
    brunch.style.display = 'block';
    root.src = "$(httpRoot)core/img/br_opened.gif";
  } else {
    brunch.style.display = 'none';
    root.src = "$(httpRoot)core/img/br_closed.gif";
  }
  return false;
}
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
function askdel(objCaller)
{
  return confirm('Подверждаете удаление?');
}
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
function formApplyClick(strForm)
{
  var objForm = document.forms[strForm];
  objForm.submitURL.value = document.URL;
}
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
function BrowseFileDialog(id, Folder)
{
  var hnd = window.open('$(httpRoot)core/dlg/BrowseFile.php?id='+id+'&root='+BrowseFileLast, 'OpenFileDialog', 'dependent=yes,width=500,height=550,resizable=yes,menubar=no,directories=no,personalbar=no,scrollbars=no,status=no,titlebar=no,toolbar=no');
}
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
function SendRequest(url, handler) 
{
  // branch for native XMLHttpRequest object
  if (window.XMLHttpRequest) {
    HttpRequest = new XMLHttpRequest();
    HttpRequest.onreadystatechange = handler;
    HttpRequest.open('GET', url, true);
    HttpRequest.send(null);
  // branch for IE/Windows ActiveX version
  } else if (window.ActiveXObject) {
    HttpRequest = new ActiveXObject('Microsoft.XMLHTTP');
    if (HttpRequest) {
      HttpRequest.onreadystatechange = handler;
      HttpRequest.open('GET', url, true);
      HttpRequest.send();
    }
  }
}        
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
