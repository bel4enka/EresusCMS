<?php
/**
 * ${product.title}
 *
 * Элемент <script>
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
 */

/**
 * Элемент <script>
 *
 * @package Eresus
 * @since 2.15
 */
class Eresus_HTML_ScriptElement extends Eresus_Html_Element
{
	/**
	 * Создаёт новый элемент <script>
	 *
	 * @param string $script [optional]  URL или код скрипта.
	 *
	 * @since 2.15
	 */
	public function __construct($script = '')
	{
		parent::__construct('script');

		$this->setAttribute('type', 'text/javascript');

		/*
		 * Считаем URL-ом всё, что:
		 * - либо содержит xxx:// в начале
		 * - либо состоит из минимум двух групп символов (любые непроблеьные или «;»), разделённых
		 *   точкой или слэшем
		 */
		if ($script !== '' && preg_match('=(^\w{3,8}://|^[^\s;]+(\.|/)[^\s;]+$)=', $script))
		{
			$this->setAttribute('src', $script);
			$this->setContents('');
		}
		else
		{
			$this->setContents($script);
		}
	}

	/**
	 * Устанавливает содержимое
	 *
	 * @param string $contents  содержимое
	 *
	 * @return void
	 *
	 * @since 2.15
	 */
	public function setContents($contents)
	{
		if ($contents)
		{
			$contents = "//<!-- <![CDATA[\n". $contents . "\n//]] -->";
		}
		parent::setContents($contents);
	}
}
