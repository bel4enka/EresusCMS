<?php
/**
 * ${product.title}
 *
 * Модульные тесты
 *
 * @version ${product.version}
 * @copyright ${product.copyright}
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
 * $Id: Request_Test.php 1708 2011-07-19 06:32:09Z mk $
 */

require_once dirname(__FILE__) . '/../../../stubs.php';
require_once dirname(__FILE__) . '/../../../../../main/core/HTTP/Request/Arguments.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_HTTP_Request_Arguments_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_HTTP_Request_Arguments::__construct
	 */
	public function test_construct()
	{
		$a = array('a' => 'b');
		$test = new Eresus_HTTP_Request_Arguments($a);
		$p_args = new ReflectionProperty('Eresus_HTTP_Request_Arguments', 'args');
		$p_args->setAccessible(true);
		$this->assertEquals($a, $p_args->getValue($test));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_HTTP_Request_Arguments::get
	 */
	public function test_get()
	{
		$args = new Eresus_HTTP_Request_Arguments(array(
			'default' => '!@#$%^&*()',
			'int' => '123abc',
			'float' => '1234.5abc',
			'pcre' => 'abcdef'
		));
		$this->assertEquals('!@#$%^&*()', $args->get('default'));
		$this->assertEquals(123, $args->get('int', 'int'));
		$this->assertEquals(1234.5, $args->get('float', 'float'));
		$this->assertEquals('abef', $args->get('pcre', '/[cd]/'));
		$this->assertEquals('!#$%^&*()', $args->get('default',
			function ($s)
			{
				return str_replace('@', '', $s);
			}
		));
		$this->assertEquals('@#$%^&*()', $args->get('default', array($this, 'helper_test_get')));
	}
	//-----------------------------------------------------------------------------

	public function helper_test_get($s)
	{
		return str_replace('!', '', $s);
	}
	//-----------------------------------------------------------------------------

	/* */
}
