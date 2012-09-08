<?php
/**
 * ${product.title}
 *
 * Абстрактный элемент документа HTML
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
 * Абстрактный элемент документа HTML
 *
 * @package Eresus
 * @since 2.15
 */
class Eresus_Html_Element
{
	/**
	 * Имя тега
	 *
	 * @var string
	 */
	private $tagName;

	/**
	 * Атрибуты
	 *
	 * @var array
	 */
	private $attrs = array();

	/**
	 * Содержимое
	 *
	 * @var string
	 */
	private $contents = null;

	/**
	 * Конструктор
	 *
	 * @param string $tagName
	 *
	 * @since 2.15
	 */
	public function __construct($tagName)
	{
		$this->tagName = $tagName;
	}

	/**
	 * Устанавливает значение атрибута
	 *
	 * @param string $name   имя атрибута
	 * @param mixed  $value  значение атрибута
	 *
	 * @return void
	 *
	 * @since 2.15
	 */
	public function setAttribute($name, $value = true)
	{
		$this->attrs[$name] = $value;
	}

	/**
	 * Возвращает значение атрибута
	 *
	 * @param string $name  имя атрибута
	 *
	 * @return mixed
	 *
	 * @since 2.15
	 */
	public function getAttribute($name)
	{
		if (!isset($this->attrs[$name]))
		{
			return null;
		}

		return $this->attrs[$name];
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
		$this->contents = $contents;
	}

	/**
	 * Возвращает разметку элемента
	 *
	 * @return string  разметка HTML
	 *
	 * @since 2.15
	 */
	public function getHTML()
	{
		// Открывающий тег
		$html = '<' . $this->tagName;

		/* Добавляем атрибуты */
		foreach ($this->attrs as $name => $value)
		{
			$html .= ' ' . $name;

			if ($value !== true)
			{
				$html .= '="' . $value . '"';
			}
		}

		$html .= '>';

		/* Если есть содержимое, то добавляем его и закрывающий тег */
		if ($this->contents !== null)
		{
			$html .= $this->contents . '</' . $this->tagName . '>';
		}

		return $html;
	}
}
