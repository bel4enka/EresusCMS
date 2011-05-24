<?php
/**
 * ${product.title}
 *
 * @version ${product.version}
 *
 * PhpUnit Tests
 *
 * @copyright 2010, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package EresusCMS
 * @subpackage Tests
 * @author Mikhail Krasilnikov <mihalych@vsepofigu.ru>
 *
 * $Id$
 */

require_once dirname(__FILE__) . '/../../stubs.php';
require_once dirname(__FILE__) . '/../../../../main/core/Config.php';
require_once dirname(__FILE__) . '/../../../../main/core/Logger.php';
require_once dirname(__FILE__) . '/../../../../main/core/Template.php';
require_once dirname(__FILE__) . '/../../../../main/core/UI/Pagination.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class Eresus_UI_Pagination_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * (non-PHPdoc)
	 * @see Framework/PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp()
	{
		$GLOBALS['Eresus'] = new stdClass();
		$GLOBALS['Eresus']->request = array('path' => '/root/');
	}
	//-----------------------------------------------------------------------------

	/**
	 * (non-PHPdoc)
	 * @see Framework/PHPUnit_Framework_TestCase::tearDown()
	 */
	protected function tearDown()
	{
		unset($GLOBALS['Eresus']);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем установку и чтение свойства $total
	 *
	 * @covers Eresus_UI_Pagination::setTotal
	 * @covers Eresus_UI_Pagination::getTotal
	 */
	public function test_setgetTotal()
	{
		$test = new Eresus_UI_Pagination();
		$test->setTotal(123);
		$this->assertEquals(123, $test->getTotal());
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем установку и чтение свойства $current
	 *
	 * @covers Eresus_UI_Pagination::setCurrent
	 * @covers Eresus_UI_Pagination::getCurrent
	 */
	public function test_setgetCurrent()
	{
		$test = new Eresus_UI_Pagination();
		$test->setCurrent(123);
		$this->assertEquals(123, $test->getCurrent());
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем конструктор
	 *
	 * @covers Eresus_UI_Pagination::__construct
	 */
	public function test_construct_wo_args()
	{
		$test = new Eresus_UI_Pagination();
		$this->assertNull($test->getTotal(), 'Case 1');
		$this->assertEquals(1, $test->getCurrent(), 'Case 1');

		$test = new Eresus_UI_Pagination(10);
		$this->assertEquals(10, $test->getTotal(), 'Case 2');
		$this->assertEquals(1, $test->getCurrent(), 'Case 2');

		$test = new Eresus_UI_Pagination(20, 5);
		$this->assertEquals(20, $test->getTotal(), 'Case 3');
		$this->assertEquals(5, $test->getCurrent(), 'Case 3');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем установку и чтение свойства $templatePath
	 *
	 * @covers Eresus_UI_Pagination::setTemplate
	 * @covers Eresus_UI_Pagination::getTemplate
	 */
	public function test_setgetTemplate()
	{
		$test = new Eresus_UI_Pagination();
		$test->setTemplate('/path/to/file');
		$this->assertEquals('/path/to/file', $test->getTemplate());
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем установку и чтение свойства $urlTemplate
	 *
	 * @covers Eresus_UI_Pagination::setUrlTemplate
	 * @covers Eresus_UI_Pagination::getUrlTemplate
	 */
	public function test_setgetUrlTemplate()
	{
		$test = new Eresus_UI_Pagination();
		$test->setUrlTemplate('/%d/');
		$this->assertEquals('/%d/', $test->getUrlTemplate());
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем установку и чтение свойства $size
	 *
	 * @covers Eresus_UI_Pagination::setSize
	 * @covers Eresus_UI_Pagination::getSize
	 */
	public function test_setgetSize()
	{
		$test = new Eresus_UI_Pagination();
		$test->setSize(5);
		$this->assertEquals(5, $test->getSize());
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем метод rewind()
	 *
	 * @covers Eresus_UI_Pagination::rewind
	 * @covers Eresus_UI_Pagination::count
	 */
	public function test_rewind()
	{
		$test = new Eresus_UI_Pagination(10, 1);
		$test->rewind();
		$this->assertEquals(10, count($test), 'Case 1');

		$test->setTotal(100);
		$test->rewind();
		$this->assertEquals(11, count($test), 'Case 2');

		$test->setCurrent(50);
		$test->rewind();
		$this->assertEquals(12, count($test), 'Case 3');

		$test->setCurrent(100);
		$test->rewind();
		$this->assertEquals(11, count($test), 'Case 4');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обычное использование
	 *
	 */
	public function test_commonUse_simple()
	{
		$test = new Eresus_UI_Pagination(10, 5);

		Eresus_Config::set('core.template.templateDir', TESTS_SRC_ROOT);
		$helper = $test->render();

		$i = 1;
		foreach ($helper as $page)
		{
			if ($i > 10)
			{
				$this->fail('Too many iterations');
			}

			if ($i == 5)
			{
				$this->assertTrue($page['current']);
			}

			$this->assertEquals($i, $helper->key());
			$this->assertEquals($i, $page['title'], 'Ivalid page number');
			$this->assertEquals('/root/p' . $i . '/', $page['url'], 'Ivalid page url');
			$i++;
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обычное использование
	 *
	 */
	public function test_commonUse_begining()
	{
		$test = new Eresus_UI_Pagination(100, 1);

		Eresus_Config::set('core.template.templateDir', TESTS_SRC_ROOT);
		$helper = $test->render();

		$i = 1;
		foreach ($helper as $page)
		{
			$this->assertEquals($i, $helper->key());

			switch (true)
			{
				case $i > 11:
					$this->fail('Too many iterations');
				break;

				case $i >= 1 && $i < 11:
					$this->assertEquals($i, $page['title'], 'Ivalid page number');
					$this->assertEquals('/root/p' . $i . '/', $page['url'], 'Ivalid page url');
				break;

				case $i == 11:
					$this->assertEquals('&rarr;', $page['title'], 'Invalid last element');
					$this->assertEquals('/root/p11/', $page['url'], 'Ivalid last page url');
				break;
			}

			$i++;
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обычное использование
	 *
	 */
	public function test_commonUse_ending()
	{
		$test = new Eresus_UI_Pagination(100, 100);

		Eresus_Config::set('core.template.templateDir', TESTS_SRC_ROOT);
		$helper = $test->render();

		$i = 1;
		foreach ($helper as $page)
		{
			$this->assertEquals($i, $helper->key());

			switch (true)
			{
				case $i > 11:
					$this->fail('Too many iterations');
				break;

				case $i == 1:
					$this->assertEquals('&larr;', $page['title'], 'Invalid first element');
					$this->assertEquals('/root/p90/', $page['url'], 'Ivalid first page url');
				break;

				case $i > 1 && $i < 11:
					$this->assertEquals($i + 89, $page['title'], 'Ivalid page number');
					$this->assertEquals('/root/p' . ($i + 89) . '/', $page['url'], 'Ivalid page url');
				break;
			}

			$i++;
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обычное использование
	 *
	 */
	public function test_commonUse_middle()
	{
		$test = new Eresus_UI_Pagination(100, 50);

		Eresus_Config::set('core.template.templateDir', TESTS_SRC_ROOT);
		$helper = $test->render();

		$i = 1;
		foreach ($helper as $page)
		{
			$this->assertEquals($i, $helper->key());

			switch (true)
			{
				case $i > 12:
					$this->fail('Too many iterations');
				break;

				case $i == 1:
					$this->assertEquals('&larr;', $page['title'], 'Invalid first element');
					$this->assertEquals('/root/p40/', $page['url'], 'Ivalid first page url');
				break;

				case $i > 1 && $i < 12:
					$this->assertEquals($i + 43, $page['title'], 'Ivalid page number');
				break;

				case $i == 12:
					$this->assertEquals('&rarr;', $page['title'], 'Invalid last element');
					$this->assertEquals('/root/p60/', $page['url'], 'Ivalid last page url');
				break;
			}

			$i++;
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 *
	 */
	public function test_size2()
	{
		$test = new Eresus_UI_Pagination(4, 4);
		$test->setSize(2);

		Eresus_Config::set('core.template.templateDir', TESTS_SRC_ROOT);
		$data = $test->render();
		$helper = $data['pagination'];

		$i = 1;
		foreach ($helper as $page)
		{
			switch (true)
			{
				case $i > 3:
					$this->fail('Too many iterations');
				break;

				case $i == 1:
					$this->assertEquals('&larr;', $page['title'], 'Invalid first element');
					$this->assertEquals('/root/p2/', $page['url'], 'Ivalid first page url');
				break;

				case $i > 1 && $i < 3:
					$this->assertEquals($i + 1, $page['title'], 'Ivalid page number');
					$this->assertEquals('/root/p' . ($i + 1) . '/', $page['url'], 'Ivalid page url');
				break;

			}

			$i++;
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 *
	 */
	public function test_size2_beginning()
	{
		$test = new Eresus_UI_Pagination(4, 1);
		$test->setSize(2);

		Eresus_Config::set('core.template.templateDir', TESTS_SRC_ROOT);
		$data = $test->render();
		$helper = $data['pagination'];

		$i = 1;
		foreach ($helper as $page)
		{
			switch (true)
			{
				case $i > 4:
					$this->fail('Too many iterations');
				break;

				case $i >= 1 && $i < 3:
					$this->assertEquals($i, $page['title'], 'Ivalid page number');
					$this->assertEquals('/root/p' . $i . '/', $page['url'], 'Ivalid last page url');
				break;

				case $i == 3:
					$this->assertEquals('&rarr;', $page['title'], 'Invalid last element');
					$this->assertEquals('/root/p3/', $page['url'], 'Ivalid last page url');
				break;

			}

			$i++;
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 *
	 */
	public function test_size2_current3()
	{
		$test = new Eresus_UI_Pagination(4, 3);
		$test->setSize(2);

		Eresus_Config::set('core.template.templateDir', TESTS_SRC_ROOT);
		$data = $test->render();
		$helper = $data['pagination'];

		$i = 1;
		foreach ($helper as $page)
		{
			switch (true)
			{
				case $i > 4:
					$this->fail('Too many iterations');
				break;

				case $i == 1:
					$this->assertEquals('&larr;', $page['title'], 'Invalid first element');
					$this->assertEquals('/root/p1/', $page['url'], 'Ivalid first page url');
				break;

				case $i > 1 && $i < 4:
					$this->assertEquals($i, $page['title'], 'Ivalid page number');
					$this->assertEquals('/root/p' . $i . '/', $page['url'], 'Ivalid last page url');
				break;

				case $i == 4:
					$this->assertEquals('&rarr;', $page['title'], 'Invalid last element');
					$this->assertEquals('/root/p4/', $page['url'], 'Ivalid last page url');
				break;

			}

			$i++;
		}
	}
	//-----------------------------------------------------------------------------

	/* */
}
