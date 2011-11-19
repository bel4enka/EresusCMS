<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2004, ProCreat Systems, http://procreat.ru/
 * @copyright 2007, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mk@procreat.ru>
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
 * @package Eresus
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



/**
 * Возвращает константу для подстановки в макросы
 *
 * @param array $matches
 *
 * @return mixed
 *
 * @since 2.14
 */
function __macroConst(array $matches)
{
	return constant($matches[1]);
}
//-----------------------------------------------------------------------------

/**
 * Возвращает глобальную переменную для подстановки в макросы
 *
 * @param array $matches
 *
 * @return mixed
 *
 * @since 2.14
 */
function __macroVar(array $matches)
{
	$result = $GLOBALS[$matches[2]];
	if (!empty($matches[3]))
	{
		@eval('$result = $result'.$matches[3].';');
	}
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
	if (PHP_SAPI == 'cli')
	{
		$result = strip_tags(preg_replace('!<br(\s/)?>!i', "\n", $msg))."\n";
	}
	else
	{
		$result =
			"<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n\n".
			"<html>\n".
			"<head>\n".
			"  <title>". i18n('Ошибка') . "</title>\n".
			"  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\n".
			"</head>\n\n".
			"<body>\n".
			"  <div align=\"center\" style=\"font-family: Arial, Helvetica, sans-serif;\">\n".
			"    <table cellspacing=\"0\" style=\"border-style: solid; " .
				"border-color: #e88 #800 #800 #e88; min-width: 500px;\">\n".
			"      <tr><td style=\"border-style: solid; border-width: 2px; " .
				"border-color: #800 #e88 #e88 #800; background-color: black; color: yellow; " .
				"font-weight: bold; text-align: center; font-size: 10pt;\">" . i18n('Ошибка') .
				"</td></tr>\n".
			"      <tr><td style=\"border-style: solid; border-width: 2px; " .
				"border-color: #800 #e88 #e88 #800; background-color: #c00; padding: 10; color: white; " .
				"font-weight: bold; font-family: verdana, tahoma, Geneva, sans-serif; font-size: 8pt;\">\n".
			"        <p style=\"text-align: center\">".$msg."</p>\n".
			"        <div align=\"center\"><br /><a href=\"javascript:history.back()\" " .
				"style=\"font-weight: bold; color: black; text-decoration: none; font-size: 10pt; " .
				"height: 20px; background-color: #aaa; border-style: solid; border-width: 1px; " .
				"border-color: #ccc #000 #000 #ccc; padding: 0 2em;\">".strReturn."</a></div>\n".
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
 * @deprecated с 2.17
 */
function ErrorBox($text, $caption = null)
{
	if (is_null($caption))
	{
		$caption = i18n('Ошибка');
	}
	$result =
		(empty($caption)?'':"<div class=\"errorBoxCap\">".$caption."</div>\n").
		"<div class=\"errorBox\">\n".
		$text.
		"</div>\n";
	return $result;
}
//------------------------------------------------------------------------------

/**
 * Функция выводит сообщение о пользовательской ошибке, но НЕ прекращает работу скрипта.
 */
function InfoBox($text, $caption=strInformation)
{
	$result =
		(empty($caption)?'':"<div class=\"infoBoxCap\">".$caption."</div>\n").
		"<div class=\"infoBox\">\n".
		$text.
		"</div>\n";
	return $result;
}
//------------------------------------------------------------------------------

function ErrorMessage($message)
{
	global $Eresus;
	$Eresus->session['msg']['errors'][] = $message;
}
//------------------------------------------------------------------------------

function InfoMessage($message)
{
	global $Eresus;
	$Eresus->session['msg']['information'][] = $message;
}
//------------------------------------------------------------------------------

/**
 * Функция проверяет права пользователя на соответствие заданной маске
 */
function UserRights($level)
{
	global $Eresus;

	return (
		(
			($Eresus->user['auth']) &&
			($Eresus->user['access'] <= $level) &&
			($Eresus->user['access'] != 0)
		) ||
		($level == GUEST)
	);
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
	if (DIRECTORY_SEPARATOR != '/')
	{
		$library = str_replace('/', DIRECTORY_SEPARATOR, $library);
	}
	$filename = DIRECTORY_SEPARATOR . $library . '.php';
	$dirs = explode(PATH_SEPARATOR, get_include_path());
	foreach ($dirs as $path)
	{
		if (is_file($path.$filename))
		{
			include_once($path . $filename);
			$result = true;
			break;
		}
	}
	return $result;
}
//------------------------------------------------------------------------------

/**
 * Функция отсылает письмо по указанному адресу
 */
function sendMail($address, $subject, $text, $html=false, $fromName='', $fromAddr='', $fromOrg='',
	$fromSign='', $replyTo='')
{
	global $Eresus;

	if (empty($fromName))
	{
		$fromName = option('mailFromName');
	}
	if (empty($fromAddr))
	{
		$fromAddr = option('mailFromAddr');
	}
	if (empty($fromOrg))
	{
		$fromOrg = option('mailFromOrg');
	}
	if (empty($fromSign))
	{
		$fromSign = option('mailFromSign');
	}
	if (empty($replyTo))
	{
		$replyTo = option('mailReplyTo');
	}
	if (empty($replyTo))
	{
		$replyTo = $fromAddr;
	}

	$charset = 'UTF-8';

	$sender = strlen($fromName) ? "=?".$charset."?B?".base64_encode($fromName)."?= <$fromAddr>" :
		$fromAddr;
	if (strlen($fromOrg))
	{
		$sender .= ' (=?'.$charset.'?B?'.base64_encode($fromOrg).'?=)';
	}
	if (mb_strpos($sender, '@') === false)
	{
		$sender = 'no-reply@'.preg_replace('/^www\./', '', httpHost);
	}
	$fromSign = "\n-- \n".$fromSign;
	if ($html)
	{
		$fromSign = nl2br($fromSign);
	}
	if (strlen($fromSign))
	{
		$text .= $fromSign;
	}

	$headers =
		"MIME-Version: 1.0\n".
		"From: $sender\n".
		"Subject: $subject\n".
		"Reply-To: $replyTo\n".
		"X-Mailer: PHP/" . phpversion()."\n";

	if ($html)
	{
		$text = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n\n" .
			"<html>\n<head></head>\n<body>\n".$text."\n</body>\n</html>";

		$boundary="=_".md5(uniqid(time()));
		$headers.="Content-Type: multipart/mixed; boundary=$boundary\n";
		$multipart="";
		$multipart.="This is a MIME encoded message.\n\n";

		$multipart.="--$boundary\n";
		$multipart.="Content-Type: text/html; charset=$charset\n";
		$multipart.="Content-Transfer-Encoding: Base64\n\n";
		$multipart.=chunk_split(base64_encode($text))."\n\n";
		$multipart.="--$boundary--\n";
		$text = $multipart;
	}
	else
	{
		$headers .= "Content-type: text/plain; charset=$charset\n";
	}

	return (mail($address, $subject, $text, $headers)===0);
}
//-----------------------------------------------------------------------------

/**
 * Возвращает время с учетом смещения
 */
function gettime($format = 'Y-m-d H:i:s')
{
	#$delta = (GMT_ZONE * 3600) - date('Z'); // Смещение на нужный часовой пояс
	$delta = 0;
	return date($format , time() + $delta); // Время, со смещением на наш часовой пояс
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
	switch (true)
	{
		case is_string($source):
			$source = strtr($source, $translationTable);
		break;

		case is_array($source):
			foreach ($source as $key => $value)
			{
				$source[$key] = strtr($value, $translationTable);
			}
		break;
	}
	return $source;
}
//-----------------------------------------------------------------------------

/**
 * Декодирует спецсимволы HTML
 */
function decodeHTML($text)
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
	if (!empty($result))
	{
		$result = str_replace("\r",'',$result);
		$result = explode("\n", $result);
		if ($assoc && count($result))
		{
			foreach ($result as $item)
			{
				$item = explode('=', $item);
				$key = trim($item[0]);
				if ($key !== '')
				{
					$value = isset($item[1]) ? trim($item[1]) : null;
					$items[$key] = $value;
				}
			}
			$result = $items;
		}
	}
	else
	{
		$result = array();
	}
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
	if (count($items))
	{
		if ($assoc)
		{
			foreach ($items as $key => $value)
			{
				$result[] = $key.'='.$value;
			}
		}
		else
		{
			$result = $items;
		}
		$result = implode("\n", $result);
	}
	return $result;
}
//-----------------------------------------------------------------------------

/**
 * Собирает настройки из массива в строку
 */
function encodeOptions($options)
{
	$result = serialize($options);
	return $result;
}
//-----------------------------------------------------------------------------

/**
 * Функция разбивает записанные в строковом виде опции на массив
 */
function decodeOptions($options, $defaults = array())
{
	if (empty($options))
	{
		$result = $defaults;
	}
	else
	{
		@$result = unserialize($options);
		if (gettype($result) != 'array')
		{
			$result = $defaults;
		}
		else
		{
			if (count($defaults))
			{
				foreach ($defaults as $key => $value)
				{
					if (!array_key_exists($key, $result))
					{
						$result[$key] = $value;
					}
				}
			}
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
	if (count($matches))
	{
		foreach ($matches as $macros)
		{
			if (__isset($source, $macros[1]))
			{
				$template = str_replace($macros[0], __property($source, $macros[1])?$macros[2]:$macros[3],
					$template);
			}
		}
	}

	// Замена обычных макросов
	preg_match_all('/\$\(([^(]+)\)/U', $template, $matches);
	if (count($matches[1]))
	{
		foreach ($matches[1] as $macros)
		{
			if (__isset($source, $macros))
			{
				$template = str_replace('$('.$macros.')', __property($source, $macros), $template);
			}
		}
	}
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
		switch ($filter)
		{
			case 'dbsafe':
				$arg = $Eresus->db->escape($arg);
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
 */
function saveRequest()
{
	global $Eresus;
	$Eresus->session['request'] = $Eresus->request;
}
//-----------------------------------------------------------------------------

/**
 * Функция сохраняет в сессии текущие аргументы
 */
function restoreRequest()
{
	global $Eresus;
	if (isset($Eresus->session['request']))
	{
		$Eresus->request = $Eresus->session['request'];
		unset($Eresus->session['request']);
	}
}
//-----------------------------------------------------------------------------

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
	for ($i=0; $i<count($items); $i++)
	{
		$Eresus->db->update($table, "`position` = $i", "`".$id."`='".$items[$i][$id]."'");
	}
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
	for ($i=0; $i<count($items); $i++)
	{
		$Eresus->db->update($table, "`position` = `position` + $delta",
			"`".$id."`='".$items[$i][$id]."'");
	}
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
	if (is_file($filename))
	{
		if (is_readable($filename))
		{
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
	if ($fp)
	{
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
	if (is_file($filename))
	{
		if (is_writeable($filename))
		{
			$result = unlink($filename);
		}
	}
	return $result;
}
//------------------------------------------------------------------------------

function upload($name, $filename, $overwrite = true)
{
	$result = false;
	if (substr($filename, -1) == '/')
	{
		$filename .= option('filesTranslitNames') ?
			Translit($_FILES[$name]['name']) :
			$_FILES[$name]['name'];
		if (file_exists($filename) &&
			((is_string($overwrite) && $filename != $overwrite ) || (is_bool($overwrite) && !$overwrite))
		)
		{
			$i = strrpos($filename, '.');
			$fname = substr($filename, 0, $i);
			$fext = substr($filename, $i);
			$i = 1;
			while (is_file($fname.$i.$fext))
			{
				$i++;
			}
			$filename = $fname.$i.$fext;
		}
	}
	switch ($_FILES[$name]['error'])
	{
		case UPLOAD_ERR_OK:
			if (is_uploaded_file($_FILES[$name]['tmp_name']))
			{
				$moved = @move_uploaded_file($_FILES[$name]['tmp_name'], $filename);
				if ($moved)
				{
					if (option('filesModeSetOnUpload'))
					{
						$mode = option('filesModeDefault');
						$mode = empty($mode) ? 0666 : octdec($mode);
						@chmod($filename, $mode);
					}
					$result = $filename;
				}
				else
				{
					ErrorMessage(sprintf(errFileMove, $_FILES[$name]['name'], $filename));
				}
			}
		break;
		case UPLOAD_ERR_INI_SIZE:
			ErrorMessage(sprintf(errUploadSizeINI, $_FILES[$name]['name']));
		break;
		case UPLOAD_ERR_FORM_SIZE:
			ErrorMessage(sprintf(errUploadSizeFORM, $_FILES[$name]['name']));
		break;
		case UPLOAD_ERR_PARTIAL:
			ErrorMessage(sprintf(errUploadPartial, $_FILES[$name]['name']));
		break;
		case UPLOAD_ERR_NO_FILE:
			if (strlen($_FILES[$name]['name']))
			{
				ErrorMessage(sprintf(errUploadNoFile, $_FILES[$name]['name']));
			}
		break;
	}
	return $result;
}
//-----------------------------------------------------------------------------

/**
 *
 * @param unknown_type $answer
 *
 * @return void
 *
 * @since ?.??
 *
 * @deprecated с 2.17
 */
function HttpAnswer($answer)
{
	Header('Content-type: text/html; charset=UTF-8');
	echo $answer;
	exit;
}
//-----------------------------------------------------------------------------

/**
 * Отправляет браузеру XML
 *
 * @deprecated с 2.17
 */
function SendXML($data)
{
	Header('Content-Type: text/xml');
	echo '<?xml version="1.0" encoding="UTF-8"?>'."\n<root>".$data."</root>";
	exit;
}
//-----------------------------------------------------------------------------

function option($name)
{
	$result = defined($name)?constant($name):'';
	return $result;
}
//-----------------------------------------------------------------------------

/**
 * function img($imagename, $alt='', $title='', $width=0, $height=0, $style='')
 * function img($imagename, $params=array())
 * Функция возвращает заполненный тэг <img>
 */
function img($imagename)
{
	$argc = func_num_args();
	$argv = func_get_args();
	if ($argc > 1)
	{
		if (is_array($argv[1]))
		{
			$p = $argv[1];
		}
		else
		{
			$p['alt'] = $argv[1];
			if ($argc > 2)
			{
				$p['title'] = $argv[2];
			}
			if ($argc > 3)
			{
				$p['width'] = $argv[3];
			}
			if ($argc > 4)
			{
				$p['height'] = $argv[4];
			}
			if ($argc > 5)
			{
				$p['style'] = $argv[5];
			}
		}
	}
	if (!isset($p['alt']))
	{
		$p['alt'] = '';
	}
	if (!isset($p['title']))
	{
		$p['title'] = '';
	}
	if (!isset($p['width']))
	{
		$p['width'] = '';
	}
	if (!isset($p['height']))
	{
		$p['height'] = '';
	}
	if (!isset($p['style']))
	{
		$p['style'] = '';
	}
	if (!isset($p['ext']))
	{
		$p['ext'] = '';
	}
	if (!isset($p['autosize']))
	{
		$p['autosize'] = true;
	}

	if (strpos($imagename, httpRoot) !== false)
	{
		$imagename = str_replace(httpRoot, '', $imagename);
	}
	if (strpos($imagename, filesRoot) !== false)
	{
		$imagename = str_replace(filesRoot, '', $imagename);
	}
	if (strpos($imagename, '://') === false)
	{
		$imagename = httpRoot . $imagename;
	}
	$local = (strpos($imagename, httpRoot) === 0);

	if ($p['autosize'] && $local && empty($p['width']) && empty($p['height']))
	{
		$filename = str_replace(httpRoot, filesRoot, $imagename);
		if (is_file($filename))
		{
			$info = getimagesize($filename);
		}
	}
	if (isset($info))
	{
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
	if ($size > 1073741824)
	{
		$size = $size / 1073741824;
		$units = 'Гб';
		$z = 2;
	}
	elseif ($size > 1048576)
	{
		$size = $size / 1048576;
		$units = 'Мб';
		$z = 2;
	}
	elseif ($size > 1024)
	{
		$size = $size / 1024;
		$units = 'Кб';
		$z = 2;
	}
	else
	{
		$units = 'Байт';
		$z = 0;
	}
	return number_format($size, $z, '.', ' ').' '.$units;
}
//-----------------------------------------------------------------------------

/**
 * @deprecated с 2.17
 */
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
	{
		foreach ($args as $key => $value)
		{
			if (gettype($args[$key]) == 'array')
			{
				$args[$key] = __clearargs($args[$key]);
			}
			else
			{
				if ( ! PHP::checkVersion('5.3') )
				{
					if (get_magic_quotes_gpc())
					{
						$value = StripSlashes($value);
					}
				}
				if (strpos($key, 'wyswyg_') === 0)
				{
					unset($args[$key]);
					$key = substr($key, 7);
					$value = preg_replace('/(<[^>]+) ilo-[^\s>]*/i', '$1', $value);
					$value = str_replace(array('%28', '%29'), array('(',')'), $value);
					$value = str_replace($Eresus->root, '$(httpRoot)', $value);
					preg_match_all('/<img.*?>/', $value, $images, PREG_OFFSET_CAPTURE);
					if (count($images[0]))
					{
						$images = $images[0];
						$delta = 0;
						for ($i = 0; $i < count($images); $i++)
						{
							if (!preg_match('/alt=/i', $images[$i][0]))
							{
								$s = preg_replace('/(\/?>)/', 'alt="" $1', $images[$i][0]);
								$value = substr_replace($value, $s, $images[$i][1]+$delta,
									mb_strlen($images[$i][0]));
								$delta += mb_strlen($s) - mb_strlen($images[$i][0]);
							}
						}
					}
				}
				$args[$key] = $value;
			}
		}
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
 * @package Eresus
 */
class Eresus
{
	/**
	 * Конфигурация
	 *
	 * @var array
	 */
	var $conf = array(
		'extensions' => array(),
	);

	/**
	 * Данные сессии
	 *
	 * @var array
	 */
	var $session;

	/**
	 * Интерфейс к расширениям системы
	 *
	 * @var unknown_type
	 */
	var $extensions;

	/**
	 * Интерфейс к БД
	 * @var MySQL
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

	//------------------------------------------------------------------------------
	// Информация о системе
	//------------------------------------------------------------------------------
	/**
	 * @deprecated since 2.14
	 */
	function isWin32()
	{
		return System::isWindows();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @deprecated since 2.14
	 */
	function isUnix()
	{
		return System::isUnixLike();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @deprecated since 2.14
	 */
	function isMac()
	{
		return System::isMac();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @deprecated since 2.14
	 */
	function isModule()
	{
		return PHP::isModule();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @deprecated since 2.14
	 */
	function isCgi()
	{
		return PHP::isCGI();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @deprecated since 2.14
	 */
	function isCli()
	{
		return PHP::isCLI();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Читает и применяет конфигурационный файл
	 */
	private function init_config()
	{
		/*
		 * Переменную $Eresus надо сделать глобальной чтобы файл конфигурации
		 * мог записывать в неё свои значения.
		 */
		global $Eresus;

		$filename = Eresus_Kernel::app()->getFsRoot() . '/cfg/main.php';
		$nativeFilename = FS::nativeForm($filename);
		if (FS::isFile($filename))
		{
			include_once $nativeFilename;
		}
		else
		{
			FatalError("Main config file '$nativeFilename' not found!");
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Инициирует сессии
	 */
	private function init_session()
	{
		session_set_cookie_params(ini_get('session.cookie_lifetime'), $this->path);
		session_name('sid');
		session_start();
		$this->session = &$_SESSION['session'];
		if (!isset($this->session['msg']))
		{
			$this->session['msg'] = array('error' => array(), 'information' => array());
		}
		$this->user = &$_SESSION['user'];

		# Обратная совместимость
		$GLOBALS['session'] = &$_SESSION['session'];
		$GLOBALS['user'] = &$_SESSION['user'];
	}
	//-----------------------------------------------------------------------------

	/**
	 * Определяет файловые пути
	 *
	 * @return void
	 */
	protected function init_resolve()
	{
		if (is_null($this->froot))
		{
			$this->froot = FS::nativeForm(Eresus_Kernel::app()->getFsRoot() . '/');
		}

		$this->fdata = $this->froot . 'data' . DIRECTORY_SEPARATOR;
		$this->fstyle = $this->froot . 'style' . DIRECTORY_SEPARATOR;

		if (is_null($this->path))
		{
			$s = $this->froot;
			$s = substr(dirname($_SERVER['SCRIPT_FILENAME']), strlen($_SERVER['DOCUMENT_ROOT']));
			$s = FS::canonicalForm($s);
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

		/**
		 * Обратная совместимость
		 * @var string
		 * @deprecated since 2.14
		 */
		define('filesRoot', $this->froot);

		/**
		 * Обратная совместимость
		 * @var string
		 * @deprecated since 2.14
		 */
		define('dataFiles', $this->fdata);
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
		if (is_file($filename))
		{
			include_once($filename);
		}
		else
		{
			FatalError("Settings file '$filename' not found!");
		}
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

		$request['url'] = $request['scheme'] . '://' . $request['host'] . $_SERVER['REQUEST_URI'];

		$request = array_merge($request, parse_url($request['url']));
		$request['file'] = substr($request['path'], strrpos($request['path'], '/')+1);
		if ($request['file'])
		{
			$request['path'] = substr($request['path'], 0, -strlen($request['file']));
		}

		# Создаем заготовку URL для GET-запросов с параметрами
		$request['link'] = $request['url'];
		if (substr($request['link'], -1) == '/')
		{
			$request['link'] .= '?';
		}
		elseif (strpos($request['link'], '?') === false)
		{
			$request['link'] .= '?';
		}
		else
		{
			$request['link'] .= '&';
		}

		if (is_null($this->path))
		{
			$s = $this->froot;
			$s = substr($s, strlen(realpath($_SERVER['DOCUMENT_ROOT']))-($this->isWin32()?2:0));
			if (!strlen($s) || sbstr($s, -1) != '/')
			{
				$s .= '/';
			}
			$this->path = (substr($s, 0, 1) != '/' ? '/' : '').$s;
		}

		/*
		 * Установка свойств объекта $Eresus
		 * Должна выполняться ДО вызова __clearargs
		 */
		$root = $request['scheme'] . '://' . $request['host'] .
			($request['port'] ? ':'.$request['port'] : '');
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
	 * Инициализация локали
	 *
	 * @access private
	 */
	function init_locale()
	{
		global $locale;

		$locale['lang'] = substr(Eresus_Config::get('eresus.cms.locale.default', 'ru_RU'), 0, 2);
		$locale['prefix'] = '';

		# Подключение строковых данных
		$filename = $this->froot.'lang/'.$locale['lang'].'.php';
		if (is_file($filename))
		{
			include_once($filename);
		}
		else
		{
			FatalError("Locale file '$filename' not found!");
		}
	}
	//------------------------------------------------------------------------------

	/**
	 * Подключение базовых классов
	 *
	 * @access private
	 */
	function init_classes()
	{
		# Подключение строковых данных
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
		if (is_file($filename))
		{
			include_once($filename);
		}

		$this->extensions = new EresusExtensions();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Подключение к источнику данных
	 *
	 * @access private
	 */
	function init_datasource()
	{
		$this->db = new MySQL();
	}
	//------------------------------------------------------------------------------

	/**
	 * Инициализация механизма плагинов
	 */
	function init_plugins()
	{
		$this->plugins = new Plugins;
		$this->plugins->init();
	}
	//------------------------------------------------------------------------------

	/**
	 * Инициализация учётной записи пользователя
	 *
	 */
	function init_user()
	{
		useLib('accounts');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка сессии
	 *
	 * @access private
	 */
	function check_session()
	{
		if (isset($this->session['time']))
		{
			if (
				(time() - $this->session['time'] >
					Eresus_Config::get('eresus.cms.session.timeout', 30) * 3600) &&
				($this->user['auth'])
			)
			{
				$this->logout(false);
			}
			else
			{
				$this->session['time'] = time();
			}
		}
	}
	//------------------------------------------------------------------------------

	/**
	 * Проверка на логин/логаут
	 *
	 */
	function check_loginout()
	{
		switch (arg('action'))
		{
			case 'login':
				$this->login(arg('user'), $this->password_hash(arg('password')), arg('autologin', 'int'));
				HTTP::redirect($this->request['url']);
			break;
			case 'logout':
				$this->logout(true);
				HTTP::redirect($this->root.'admin/');
			break;
		}
	}
	//------------------------------------------------------------------------------

	/**
	 * Попытка cookie-логина
	 */
	function check_cookies()
	{
		if (!$this->user['auth'] && isset($_COOKIE['eresus_login']))
		{
			if (!$this->login($_COOKIE['eresus_login'], $_COOKIE['eresus_key'], true, true))
			{
				$this->clear_login_cookies();
			}
		}
	}
	//------------------------------------------------------------------------------

	/**
	 * Обновление данных о пользователе
	 */
	function reset_login()
	{
		$this->user['auth'] = isset($this->user['auth']) ? $this->user['auth'] : false;
		if ($this->user['auth'])
		{
			$item = $this->db->selectItem('users', "`id`='".$this->user['id']."'");
			if (!is_null($item))
			{
				# Если такой пользователь есть...
				if ($item['active'])
				{
					# Если учетная запись активна...
					$this->user['name'] = $item['name'];
					$this->user['mail'] = $item['mail'];
					$this->user['access'] = $item['access'];
					$this->user['profile'] = decodeOptions($item['profile']);
				}
				else
				{
					ErrorMessage(sprintf(errAccountNotActive, $item['login']));
					$this->logout();
				}
			}
			else
			{
				$this->logout();
			}
		}
		else
		{
			$this->user['access'] = GUEST;
		}
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
		if (!PHP::checkVersion('5.3'))
		{
			set_magic_quotes_runtime(0);
		}
		# Читаем конфигурацию
		$this->init_config();
		if (Eresus_Config::get('eresus.cms.timezone'))
		{
			date_default_timezone_set(Eresus_Config::get('eresus.cms.timezone'));
		}
		# Определение путей
		$this->init_resolve();
		# Инициализация сессии
		$this->init_session();
		# Изменяем путь поиска подключаемых файлов
		set_include_path(dirname(__FILE__) . '/lib' . PATH_SEPARATOR . get_include_path());
		# Читаем настройки
		$this->init_settings();
		# Первичный разбор запроса
		$this->init_request();
		# Настройка локали
		$this->init_locale();
		# Подключение базовых классов
		$this->init_classes();
		# Инициализация расширений
		$this->init_extensions();
		# Подключение к источнику данных
		$this->init_datasource();
		# Инициализация механизма плагинов
		$this->init_plugins();
		# Инициализация учётной записи пользователя
		$this->init_user();
		# Проверка сессии
		$this->check_session();
		# Проверка логина/логаута
		$this->check_loginout();
		# Попытка cookie-логина
		$this->check_cookies();
		# Обновление данных о пользователе
		$this->reset_login();
		# Подключение работы с разделами сайта
		useLib('sections');
		$this->sections = new Sections;
		$GLOBALS['KERNEL']['loaded'] = true; # Флаг загрузки ядра
	}
	//------------------------------------------------------------------------------

	/**
	 * Хеширует пароль
	 *
	 * @param string $password  Пароль
	 * @return string  Хеш
	 */
	function password_hash($password)
	{
		$result = md5(md5($password));
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает авторизационные кукисы
	 *
	 * @param string $login
	 * @param string $key
	 */
	function set_login_cookies($login, $key)
	{
		setcookie('eresus_login', $login, time()+2592000, $this->path);
		setcookie('eresus_key', $key, time()+2592000, $this->path);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Удалаяет авторизационные кукисы
	 *
	 */
	function clear_login_cookies()
	{
		setcookie('eresus_login', '', time()-3600, $this->path);
		setcookie('eresus_key', '', time()-3600, $this->path);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Авторизация пользователя
	 *
	 * @param string $unsafeLogin   Имя пользователя
	 * @param string $key		       Ключ учётной записи
	 * @param bool   $auto		       Сохранить авторизационные данные на комптютере посетителя
	 * @param bool   $cookie        Авторизация при помощи cookie
	 * @return bool Результат
	 */
	function login($unsafeLogin, $key, $auto = false, $cookie = false)
	{
		$result = false;

		$login = preg_replace('/[^a-z0-9_\-\.\@]/', '', $unsafeLogin);

		if ($login != $unsafeLogin)
		{
			ErrorMessage(errInvalidPassword);
			return false;
		}

		$item = $this->db->selectItem('users', "`login`='$login'");
		if (!is_null($item))
		{
			// Если такой пользователь есть...
			if ($item['active'])
			{
				// Если учетная запись активна...
				if (time() - $item['lastLoginTime'] > $item['loginErrors'])
				{
					if ($key == $item['hash'])
					{
						// Если пароль верен...
						if ($auto)
						{
							$this->set_login_cookies($login, $key);
						}
						else
						{
							$this->clear_login_cookies();
						}
						$setVisitTime = (! isset($this->uset['id'])) || (! (bool) $this->user['id']);
						$this->user = $item;
						$this->user['profile'] = decodeOptions($this->user['profile']);
						$this->user['auth'] = true; # Устанавливаем флаг авторизации
						// Хэш пароля используется для подтверждения аутентификации
						$this->user['hash'] = $item['hash'];
						if ($setVisitTime)
						{
							$item['lastVisit'] = gettime(); # Записываем время последнего входа
						}
						$item['lastLoginTime'] = time();
						$item['loginErrors'] = 0;
						$this->db->updateItem('users', $item,"`id`='".$item['id']."'");
						$this->session['time'] = time(); # Инициализируем время последней активности сессии.
						$result = true;
					}
					else
					{
						// Если пароль не верен...
						if (!$cookie)
						{
							ErrorMessage(errInvalidPassword);
							$item['lastLoginTime'] = time();
							$item['loginErrors']++;
							$this->db->updateItem('users', $item,"`id`='".$item['id']."'");
						}
					}
				}
				else
				{
					// Если авторизация проведена слишком рано
					ErrorMessage(sprintf(errTooEarlyRelogin, $item['loginErrors']));
					$item['lastLoginTime'] = time();
					$this->db->updateItem('users', $item,"`id`='".$item['id']."'");
				}
			}
			else
			{
				ErrorMessage(sprintf(errAccountNotActive, $login));
			}
		}
		else
		{
			ErrorMessage(errInvalidPassword);
		}
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Завершение сеанса работы с системой
	 *
	 * @param bool $clearCookies
	 */
	public function logout($clearCookies=true)
	{
		$this->user['id'] = null;
		$this->user['auth'] = false;
		$this->user['access'] = GUEST;
		if ($clearCookies)
		{
			$this->clear_login_cookies();
		}
	}
	//-----------------------------------------------------------------------------
}
