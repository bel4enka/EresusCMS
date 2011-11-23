<?php
/**
 * ${product.title}
 *
 * Элемент XML
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
 *
 * $Id$
 */


/**
 * Элемент XML
 *
 * @package Eresus
 * @since 2.17
 */
class Eresus_XML_Element extends SimpleXMLElement
{
	/**
	 * Возвращает локализованный текст узла
	 *
	 * @return string
	 *
	 * @since 2.17
	 */
	public function getLocalized()
	{
		$locale = Eresus_Kernel::sc()->i18n->getLocale();
		$tags = $this->$locale;
		if (count($tags))
		{
			return strval($tags[0]);
		}

		$locale = substr($locale, 0, 3);
		$tags = $this->xpath('//*[starts-with(name(), "' . $locale . '")]');
		if (count($tags))
		{
			return strval($tags[0]);
		}

		$locale = Eresus_Config::get('eresus.cms.locale.default', 'ru_RU');
		$tags = $this->$locale;
		if (count($tags))
		{
			return strval($tags[0]);
		}

		return '';
	}
	//-----------------------------------------------------------------------------
}
