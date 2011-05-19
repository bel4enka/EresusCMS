<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2004, ProCreat Systems, http://procreat.ru/
 * @copyright 2007, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mihalych@vsepofigu.ru>
 *
 * Данная программа является свободным программным обеспечением. Вы
 * вправе распространять ее и/или модифицировать в соответствии с
 * условиями версии 3 либо (по вашему выбору) с условиями более поздней
 * версии Стандартной Общественной Лицензии GNU, опубликованной Free
 * Software Foundation.
 *
 * Мы распространяем эту программу в надежде на то, что она будет вам
 * полезной, однако НЕ ПРЕДОСТАВЛЯЕМ НА НЕЕ НИКАКИХ ГАРАНТИЙ, в том
 * числе ГАРАНТИИ ТОВАРНОГО СОСТОЯНИЯ ПРИ ПРОДАЖЕ и ПРИГОДНОСТИ ДЛЯ
 * ИСПОЛЬЗОВАНИЯ В КОНКРЕТНЫХ ЦЕЛЯХ. Для получения более подробной
 * информации ознакомьтесь со Стандартной Общественной Лицензией GNU.
 *
 * Вы должны были получить копию Стандартной Общественной Лицензии
 * GNU с этой программой. Если Вы ее не получили, смотрите документ на
 * <http://www.gnu.org/licenses/>
 *
 * @package Core
 *
 * $Id$
 */

/**
 * Название системы
 * @var string
 */
define('CMSNAME', 'Eresus');
define('CMSVERSION', '${product.version}'); # Версия системы
define('CMSLINK', 'http://eresus.ru/'); # Веб-сайт

define('KERNELNAME', 'ERESUS'); # Имя ядра
define('KERNELDATE', '${builddate}'); # Дата обновления ядра

# Уровни доступа
define('ROOT',   1); # Главный администратор
define('ADMIN',  2); # Администратор
define('EDITOR', 3); # Редактор
define('USER',   4); # Пользователь
define('GUEST',  5); # Гость (не зарегистрирован)

function __macroConst($matches)
{
	return constant($matches[1]);
}
//-----------------------------------------------------------------------------

function __macroVar($matches)
{
	$result = $GLOBALS[$matches[2]];
	if (!empty($matches[3])) @eval('$result = $result'.$matches[3].';');
	return $result;
}
//-----------------------------------------------------------------------------

/**
 * Функция выводит сообщение о пользовательской ошибке и прекращает работу скрипта.
 *
 * @param string $msg  Текст сообщения
 */
function FatalError($msg)
{
	if (PHP_SAPI == 'cli') {
		$result = strip_tags(preg_replace('!<br(\s/)?>!i', "\n", $msg))."\n";
	} else {
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
	}
	die($result);
}
//------------------------------------------------------------------------------

/**
 * Вывод сообщения о пользовательской ошибке
 *
 * @param string $text     Текст сообщения
 * @param string $caption  Заголовок окна сообщения
 */
function ErrorBox($text, $caption=errError)
{
	$result =
		(empty($caption)?'':"<div class=\"errorBoxCap\">".$caption."</div>\n").
		"<div class=\"errorBox\">\n".
		$text.
		"</div>\n";
	return $result;
}
//------------------------------------------------------------------------------

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
//------------------------------------------------------------------------------

/**
 * Помещает сообщение об ошибке в очередь сообщений в сесии
 *
 * @param string $message
 *
 * @return void
 *
 * @since ?.??
 */
function ErrorMessage($message)
{
	if (!isset($_SESSION['msg']))
	{
		$_SESSION['msg'] = array();
	}
	if (!isset($_SESSION['msg']['errors']))
	{
		$_SESSION['msg']['errors'] = array();
	}
	$_SESSION['msg']['errors'] []= $message;
}
//------------------------------------------------------------------------------

/**
 * Помещает сообщение об ошибке в очередь сообщений в сесии
 *
 * @param string $message
 *
 * @return void
 *
 * @since ?.??
 */
function InfoMessage($message)
{
	if (!isset($_SESSION['msg']))
	{
		$_SESSION['msg'] = array();
	}
	if (!isset($_SESSION['msg']['information']))
	{
		$_SESSION['msg']['information'] = array();
	}
	$_SESSION['msg']['information'] []= $message;
}
//------------------------------------------------------------------------------

