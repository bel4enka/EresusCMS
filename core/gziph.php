<?
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# Система управления контентом Eresus™
# Версия 2.07
# © 2004-2007, ProCreat Systems
# http://procreat.ru/
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# Механизм GZIP-сжатия вывода
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
error_reporting(E_NONE);

#----------------------------------------------------------------------------------------------------------------------
function ContentLength($contents) {
  Header("Content-Length: ".strlen($contents));
  return $contents;
}
#----------------------------------------------------------------------------------------------------------------------
$filesRoot = __FILE__; 
$filesRoot = str_replace('\\','/',$filesRoot);
$filesRoot = substr($filesRoot, 0, strpos($filesRoot, '/core/')+1);
$httpPath = substr($filesRoot, strpos($filesRoot, $_SERVER['DOCUMENT_ROOT'])+strlen($_SERVER['DOCUMENT_ROOT'])-($_SERVER['DOCUMENT_ROOT'][strlen($_SERVER['DOCUMENT_ROOT'])-1] == '/'?1:0));
if ($filesRoot[1] == ':') $filesRoot = substr($filesRoot, 2);
$httpRoot = 'http://'.$_SERVER['HTTP_HOST'].$httpPath;
$styleRoot = $httpRoot.'style/';
$dataRoot = $httpRoot.'data/';
$dataFiles = $filesRoot.'data/';

ob_start('ContentLength');
ob_start('ob_gzhandler');
$type = isset($_REQUEST['type'])?$_REQUEST['type']:'text/plain';
$file = isset($_REQUEST['file'])?$_REQUEST['file']:'';
if (empty($file)) exit;
$filename = AddSlashes($filesRoot.$file);
Header('Content-type: '.$type.(isset($_GET['charset'])?'; charset='.$_GET['charset']:''));
Header('Cache-Control: '.(isset($_GET['cache'])?$_GET['cache']:'public'));
Header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($filename)) . ' GMT');
#Header("Expires: " .gmdate("D, d M Y H:i:s", time() + 3600) . " GMT");
$text = file_get_contents($filename);
$text = str_replace(array(
  '$(httpHost)',
  '$(httpPath)',
  '$(httpRoot)',
  '$(styleRoot)',
  '$(dataRoot)',
  '$(dataFiles)',
), array(
  $_SERVER['HTTP_HOST'],
  $httpPath,
  $httpRoot,
  $styleRoot,
  $dataRoot,
  $dataFiles,
), $text);
echo $text;
ob_end_flush();
?>