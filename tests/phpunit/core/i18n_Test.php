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
 * $Id$
 */

require_once dirname(__FILE__) . '/../stubs.php';
require_once dirname(__FILE__) . '/../../../main/core/i18n.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_i18n_Test extends PHPUnit_Extensions_OutputTestCase
{
	/**
	 * Log filename saver
	 *
	 * @var string
	 */
	protected $logSaver;

	/**
	 * Temporary log filename
	 *
	 * @var string
	 */
	protected $logFilename;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp()
	{
		$this->logSaver = ini_get('error_log');
		$TMP = isset($_ENV['TMP']) ? $_ENV['TMP'] : '/tmp';
		$this->logFilename = tempnam($TMP, 'eresus-core-');
		ini_set('error_log', $this->logFilename);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @see PHPUnit_Framework_TestCase::tearDown()
	 */
	protected function tearDown()
	{
		@unlink($this->logFilename);
		ini_set('error_log', $this->logSaver);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_i18n::getInstance
	 * @covers Eresus_i18n::__construct
	 */
	public function test_getInstance()
	{
		$app = $this->getMock('stdClass', array('getRootDir'));
		$p_app = new ReflectionProperty('Eresus_Kernel', 'app');
		$p_app->setAccessible(true);
		$p_app->setValue('Eresus_Kernel', $app);
		$test = Eresus_i18n::getInstance();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_i18n::setLocale
	 * @covers Eresus_i18n::getLocale
	 */
	public function test_setgetLocale()
	{
		$i18n = $this->getMockBuilder('Eresus_i18n')->setMethods(array('getInstance'))->
			disableOriginalConstructor()->getMock();
		$i18n->setLocale('ru_RU');
		$this->assertEquals('ru_RU', $i18n->getLocale());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_i18n::setLocale
	 * @expectedException InvalidArgumentException
	 */
	public function test_setLocale_invalid()
	{
		$i18n = $this->getMockBuilder('Eresus_i18n')->setMethods(array('getInstance'))->
			disableOriginalConstructor()->getMock();
		$i18n->setLocale('ru');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_i18n::localeLazyLoad
	 */
	public function test_localeLazyLoad()
	{
		$i18n = $this->getMockBuilder('Eresus_i18n')->setMethods(array('getInstance'))->
			disableOriginalConstructor()->getMock();
		$m_localeLazyLoad = new ReflectionMethod('Eresus_i18n', 'localeLazyLoad');
		$m_localeLazyLoad->setAccessible(true);

		$p_locale = new ReflectionProperty('Eresus_i18n', 'locale');
		$p_locale->setAccessible(true);

		$p_path = new ReflectionProperty('Eresus_i18n', 'path');
		$p_path->setAccessible(true);

		$p_data = new ReflectionProperty('Eresus_i18n', 'data');
		$p_data->setAccessible(true);

		Eresus_Config::set('eresus.cms.log.level', LOG_WARNING);
		$m_localeLazyLoad->invoke($i18n);
		$message = file_get_contents($this->logFilename);
		$this->assertContains('Locale not set', $message);

		$p_locale->setValue($i18n, 'ru_RU');
		$m_localeLazyLoad->invoke($i18n);
		$message = file_get_contents($this->logFilename);
		$this->assertContains('Can not load language file', $message);

		$p_path->setValue($i18n, TESTS_SRC_ROOT . '/lang');
		$m_localeLazyLoad->invoke($i18n);
		$this->assertTrue(is_array($p_data->getValue($i18n)));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_i18n::get
	 */
	public function test_get()
	{
		$i18n = $this->getMockBuilder('Eresus_i18n')->
			setMethods(array('getInstance', 'localeLazyLoad'))->disableOriginalConstructor()->getMock();

		$p_data = new ReflectionProperty('Eresus_i18n', 'data');
		$p_data->setAccessible(true);
		$p_data->setValue($i18n, array(
			'ru_RU' => array(
				'messages' => array(
					'global' => array(
						'A' => 'B',
					),
					'some.context' => array(
						'A' => 'C',
					)
				)
			)
		));

		$i18n->setLocale('xx_XX');
		$this->assertEquals('A', $i18n->get('A'));
		$i18n->setLocale('ru_RU');
		$this->assertEquals('Z', $i18n->get('Z'));
		$this->assertEquals('B', $i18n->get('A'));
		$this->assertEquals('C', $i18n->get('A', 'some.context'));
	}
	//-----------------------------------------------------------------------------

	/**
	 *
	 */
	public function test_i18n()
	{
		$i18n = $this->getMockBuilder('Eresus_i18n')->setMethods(array('get'))->
			disableOriginalConstructor()->getMock();
		$i18n->expects($this->once())->method('get')->with('phrase', 'context');

		$p_instance = new ReflectionProperty('Eresus_i18n', 'instance');
		$p_instance->setAccessible(true);
		$p_instance->setValue('Eresus_i18n', $i18n);

		i18n('phrase', 'context');
	}
	//-----------------------------------------------------------------------------
	/* */
}
