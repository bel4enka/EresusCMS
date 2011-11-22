<?php
/**
 * ${product.title}
 *
 * Модульные тесты
 *
 * @version ${product.version}
 *
 * @copyright 2004, ProCreat Systems, http://procreat.ru/
 * @copyright 2007, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Михаил Красильников <mihalych@vsepofigu.ru>
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
 * @subpackage Tests
 *
 * $Id$
 */

define('filesRoot', '/home/exmaple.org/');


function eresus_log() {}

/**
 * @package Eresus
 * @subpackage Tests
 * @since 2.15
 */
class FS
{
	public static function canonicalForm($filename)
	{
		/* Convert slashes */
    $filename = str_replace('\\', '/', $filename);

    /* Prepend drive letter with slash if needed */
    if (substr($filename, 1, 1) == ':')
      $filename = '/' . $filename;

    return $filename;
	}
	//-----------------------------------------------------------------------------

	public static function isFile($filename)
	{
    return is_file($filename);
	}
	//-----------------------------------------------------------------------------
}


/**
 * @package EresusCMS
 * @subpackage Tests
 * @since 2.15
 */
class EresusRuntimeException extends Exception
{
}

/**
 * @package EresusCMS
 * @subpackage Tests
 * @since 2.15
 */
class EresusApplication
{
	public $fsRoot;

	public function getFsRoot()
	{
		return $this->fsRoot;
	}
	//-----------------------------------------------------------------------------
}

/**
 * @package EresusCMS
 * @subpackage Tests
 * @since 2.15
 */
class HttpRequest
{
	public $localRoot;

	public function setLocalRoot($value)
	{
		$this->localRoot = $value;
	}
	//-----------------------------------------------------------------------------

	public function getLocalRoot()
	{
		return $this->localRoot;
	}
	//-----------------------------------------------------------------------------

	public function getScheme()
	{
		return 'http';
	}
	//-----------------------------------------------------------------------------

	public function getHost()
	{
		return 'example.org';
	}
	//-----------------------------------------------------------------------------
}

/**
 * @package EresusCMS
 * @subpackage Tests
 * @since 2.15
 */
class TemplateSettings
{
	public static function setGlobalValue($a, $b)
	{
		;
	}
	//-----------------------------------------------------------------------------
}
