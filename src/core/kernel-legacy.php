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
 * ������ ��������� �������� ��������� ����������� ������������. ��
 * ������ �������������� �� �/��� �������������� � ������������ �
 * ��������� ������ 3 ���� (�� ������ ������) � ��������� ����� �������
 * ������ ����������� ������������ �������� GNU, �������������� Free
 * Software Foundation.
 *
 * �� �������������� ��� ��������� � ������� �� ��, ��� ��� ����� ���
 * ��������, ������ �� ������������� �� ��� ������� ��������, � ���
 * ����� �������� ��������� ��������� ��� ������� � ����������� ���
 * ������������� � ���������� �����. ��� ��������� ����� ���������
 * ���������� ������������ �� ����������� ������������ ��������� GNU.
 *
 * �� ������ ���� �������� ����� ����������� ������������ ��������
 * GNU � ���� ����������. ���� �� �� �� ��������, �������� �������� ��
 * <http://www.gnu.org/licenses/>
 *
 * @package Eresus
 *
 * $Id$
 */

/**
 * �������� �������
 * @var string
 */
define('CMSNAME', 'Eresus');
define('CMSVERSION', '${product.version}'); # ������ �������
define('CMSLINK', 'http://eresus.ru/'); # ���-����

define('KERNELNAME', 'ERESUS'); # ��� ����
define('KERNELDATE', '${builddate}'); # ���� ���������� ����

