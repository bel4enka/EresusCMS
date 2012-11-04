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

use Eresus\CmsBundle\HTTP\Request;

require_once __DIR__ . '/../../../bootstrap.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_UI_PaginationTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @see Framework/PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp()
	{
		$request = Request::create('/root/');
		Eresus_Kernel::sc()->set('request', $request);
	}

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

	/**
	 * Обычное использование
	 *
	 */
	public function test_commonUse_simple()
	{
		$test = new Eresus_UI_Pagination(10, 5);

		$data = $test->render();
		$this->assertArrayHasKey('pagination', $data, '$data does not contain "pagination" entry');

		$helper = $data['pagination'];

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
			$this->assertEquals($i, $page['title'], 'Invalid page number');
			$this->assertEquals('/root/p' . $i . '/', $page['url'], 'Invalid page url');
			$i++;
		}
	}

	/**
	 * Обычное использование
	 *
	 */
	public function test_commonUse_beginning()
	{
		$test = new Eresus_UI_Pagination(100, 1);

		$data = $test->render();
		$this->assertArrayHasKey('pagination', $data, '$data does not contain "pagination" entry');

		$helper = $data['pagination'];

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
					$this->assertEquals($i, $page['title'], 'Invalid page number');
					$this->assertEquals('/root/p' . $i . '/', $page['url'], 'Invalid page url');
				break;

				case $i == 11:
					$this->assertEquals('&rarr;', $page['title'], 'Invalid last element');
					$this->assertEquals('/root/p11/', $page['url'], 'Invalid last page url');
				break;
			}

			$i++;
		}
	}

	/**
	 * Обычное использование
	 *
	 */
	public function test_commonUse_ending()
	{
		$test = new Eresus_UI_Pagination(100, 100);

		$data = $test->render();
		$this->assertArrayHasKey('pagination', $data, '$data does not contain "pagination" entry');

		$helper = $data['pagination'];

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
					$this->assertEquals('/root/p90/', $page['url'], 'Invalid first page url');
				break;

				case $i > 1 && $i < 11:
					$this->assertEquals($i + 89, $page['title'], 'Invalid page number');
					$this->assertEquals('/root/p' . ($i + 89) . '/', $page['url'], 'Invalid page url');
				break;
			}

			$i++;
		}
	}

	/**
	 * Обычное использование
	 *
	 */
	public function test_commonUse_middle()
	{
		$test = new Eresus_UI_Pagination(100, 50);

		$data = $test->render();
		$this->assertArrayHasKey('pagination', $data, '$data does not contain "pagination" entry');

		$helper = $data['pagination'];

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
					$this->assertEquals('/root/p40/', $page['url'], 'Invalid first page url');
				break;

				case $i > 1 && $i < 12:
					$this->assertEquals($i + 43, $page['title'], 'Invalid page number');
				break;

				case $i == 12:
					$this->assertEquals('&rarr;', $page['title'], 'Invalid last element');
					$this->assertEquals('/root/p60/', $page['url'], 'Invalid last page url');
				break;
			}

			$i++;
		}
	}

	/**
	 *
	 */
	public function test_size2()
	{
		$test = new Eresus_UI_Pagination(4, 4);
		$test->setSize(2);

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
					$this->assertEquals('/root/p2/', $page['url'], 'Invalid first page url');
				break;

				case $i > 1 && $i < 3:
					$this->assertEquals($i + 1, $page['title'], 'Invalid page number');
					$this->assertEquals('/root/p' . ($i + 1) . '/', $page['url'], 'Invalid page url');
				break;

			}

			$i++;
		}
	}

	/**
	 *
	 */
	public function test_size2_beginning()
	{
		$test = new Eresus_UI_Pagination(4, 1);
		$test->setSize(2);

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
					$this->assertEquals($i, $page['title'], 'Invalid page number');
					$this->assertEquals('/root/p' . $i . '/', $page['url'], 'Invalid last page url');
				break;

				case $i == 3:
					$this->assertEquals('&rarr;', $page['title'], 'Invalid last element');
					$this->assertEquals('/root/p3/', $page['url'], 'Invalid last page url');
				break;

			}

			$i++;
		}
	}

	/**
	 *
	 */
	public function test_size2_current3()
	{
		$test = new Eresus_UI_Pagination(4, 3);
		$test->setSize(2);

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
					$this->assertEquals('/root/p1/', $page['url'], 'Invalid first page url');
				break;

				case $i > 1 && $i < 4:
					$this->assertEquals($i, $page['title'], 'Invalid page number');
					$this->assertEquals('/root/p' . $i . '/', $page['url'], 'Invalid last page url');
				break;

				case $i == 4:
					$this->assertEquals('&rarr;', $page['title'], 'Invalid last element');
					$this->assertEquals('/root/p4/', $page['url'], 'Invalid last page url');
				break;

			}

			$i++;
		}
	}
}
