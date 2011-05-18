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
 * $Id: AdminUITest.php 1376 2011-01-17 19:17:36Z mk $
 */

require_once dirname(__FILE__) . '/../../stubs.php';
require_once dirname(__FILE__) . '/../../../../main/core/Mail/EresusMail.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class EresusMailTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers EresusMail::__construct
	 */
	public function test_construct()
	{
		$mail = new EresusMail();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusMail::setTransport
	 */
	public function test_setTransport()
	{
		$mail = new EresusMail();
		$transport = new ezcMailMtaTransport();
		$mail->setTransport($transport);
		$this->assertAttributeSame($transport, 'transport', $mail);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusMail::getTransport
	 */
	public function test_getTransport()
	{
		$mail = new EresusMail();
		$transport = $mail->getTransport();
		$this->assertInstanceOf('ezcMailMtaTransport', $transport);

		$transport = new ezcMailMtaTransport();
		$mail->setTransport($transport);
		$this->assertAttributeSame($transport, 'transport', $mail);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusMail::setComposer
	 */
	public function test_setComposer()
	{
		$mail = new EresusMail();
		$composer = new ezcMailComposer();
		$mail->setComposer($composer);
		$this->assertAttributeSame($composer, 'composer', $mail);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusMail::getComposer
	 */
	public function test_getComposer()
	{
		$mail = new EresusMail();
		$composer = $mail->getComposer();
		$this->assertInstanceOf('ezcMailComposer', $composer);

		$composer = new ezcMailComposer();
		$mail->setComposer($composer);
		$this->assertAttributeSame($composer, 'composer', $mail);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusMail::addTo
	 */
	public function test_addTo()
	{
		$mail = new EresusMail();
		$composer = $this->getMock('ezcMailComposer', array('addTo'));
		$composer->expects($this->once())->method('addTo');
		$mail->setComposer($composer);
		$this->assertSame($mail, $mail->addTo('user@example.org', 'Some name'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusMail::addCc
	 */
	public function test_addCc()
	{
		$mail = new EresusMail();
		$composer = $this->getMock('ezcMailComposer', array('addCc'));
		$composer->expects($this->once())->method('addCc');
		$mail->setComposer($composer);
		$this->assertSame($mail, $mail->addCc('user@example.org', 'Some name'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusMail::addBcc
	 */
	public function test_addBcc()
	{
		$mail = new EresusMail();
		$composer = $this->getMock('ezcMailComposer', array('addBcc'));
		$composer->expects($this->once())->method('addBcc');
		$mail->setComposer($composer);
		$this->assertSame($mail, $mail->addBcc('user@example.org', 'Some name'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusMail::setFrom
	 */
	public function test_setFrom()
	{
		$mail = new EresusMail();
		$composer = new ezcMailComposer();
		$mail->setComposer($composer);
		$this->assertSame($mail, $mail->setFrom('user@example.org', 'Some name'));
		$this->assertInstanceOf('ezcMailAddress', $composer->from);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusMail::setReplyTo
	 */
	public function test_setReplyTo()
	{
		$mail = $this->getMock('EresusMail', array('setHeader'));
		$mail->expects($this->once())->method('setHeader')->with('Reply-To', 'mail@example.org');
		$this->assertSame($mail, $mail->setReplyTo('mail@example.org'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusMail::setSubject
	 */
	public function test_setSubject()
	{
		$mail = new EresusMail();
		$composer = new ezcMailComposer();
		$mail->setComposer($composer);
		$this->assertSame($mail, $mail->setSubject('Subject'));
		$this->assertEquals('Subject', $composer->subject);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusMail::setHTML
	 */
	public function test_setHTML()
	{
		$mail = new EresusMail();
		$this->assertSame($mail, $mail->setHTML('<html><body></body></html>'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusMail::setText
	 */
	public function test_setText()
	{
		$mail = new EresusMail();
		$this->assertSame($mail, $mail->setText('text'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusMail::addAttachment
	 */
	public function test_addAttachment()
	{
		$mail = new EresusMail();
		$composer = $this->getMock('ezcMailComposer', array('addAttachment'));
		$composer->expects($this->once())->method('addAttachment')->
			with('filename', 'content', 'text', 'html');
		$mail->setComposer($composer);
		$this->assertSame($mail, $mail->addAttachment('filename', 'content', 'text', 'html'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusMail::setHeader
	 */
	public function test_setHeader()
	{
		$mail = new EresusMail();
		$composer = $this->getMock('ezcMailComposer', array('setHeader'));
		$composer->expects($this->once())->method('setHeader')->with('header-name', 'header-value');
		$mail->setComposer($composer);
		$this->assertSame($mail, $mail->setHeader('header-name', 'header-value'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusMail::send
	 */
	public function test_send()
	{
		$mail = new EresusMail();

		$composer = $this->getMock('ezcMailComposer', array('build'));
		$composer->from = null;
		$composer->expects($this->once())->method('build');
		$mail->setComposer($composer);

		$transport = $this->getMock('ezcMailTransport', array('send'));
		$transport->expects($this->once())->method('send')->with($composer);
		$mail->setTransport($transport);

		$mail->send();
	}
	//-----------------------------------------------------------------------------

	/* */
}