/**
 * Првоеряет уровень доступа пользователя на соответствие заданному
 *
 * @param int $level  минимальный требуемый уровень доступа
 *
 * @return bool
 *
 * @since 2.00
 * @deprecated
 */
function UserRights($level)
{
	if ($level == GUEST)
	{
		// Гость - самый низкий уровень.
		return true;
	}

	$user = Eresus_Security_AuthService::getInstance()->getUser();

	if (!$user)
	{
		// Если не аутентифицирован - запрещаем доступ
		return false;
	}

	if ($user->access == 0)
	{
		// Неправильный уровень доступа - запрещаем доступ
		return false;
	}

	return $user->access <= $level;
}
//------------------------------------------------------------------------------

/**
 * Подключение библиотеки
 *
 * @param  string  $libaray  Имя библиотеки
 *
 * @return  bool  Результат
 */
function useLib($library)
{
	$result = false;
	if (DIRECTORY_SEPARATOR != '/') $library = str_replace('/', DIRECTORY_SEPARATOR, $library);
	$filename = DIRECTORY_SEPARATOR.$library.'.php';
	$dirs = explode(PATH_SEPARATOR, get_include_path());
	foreach ($dirs as $path) if (is_file($path.$filename)) {
		include_once($path.$filename);
		$result = true;
		break;
	}
	return $result;
}
//------------------------------------------------------------------------------

/**
 * Отсылает письмо по указанному адресу
 *
 * @param string $address   адрес получателя
 * @param string $subject   тема письма
 * @param string $text      текст письма
 * @param bool   $html      отправить текст как HTML
 * @param string $fromName  имя отправителя
 * @param string $fromAddr  адрес отпрвителя
 * @param string $fromOrg   игнорируется
 * @param string $fromSign  игнорируется
 * @param string $replyTo   адрес для ответа
 *
 * @return bool
 *
 * @since ?.??
 * @deprecated с версии 2.16. Используйте класс {@link Eresus_Mail}
 */
function sendMail($address, $subject, $text, $html=false, $fromName='', $fromAddr='', $fromOrg='',
	$fromSign='', $replyTo='')
{
	$mail = new Eresus_Mail();
	$mail->addTo($address)->setSubject($subject);
	if ($html)
	{
		$mail->setHTML($text);
	}
	else
	{
		$mail->setText($text);
	}
	if ($fromAddr)
	{
		$mail->setFrom($fromAddr, $fromName);
	}
	elseif ($fromName)
	{
		$mail->setFrom(option('mailFromAddr'), $fromName);
	}
	if ($replyTo)
	{
		$mail->setReplyTo($replyTo);
	}
	try
	{
		$mail->send();
	}
	catch (Exception $e)
	{
		Eresus_Logger::exception($e);
		return false;
	}
	return true;
}
//-----------------------------------------------------------------------------

function gettime($format = 'Y-m-d H:i:s')
# Возвращает время с учетом смещения
{
	#$delta = (GMT_ZONE * 3600) - date('Z'); // Смещение на нужный часовой пояс
	$delta = 0;
	return date($format , time() + $delta); // Время, со смещением на наш часовой пояс
}
//-----------------------------------------------------------------------------

/**
 * Форматирование даты
 *
 * @param string $date    Дата в формате YYYY-MM-DD hh:mm:ss
 * @param string $format  Правила форматирования даты
 *
 * @return string Отформатированная дата
 */
function FormatDate($date, $format=DATETIME_NORMAL)
{
	if (empty($date)) $result = DATETIME_UNKNOWN; else {
		preg_match_all('/(?<!\\\)[hHisdDmMyY]/', $format, $m, PREG_OFFSET_CAPTURE);
		$repl = array(
			'Y' => substr($date, 0, 4),
			'm' => substr($date, 5, 2),
			'd' => substr($date, 8, 2),
			'h' => substr($date, 11, 2),
			'i' => substr($date, 14, 2),
			's' => substr($date, 17, 2)
		);
		$repl['y'] = substr($repl['Y'], 2, 2);
		$repl['M'] = constant('MONTH_'.$repl['m']);
		$repl['D'] = $repl['d']{0} == '0' ? $repl['d']{1} : $repl['d'];
		$repl['H'] = $repl['h']{0} == '0' ? $repl['h']{1} : $repl['h'];

		$delta = 0;
		for($i = 0; $i<count($m[0]); $i++) {
			$format = substr_replace($format, $repl[$m[0][$i][0]], $m[0][$i][1]+$delta, 1);
			$delta += strlen($repl[$m[0][$i][0]]) - 1;
		}
	}
	return $format;
}
//-----------------------------------------------------------------------------

