<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Модульные тесты. Обратная совместимость
 *
 * @copyright 2004-2007, ProCreat Systems, http://procreat.ru/
 * @copyright 2007-${build.year}, Eresus Project, http://eresus.ru/
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
 * @package EresusCMS
 * @subpackage Tests
 *
 * $Id$
 */

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class LegacyTest extends PHPUnit_Framework_TestCase {

	/**
	 *
	 */
	function testCMSConsts()
	{
		$this->assertTrue(defined('CMSNAME'), 'Constant "CMSNAME" not defined');
		$this->assertTrue(defined('CMSVERSION'), 'Constant "CMSVERSION" not defined');
		$this->assertTrue(defined('CMSLINK'), 'Constant "CMSLINK" not defined');
	}
	//-----------------------------------------------------------------------------

	/**
	 *
	 */
	function testKernelConsts()
	{
		$this->assertTrue(defined('KERNELNAME'), 'Constant "KERNELNAME" not defined');
		$this->assertTrue(defined('KERNELDATE'), 'Constant "KERNELDATE" not defined');
	}
	//-----------------------------------------------------------------------------

	/**
	 *
	 */
	function testAccessConsts()
	{
		$this->assertTrue(defined('ROOT'), 'Constant "ROOT" not defined');
		$this->assertEquals(1, ROOT, 'Constant "ROOT" not equal "1"');
		$this->assertTrue(defined('ADMIN'), 'Constant "ADMIN" not defined');
		$this->assertEquals(2, ADMIN, 'Constant "ADMIN" not equal "2"');
		$this->assertTrue(defined('EDITOR'), 'Constant "EDITOR" not defined');
		$this->assertEquals(3, EDITOR, 'Constant "EDITOR" not equal "3"');
		$this->assertTrue(defined('USER'), 'Constant "USER" not defined');
		$this->assertEquals(4, USER, 'Constant "USER" not equal "4"');
		$this->assertTrue(defined('GUEST'), 'Constant "GUEST" not defined');
		$this->assertEquals(5, GUEST, 'Constant "GUEST" not equal "5"');
	}
	//-----------------------------------------------------------------------------

	/**
	 *
	 */
	function testFunction()
	{
		$this->assertTrue(function_exists('FatalError'), 'Function "FatalError" not exists');
		$this->assertTrue(function_exists('ErrorBox'), 'Function "ErrorBox" not exists');
		$this->assertTrue(function_exists('InfoBox'), 'Function "InfoBox" not exists');
		$this->assertTrue(function_exists('ErrorHandler'), 'Function "ErrorHandler" not exists');
		$this->assertTrue(function_exists('ErrorMessage'), 'Function "ErrorMessage" not exists');
		$this->assertTrue(function_exists('InfoMessage'), 'Function "InfoMessage" not exists');
		$this->assertTrue(function_exists('UserRights'), 'Function "UserRights" not exists');
		$this->assertTrue(function_exists('resetLastVisitTime'), 'Function "resetLastVisitTime" not exists');
		$this->assertTrue(function_exists('useLib'), 'Function "useLib" not exists');
		$this->assertTrue(function_exists('useClass'), 'Function "useClass" not exists');
		$this->assertTrue(function_exists('sendMail'), 'Function "sendMail" not exists');
		$this->assertTrue(function_exists('sendNotify'), 'Function "sendNotify" not exists');
		$this->assertTrue(function_exists('gettime'), 'Function "gettime" not exists');
		$this->assertTrue(function_exists('FormatDate'), 'Function "FormatDate" not exists');
		$this->assertTrue(function_exists('encodeHTML'), 'Function "encodeHTML" not exists');
		$this->assertTrue(function_exists('decodeHTML'), 'Function "decodeHTML" not exists');
		$this->assertTrue(function_exists('text2array'), 'Function "text2array" not exists');
		$this->assertTrue(function_exists('array2text'), 'Function "array2text" not exists');
		$this->assertTrue(function_exists('encodeOptions'), 'Function "encodeOptions" not exists');
		$this->assertTrue(function_exists('decodeOptions'), 'Function "decodeOptions" not exists');
		$this->assertTrue(function_exists('replaceMacros'), 'Function "replaceMacros" not exists');
		$this->assertTrue(function_exists('GetArgs'), 'Function "GetArgs" not exists');
		$this->assertTrue(function_exists('arg'), 'Function "arg" not exists');
		$this->assertTrue(function_exists('saveRequest'), 'Function "saveRequest" not exists');
		$this->assertTrue(function_exists('restoreRequest'), 'Function "restoreRequest" not exists');
		$this->assertTrue(function_exists('dbReorderItems'), 'Function "dbReorderItems" not exists');
		$this->assertTrue(function_exists('dbShiftItems'), 'Function "dbShiftItems" not exists');
		$this->assertTrue(function_exists('fileread'), 'Function "fileread" not exists');
		$this->assertTrue(function_exists('filewrite'), 'Function "filewrite" not exists');
		$this->assertTrue(function_exists('filedelete'), 'Function "filedelete" not exists');
		$this->assertTrue(function_exists('upload'), 'Function "upload" not exists');
		$this->assertTrue(function_exists('loadTemplate'), 'Function "loadTemplate" not exists');
		$this->assertTrue(function_exists('saveTemplate'), 'Function "saveTemplate" not exists');
		$this->assertTrue(function_exists('goto'), 'Function "goto" not exists');
		$this->assertTrue(function_exists('HttpAnswer'), 'Function "HttpAnswer" not exists');
		$this->assertTrue(function_exists('SendXML'), 'Function "SendXML" not exists');
		$this->assertTrue(function_exists('option'), 'Function "option" not exists');
		$this->assertTrue(function_exists('img'), 'Function "img" not exists');
		$this->assertTrue(function_exists('FormatSize'), 'Function "FormatSize" not exists');
		$this->assertTrue(function_exists('Translit'), 'Function "Translit" not exists');
		$this->assertTrue(function_exists('__clearargs'), 'Function "__clearargs" not exists');
		$this->assertTrue(function_exists('__isset'), 'Function "__isset" not exists');
		$this->assertTrue(function_exists('__property'), 'Function "__property" not exists');

	}
	//-----------------------------------------------------------------------------

	/**/
}
