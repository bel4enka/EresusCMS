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
 * @author Mikhail Krasilnikov <mk@eresus.ru>
 *
 * $Id$
 */

require_once dirname(__FILE__) . '/../../../../../main/core/classes/helpers/PaginationHelper.php';

/**
 * @package Kernel
 * @subpackage Tests
 */
class PaginationHelperTest extends PHPUnit_Framework_TestCase
{

	/**
	 * Проверяем установку и чтение свойства $total
	 *
	 * @covers PaginationHelper::setTotal
	 * @covers PaginationHelper::getTotal
	 */
	public function test_setgetTotal()
	{
		$test = new PaginationHelper();
		$test->setTotal(123);
		$this->assertEquals(123, $test->getTotal());
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем установку и чтение свойства $current
	 *
	 * @covers PaginationHelper::setCurrent
	 * @covers PaginationHelper::getCurrent
	 */
	public function test_setgetCurrent()
	{
		$test = new PaginationHelper();
		$test->setCurrent(123);
		$this->assertEquals(123, $test->getCurrent());
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем конструктор
	 *
	 * @covers PaginationHelper::__construct
	 */
	public function test_construct_wo_args()
	{
		$test = new PaginationHelper();
		$this->assertNull($test->getTotal(), 'Case 1');
		$this->assertEquals(1, $test->getCurrent(), 'Case 1');

		$test = new PaginationHelper(10);
		$this->assertEquals(10, $test->getTotal(), 'Case 2');
		$this->assertEquals(1, $test->getCurrent(), 'Case 2');

		$test = new PaginationHelper(20, 5);
		$this->assertEquals(20, $test->getTotal(), 'Case 3');
		$this->assertEquals(5, $test->getCurrent(), 'Case 3');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем установку и чтение свойства $templatePath
	 *
	 * @covers PaginationHelper::setTemplate
	 * @covers PaginationHelper::getTemplate
	 */
	public function test_setgetTemplate()
	{
		$test = new PaginationHelper();
		$test->setTemplate('/path/to/file');
		$this->assertEquals('/path/to/file', $test->getTemplate());
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяем метод rewind()
	 *
	 */
	public function test_rewind()
	{
		$test = new PaginationHelper(10, 1);
		$test->rewind();
		$this->assertAttributeEquals(1, 'first', $test, 'Case 1');
		$this->assertAttributeEquals(10, 'last', $test, 'Case 1');
		$this->assertAttributeEquals(10, 'totalIterations', $test, 'Case 1');

		$test->setCurrent(10);
		$test->rewind();
		$this->assertAttributeEquals(1, 'first', $test, 'Case 2');
		$this->assertAttributeEquals(10, 'last', $test, 'Case 2');
		$this->assertAttributeEquals(10, 'totalIterations', $test, 'Case 2');

		$test->setTotal(100);
		$test->setCurrent(1);
		$test->rewind();
		$this->assertAttributeEquals(1, 'first', $test, 'Case 3');
		$this->assertAttributeEquals(10, 'last', $test, 'Case 3');
		$this->assertAttributeEquals(11, 'totalIterations', $test, 'Case 3');

		$test->setCurrent(50);
		$test->rewind();
		$this->assertAttributeEquals(45, 'first', $test, 'Case 4');
		$this->assertAttributeEquals(54, 'last', $test, 'Case 4');
		$this->assertAttributeEquals(12, 'totalIterations', $test, 'Case 4');

		$test->setCurrent(100);
		$test->rewind();
		$this->assertAttributeEquals(91, 'first', $test, 'Case 5');
		$this->assertAttributeEquals(100, 'last', $test, 'Case 5');
		$this->assertAttributeEquals(11, 'totalIterations', $test, 'Case 5');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обычное использование
	 *
	 */
	public function test_commonUse_simple()
	{
		$test = new PaginationHelper(10, 5);

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

			$this->assertEquals($i, $helper->key());
			$this->assertEquals($i, $page['title'], 'Ivalid page number');
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
		$test = new PaginationHelper(100, 1);

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
					$this->assertEquals($i, $page['title'], 'Ivalid page number');
				break;

				case $i == 11:
					$this->assertEquals('&rarr;', $page['title'], 'Invalid last element');
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
		$test = new PaginationHelper(100, 100);

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
				break;

				case $i > 1 && $i < 11:
					$this->assertEquals($i + 90, $page['title'], 'Ivalid page number');
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
		$test = new PaginationHelper(100, 50);

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
				break;

				case $i > 1 && $i < 12:
					$this->assertEquals($i + 44, $page['title'], 'Ivalid page number');
				break;

				case $i == 12:
					$this->assertEquals('&rarr;', $page['title'], 'Invalid last element');
				break;
			}

			$i++;
		}
	}
	//-----------------------------------------------------------------------------

	/* */
}


class Template
{
	public function compile($data)
	{
		return $data;
	}
	//-----------------------------------------------------------------------------
}