/**
 * Кодирует спецсимволы HTML
 *
 * @param mixed $source
 * @return mixed
 */
function encodeHTML($source)
{
	$translationTable = get_html_translation_table(HTML_SPECIALCHARS, ENT_QUOTES);
	switch (true) {
		case is_string($source):
			$source = strtr($source, $translationTable);
		break;
		case is_array($source):
			foreach($source as $key => $value)
				$source[$key] = strtr($value, $translationTable);
		break;
	}
	return $source;
}
//-----------------------------------------------------------------------------

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
//-----------------------------------------------------------------------------

/**
 * Разбивает текст на строки и возвращает массив из них
 *
 * @param string $value
 * @param bool   $assoc[optional]
 * @return array
 */
function text2array($value, $assoc = false)
{
	$result = trim($value);
	if (!empty($result)) {
		$result = str_replace("\r",'',$result);
		$result = explode("\n", $result);
		if ($assoc && count($result)) {
			foreach($result as $item) {
				$item = explode('=', $item);
				$key = trim($item[0]);
				if ($key !== '') {
					$value = isset($item[1]) ? trim($item[1]) : null;
					$items[$key] = $value;
				}
			}
			$result = $items;
		}
	} else $result = array();
	return $result;
}
//-----------------------------------------------------------------------------

/**
 * Собирает текст из массива
 * @param string $value
 * @param bool   $assoc[optional]
 * @return string
 */
function array2text($items, $assoc = false)
{
	$result = '';
	if (count($items)) {
		if ($assoc)
			foreach($items as $key => $value) $result[] = $key.'='.$value;
		else
			$result = $items;
		$result = implode("\n", $result);
	}
	return $result;
}
//-----------------------------------------------------------------------------

function encodeOptions($options)
# Собирает настройки из массива в строку
{
	$result = serialize($options);
	return $result;
}
//-----------------------------------------------------------------------------

function decodeOptions($options, $defaults = array())
# Функция разбивает записанные в строковом виде опции на массив
{
	if (empty($options)) $result = $defaults; else {
		@$result = unserialize($options);
		if (gettype($result) != 'array') $result = $defaults; else {
			if (count($defaults)) foreach($defaults as $key => $value) if (!array_key_exists($key, $result)) $result[$key] = $value;
		}
	}
	return $result;
}
//-----------------------------------------------------------------------------

/**
 * Замена макросов
 *
 * @param string $template  Шаблон
 * @param mixed  $source    Источник для замены
 * @return Обработанный текст
 *
 * @see __propery
 */
function replaceMacros($template, $source)
{
	# Замена условных макросов
	preg_match_all('/\$\(([^\)\?:]+)\?([^:\)]*):([^\)]*)\)/U', $template, $matches, PREG_SET_ORDER);
	if (count($matches)) foreach($matches as $macros) {
		if (__isset($source, $macros[1])) $template = str_replace($macros[0], __property($source, $macros[1])?$macros[2]:$macros[3], $template);
	}
		# Замена обычных макросов
	preg_match_all('/\$\(([^(]+)\)/U', $template, $matches);
	if (count($matches[1])) foreach($matches[1] as $macros)
		if (__isset($source, $macros)) $template = str_replace('$('.$macros.')', __property($source, $macros), $template);
	return $template;
}
//------------------------------------------------------------------------------

/**
 * Возвращает значение аргумента запроса
 *
 * @param string $arg     Имя аргумента
 * @param mixed  $filter  Фильтр на значение
 *
 * @return mixed
 */
function arg($arg, $filter = null)
{
	global $Eresus;

	$arg = isset($Eresus->request['arg'][$arg]) ?
		$Eresus->request['arg'][$arg] :
		null;

	if ($arg !== false && !is_null($filter))
	{
		switch($filter)
		{
			case 'dbsafe':
				// Оставлено для предотвращения распознавания "dbsafe" в качестве регулярного выражения
			break;

			case 'int':
			case 'integer':
					$arg = intval($arg);
			break;

			case 'float':
					$arg = floatval($arg);
			break;

			case 'word':
					$arg = preg_replace('/\W/', '', $arg);
			break;

			default:
				$arg = preg_replace($filter, '', $arg);
			break;
		}
	}
	return $arg;
}
//-----------------------------------------------------------------------------

