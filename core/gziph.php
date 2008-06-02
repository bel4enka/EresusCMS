<?
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# Система управления контентом Eresus™
# Версия 2.05
# © 2004-2006, ProCreat Systems
# http://procreat.ru/
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# Механизм GZIP-сжатия вывода
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
error_reporting(E_NONE);

$filesRoot = __FILE__; 
$filesRoot = str_replace('\\','/',$filesRoot);
$filesRoot = substr($filesRoot, 0, strpos($filesRoot, '/core/')+1);
$httpPath = substr($filesRoot, strpos($filesRoot, $_SERVER['DOCUMENT_ROOT'])+strlen($_SERVER['DOCUMENT_ROOT'])-($_SERVER['DOCUMENT_ROOT'][strlen($_SERVER['DOCUMENT_ROOT'])-1] == '/'?1:0));
if ($filesRoot[1] == ':') $filesRoot = substr($filesRoot, 2);
$httpRoot = 'http://'.$_SERVER['HTTP_HOST'].$httpPath;
$styleRoot = $httpRoot.'style/';
$dataRoot = $httpRoot.'data/';
$dataFiles = $filesRoot.'data/';

ob_start('ob_gzhandler');
$type = isset($_REQUEST['type'])?$_REQUEST['type']:'text/plain';
$file = isset($_REQUEST['file'])?$_REQUEST['file']:'';
if (empty($file)) exit;
Header('Content-type: '.$type);
Header('Cache-Control: '.(isset($_GET['cache'])?$_GET['cache']:'no-cache'));
Header("Expires: " .gmdate("D, d M Y H:i:s", time() + 3600) . " GMT");
$filename = AddSlashes($filesRoot.$file);
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