# ������ �������
define('ROOT',   1); # ������� �������������
define('ADMIN',  2); # �������������
define('EDITOR', 3); # ��������
define('USER',   4); # ������������
define('GUEST',  5); # ����� (�� ���������������)

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function __macroConst($matches) {
	return constant($matches[1]);
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function __macroVar($matches) {
	$result = $GLOBALS[$matches[2]];
	if (!empty($matches[3])) @eval('$result = $result'.$matches[3].';');
	return $result;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

/**
 * ������� ������� ��������� � ���������������� ������ � ���������� ������ �������.
 *
 * @param string $msg  ����� ���������
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
 * ����� ��������� � ���������������� ������
 *
 * @param string $text     ����� ���������
 * @param string $caption  ��������� ���� ���������
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
# ������� ������� ��������� � ���������������� ������, �� �� ���������� ������ �������.
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

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# ������������
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function UserRights($level)
# ������� ��������� ����� ������������ �� ������������ �������� �����
{
	global $Eresus;

	return ((($Eresus->user['auth']) && ($Eresus->user['access'] <= $level) && ($Eresus->user['access'] != 0)) || ($level == GUEST));
}
//------------------------------------------------------------------------------

/**
 * ����������� ����������
 *
 * @param  string  $libaray  ��� ����������
 *
 * @return  bool  ���������
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
 * ���������� �������� ������
 *
 * @access  public
 *
 * @param  string  $className   ��� ������
 *
 * @return  bool  ��������� ����������
 */
function useClass($className)
{
	$result = false;
	if (DIRECTORY_SEPARATOR != '/') $className = str_replace('/', DIRECTORY_SEPARATOR, $className);
	$filename = realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.$className.'.php';
	if (is_file($filename)) {
		include_once($filename);
		$result = true;
	}
	return $result;
}
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# �������� �������
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function sendMail($address, $subject, $text, $html=false, $fromName='', $fromAddr='', $fromOrg='', $fromSign='', $replyTo='')
# ������� �������� ������ �� ���������� ������
{
	global $Eresus;

	if (empty($fromName)) $fromName = option('mailFromName');
	if (empty($fromAddr)) $fromAddr = option('mailFromAddr');
	if (empty($fromOrg)) $fromOrg = option('mailFromOrg');
	if (empty($fromSign)) $fromSign = option('mailFromSign');
	if (empty($replyTo)) $replyTo = option('mailReplyTo');
	if (empty($replyTo)) $replyTo = $fromAddr;

	$charset = CHARSET;

	$sender = strlen($fromName) ? "=?".$charset."?B?".base64_encode($fromName)."?= <$fromAddr>" : $fromAddr;
	if (strlen($fromOrg)) $sender .= ' (=?'.$charset.'?B?'.base64_encode($fromOrg).'?=)';
	if (strpos($sender, '@') === false) $sender = 'no-reply@'.preg_replace('/^www\./', '', httpHost);
	$fromSign = "\n-- \n".$fromSign;
	if ($html) $fromSign = nl2br($fromSign);
	if (strlen($fromSign)) $text .= $fromSign;

	$headers =
	 "MIME-Version: 1.0\n".
	 "From: $sender\n".
	 "Subject: $subject\n".
	 "Reply-To: $replyTo\n".
	 "X-Mailer: PHP/" . phpversion()."\n";

	if ($html) {

		$text = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n\n<html>\n<head></head>\n<body>\n".$text."\n</body>\n</html>";

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
	} else $headers .= "Content-type: text/plain; charset=$charset\n";

	if ($Eresus->conf['debug']['enable'] && $Eresus->conf['debug']['mail'] !== true) {
		if (is_string($Eresus->conf['debug']['mail'])) {
			$hnd = @fopen($Eresus->conf['debug']['mail'], 'a');
			if ($hnd) {
				fputs($hnd, "\n================================================================================\n$headers\nTo: $address\nSubject: $subject\n\n$text\n");
				fclose($hnd);
			}
			return true;
		}
	} else return (mail($address, $subject, $text, $headers)===0);
}
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-//

//------------------------------------------------------------------------------
# ����/�����
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function gettime($format = 'Y-m-d H:i:s')
# ���������� ����� � ������ ��������
{
	#$delta = (GMT_ZONE * 3600) - date('Z'); // �������� �� ������ ������� ����
	$delta = 0;
	return date($format , time() + $delta); // �����, �� ��������� �� ��� ������� ����
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

/**
 * �������������� ����
 *
 * @param string $date    ���� � ������� YYYY-MM-DD hh:mm:ss
 * @param string $format  ������� �������������� ����
 *
 * @return string ����������������� ����
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

//------------------------------------------------------------------------------
# ������ � �������

/**
 * �������� ����������� HTML
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
# ���������� ����������� HTML
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
/**
 * ��������� ����� �� ������ � ���������� ������ �� ���
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
 * �������� ����� �� �������
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
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function encodeOptions($options)
# �������� ��������� �� ������� � ������
{
	$result = serialize($options);
	return $result;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function decodeOptions($options, $defaults = array())
# ������� ��������� ���������� � ��������� ���� ����� �� ������
{
	if (empty($options)) $result = $defaults; else {
		@$result = unserialize($options);
		if (gettype($result) != 'array') $result = $defaults; else {
			if (count($defaults)) foreach($defaults as $key => $value) if (!array_key_exists($key, $result)) $result[$key] = $value;
		}
	}
	return $result;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
/**
 * ������ ��������
 *
 * @param string $template  ������
 * @param mixed  $source    �������� ��� ������
 * @return ������������ �����
 *
 * @see __propery
 */
function replaceMacros($template, $source)
{
	# ������ �������� ��������
	preg_match_all('/\$\(([^\)\?:]+)\?([^:\)]*):([^\)]*)\)/U', $template, $matches, PREG_SET_ORDER);
	if (count($matches)) foreach($matches as $macros) {
		if (__isset($source, $macros[1])) $template = str_replace($macros[0], __property($source, $macros[1])?$macros[2]:$macros[3], $template);
	}
		# ������ ������� ��������
	preg_match_all('/\$\(([^(]+)\)/U', $template, $matches);
	if (count($matches[1])) foreach($matches[1] as $macros)
		if (__isset($source, $macros)) $template = str_replace('$('.$macros.')', __property($source, $macros), $template);
	return $template;
}
//------------------------------------------------------------------------------

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# ������ � HTTP-��������
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
/**
 * ���������� �������� ��������� �������
 *
 * @param string $arg     ��� ���������
 * @param mixed  $filter  ������ �� ��������
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

function saveRequest()
# ������� ��������� � ������ ������� ���������
{
	global $Eresus;
	$Eresus->session['request'] = $Eresus->request;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function restoreRequest()
# ������� ��������� � ������ ������� ���������
{
	global $Eresus;
	if (isset($Eresus->session['request'])) {
		$Eresus->request = $Eresus->session['request'];
		unset($Eresus->session['request']);
	}
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#


 /*
 	* ������ � ��
	*/

/**
 * �������������� ���������
 *
 * @param string $table      �������
 * @param string $condition  �������
 * @param string $id         ��� ��������� ����
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
 * ����� ������� ���������
 *
 * @param string $table      �������
 * @param string $condition  �������
 * @param string $delta      �������� ������
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

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# ������ � �������
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
/**
 * ������ �����
 *
 * @param string $filename ��� �����
 * @return mixed ���������� ����� ��� false
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
 * ������ � ����
 *
 * @param string $filename ��� �����
 * @param string $content  ����������
 * @param int    $flags    �����
 * @return bool ��������� ����������
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
 * ������� ����
 *
 * @param string $filename ��� �����
 * @return bool ��������� ����������
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
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

function HttpAnswer($answer)
{
	Header('Content-type: text/html; charset='.CHARSET);
	echo $answer;
	exit;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function SendXML($data)
# ���������� �������� XML
{
	Header('Content-Type: text/xml');
	echo '<?xml version="1.0" encoding="'.CHARSET.'"?>'."\n<root>".$data."</root>";
	exit;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function option($name)
{
	$result = defined($name)?constant($name):'';
	return $result;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function img($imagename)
# function img($imagename, $alt='', $title='', $width=0, $height=0, $style='')
# function img($imagename, $params=array())
# ������� ���������� ����������� ��� <img>
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
	if (strpos($imagename, filesRoot) !== false) $imagename = str_replace(filesRoot, '', $imagename);
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
	if ($size > 1073741824) {$size = $size / 1073741824; $units = '��'; $z = 2;}
	elseif ($size > 1048576) {$size = $size / 1048576; $units = '��'; $z = 2;}
	elseif ($size > 1024) {$size = $size / 1024; $units = '��'; $z = 2;}
	else {$units = '����'; $z = 0;}
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
# ���������� �������
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
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
				if ( ! PHP::checkVersion('5.3') )
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
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
/**
 * ���������� ����������� �� �������� � ��������
 *
 * @param mixed  $object    �������
 * @param string $property  ��������
 * @return bool ��������
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
 * ���������� �������� ��������
 *
 * @param mixed  $object    �������
 * @param string $property  ��������
 * @return string ��������
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
 * �������� ����� ����������
 *
 * @package Eresus
 */
class Eresus
{
	/**
	 * ������������
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
	 * ������ ������
	 *
	 * @var array
	 */
	var $session;
 /**
	* ��������� � ����������� �������
	*
	* @var unknown_type
	*/
	var $extensions;

	/**
	 * ��������� � ��
	 * @var MySQL
	 */
	public $db;

	/**
	 * �������
	 * @var Plugins
	 */
	public $plugins;
 /**
	* ������� ������ ������������
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
	var $root; # �������� URL
	var $data; # URL ������
	var $style; # URL ������
	var $froot; # �������� ����������
	var $fdata; # ���������� ������
	var $fstyle; # ���������� ������

	var $request;
	var $sections;

	//------------------------------------------------------------------------------
	// ���������� � �������
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
	 * ������ � ��������� ���������������� ����
	 */
	private function init_config()
	{
		/*
		 * ���������� $Eresus ���� ������� ���������� ����� ���� ������������
		 * ��� ���������� � �� ���� ��������.
		 */
		global $Eresus;

		$filename = Core::app()->getFsRoot() . '/cfg/main.php';
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
	 * ���������� ������
	 */
	private function init_session()
	{
		session_set_cookie_params(ini_get('session.cookie_lifetime'), $this->path);
		session_name('sid');
		session_start();
		$this->session = &$_SESSION['session'];
		if (!isset($this->session['msg'])) $this->session['msg'] = array('error' => array(), 'information' => array());
		$this->user = &$_SESSION['user'];

		# �������� �������������
		$GLOBALS['session'] = &$_SESSION['session'];
		$GLOBALS['user'] = &$_SESSION['user'];
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� �������� ����
	 *
	 * @return void
	 */
	protected function init_resolve()
	{
		if (is_null($this->froot))
		{
			$this->froot = FS::nativeForm(Core::app()->getFsRoot() . '/');
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
		 * �������� �������������
		 * @var string
		 * @deprecated since 2.14
		 */
		define('filesRoot', $this->froot);

		/**
		 * �������� �������������
		 * @var string
		 * @deprecated since 2.14
		 */
		define('dataFiles', $this->fdata);
	}
	//------------------------------------------------------------------------------

	/**
	* ������ ���������
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
	* ��������� ������ �������
	*
	* @access  private
	*/
	function init_request()
	{
		global $request;

		# �������� �� ���������
		$request = array(
			'method' => $_SERVER['REQUEST_METHOD'],
			'scheme' => isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https' : 'http',
			'host' => strtolower(is_null($this->host) ? $_SERVER['HTTP_HOST'] : $this->host),
			'port' => '',
			'user' => isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : '',
			'pass' => isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '',
			'path' => '',
			'query' => '',
			'fragment' => '', # TODO: ����� �� ������ �������� ����� ����������?
			'referer' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
		);

		$request['url'] = $request['scheme'].'://'.$request['host'].$_SERVER['REQUEST_URI'];

		$request = array_merge($request, parse_url($request['url']));
		$request['file'] = substr($request['path'], strrpos($request['path'], '/')+1);
		if ($request['file']) $request['path'] = substr($request['path'], 0, -strlen($request['file']));

		# ������� ��������� URL ��� GET-�������� � �����������
		$request['link'] = $request['url'];
		if (substr($request['link'], -1) == '/') $request['link'] .= '?';
		elseif (strpos($request['link'], '?') === false)  $request['link'] .= '?';
		else $request['link'] .= '&';

		if (is_null($this->path)) {
			$s = $this->froot;
			$s = substr($s, strlen(realpath($_SERVER['DOCUMENT_ROOT']))-($this->isWin32()?2:0));
			if (!strlen($s) || sbstr($s, -1) != '/') $s .= '/';
			$this->path = (substr($s, 0, 1) != '/' ? '/' : '').$s;
		}

		/*
		 * ��������� ������� ������� $Eresus
		 * ������ ����������� �� ������ __clearargs
		 */
		$root = $request['scheme'].'://'.$request['host'].($request['port'] ? ':'.$request['port'] : '');
		$this->host = $request['host'];
		$this->root = $root.$this->path;
		$this->data = $this->root.'data/';
		$this->style = $this->root.'style/';


		# ���� ���������� ������
		$request['arg'] = __clearargs(array_merge($_GET, $_POST));
		# �������� ���������� ������ �������
		$s = substr($request['path'], strlen($this->path));
		$request['params'] = $s ? explode('/', substr($s, 0, -1)) : array();

		$request['path'] = $root.$request['path'];

		# �������� �������������
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
	* ������������� ������
	*
	* @access private
	*/
	function init_locale()
	{
		global $locale;

		$locale['lang'] = $this->conf['lang'];
		$locale['prefix'] = '';

		# ����������� ��������� ������
		$filename = $this->froot.'lang/'.$locale['lang'].'.php';
		if (is_file($filename)) include_once($filename);
		else FatalError("Locale file '$filename' not found!");
	}
	//------------------------------------------------------------------------------
 /**
	* ����������� ������� �������
	*
	* @access private
	*/
	function init_classes()
	{
		# ����������� ��������� ������
		$filename = $this->froot.'core/classes.php';
		if (is_file($filename)) include_once($filename);
		else FatalError("Classes file '$filename' not found!");
		if ($this->conf['backward']['TListContentPlugin']) useClass('backward/TListContentPlugin');
		elseif ($this->conf['backward']['TContentPlugin']) useClass('backward/TContentPlugin');
		elseif ($this->conf['backward']['TPlugin']) useClass('backward/TPlugin');
	}
	//------------------------------------------------------------------------------
 /**
	* ������������� ����������
	*/
	function init_extensions()
	{
		$filename = $this->froot.'cfg/extensions.php';
		if (is_file($filename)) include_once($filename);

		$this->extensions = new EresusExtensions();
	}
	//-----------------------------------------------------------------------------
	/**
	* ����������� � ��������� ������
	*
	* @access private
	*/
	function init_datasource()
	{
		if (useLib($this->conf['db']['engine']))
		{
			$this->db = new $this->conf['db']['engine'];
			$this->db->init($this->conf['db']['host'], $this->conf['db']['user'], $this->conf['db']['password'], $this->conf['db']['name'], $this->conf['db']['prefix']);
		}
			else FatalError(sprintf(errLibNotFound, $this->conf['db']['engine']));
	}
	//------------------------------------------------------------------------------
 /**
	* ������������� ��������� ��������
	*/
	function init_plugins()
	{
		$this->plugins = new Plugins;
	}
	//------------------------------------------------------------------------------
	/**
	* ������������� ������� ������ ������������
	*
	*/
	function init_user()
	{
		useLib('accounts');
	}
	//-----------------------------------------------------------------------------
	/**
	* �������� ������
	*
	* @access private
	*/
	function check_session()
	{
		if (isset($this->session['time'])) {
			if ((time() - $this->session['time'] > $this->conf['session']['timeout']*3600)&&($this->user['auth'])) $this->logout(false);
			else $this->session['time'] = time();
		}
	}
	//------------------------------------------------------------------------------
 /**
	* �������� �� �����/������
	*
	*/
	function check_loginout()
	{
		switch (arg('action')) {
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
	* ������� cookie-������
	*/
	function check_cookies()
	{
		if (!$this->user['auth'] && isset($_COOKIE['eresus_login'])) {
			if (!$this->login($_COOKIE['eresus_login'], $_COOKIE['eresus_key'], true, true))
				$this->clear_login_cookies();
		}
	}
	//------------------------------------------------------------------------------
 /**
	* ���������� ������ � ������������
	*/
	function reset_login()
	{
		$this->user['auth'] = isset($this->user['auth'])?$this->user['auth']:false;
		if ($this->user['auth']) {
			$item = $this->db->selectItem('users', "`id`='".$this->user['id']."'");
			if (!is_null($item)) { # ���� ����� ������������ ����...
				if ($item['active']) { # ���� ������� ������ �������...
					$this->user['name'] = $item['name'];
					$this->user['mail'] = $item['mail'];
					$this->user['access'] = $item['access'];
					$this->user['profile'] = decodeOptions($item['profile']);
				} else {
					ErrorMessage(sprintf(errAccountNotActive, $item['login']));
					$this->logout();
				}
			} else $this->logout();
		} else $this->user['access'] = GUEST;
	}
	//------------------------------------------------------------------------------
 /**
	* ������������� �������
	*
	* @access public
	*/
	function init()
	{
		// ���������� ������������� ������������ ������
		if (!PHP::checkVersion('5.3'))
			set_magic_quotes_runtime(0);
		# ������ ������������
		$this->init_config();
		if ($this->conf['timezone'])
			date_default_timezone_set($this->conf['timezone']);
		# ����������� �����
		$this->init_resolve();
		# ������������� ������
		$this->init_session();
		# �������� ���� ������ ������������ ������
		set_include_path(dirname(__FILE__).DIRECTORY_SEPARATOR.'lib'.PATH_SEPARATOR.get_include_path());
		# ������ ���������
		$this->init_settings();
		# ��������� ������ �������
		$this->init_request();
		# ��������� ������
		$this->init_locale();
		# ����������� ������� �������
		$this->init_classes();
		# ������������� ����������
		$this->init_extensions();
		# ����������� � ��������� ������
		$this->init_datasource();
		# ������������� ��������� ��������
		$this->init_plugins();
		# ������������� ������� ������ ������������
		$this->init_user();
		# �������� ������
		$this->check_session();
		# �������� ������/�������
		$this->check_loginout();
		# ������� cookie-������
		$this->check_cookies();
		# ���������� ������ � ������������
		$this->reset_login();
		# ����������� ������ � ��������� �����
		useLib('sections');
		$this->sections = new Sections;
		$GLOBALS['KERNEL']['loaded'] = true; # ���� �������� ����
	}
	//------------------------------------------------------------------------------
 /**
	* �������� ������
	*
	* @param string $password  ������
	* @return string  ���
	*/
	function password_hash($password)
	{
		$result = md5($password);
		if (!$this->conf['backward']['weak_password']) $result = md5($result);
		return $result;
	}
	//-----------------------------------------------------------------------------
 /**
	* ������������� ��������������� ������
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
	* �������� ��������������� ������
	*
	*/
	function clear_login_cookies()
	{
		setcookie('eresus_login', '', time()-3600, $this->path);
		setcookie('eresus_key', '', time()-3600, $this->path);
	}
	//-----------------------------------------------------------------------------
 /**
	* ����������� ������������
	*
	* @param string $unsafeLogin   ��� ������������
	* @param string $key		       ���� ������� ������
	* @param bool   $auto		       ��������� ��������������� ������ �� ���������� ����������
	* @param bool   $cookie        ����������� ��� ������ cookie
	* @return bool ���������
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
			// ���� ����� ������������ ����...
			if ($item['active'])
			{
				// ���� ������� ������ �������...
				if (time() - $item['lastLoginTime'] > $item['loginErrors'])
				{
					if ($key == $item['hash'])
					{
						// ���� ������ �����...
						if ($auto)
						{
							$this->set_login_cookies($login, $key);
						}
						else
						{
							$this->clear_login_cookies();
						}
						$setVisitTime = (! isset($this->uset['id'])) || (! (bool)$this->user['id']);
						$lastVisit = isset($this->user['lastVisit'])?$this->user['lastVisit']:'';
						$this->user = $item;
						$this->user['profile'] = decodeOptions($this->user['profile']);
						$this->user['auth'] = true; # ������������� ���� �����������
						$this->user['hash'] = $item['hash']; # ��� ������ ������������ ��� ������������� ��������������
						if ($setVisitTime) $item['lastVisit'] = gettime(); # ���������� ����� ���������� �����
						$item['lastLoginTime'] = time();
						$item['loginErrors'] = 0;
						$this->db->updateItem('users', $item,"`id`='".$item['id']."'");
						$this->session['time'] = time(); # �������������� ����� ��������� ���������� ������.
						$result = true;
					}
					else
					{
						// ���� ������ �� �����...
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
					// ���� ����������� ��������� ������� ����
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
	* ���������� ������ ������ � ��������
	*
	* @param bool $clearCookies
	*/
	function logout($clearCookies=true)
	{
		$this->user['id'] = null;
		$this->user['auth'] = false;
		$this->user['access'] = GUEST;
		if ($clearCookies) $this->clear_login_cookies();
	}
	//-----------------------------------------------------------------------------
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
