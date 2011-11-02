<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2010, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mk@procreat.ru>
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
 *
 * $Id$
 */

/**
 * Страница "О программе"
 *
 * @package Eresus
 */
class TAbout
{
	/**
	 * Возвращает страницу "О программе"
	 *
	 * @return string  HTML
	 */
	public function adminRender()
	{
		global $Eresus, $page, $locale;

		$xml = new DOMDocument('1.0', 'UTF-8');
		$xml->load($Eresus->froot . 'core/about.xml');

		$data = array();

		$product = $xml->getElementsByTagName('product')->item(0);
		$data['product'] = array();
		$data['product']['title'] = $product->getAttribute('title');
		$data['product']['version'] = $product->getAttribute('version');

		$data['product']['copyrights'] = array();
		$copyrights = $product->getElementsByTagName('copyright');
		for ($i = 0; $i < $copyrights->length; $i++)
		{
			$data['product']['copyrights'] []= array(
				'year' => $copyrights->item($i)->getAttribute('year'),
				'owner' => $copyrights->item($i)->getAttribute('owner'),
				'url' => $copyrights->item($i)->getAttribute('url'),
			);
		}

		$license = $xml->getElementsByTagName('license')->item(0);
		$data['license'] = array();
		$data['license']['text'] = $license->getElementsByTagName($locale['lang'])->item(0)->textContent;

		$tmpl = $page->getUITheme()->getTemplate('misc/about.html');
		$html = $tmpl->compile($data);

		return $html;
	}
	//-----------------------------------------------------------------------------
}
