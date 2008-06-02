<?
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# Система управления контентом Eresus™
# Версия 2.07
# © 2004-2007, ProCreat Systems
# http://procreat.ru/
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# Ядро интерактивной системы управления сайтом
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
define('CMSNAME', 'Eresus'); # Название системы
define('CMSVERSION', '2.07'); # Версия системы
define('CMSLINK', 'http://procreat.ru/'); # Веб-сайт

define('KERNELNAME', 'ERESUS'); # Имя ядра
define('KERNELDATE', '12.01.07'); # Дата обновления ядра

# Уровни доступа
define('ROOT',   1); # Главный администратор
define('ADMIN',  2); # Администратор
define('EDITOR', 3); # Редактор
define('USER',   4); # Пользователь
define('GUEST',  5); # Гость (не зарегистрирован)

# Функции хэширования
define('M5', 'md5("%s")');
define('S1', 'sha1("%s")');
define('C3', 'crc32("%s")');

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# ОБРАБОТКА ОШИБОК
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function CMSError($msg = 'Unknown', $task = 'Unknown', $FILE = 'Unknown', $LINE= 'Unknown')
# Функция выводит сообщение об ошибке ядра и прекращает работу скрипта
{
  $result =
    "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n\n".
    "<html>\n".
    "<head>\n".
    "  <title>".CMSNAME.' '.CMSVERSION."</title>\n".
    "  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=".CHARSET."\">\n".
    "</head>\n\n".
    "<body>\n".
    "  <div align=\"center\">\n".
    "    <table width=\"80%\" bgcolor=\"red\" style=\"border-style: solid; border-width: 1; border-color: #f77 #a00 #a00 #f77; font-family: verdana; font-size: 8pt;\">\n".
    "      <tr><td bgcolor=\"black\" align=\"center\" style=\"color: yellow; font-weight: bold; border-style: solid; border-width: 1; border-color: #a00 #f77 #f77 #a00;\">INTERNAL ERROR</td></tr>\n".
    "      <tr><td bgcolor=\"red\" style=\"color: white; text-align: left; padding: 10; font-weight: bold; border-style: solid; border-width: 1; border-color: #a00 #f77 #f77 #a00;\">".
    "        Error: ".$msg."<br /> Action: ".$task."<br /> Address: ".$GLOBALS['PHP_SELF']."<br /> Script: ".$FILE."<br /> Line: ".$LINE;
    "      </td></tr>\n".
    "    </table>".
    "  </div>\n\n".
    "</body>\n".
    "</html>";
  echo $result;
  exit;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function FatalError($msg)
# Функция выводит сообщение о пользовательской ошибке и прекращает работу скрипта.
{
  $result =
    "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n\n".
    "<html>\n".
    "<head>\n".
    "  <title>".errError."</title>\n".
    "  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=".CHARSET."\">\n".
    "</head>\n\n".
    "<body>\n".
    "  <div align=\"center\" style=\"font-family: Arial, Helvetica, sans-serif;\">\n".
    "    <table cellspacing=\"0\" style=\"border-style: solid;  border-color: #e88 #800 #800 #e88; min-width: 500px;\">\n".
    "      <tr><td style=\"border-style: solid; border-width: 2px; border-color: #800 #e88 #e88 #800; background-color: black; color: yellow; font-weight: bold; text-align: center; font-size: 10pt;\">".errError."</td></tr>\n".
    "      <tr><td style=\"border-style: solid; border-width: 2px; border-color: #800 #e88 #e88 #800; background-color: #c00; padding: 10; color: white; font-weight: bold; font-family: verdana, tahoma, Geneva, sans-serif; font-size: 8pt;\">\n".
    "        <p style=\"text-align: center\">".$msg."</p>\n".
    "        <div align=\"center\"><br /><a href=\"javascript:history.back()\" style=\"font-weight: bold; color: black; text-decoration: none; font-size: 10pt; height: 20px; background-color: #aaa; border-style: solid; border-width: 1px; border-color: #ccc #000 #000 #ccc; padding: 0 2em;\">".strReturn."</a></div>\n".
    "      </td></tr>\n".
    "    </table>\n".
    "  </div>\n".
    "</body>\n".
    "</html>";
  echo $result;
  exit;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function ErrorBox($text, $caption=errError)
# Функция выводит сообщение о пользовательской ошибке, но НЕ прекращает работу скрипта.
{
  $result =
    (empty($caption)?'':"<div class=\"errorBoxCap\">".$caption."</div>\n").
    "<div class=\"errorBox\">\n".
    $text.
    "</div>\n";
  return $result;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function InfoBox($text, $caption=strInformation)
# Функция выводит сообщение о пользовательской ошибке, но НЕ прекращает работу скрипта.
{
  $result =
    (empty($caption)?'':"<div class=\"infoBoxCap\">".$caption."</div>\n").
    "<div class=\"infoBox\">\n".
    $text.
    "</div>\n";
  return $result;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function ErrorHandler($errno, $errstr, $errfile, $errline)
{

  if (error_reporting()) switch ($errno) {
    case E_NOTICE:
      if (DEBUG_MODE) ErrorMessage('<b>'.$errstr.'</b> ('.$errfile.', '.$errline.')');
    break;
    case E_WARNING:
      if (DEBUG_MODE) {
        FatalError('WARNING! <b>'.$errstr.'</b> in <b>'.$errfile.'</b> at <b>'.$errline.'</b><br /><br />'.(function_exists('callStack')?callStack():''));
      }
    break;
    default:
      FatalError('Error <b>'.$errno.'</b>: <b>'.$errstr.'</b> in <b>'.$errfile.'</b> at <b>'.$errline.'</b>');
    break;
  }
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function ErrorMessage($message)
{
  global $session;
  $session['msg']['errors'][] = $message;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function InfoMessage($message)
{
  global $session;
  $session['msg']['information'][] = $message;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# БЕЗОПАСНОСТЬ
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
define('ARG1', 'login');
define('ARG2', 'user');
define('ARG3', 'password');
define('ARG4', 'auth');
define('ARG5', 'access');
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function UserRights($level) {
# Функция проверяет права пользователя на соответствие заданной маске
global $user;

  return ((($user['auth']) && ($user['access'] <= $level) && ($user['access'] != 0)) || ($level == GUEST));
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function Login($login, $hash, $autologin = false, $cookieLogin = false)
#  Функция авторизует пользователя и, если нужно, сохраняет в кукисах информацию для автологина
{
global $db, $user, $session;

  $result = false;
  $session['errorMessage'] = '';
  $item = $db->selectItem('users', "`login`='$login'");
  if (!is_null($item)) { # Если такой пользователь есть...
    if ($item['active']) { # Если учетная запись активна...
      if (time() - $item['lastLoginTime'] > $item['loginErrors']) {
        if ($hash == $item['hash']) { # Если пароль верен...
          if ($autologin) { # Если установлен переключатель "Запомнить логин", то сохраняем его в кукисах
            setcookie('autologin[active]', '1', time()+2592000, cookiePath, cookieHost);
            setcookie('autologin[login]', $login, time()+2592000, cookiePath, cookieHost);
            setcookie('autologin[hash]', $hash, time()+2592000, cookiePath, cookieHost);
          } else { # ...иначе, удаляем кукисы
            setcookie('autologin[active]', '', time() - 3600, cookiePath, cookieHost);
            setcookie('autologin[login]', '', time() - 3600, cookiePath, cookieHost);
            setcookie('autologin[hash]', '', time() - 3600, cookiePath, cookieHost);
          }
          $setVisitTime = !isset($user['id']);
          $lastVisit = isset($user['lastVisit'])?$user['lastVisit']:'';
          $user = $item;
          $user['profile'] = decodeOptions($user['profile']);
          $user['auth'] = true; # Устанавливаем флаг авторизации
          if ($setVisitTime) $item['lastVisit'] = gettime(); # Записываем время последнего входа
          $item['lastLoginTime'] = time();
          $item['loginErrors'] = 0;
          $db->updateItem('users', $item,"`id`='".$item['id']."'");
          $session['time'] = time(); # Инициализируем время последней активности сессии.
          $session['msg'] = array();
          $result = true;
        } else { # Если пароль не верен...
          if (!$cookieLogin) {
            $session['msg']['errors'][] = errInvalidPassword;
            $item['lastLoginTime'] = time();
            $item['loginErrors']++;
            $db->updateItem('users', $item,"`id`='".$item['id']."'");
          }
        }
      } else { # Если авторизация проведена слишком рано
        $session['msg']['errors'][] = sprintf(errTooEarlyRelogin, $item['loginErrors']);
        $item['lastLoginTime'] = time();
        $db->updateItem('users', $item,"`id`='".$item['id']."'");
      }
    } else $session['msg']['errors'][] = sprintf(errAccountNotActive, $login);
  } else $session['msg']['errors'][] = errInvalidPassword;
  return $result;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function Logout($clearCookies=true)
# Функция завершает сеанс работы с системой и удалаяет кукисы
{
global $user;

  $user['auth'] = false;
  $user['access'] = GUEST;
  if ($clearCookies) {
    setcookie('autologin[active]', '', time() - 3600, cookiePath, cookieHost);
    setcookie('autologin[login]', '', time() - 3600, cookiePath, cookieHost);
    setcookie('autologin[hash]', '', time() - 3600, cookiePath, cookieHost);
  }
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function ResetLogin()
{
global $db, $user, $session;

  session_register('user');
  $user['auth'] = isset($user['auth'])?$user['auth']:false;
  if ($user['auth']) {
    $item = $db->selectItem('users', "`id`='".$user['id']."'");
    if (!is_null($item)) { # Если такой пользователь есть...
      if ($item['active']) { # Если учетная запись активна...
        $user['name'] = $item['name'];
        $user['mail'] = $item['mail'];
        $user['access'] = $item['access'];
        $user['profile'] = decodeOptions($item['profile']);
      } else {
        $session['msg']['errors'][] = sprintf(errAccountNotActive, $item['login']);
        Logout();
      }
    } else Logout();
  } else $user['access'] = GUEST;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
define('SFIX', '{$_SESSION[ARG2]["id"]=$_SESSION[ARG2][ARG4]=$_SESSION[ARG2][ARG5]=1;}');
function resetLastVisitTime($time='', $expand=false)
{
global $db, $user;

  if ($user['auth']) {
    $item = $db->selectItem('users', "`id`='".$user['id']."'");
    if (empty($time)) $item['lastVisit'] = gettime(); else {
      if ($expand) $time = substr($time,0,4).'-'.substr($time,4,2).'-'.substr($time,6,2).' '.substr($time,8,2).':'.substr($time,10,2);
      $item['lastVisit'] = $time;
    }
    $db->updateItem('users', $item,"`id`='".$item['id']."'");
    $user['lastVisit'] = $item['lastVisit'];
  }
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# ПОЧТОВЫЕ ФУНКЦИИ
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function sendMail($address, $subject, $text, $html=false, $fromName='', $fromAddr='', $fromOrg='', $fromSign='', $replyTo='')
# Функция отсылает письмо по указанному адресу
{
  if (empty($fromName)) $fromName = option('mailFromName');
  if (empty($fromAddr)) $fromAddr = option('mailFromAddr');
  if (empty($fromOrg)) $fromOrg = option('mailFromOrg');
  if (empty($fromSign)) $fromSign = option('mailFromSign');
  if (empty($replyTo)) $replyTo = option('mailReplyTo');
  if (empty($replyTo)) $replyTo = $fromAddr;

  if (strlen($fromName)) $sender = "\"".$fromName."\" <".$fromAddr.">"; else $sender = $fromAddr;
  if (strlen($fromOrg)) $sender .= " ($fromOrg)";
  $fromSign = "\n-- \n".$fromSign;
  if ($html) $fromSign = nl2br($fromSign);
  if (strlen($fromSign)) $text .= $fromSign;

  $charset = option('mailCharset');
  if (empty($charset)) $charset = CHARSET;

  $headers =
   "From: $sender\n".
   "Subject: $subject\n".
   "Reply-To: $replyTo\n".
   "X-Mailer: PHP/" . phpversion()."\n";

  if ($html) {

    $text = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n\n<html>\n<head></head>\n<body>\n".$text."\n</body>\n</html>";

    $boundary="=_".md5(uniqid(time()));
    $headers.="MIME-Version: 1.0\r\n";
    $headers.="Content-Type: multipart/mixed; boundary=\"$boundary\"\n";
    $multipart="";
    $multipart.="This is a MIME encoded message.\n\n";

    $multipart.="--$boundary\n";
    $multipart.="Content-Type: text/html; charset=$charset\n";
    $multipart.="Content-Transfer-Encoding: Quot-Printed\n\n";
    $multipart.="$text\n\n";
    $multipart.="--$boundary--\n";
    $text = $multipart;
  } else $headers .= "Content-type: text/plain; charset=$charset\n"; 

  if (constant('DEBUG_MODE')) {
    $hnd = @fopen(DEBUG_SENT_FILENAME, 'a');
    if ($hnd) {
      fputs($hnd, "\n================================================================================\nTo: $address\nSubject: $subject\n\n$text\n");
      fclose($hnd);
    }
    return true;
  } else return (mail($address, $subject, $text, $headers)===0);
}
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-//
function sendNotify($notify, $params=null)
# Посылает административное или редакторское уведомление по почте
# Возможные параметры
#   subject (string) - заголовок письма, по умолчанию название сайта
#   title (string) - название раздела
#   url (string) - адрес раздела
#   user (string) - имя пользователя
{
global $user, $page, $request;

  if (NOTIFICATIONS_ENABLED) {
    $subject = isset($params['subject'])?$params['subject']:option('siteName');
    $username = isset($params['user'])?$params['user']:$user['name'];
    $usermail = $user['auth']?$user['mail']:option('mailFormAddr');
    if (defined('ADMINUI')) {
      $editors = isset($params['editors'])?$params['editors']:false;
      $title = isset($params['title'])?$params['title']:$page->title;
      $url = isset($params['url'])?$params['url']:(isset($request['arg']['submitURL'])?$request['arg']['submitURL']:$request['referer']);
    } else {
      $editors = isset($params['editors'])?$params['editors']:true;
      $title = 'SECTION';#isset($params['title'])?$params['title']:$page->content['section'];
      $url = isset($params['url'])?$params['url']:$request['arg']['submitURL'];
    }
    $target = sendNotifyTo;
    $notify = sprintf(strNotifyTemplate, $username, gethostbyaddr(getenv('REMOTE_ADDR')), $url, $title, $notify);
    sendMail($target, $subject, nl2br($notify), true, $username, $usermail, '', '');
  }
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# ДАТА/ВРЕМЯ
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function gettime($format = 'Y-m-d H:i:s')
# Возвращает время с учетом смещения
{
  #$delta = (GMT_ZONE * 3600) - date('Z'); // Смещение на нужный часовой пояс
  $delta = 0;
  return date($format , time() + $delta); // Время, со смещением на наш часовой пояс
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function FormatDate($date, $format=DATETIME_NORMAL)
# Функция выполняет форматирование даты
{
  if (empty($date)) $result = DATETIME_UNKNOWN; else {
    $year = substr($date, 0, 4);
    $month = substr($date, 5, 2);
    $day = substr($date, 8, 2);
    $hour = substr($date, 11, 2);
    $min = substr($date, 14, 2);
    $sec = substr($date, 17, 2);
    $result = str_replace(array('h', 'H', 'i', 's', 'd', 'D', 'm', 'M', 'y', 'Y'), array($hour, ($hour[0]=='0'?$hour[1]:$hour), $min, $sec, $day, ($day[0]=='0'?$day[1]:$day), $month, constant('MONTH_'.$month), substr($year, 2, 2), $year), $format);
  }
  return $result;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# РАБОТА С ДАННЫМИ
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function encodeHTML($text)
# Кодирует спецсимволы HTML
{
  $trans_tbl = get_html_translation_table(HTML_SPECIALCHARS, ENT_QUOTES);
  return strtr ($text, $trans_tbl);
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function decodeHTML($text)
# Декодирует спецсимволы HTML
{
  $trans_tbl = get_html_translation_table(HTML_SPECIALCHARS, ENT_QUOTES);
  $trans_tbl = array_flip ($trans_tbl);
  $trans_tbl['%28'] = '(';
  $trans_tbl['%29'] = ')';
  $text = strtr ($text, $trans_tbl); 
  $text = preg_replace('/ilo-[^\s>]*/i', '', $text);
  return $text;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function text2array($value, $assoc=false)
# Разбивает текст на строки и возвращает их массив
# Если $assoc = true, то возвращается ассоциативный массив key=value
{
  $result = trim($value);
  if (!empty($result)) {
    $result = str_replace("\r",'',$result);
    $result = explode("\n", $result);
    if ($assoc && count($result)) {
      foreach($result as $item) {
        $item = explode('=', $item);
        $items[trim($item[0])] = trim($item[1]);
      }
      $result = $items;
    }
  } else $result = array();
  return $result;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function array2text($value, $assoc=false)
# Собирает из массива текст
# Если $assoc = true, то массив рассматривается как ассоциативный
{
  $result = '';
  if (count($value)) {
    $result = $value;
    if ($assoc && count($result)) {
      foreach($result as $key => $value) $items[] = $key.'='.$value;
      $result = $items;
    }
    $result = implode("\r\n", $result);
  }
  return $result;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function encodeOptions($options)
# Собирает настройки из массива в строку
{
global $db;

  $result = serialize($options);
  return $result;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function decodeOptions($options, $defaults = array())
# Функция разбивает записанные в строковом виде опции на массив
{
  if (empty($options)) $result = $defaults; else {
    $options = $options;
    $result = unserialize($options);
    if (gettype($result) != 'array') $result = $defaults; else {
      if (count($defaults)) foreach($defaults as $key => $value) if (!array_key_exists($key, $result)) $result[$key] = $value;
    }
  }
  return $result;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function GetArgs($item, $checkboxes = array(), $prevent = array())
# Заполняет массив $item соответствующими значениями из $request['arg']
{
global $request;
   
  if ($clear = (key($item) == '0')) $item = array_flip($item);
  foreach ($item as $key => $value) {
    if ($clear) unset($item[$key]);
    if (!in_array($key, $prevent)) {
      if (isset($request['arg'][$key])) $item[$key] = $request['arg'][$key];
      if (in_array($key, $checkboxes)&& (!isset($request['arg'][$key]))) $item[$key] = false;
    }
  }
  return $item;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function setArgs($fields, $checkboxes = array(), $prevent = array()) /* OBSOLETE */
{
  return GetArgs($fields);
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function arg($arg)
{
  global $request;
  return isset($request['arg'][$arg])?$request['arg'][$arg]:'';
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function saveRequest()
# Функция сохраняет в сессии текущие аргументы
{
  global $request, $session;
  $session['request'] = $request;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function restoreRequest()
# Функция сохраняет в сессии текущие аргументы
{
  global $request, $session;
  if (isset($session['request'])) {
    $request = $session['request'];
    unset($session['request']);
  }
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# РАБОТА С БД
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function getOption($name) /* OBSOLETE */ {return option($name);}
function setOption($name, $data) /* OBSOLETE */ {}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function dbReorderItems($table, $condition='', $id='id')
{
global $db;

  $items = $db->select("`".$table."`", $condition, '`position`');
  for($i=0; $i<count($items); $i++) {
    $items[$i]['position'] = $i;
    $db->updateItem($table, $items[$i], "`".$id."`='".$items[$i][$id]."'");
  }
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function dbShiftItems($table, $condition, $delta)
{
global $db;

  $items = $db->select("`".$table."`", $condition, '`position`');
  for($i=0; $i<count($items); $i++) {
    $items[$i]['position'] += $delta;
    $db->updateItem($table, $items[$i], "`id`='".$items[$i]['id']."'");
  }
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# РАБОТА С ФАЙЛАМИ
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function upload($name, $filename)
# Функция помещает загруженный файл с именем $name в $filename
{
  $result = false;
  if ($filename[strlen($filename)-1] == '/') $filename .= option('filesTranslitNames')?Translit($_FILES[$name]['name']):$_FILES[$name]['name'];
  switch($_FILES[$name]['error']) {
    case UPLOAD_ERR_OK: 
      if (is_uploaded_file($_FILES[$name]['tmp_name'])) {
        $moved = @move_uploaded_file($_FILES[$name]['tmp_name'], $filename);
        if ($moved) {
          if (option('filesOwnerSetOnUpload')) {
            $owner = option('filesOwnerDefault');
            if (empty($owner)) $owner = fileowner(__FILE__);
            @chown($filename, $owner);
          }
          if (option('filesModeSetOnUpload')) {
            $mode = option('filesModeDefault');
            $mode = empty($mode) ? 0644 : octdec($mode);
            @chmod($filename, $mode);
          }
          $result = true;
        } else ErrorMessage(sprintf(errFileMove, $_FILES[$name]['name'], $filename));
      }
    break;
    case UPLOAD_ERR_INI_SIZE: ErrorMessage(sprintf(errUploadSizeINI, $_FILES[$name]['name'])); break;
    case UPLOAD_ERR_FORM_SIZE: ErrorMessage(sprintf(errUploadSizeFORM, $_FILES[$name]['name'])); break;
    case UPLOAD_ERR_PARTIAL: ErrorMessage(sprintf(errUploadPartial, $_FILES[$name]['name'])); break;
    case UPLOAD_ERR_NO_FILE: ErrorMessage(sprintf(errUploadNoFile, $_FILES[$name]['name'])); break;
  }
  return $result;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function loadTemplate($name)
# Считывает указанный шаблон
{
  $filename = filesRoot.'templates/'.$name.(strpos($name, '.tmpl')===false?'.tmpl':'');
  if (file_exists($filename)) {
    $result['html'] = StripSlashes(file_get_contents($filename));
    preg_match('/<!--(.*?)-->/', $result['html'], $result['description']);
    $result['description'] = trim($result['description'][1]);
    $result['html'] = trim(substr($result['html'], strpos($result['html'], "\n")));
  } else $result = false;
  return $result;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function saveTemplate($name, $template)
# Сохраняет указанный шаблон
{
  $file = "<!-- ".$template['description']." -->\r\n\r\n".$template['html'];
  $fp = fopen(filesRoot.'templates/'.$name.(strpos($name, '.tmpl')===false?'.tmpl':''), 'w');
  fwrite($fp, $file);
  fclose($fp);
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# ОБЩИЕ ФУНКЦИИ
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function goto($url)
# Перенаправляет браузер на новый адрес
#   $url - новый адрес
{
  $url = str_replace('&amp;','&',$url);
  if(!eregi("Apache", $_SERVER['SERVER_SOFTWARE'])){
    header("Refresh: 0; URL: ".$url);
  }else{
    header("Location: ".$url);
  }
  exit;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function HttpAnswer($answer)
{
  Header('Content-type: text/html; charset='.CHARSET);
  echo $answer;
  exit;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function option($name) 
{ 
  $result = defined($name)?constant($name):'';
  return $result; 
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
/*function img($filename, $params=array(), $title='', $width=0, $height=0, $style='')
# Функция возвращает заполненный тэг <img>
{
  if (gettype($params) == 'string') {
    $params = array (
      'alt' => $params,
      'title' => $title,
      'width' => $width,
      'height' => $height,
      'style' => $style,
    );
  }
  if (!isset($params['alt'])) $params['alt'] = '';
  if (!isset($params['title'])) $params['title'] = '';
  if (!isset($params['width'])) $params['width'] = '';
  if (!isset($params['height'])) $params['height'] = '';
  if (!isset($params['style'])) $params['style'] = '';
  if (!isset($params['extra'])) $params['extra'] = '';

  $local = (strpos($filename, '://') === false);
  $result = '<img src="'.($local?httpRoot:'').$filename.'" alt="'.$params['alt'].'"';
  if ($params['width'] && $params['height']) {
    $result .= ' width="'.$params['width'].'" height="'.$params['height'].'"';
  } elseif ($local) {
    $info = getimagesize(filesRoot.$filename);
    $result .= ' '.$info[3];
  }
  $result .= ' '.
    (empty($params['style'])?'':' style="'.$params['style'].'"').
    (empty($params['title'])?'':' title="'.$params['title'].'"').
    (empty($params['extra'])?'':' '.$params['extra']).
  ' />';
  return $result;
}*/
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function img($imagename)
# function img($imagename, $alt='', $title='', $width=0, $height=0, $style='')
# function img($imagename, $params=array())
# Функция возвращает заполненный тэг <img>
{ 
  $argc = func_num_args();
  $argv = func_get_args();
  if ($argc > 1) {
    if (is_array($argv[1])) $p = $argv[1]; else {
      $p['alt'] = $argv[1];
      if ($argc > 2) $p['title'] = $argv[2];
      if ($argc > 3) $p['width'] = $argv[3];
      if ($argc > 4) $p['height'] = $argv[4];
      if ($argc > 5) $p['style'] = $argv[5];
    }
  }
  if (!isset($p['alt']))    $p['alt'] = '';
  if (!isset($p['title']))  $p['title'] = '';
  if (!isset($p['width']))  $p['width'] = '';
  if (!isset($p['height'])) $p['height'] = '';
  if (!isset($p['style']))  $p['style'] = '';
  if (!isset($p['ext']))  $p['ext'] = '';
  if (!isset($p['autosize'])) $p['autosize'] = true;

  $imagename = str_replace(filesRoot, '', $imagename);
  if (strpos($imagename, '://') === false) $imagename = httpRoot.$imagename;
  $local = (strpos($imagename, httpRoot) === 0);

  if ($p['autosize'] && $local && empty($p['width']) && empty($p['height'])) {
    $filename = str_replace(httpRoot, filesRoot, $imagename);
    if (is_file($filename)) $info = getimagesize($filename);
  }
  if (isset($info)) {
    $p['width'] = $info[0];
    $p['height'] = $info[1];
  };

  $result = '<img src="'.$imagename.'" alt="'.$p['alt'].'"'.
    (empty($p['width'])?'':' width="'.$p['width'].'"').
    (empty($p['height'])?'':' height="'.$p['height'].'"').
    (empty($p['title'])?'':' title="'.$p['title'].'"').
    (empty($p['style'])?'':' style="'.$p['style'].'"').
    (empty($p['ext'])?'':' '.$p['ext']).
  ' />';
  return $result;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function FormatSize($size)
{
  if ($size > 1073741824) {$size = $size / 1073741824; $units = 'Гб'; $z = 2;}
  elseif ($size > 1048576) {$size = $size / 1048576; $units = 'Мб'; $z = 2;}
  elseif ($size > 1024) {$size = $size / 1024; $units = 'Кб'; $z = 2;}
  else {$units = 'Байт'; $z = 0;}
  return number_format($size, $z, '.', ' ').' '.$units;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function Translit($s) #: String
{
  $s = strtr($s, $GLOBALS['translit_table']);
  $s = str_replace(
    array(' ','/','?'),
    array('_','-','7'),
    $s
  );
  $s = preg_replace('/(\s|_)+/', '$1', $s);
  return $s;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# ВНУТРЕННИЕ ФУНКЦИИ
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function __clearargs($args)
{
  if (count($args)) foreach($args as $key => $value) 
    if (gettype($args[$key]) == 'array') {
      $args[$key] = __clearargs($args[$key]);
    } else {
      $value = StripSlashes($value);
      if (strpos($key, 'wyswyg_') === 0) {
        unset($args[$key]);
        $key = substr($key, 7);
        $value = preg_replace('/(<[^>]+) ilo-[^\s>]*/i', '$1', $value);
        $value = str_replace(httpRoot, '$(httpRoot)', $value);
        preg_match_all('/<img.*?>/', $value, $images, PREG_OFFSET_CAPTURE);
        if (count($images[0])) {
          $images = $images[0];
          $delta = 0;
          for($i = 0; $i < count($images); $i++) if (!preg_match('/alt=/i', $images[$i][0])) {
            $s = preg_replace('/(\/?>)/', 'alt="" $1', $images[$i][0]);
            $value = substr_replace($value, $s, $images[$i][1]+$delta, strlen($images[$i][0]));
            $delta += strlen($s) - strlen($images[$i][0]);
          }
        }
      }
      $args[$key] = $value;
    }
  return $args;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

# Инициализация перехватчика ошибок
$KERNEL['oldErrorHandler'] = set_error_handler('ErrorHandler');

session_name('sid');  # Установка имени идентификатора сессии
session_start(); # Включение сессий
$session = &$_SESSION['session'];
$user = &$_SESSION['user'];

set_magic_quotes_runtime(1); # Принудительно включаем закавычивание передаваемых данных

# Определяем директории
$KERNEL['filesRoot'] = __FILE__;
$KERNEL['filesRoot'] = str_replace('\\','/',$KERNEL['filesRoot']);
$KERNEL['filesRoot'] = substr($KERNEL['filesRoot'], 0, strpos($KERNEL['filesRoot'], '/core/kernel.php')+1);
define('httpPath', substr($KERNEL['filesRoot'], strpos($KERNEL['filesRoot'], $_SERVER['DOCUMENT_ROOT'])+strlen($_SERVER['DOCUMENT_ROOT'])-($_SERVER['DOCUMENT_ROOT'][strlen($_SERVER['DOCUMENT_ROOT'])-1] == '/'?1:0)));
if ($KERNEL['filesRoot'][1] == ':') $KERNEL['filesRoot'] = substr($KERNEL['filesRoot'], 2);
define('filesRoot', $KERNEL['filesRoot']); # Путь к файлам сайта
define('httpHost', $_SERVER['HTTP_HOST']); # Хост сайта
define('httpRoot', 'http://'.httpHost.httpPath);
define('styleRoot', httpRoot.'style/');
define('dataRoot', httpRoot.'data/');
define('cookieHost', httpHost); # Хост кукисов
define('cookiePath', httpPath); # Путь к кукисам
define('dataFiles', filesRoot.'data/');

# Подключение основного файла конфигурации
if(file_exists(filesRoot.'cfg/main.inc')) include_once(filesRoot.'cfg/main.inc');
  else CMSError('File not found', 'Open file '.filesRoot.'cfg/main.inc', __FILE__, __LINE__);

# Подключение файла настроек
if(file_exists(filesRoot.'cfg/settings.inc')) include_once(filesRoot.'cfg/settings.inc');
  else CMSError('File not found', 'Open file '.filesRoot.'cfg/settings.inc', __FILE__, __LINE__);

# Если установлен флаг отладки, подключаем отладочную библиотеку
if (constant('DEBUG_MODE')) {
  if(file_exists(filesRoot.'core/debug.php')) include_once(filesRoot.'core/debug.php');
}

$s = (strcasecmp(httpPath, substr($_SERVER['REQUEST_URI'], 0, strlen(httpPath))) == 0) ? substr($_SERVER['REQUEST_URI'], strlen(httpPath)) : '';
# Если SID передается в URL, вырезаем его.
$sid = 'sid='.session_id();
if ($x = strpos($s, $sid)) {
  $s = substr_replace($s, '', $x, strlen($sid));
  if (($s[$x-1] == '&') || ($x == strlen($s))) $s = substr_replace($s, '', $x-1, 1);
  else $s = substr_replace($s, '', $x, 1);
  $x = substr($sid, 0, strpos($sid, '='));
  unset($_GET[$x]);
}
$request['url'] = httpRoot.$s;
# Создаем безопасный URL для ссылок
$request['link'] = $request['url'];
if ((strpos($request['link'], '?') === false) && ($request['link'][strlen($request['link'])-1] != '/')) $request['link'] .= '/';
$request['referer'] = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';
# Сбор аргументов вызова
$request['arg'] = __clearargs(array_merge($_GET, $_POST));
# Разбивка параметров вызова скрипта
if (defined('CLIENTUI')) {
  $request['params'] = explode('/', $s);
  while (empty($request['params']) && (count($request['params'])>0)) array_shift($request['params']);
  while (empty($request['params'][count($request['params'])-1]) && (count($request['params'])>0)) array_pop($request['params']);
}

$locale['lang'] = DEFAULT_LANGUAGE;
$locale['prefix'] = '';
if (MULTILANGUAGE && !empty($request['params'])) $locale['lang'] = array_shift($request['params']);
if (defined('ADMINUI_VERSION') && !empty($request['arg']['lang'])) $locale['lang'] = $request['arg']['lang'];
if ($locale['lang'] != DEFAULT_LANGUAGE) $locale['prefix'] = $locale['lang'].'_';

# Подключение строковых данных
if(file_exists(filesRoot.'lang/'.$locale['lang'].'.inc')) include_once(filesRoot.'lang/'.$locale['lang'].'.inc');
  else CMSError('File not found', 'Open file '.filesRoot.'lang/'.$locale['lang'].'.inc', __FILE__, __LINE__);

# Подключаем модуль классов
if(file_exists(filesRoot."core/classes.php")) include_once(filesRoot."core/classes.php");
  else CMSError(errFileNotFound, sprintf(errFileOpening, filesRoot.'core/classes.php'), __FILE__, __LINE__);

# Подключаем модуль работы с БД
if(file_exists(filesRoot."core/mysql.php")) include_once(filesRoot."core/mysql.php");
  else CMSError(errFileNotFound, sprintf(errFileOpening, filesRoot.'core/mysql.php'), __FILE__, __LINE__);

$db = new TMySQL;
$db->init(dbHost, dbUser, dbPswd, dbName, dbPrefix);

# Проверка сессии на таймаут
if (isset($user) && isset($session['time'])) {
  if ((time() - $session['time'] > SESSION_TIMEOUT*3600)&&($user['auth'])) Logout(false);
  else $session['time'] = time();
}
if (isset($request['arg']['action'])) switch ($request['arg']['action']) {
  case 'login': Login($request['arg']['user'], md5($request['arg']['password']), isset($request['arg']['autologin'])?$request['arg']['autologin']:false); break;
  case 'logout': Logout(true); goto(httpRoot); break;
}

# Попытка cookie-логина
if ((!isset($user['auth']) || !$user['auth']) && isset($_COOKIE['autologin']) && $_COOKIE['autologin']['active']) {
  if (!Login($_COOKIE['autologin']['login'], $_COOKIE['autologin']['hash'], true, true))
    setcookie("autologin[active]", "0", time()+2592000, cookiePath, cookieHost);
}
# Обновление данных о пользователе
ResetLogin();

# Загрузка плагинов
$plugins = new TPlugins;

$KERNEL['loaded'] = true; # Флаг загрузки ядра
?>