/*-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# Система управления контентом Eresus™
# Версия 2.00
# © 2004-2006, ProCreat Systems
# http://procreat.ru/
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# Скрипты интерфейса администратора
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-*/
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
  if (!confirm('Подверждаете удаление?')) objCaller.href = window.location;
}
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
function tableRow(objCaller, intCols, intState)
{
  var strColor, i;
  if (intState) strColor = '#cfc';
  else strColor = 'white';
  for (i=0; i<intCols; i++) objCaller.cells[i].style.backgroundColor = strColor;
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
  var hnd = window.open('$(httpRoot)core/dlg/BrowseFile.php?id='+id+'&root='+Folder, 'OpenFileDialog', 'dependent=yes,width=500,height=550,resizable=yes,menubar=no,directories=no,personalbar=no,scrollbars=no,status=no,titlebar=no,toolbar=no');
}
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
