<?php
/**
 * ${product.title}
 *
 * Тесты
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
 */

require_once __DIR__ . '/../bootstrap.php';
require_once TESTS_SRC_DIR . '/core/Eresus/Admin/Controllers/Settings.php';

/**
 * @package Eresus_CMS
 * @subpackage Tests
 */
class Eresus_TSettingsTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_Admin_Controllers_Settings::mkstr
	 */
	public function test_mkstr()
	{
		$mkstr = new ReflectionMethod('Eresus_Admin_Controllers_Settings', 'mkstr');
		$mkstr->setAccessible(true);

		$settings = new Eresus_Admin_Controllers_Settings('');
		$this->assertEquals("  define('foo', '');\n", $mkstr->invoke($settings, 'foo'));
		$this->assertEquals("  define('foo', false);\n", $mkstr->invoke($settings, 'foo', 'bool'));
		$this->assertEquals("  define('foo', 0);\n", $mkstr->invoke($settings, 'foo', 'int'));

		$_POST['foo'] = "' \\ \" \r \n";

		$options = array('nobr' => true);
		$this->assertEquals("  define('foo', '\\' \\\\ \"    ');\n",
			$mkstr->invoke($settings, "foo", 'string', $options));

		$options = array('savebr' => true);
		$this->assertEquals("  define('foo', \"' \\\\ \\\\\\\" \\\\r \\\\n\");\n",
			$mkstr->invoke($settings, "foo", 'string', $options));
	}
	/* */
}
