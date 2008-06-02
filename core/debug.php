<?
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# ������� ���������� ��������� Eresus�
# ������ 2.05
# � 2004-2006, ProCreat Systems
# http://procreat.ru/
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function plaintext()
{
  if (!headers_sent()) header("Content-type: text/plain");
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function dbglog($msg)
{
  $hnd = fopen(DEBUG_LOG_FILENAME, 'a');
  fputs($hnd, "[".date('Y-m-d H:i:s').'] - '.$msg."\n");
  fclose($hnd);
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function renderTemplate($template)
{
  $template = StripSlashes(file_get_contents(filesRoot.'templates/'.$template.'.tmpl'));

    $template = str_replace(
      array(
        '$(httpHost)',
        '$(httpPath)',
        '$(httpRoot)',
        '$(styleRoot)',
        '$(dataRoot)',
        
        '$(siteName)',
        '$(siteTitle)',
        '$(siteKeywords)',
        '$(siteDescription)',
      ),
      array(
        httpHost, 
        httpPath, 
        httpRoot, 
        styleRoot,
        dataRoot,
        
        siteName,
        siteTitle,
        siteKeywords,
        siteDescription,
      ),
      $template
    );

  echo $template;
  exit;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function callStack()
{
  $callstack = debug_backtrace();
  $result = '<div style="font-weight: normal; text-align: left;">';
  for ($i = 1; $i < count($callstack); $i++) if ($callstack[$i]['function'] != 'errorhandler') {
    $result .= 'File <b>'.$callstack[$i]['file'].'</b> line <b>'.$callstack[$i]['line'].'</b>:<br />';
    $args = '';
    if (isset($callstack[$i]['args']) && count($callstack[$i]['args'])) {
      $args = array();
      foreach($callstack[$i]['args'] as $arg) {
        ob_start();
        var_dump($arg);
        $args[] = htmlentities(ob_get_contents());
        ob_end_clean();
      }
      $args = implode(', ', $args);
    }
    
    $result .= 'Call <b>'.(empty($callstack[$i]['class'])?'':$callstack[$i]['class']).(empty($callstack[$i]['type'])?'':$callstack[$i]['type']).$callstack[$i]['function'].'</b>('.$args.')<br /><br />';
  }
  $result .='</div>';
  return $result;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
if (isset($_GET['template'])) renderTemplate($_GET['template']);
?>