/**
 * Функция сохраняет в сессии текущие аргументы
 *
 * @return void
 *
 * @since ?.??
 */
function saveRequest()
{
	$_SESSION['request'] = $Eresus->request;
}
//-----------------------------------------------------------------------------

/**
 * Функция сохраняет в сессии текущие аргументы
 *
 * @return void
 *
 * @since ?.??
 */
function restoreRequest()
{
	if (isset($_SESSION['request']))
	{
		$Eresus->request = $_SESSION['request'];
		unset($_SESSION);
	}
}
//-----------------------------------------------------------------------------


 /*
 	* РАБОТА С БД
	*/

/**
 * Упорядочивание элементов
 *
 * @param string $table      Таблица
 * @param string $condition  Условие
 * @param string $id         Имя ключевого поля
 *
 * @deprecated
 */
function dbReorderItems($table, $condition='', $id='id')
{
	global $Eresus;

	$items = $Eresus->db->select("`".$table."`", $condition, '`position`', $id);
	for($i=0; $i<count($items); $i++) $Eresus->db->update($table, "`position` = $i", "`".$id."`='".$items[$i][$id]."'");
	}
//------------------------------------------------------------------------------

/**
 * Сдвиг позиций элементов
 *
 * @param string $table      Таблица
 * @param string $condition  Условие
 * @param string $delta      Величина сдвига
 *
 * @deprecated
 *  */
function dbShiftItems($table, $condition, $delta, $id='id')
{
global $Eresus;

	$items = $Eresus->db->select("`".$table."`", $condition, '`position`', $id);
	for($i=0; $i<count($items); $i++) $Eresus->db->update($table, "`position` = `position` + $delta", "`".$id."`='".$items[$i][$id]."'");
	}
//------------------------------------------------------------------------------

/**
 * Чтение файла
 *
 * @param string $filename Имя файла
 * @return mixed Содержимое файла или false
 */
function fileread($filename)
{
	$result = false;
	if (is_file($filename)) {
		if (is_readable($filename)) {
			$result = file_get_contents($filename);
		}
	}
	return $result;
}
//------------------------------------------------------------------------------

/**
 * Запись в файл
 *
 * @param string $filename Имя файла
 * @param string $content  Содержимое
 * @param int    $flags    Флаги
 * @return bool Результат выполнения
 */
function filewrite($filename, $content, $flags = 0)
{
	$result = false;
	@$fp = fopen($filename, ($flags && FILE_APPEND)?'ab':'wb');
	if ($fp) {
		$result = fwrite($fp, $content) == strlen($content);
		fclose($fp);
	}
	return $result;
}
//------------------------------------------------------------------------------

/**
 * Удаляет файл
 *
 * @param string $filename Имя файла
 * @return bool Результат выполнения
 */
function filedelete($filename)
{
	$result = false;
	if (is_file($filename)) {
		if (is_writeable($filename)) {
			$result = unlink($filename);
		}
	}
	return $result;
}
//------------------------------------------------------------------------------

