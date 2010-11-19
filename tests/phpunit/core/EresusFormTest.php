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

require_once dirname(__FILE__) . '/../stubs.php';
require_once dirname(__FILE__) . '/../../../main/core/EresusForm.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class EresusFormTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers EresusForm::__construct
	 */
	public function test_construct()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		$template = new ReflectionProperty('EresusForm', 'template');
		$template->setAccessible(true);

		$charset = new ReflectionProperty('EresusForm', 'charset');
		$charset->setAccessible(true);

		$form = new EresusForm('my_template'); // TODO заменить на объект Template!
		$this->assertEquals('my_template', $template->getValue($form));
		$this->assertEquals('UTF-8', $charset->getValue($form));

		$form = new EresusForm('my_template', 'cp1251'); // TODO заменить на объект Template!
		$this->assertEquals('my_template', $template->getValue($form));
		$this->assertEquals('CP1251', $charset->getValue($form));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusForm::setValue
	 */
	public function test_setValue()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		$values = new ReflectionProperty('EresusForm', 'values');
		$values->setAccessible(true);

		$form = new EresusForm('my_template'); // TODO заменить на объект Template!
		$form->setValue('my_key', 'my_val');

		$this->assertEquals(array('my_key' => 'my_val'), $values->getValue($form));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusForm::toUTF
	 */
	public function test_toUTF()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		$toUTF = new ReflectionMethod('EresusForm', 'toUTF');
		$toUTF->setAccessible(true);

		$form = new EresusForm('my_template', 'cp1251'); // TODO заменить на объект Template!

		$textUTF = 'тестовая строка';
		$textNonUTF = iconv('utf-8', 'cp1251', 'тестовая строка');

		$result = $toUTF->invoke($form, $textNonUTF);

		$this->assertEquals($textUTF, $result);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusForm::fromUTF
	 */
	public function test_fromUTF()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		$fromUTF = new ReflectionMethod('EresusForm', 'fromUTF');
		$fromUTF->setAccessible(true);

		$form = new EresusForm('my_template', 'cp1251'); // TODO заменить на объект Template!

		$textUTF = 'тестовая строка';
		$textNonUTF = iconv('utf-8', 'cp1251', 'тестовая строка');

		$result = $fromUTF->invoke($form, $textUTF);

		$this->assertEquals($textNonUTF, $result);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusForm::unpackData
	 */
	public function test_unpackData()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		$values = new ReflectionProperty('EresusForm', 'values');
		$values->setAccessible(true);

		$isInvalid = new ReflectionProperty('EresusForm', 'isInvalid');
		$isInvalid->setAccessible(true);

		$messages = new ReflectionProperty('EresusForm', 'messages');
		$messages->setAccessible(true);

		$unpackData = new ReflectionMethod('EresusForm', 'unpackData');
		$unpackData->setAccessible(true);

		$form = new EresusForm('my_template'); // TODO заменить на объект Template!
		$data = array(
			'values' => array('a' => 'b'),
			'isInvalid' => true,
			'messages' => array('msg')
		);

		$unpackData->invoke($form, $data);

		$this->assertEquals(array('a' => 'b'), $values->getValue($form));
		$this->assertTrue($isInvalid->getValue($form));
		$this->assertEquals(array('msg'), $messages->getValue($form));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusForm::isValid
	 */
	public function test_isValid()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		$form = new EresusForm('my_template'); // TODO заменить на объект Template!

		$invalidData = new ReflectionProperty('EresusForm', 'invalidData');
		$invalidData->setAccessible(true);
		$invalidData->setValue($form, array('a' => true));

		$this->assertFalse($form->isValid('a'));
		$this->assertTrue($form->isValid('b'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusForm::getValue
	 */
	public function test_getValue()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		$form = new EresusForm('my_template'); // TODO заменить на объект Template!

		$values = new ReflectionProperty('EresusForm', 'values');
		$values->setAccessible(true);
		$values->setValue($form, array('a' => 'valueA'));

		$req = $this->getMock('stdClass', array('arg'));
		$req->expects($this->never())->
			method('arg')->
			will($this->returnValue('valueB'));

		$request = new ReflectionProperty('EresusForm', 'request');
		$request->setAccessible(true);
		$request->setValue($form, $req);

		$this->assertEquals('valueA', $form->getValue('a'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusForm::getValue
	 */
	public function test_getValue_from_request()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		$form = new EresusForm('my_template'); // TODO заменить на объект Template!

		$values = new ReflectionProperty('EresusForm', 'values');
		$values->setAccessible(true);
		$values->setValue($form, array('a' => 'valueA'));

		$req = $this->getMock('stdClass', array('arg'));
		$req->expects($this->once())->
			method('arg')->
			will($this->returnValue('valueB'));

		$request = new ReflectionProperty('EresusForm', 'request');
		$request->setAccessible(true);
		$request->setValue($form, $req);

		$this->assertEquals('valueB', $form->getValue('b'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusForm::invalidValue
	 */
	public function test_invalidValue()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		$invalidData = new ReflectionProperty('EresusForm', 'invalidData');
		$invalidData->setAccessible(true);

		$messages = new ReflectionProperty('EresusForm', 'messages');
		$messages->setAccessible(true);

		$form = new EresusForm('my_template'); // TODO заменить на объект Template!
		$form->invalidValue('a', 'b');

		$this->assertEquals(array('a' => true), $invalidData->getValue($form));
		$this->assertEquals(array('a' => 'b'), $messages->getValue($form));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusForm::invalidData
	 */
	public function test_invalidData()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		$invalidData = new ReflectionProperty('EresusForm', 'invalidData');
		$invalidData->setAccessible(true);

		$isInvalid = new ReflectionProperty('EresusForm', 'isInvalid');
		$isInvalid->setAccessible(true);

		$form = new EresusForm('my_template'); // TODO заменить на объект Template!

		$invalidData->setValue($form, array());
		$isInvalid->setValue($form, false);
		$this->assertFalse($form->invalidData());

		$invalidData->setValue($form, array('message'));
		$isInvalid->setValue($form, false);
		$this->assertTrue($form->invalidData());

		$invalidData->setValue($form, array());
		$isInvalid->setValue($form, true);
		$this->assertTrue($form->invalidData());

		$invalidData->setValue($form, array('message'));
		$isInvalid->setValue($form, true);
		$this->assertTrue($form->invalidData());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusForm::packData
	 */
	public function test_packData()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		$values = new ReflectionProperty('EresusForm', 'values');
		$values->setAccessible(true);

		$isInvalid = new ReflectionProperty('EresusForm', 'isInvalid');
		$isInvalid->setAccessible(true);

		$messages = new ReflectionProperty('EresusForm', 'messages');
		$messages->setAccessible(true);

		$packData = new ReflectionMethod('EresusForm', 'packData');
		$packData->setAccessible(true);

		$form = new EresusForm('my_template'); // TODO заменить на объект Template!

		$values->setValue($form, array('a' => 'b'));
		$isInvalid->setValue($form, true);
		$messages->setValue($form, array('message1'));

		$this->assertEquals(array(
			'values' => array('a' => 'b'),
			'isInvalid' => true,
			'messages' => array('message1')
		), $packData->invoke($form));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusForm::sessionStore
	 */
	public function test_sessionStore()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		$form = new EresusForm('my_template'); // TODO заменить на объект Template!

		$id = new ReflectionProperty('EresusForm', 'id');
		$id->setAccessible(true);
		$id->setValue($form, 'formID');

		$sessionStore = new ReflectionMethod('EresusForm', 'sessionStore');
		$sessionStore->setAccessible(true);

		$sessionStore->invoke($form);

		$this->assertEquals(array(
			'EresusForm' => array(
				'formID' => array(
					'values' => array(),
					'isInvalid' => false,
					'messages' => array()
				)
			)
		), $_SESSION);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusForm::addMessage
	 */
	public function test_addMessage()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		$form = new EresusForm('my_template'); // TODO заменить на объект Template!

		$messages = new ReflectionProperty('EresusForm', 'messages');
		$messages->setAccessible(true);
		$messages->setValue($form, array());

		$addMessage = new ReflectionMethod('EresusForm', 'addMessage');
		$addMessage->setAccessible(true);

		$addMessage->invoke($form, 'a', 'messageA');

		$this->assertEquals(array('a' => 'messageA'), $messages->getValue($form));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusForm::childrenAsArray
	 */
	public function test_childrenAsArray()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		$doc = new DOMDocument('1.0', 'UTF-8');
		$rootNode = $doc->createElement('form');
		$doc->appendChild($rootNode);

		$node1 = $doc->createElement('div', '1');
		$rootNode->appendChild($node1);
		$node2 = $doc->createElement('div', '2');
		$rootNode->appendChild($node2);

		$childrenAsArray = new ReflectionMethod('EresusForm', 'childrenAsArray');
		$childrenAsArray->setAccessible(true);

		$form = new EresusForm('my_template'); // TODO заменить на объект Template!

		$this->assertEmpty($childrenAsArray->invoke($form, 'some string'));
		$this->assertEmpty($childrenAsArray->invoke($form, new stdClass()));
		$this->assertEquals(array($node1, $node2),
			$childrenAsArray->invoke($form, $rootNode->childNodes));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusForm::copyElement
	 */
	public function test_copyElement()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		$doc = new DOMDocument('1.0', 'UTF-8');
		$sourceNode = $doc->createElement('div');
		$doc->appendChild($sourceNode);
		$sourceNode->setAttribute('class', 'my-class');
		$sourceNode->appendChild($doc->createElement('span', 'test'));

		$targetNode = $doc->createElement('div');
		$doc->appendChild($targetNode);

		$copyElement = new ReflectionMethod('EresusForm', 'copyElement');
		$copyElement->setAccessible(true);

		$form = new EresusForm('my_template'); // TODO заменить на объект Template!

		$copyElement->invoke($form, $sourceNode, $targetNode);

		$this->assertEquals('<div class="my-class"><span>test</span></div>',
			$doc->saveXML($targetNode));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusForm::valueOf
	 */
	public function test_valueOf()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		$form = new EresusForm('my_template'); // TODO заменить на объект Template!

		$valueOf = new ReflectionMethod('EresusForm', 'valueOf');
		$valueOf->setAccessible(true);

		$doc = new DOMDocument('1.0', 'UTF-8');

		$node = $doc->createElement('div');
		$this->assertNull($valueOf->invoke($form, $node));

		$node = $doc->createElement('input');
		$node->setAttribute('value', 'val1');
		$this->assertEquals('val1', $valueOf->invoke($form, $node));

		$node = $doc->createElement('input');
		$node->setAttribute('type', 'checkbox');
		$node->setAttribute('value', 'val2');
		$this->assertFalse($valueOf->invoke($form, $node));

		$node->setAttribute('checked', 'checked');
		$this->assertEquals('val2', $valueOf->invoke($form, $node));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusForm::detectAutoValidate
	 */
	public function test_detectAutoValidate()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		$doc = new DOMDocument('1.0', 'UTF-8');
		$unknownNode = $doc->createElement('unknown');
		$doc->appendChild($unknownNode);
		$formNode = $doc->createElement('form');
		$formNode->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:fe', EresusForm::NS);
		$doc->appendChild($formNode);

		$form = new EresusForm('my_template'); // TODO заменить на объект Template!

		$xml = new ReflectionProperty('EresusForm', 'xml');
		$xml->setAccessible(true);
		$xml->setValue($form, $doc);

		$detectAutoValidate = new ReflectionMethod('EresusForm', 'detectAutoValidate');
		$detectAutoValidate->setAccessible(true);

		$detectAutoValidate->invoke($form);

		$autoValidate = new ReflectionProperty('EresusForm', 'autoValidate');
		$autoValidate->setAccessible(true);

		$this->assertTrue($autoValidate->getValue($form));

		$formNode->setAttributeNS(EresusForm::NS, 'validate', '');
		$detectAutoValidate->invoke($form);
		$this->assertFalse($autoValidate->getValue($form));

		$formNode->setAttributeNS(EresusForm::NS, 'validate', '0');
		$detectAutoValidate->invoke($form);
		$this->assertFalse($autoValidate->getValue($form));

		$formNode->setAttributeNS(EresusForm::NS, 'validate', 'false');
		$detectAutoValidate->invoke($form);
		$this->assertFalse($autoValidate->getValue($form));
	}
	//-----------------------------------------------------------------------------

	/* */
}
