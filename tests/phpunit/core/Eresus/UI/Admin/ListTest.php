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
 */

require_once __DIR__ . '/../../../../bootstrap.php';
require_once TESTS_SRC_DIR . '/Eresus/UI/Admin/List.php';

/**
 */
class Eresus_UI_Admin_ListTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_UI_Admin_List::setColumn
	 */
	public function test_setColumn()
	{
		 $AdminList = new Eresus_UI_Admin_List();
		 $AdminList->columns = array(0=>array(1, 2));
     $AdminList->setColumn(1, array(3, 4));      
     $this->assertEquals(array(array(1, 2), array(3, 4)),$AdminList->columns);
		
		 $AdminList->setColumn(0, array(3, 4));      
     $this->assertEquals(array(array(1, 2, 3, 4), array(3, 4)),$AdminList->columns);
           
	}
	
	/**
	 * @covers Eresus_UI_Admin_List::addRow
	 */
	public function test_addRow()
	{
		$AdminList = new Eresus_UI_Admin_List();
		$cells=array(
				0=>'foo',
				1=>array('bar'),
				2=>array('text'=>'go')	
			);
		
		$AdminList->addRow($cells);  
    $this->assertEquals(array(
				0=>array(
						0=>array('text'=>'foo'),
				    1=>array('text'=>'bar'),     
			    	2=>array('text'=>'go')
			)),$AdminList->body);          
	}
	
	/**
	 * @covers Eresus_UI_Admin_List::addRows
	 */
	public function test_addRows()
	{
		$AdminList = new Eresus_UI_Admin_List();
		$rows=array(
			0=>array(0=>'foo', 1=>array('bar'),	2=>array('text'=>'go')),	
			1=>array(0=>'foo', 1=>array('bar'),	2=>array('text'=>'go'))
			);
		
		$AdminList->addRows($rows);  
    $this->assertEquals(array(
				0=>array(
						0=>array('text'=>'foo'),
				    1=>array('text'=>'bar'),     
			    	2=>array('text'=>'go')),
				1=>array(
						0=>array('text'=>'foo'),
				    1=>array('text'=>'bar'),     
			    	2=>array('text'=>'go'))			
			),$AdminList->body);          
	}
	
	/**
	 * @covers Eresus_UI_Admin_List::renderCell
	 */
	public function test_renderCell()
	{
		$AdminList = new Eresus_UI_Admin_List();
		$cell=array(
			'href'=>'foo',
			'align'=>'bar',
			'style'=>'go',
			'text'=>'world'
			);

		$renderCell = new ReflectionMethod('Eresus_UI_Admin_List', 'renderCell');
		$renderCell->setAccessible(true);
    $this->assertEquals('<meta style="text-align: bar;go"><a href="foo">world</a></meta>',
			$renderCell->invoke($AdminList, 'meta', $cell));
		
		$cell=array();  
    $this->assertEquals('<meta></meta>',	$renderCell->invoke($AdminList, 'meta', $cell));
	}	

	/**
	 * @covers Eresus_UI_Admin_List::setHead
	 */
	public function test_setHead()
	{
		$AdminList = new Eresus_UI_Admin_List();

		$AdminList->setHead('foo', array('bar','text'=>'go')); 
    $this->assertEquals( array(array('text'=>'foo'), array('bar','text'=>'go')),	$AdminList->head); 
		
		$AdminList->setHead(); 
		$this->assertEquals( array(),	$AdminList->head);
		
		$AdminList->setHead(1, 2, 3); 
		$this->assertEquals( array(),	$AdminList->head);
	}	

	/* */
}