function upload($name, $filename, $overwrite = true)
{
	$result = false;
	if (substr($filename, -1) == '/') {
		$filename .= option('filesTranslitNames')?Translit($_FILES[$name]['name']):$_FILES[$name]['name'];
		if (file_exists($filename) && ((is_string($overwrite) && $filename != $overwrite ) || (is_bool($overwrite) && !$overwrite))) {
			$i = strrpos($filename, '.');
			$fname = substr($filename, 0, $i);
			$fext = substr($filename, $i);
			$i = 1;
			while (is_file($fname.$i.$fext)) $i++;
			$filename = $fname.$i.$fext;
		}
	}
	switch($_FILES[$name]['error']) {
		case UPLOAD_ERR_OK:
			if (is_uploaded_file($_FILES[$name]['tmp_name'])) {
				$moved = @move_uploaded_file($_FILES[$name]['tmp_name'], $filename);
				if ($moved) {
					if (option('filesModeSetOnUpload')) {
						$mode = option('filesModeDefault');
						$mode = empty($mode) ? 0666 : octdec($mode);
						@chmod($filename, $mode);
					}
					$result = $filename;
				} else ErrorMessage(sprintf(errFileMove, $_FILES[$name]['name'], $filename));
			}
		break;
		case UPLOAD_ERR_INI_SIZE: ErrorMessage(sprintf(errUploadSizeINI, $_FILES[$name]['name'])); break;
		case UPLOAD_ERR_FORM_SIZE: ErrorMessage(sprintf(errUploadSizeFORM, $_FILES[$name]['name'])); break;
		case UPLOAD_ERR_PARTIAL: ErrorMessage(sprintf(errUploadPartial, $_FILES[$name]['name'])); break;
		case UPLOAD_ERR_NO_FILE: if (strlen($_FILES[$name]['name'])) ErrorMessage(sprintf(errUploadNoFile, $_FILES[$name]['name'])); break;
	}
	return $result;
}
//-----------------------------------------------------------------------------

/**
 * @deprecated
 */
function loadTemplate($name)
# Считывает указанный шаблон
{
	$filename = Eresus_CMS::app()->getFsRoot() . '/templates/'.$name.(strpos($name, '.html')===false?'.html':'');
	if (file_exists($filename)) {
		$result['html'] = file_get_contents($filename);
		preg_match('/<!--(.*?)-->/', $result['html'], $result['description']);
		$result['description'] = trim($result['description'][1]);
		$result['html'] = trim(substr($result['html'], strpos($result['html'], "\n")));
	} else $result = false;
	return $result;
}
//-----------------------------------------------------------------------------

/**
 * @deprecated
 */
function saveTemplate($name, $template)
# Сохраняет указанный шаблон
{
	$file = "<!-- ".$template['description']." -->\r\n\r\n".$template['html'];
	$fp = fopen(Eresus_CMS::app()->getFsRoot() . '/templates/'.$name.(strpos($name, '.tmpl')===false?'.html':''), 'w');
	fwrite($fp, $file);
	fclose($fp);
}
//-----------------------------------------------------------------------------

function HttpAnswer($answer)
{
	Header('Content-type: text/html; charset='.CHARSET);
	echo $answer;
	exit;
}
//-----------------------------------------------------------------------------

function SendXML($data)
# Отправляет браузеру XML
{
	Header('Content-Type: text/xml');
	echo '<?xml version="1.0" encoding="'.CHARSET.'"?>'."\n<root>".$data."</root>";
	exit;
}
//-----------------------------------------------------------------------------

