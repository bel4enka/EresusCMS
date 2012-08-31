/*-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# Система управления контентом Eresus™
# Версия 2.00
# © 2004-2006, ProCreat Systems
# http://procreat.ru/
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# Скрипты пользовательской части файлового менеджера
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-*/
var objRowSel = null;
var httpRoot;
var slctPanel;

function filesInit(root, panel)
{
  httpRoot = root;
  slctPanel = panel;
  var obj = document.getElementById(panel+'Panel');
  rowSelect(iBrowser['Engine']=='IE'?obj.children[0].children[2]:obj.childNodes[1].childNodes[4]);
}

function setPanel(url)
{
  url = url.toString();
  if (url.indexOf('&sp=') != -1) url = url.replace(/sp=([lr])/,'sp='+slctPanel);
  else url += '&sp='+slctPanel;
  return url;
}

function buttonOver(objButton)
{
  objButton.bgColor='dddddd';
  objButton.style.border = 'solid 1px #777';
}

function buttonOut(objButton){
  objButton.bgColor='silver';
  objButton.style.border = 'solid 1px silver';
}

function keyboardEvents()
{
  alert('test');
}

function getCurrentFolder()
{
  var folder = objRowSel.parentNode.childNodes[0].childNodes[0].innerHTML.substr(2);
  if (folder.length) folder += '/';
  return folder;
}

function rowSelect(objRow)
{
  if (objRowSel != null) {
    objRowSel.bgColor ='#ffffff';
    objRowSel.style.color = '#000050';
  }
  objRow.bgColor ='#4682B4';
  objRow.style.color = 'white';
  objRowSel = objRow;
  var objStatus = document.getElementById('SelFileName');
  objStatus.value = httpRoot+getCurrentFolder()+objRowSel.childNodes[1].innerHTML;
  slctPanel = objRowSel.parentNode.parentNode.id.substr(0,1);
  document.upload.action = document.upload.action.replace(/sp=\w/, 'sp='+slctPanel);
}

function Copy(strControlName)
{
  var ua = navigator.userAgent.toLowerCase();
  var isIE = ((ua.indexOf("msie") != -1) && (ua.indexOf("opera") == -1) && (ua.indexOf("webtv") == -1)); 
  if (isIE) {
    var objControl = document.getElementById(strControlName);
    objControl.createTextRange().execCommand("Copy");
    objControl.focus();
  } else alert('This function avaliable only in Internet Explorer!');
}

function filesCD(url)
{
  window.location = setPanel(url);
}

function filesMkDir()
{
  var folder = prompt('Folder name','');
  if (folder != undefined && folder.length) window.location = setPanel(window.location)+'&mkdir='+folder;
}

function filesCopy()
{
  if (objRowSel != null) {
    var filename = objRowSel.childNodes[1].innerHTML;
    if (filename.substr(-2) != '..') {
      var obj = document.getElementById((slctPanel=='l'?'r':'l')+'Panel');
      obj = (iBrowser['Engine']=='IE')?obj.children[0].children:obj.childNodes[1].childNodes;
      for (var i=4; i < obj.length; i+=2) if (obj[i].childNodes[1].innerHTML == filename) 
        if (confirm('File "'+filename+'" already exists. Overwrite?')) break;
        else return;
      window.location = setPanel(window.location)+'&copyfile='+filename;
    }
  }
}

function filesMove()
{
  if (objRowSel != null) {
    var filename = objRowSel.childNodes[1].innerHTML;
    if (filename.substr(-2) != '..') {
      var obj = document.getElementById((slctPanel=='l'?'r':'l')+'Panel');
      obj = (iBrowser['Engine']=='IE')?obj.children[0].children:obj.childNodes[1].childNodes;
      for (var i=4; i < obj.length; i+=2) if (obj[i].childNodes[1].innerHTML == filename) 
        if (confirm('File "'+filename+'" already exists. Overwrite?')) break;
        else return;
      window.location = setPanel(window.location)+'&movefile='+filename;
    }
  }
}

function filesDelete()
{
  if (objRowSel != null) {
    var filename = objRowSel.childNodes[1].innerHTML;
    if ((filename.substr(-2) != '..') && confirm('Are you sure, you want to delete "'+filename+'"?')) {
      window.location = setPanel(window.location)+'&delete='+filename;
    }
  }
}

