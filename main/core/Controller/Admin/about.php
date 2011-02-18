<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2010, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mihalych@vsepofigu.ru>
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
 * @package BusinessLogic
 *
 * $Id$
 */

/**
 * Страница "О программе"
 *
 * @package BusinessLogic
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

		$xml = simplexml_load_file($Eresus->froot . 'core/about.xml');

		$data = array();

		$data['product'] = array();
		$data['product']['title'] = $xml->product['title'];
		$data['product']['version'] = $xml->product['version'];
		$data['product']['copyrights'] = array();
		foreach ($xml->product->copyright as $copyright)
		{
			$data['product']['copyrights'] []= array(
				'year' => $copyright['year'],
				'owner' => iconv('utf-8', 'cp1251', $copyright['owner']),
				'url' => $copyright['url'],
			);
		}

		$data['license'] = array();
		$data['license']['text'] = iconv('utf-8', 'cp1251', $xml->license->ru->asXML());

		$data['uses'] = array();
		foreach ($xml->uses->item as $item)
		{
			$data['uses'] []= array(
				'title' => iconv('utf-8', 'cp1251', $item['title']),
				'url' => $item['url'],
				'logo' => $item['logo'],
				'info' => iconv('utf-8', 'cp1251', $item->asXML())
			);
		}

		$tmpl = new Template('core/templates/misc/about.html');
		$html = $tmpl->compile($data);

		return $html;
	}
	//-----------------------------------------------------------------------------
}