function option($name)
{
	$result = defined($name)?constant($name):'';
	return $result;
}
//-----------------------------------------------------------------------------

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


	if (strpos($imagename, httpRoot) !== false) $imagename = str_replace(httpRoot, '', $imagename);
	if (strpos($imagename, Eresus_CMS::app()->getFsRoot()) !== false)
	{
		$imagename = str_replace(Eresus_CMS::app()->getFsRoot(), '', $imagename);
	}
	if (strpos($imagename, '://') === false) $imagename = httpRoot.$imagename;
	$local = (strpos($imagename, httpRoot) === 0);

	if ($p['autosize'] && $local && empty($p['width']) && empty($p['height'])) {
		$filename = str_replace(httpRoot, Eresus_CMS::app()->getFsRoot(), $imagename);
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
//-----------------------------------------------------------------------------

function FormatSize($size)
{
	if ($size > 1073741824) {$size = $size / 1073741824; $units = 'Гб'; $z = 2;}
	elseif ($size > 1048576) {$size = $size / 1048576; $units = 'Мб'; $z = 2;}
	elseif ($size > 1024) {$size = $size / 1024; $units = 'Кб'; $z = 2;}
	else {$units = 'Байт'; $z = 0;}
	return number_format($size, $z, '.', ' ').' '.$units;
}
//-----------------------------------------------------------------------------

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
//-----------------------------------------------------------------------------

function __clearargs($args)
{
	global $Eresus;

	if (count($args))
		foreach($args as $key => $value)
			if (gettype($args[$key]) == 'array')
			{
				$args[$key] = __clearargs($args[$key]);
			}
				else
			{
				if (version_compare(PHP_VERSION, '5.3', '<'))
				{
					if (get_magic_quotes_gpc())
						$value = StripSlashes($value);
				}
				if (strpos($key, 'wyswyg_') === 0)
				{
					unset($args[$key]);
					$key = substr($key, 7);
					$value = preg_replace('/(<[^>]+) ilo-[^\s>]*/i', '$1', $value);
					$value = str_replace(array('%28', '%29'), array('(',')'), $value);
					$value = str_replace($Eresus->root, '$(httpRoot)', $value);
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
//-----------------------------------------------------------------------------

/**
 * Определяет установлено ли свойство у элемента
 *
 * @param mixed  $object    Элемент
 * @param string $property  Свойство
 * @return bool Значение
 *
 * @see replaceMacros
 */
function __isset($object, $property)
{
	return
		is_object($object) ? isset($object->$property) : (
			is_array ($object) ? isset($object[$property]) :
			false
		);
}
//-----------------------------------------------------------------------------

/**
 * Возвращает свойство элемента
 *
 * @param mixed  $object    Элемент
 * @param string $property  Свойство
 * @return string Значение
 *
 * @see replaceMacros
 */
function __property($object, $property)
{
	return
		is_object($object) ? $object->$property : (
			is_array ($object) ? $object[$property] :
			''
		);
}
//-----------------------------------------------------------------------------

/**
 * Основной класс приложения
 *
 * @package Core
 */
class Eresus
{
	/**
	 * Конфигурация
	 *
	 * @var array
	 */
	var $conf = array(
		'lang' => 'ru',
		'timezone' => '',
		'db' => array(
			'engine'   => 'mysql',
			'host'     => 'localhost',
			'user'     => '',
			'password' => '',
			'name'     => '',
			'prefix'   => '',
		),
		'session' => array(
			'timeout' => 30,
		),
		'extensions' => array(),
		'backward' => array(
			'TPlugin' => false,
			'TContentPlugin' => false,
			'TListContentPlugin' => false,
		),
		'debug' => array(
			'enable' => false,
			'mail' => true,
		),
	);

 /**
	* Интерфейс к расширениям системы
	*
	* @var unknown_type
	*/
	var $extensions;

	/**
	 * Интерфейс к БД
	 * @var LegacyDB
	 */
	public $db;

	/**
	 * Плагины
	 * @var Plugins
	 */
	public $plugins;
 /**
	* Учётная запись пользователя
	*
	* @var EresusAccount
	*/
	var $user;

	var $host;
 /**
	* @deprecated since 2.11
	*/
	var $https;
	var $path;
	var $root; # Корневой URL
	var $data; # URL данных
	var $style; # URL стилей
	var $froot; # Корневая директория
	var $fdata; # Директория данных
	var $fstyle; # Директория стилей

	var $request;
	var $sections;

	/**
	 * Определяет файловые пути
	 *
	 * @return void
	 */
	protected function init_resolve()
	{
		if (is_null($this->froot))
		{
			$this->froot = FS::driver()->nativeForm(Eresus_CMS::app()->getFsRoot() . '/');
		}

		$this->fdata = $this->froot . 'data' . DIRECTORY_SEPARATOR;
		$this->fstyle = $this->froot . 'style' . DIRECTORY_SEPARATOR;

		if (is_null($this->path))
		{
			$s = $this->froot;
			$s = substr(dirname($_SERVER['SCRIPT_FILENAME']), strlen($_SERVER['DOCUMENT_ROOT']));
			$s = FS::driver()->canonicalForm($s);
			if (strlen($s) == 0 || substr($s, -1) != '/')
			{
				$s .= '/';
			}
			if (substr($s, 0, 1) != '/')
			{
				$s = '/' . $s;
			}
			$this->path = $s;
		}
	}
	//------------------------------------------------------------------------------

	/**
	* Читает настройки
	*
	* @access  private
	*/
	function init_settings()
	{
		$filename = $this->froot.'cfg/settings.php';
		if (is_file($filename)) include_once($filename);
		else FatalError("Settings file '$filename' not found!");
	}
	//------------------------------------------------------------------------------
	/**
	* Первичный разбор запроса
	*
	* @access  private
	*/
	function init_request()
	{
		global $request;

		# Значения по умолчанию
		$request = array(
			'method' => $_SERVER['REQUEST_METHOD'],
			'scheme' => isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https' : 'http',
			'host' => strtolower(is_null($this->host) ? $_SERVER['HTTP_HOST'] : $this->host),
			'port' => '',
			'user' => isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : '',
			'pass' => isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '',
			'path' => '',
			'query' => '',
			'fragment' => '', # TODO: Можно ли узнать значение этого компонента?
			'referer' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
		);

		$request['url'] = $request['scheme'].'://'.$request['host'].$_SERVER['REQUEST_URI'];

		$request = array_merge($request, parse_url($request['url']));
		$request['file'] = substr($request['path'], strrpos($request['path'], '/')+1);
		if ($request['file']) $request['path'] = substr($request['path'], 0, -strlen($request['file']));

		# Создаем заготовку URL для GET-запросов с параметрами
		$request['link'] = $request['url'];
		if (substr($request['link'], -1) == '/') $request['link'] .= '?';
		elseif (strpos($request['link'], '?') === false)  $request['link'] .= '?';
		else $request['link'] .= '&';

		if (is_null($this->path)) {
			$s = $this->froot;
			$s = substr($s, strlen(realpath($_SERVER['DOCUMENT_ROOT']))-(Eresus_Kernel::isWindows()?2:0));
			if (!strlen($s) || sbstr($s, -1) != '/') $s .= '/';
			$this->path = (substr($s, 0, 1) != '/' ? '/' : '').$s;
		}

		/*
		 * Установка свойств объекта $Eresus
		 * Должна выполняться ДО вызова __clearargs
		 */
		$root = $request['scheme'].'://'.$request['host'].($request['port'] ? ':'.$request['port'] : '');
		$this->host = $request['host'];
		$this->root = $root.$this->path;
		$this->data = $this->root.'data/';
		$this->style = $this->root.'style/';


		# Сбор аргументов вызова
		$request['arg'] = __clearargs(array_merge($_GET, $_POST));
		# Разбивка параметров вызова скрипта
		$s = substr($request['path'], strlen($this->path));
		$request['params'] = $s ? explode('/', substr($s, 0, -1)) : array();

		$request['path'] = $root.$request['path'];

		# Обратная совместимость
		# <= 2.9
		$this->request = &$request;
		define('httpPath', $this->path);
		define('httpHost', $this->host);
		define('httpRoot', $this->root);
		define('styleRoot', $this->style);
		define('dataRoot', $this->data);
		define('cookieHost', $this->host);
		define('cookiePath', $this->path);
		# 2.10
		$this->https = $request['scheme'] == 'https';
	}
	//------------------------------------------------------------------------------

 /**
	* Подключение базовых классов
	*
	* @access private
	*/
	function init_classes()
	{
		$filename = $this->froot.'core/classes.php';
		if (is_file($filename))
		{
			include_once($filename);
		}
		else
		{
			FatalError("Classes file '$filename' not found!");
		}
	}
	//------------------------------------------------------------------------------
 /**
	* Инициализация расширений
	*/
	function init_extensions()
	{
		$filename = $this->froot.'cfg/extensions.php';
		if (is_file($filename)) include_once($filename);

		$this->extensions = new EresusExtensions();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Инициализация механизма плагинов
	 */
	function init_plugins()
	{
		$this->plugins = new Plugins;
	}
	//------------------------------------------------------------------------------

	/**
	 * Инициализация системы
	 *
	 * @access public
	 */
	function init()
	{
		// Отключение закавычивания передаваемых данных
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			set_magic_quotes_runtime(0);
		}
		if ($this->conf['timezone'])
		{
			date_default_timezone_set($this->conf['timezone']);
		}
		# Определение путей
		$this->init_resolve();
		# Изменяем путь поиска подключаемых файлов
		set_include_path(dirname(__FILE__).DIRECTORY_SEPARATOR.'lib'.PATH_SEPARATOR.get_include_path());
		# Читаем настройки
		$this->init_settings();
		# Первичный разбор запроса
		$this->init_request();
		# Подключение базовых классов
		$this->init_classes();
		# Инициализация расширений
		$this->init_extensions();
		# Инициализация механизма плагинов
		$this->init_plugins();
		# Подключение работы с разделами сайта
		useLib('sections');
		$this->sections = new Sections;
		$GLOBALS['KERNEL']['loaded'] = true; # Флаг загрузки ядра
	}
	//------------------------------------------------------------------------------
}

