<?php
/**
 * ${product.title} ${product.version}
 *
 * Модульные тесты
 *
 * @copyright 2011, Eresus Project, http://eresus.ru/
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
 * $Id: Record_Test.php 1983 2011-11-23 06:37:58Z mk $
 */

require_once __DIR__ . '/../../../bootstrap.php';
require_once TESTS_SRC_DIR . '/core/PluginInfo.php';
require_once TESTS_SRC_DIR . '/core/Admin/Controller.php';
require_once TESTS_SRC_DIR . '/core/Admin/Controller/PluginInstaller.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_Admin_Controller_PluginInstaller_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_Admin_Controller_PluginInstaller::getLocalPlugins
	 */
	public function test_getLocalPlugins()
	{
		$container = new sfServiceContainerBuilder();
		$container->setService('app', new Eresus_CMS);

		$p_rootDir = new ReflectionProperty('Eresus_CMS', 'rootDir');
		$p_rootDir->setAccessible(true);
		$p_rootDir->setValue($container->app, TESTS_SRC_DIR);

		$ctrl = new Eresus_Admin_Controller_PluginInstaller($container);

		$m_getLocalPlugins = new ReflectionMethod('Eresus_Admin_Controller_PluginInstaller',
			'getLocalPlugins');
		$m_getLocalPlugins->setAccessible(true);

		$list = $m_getLocalPlugins->invoke($ctrl);
		$this->assertInternalType('array', $list);
		$this->assertArrayHasKey('ru.eresus.plugins.Test', $list);
	}
	//-----------------------------------------------------------------------------

	/* */
}
