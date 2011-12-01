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
 *
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
class Eresus_XML_Element implements Serializable, ArrayAccess
{
	/**
	 * Объект SimpleXMLElement
	 *
	 * Нельзя унаследовать Eresus_XML_Element от SimpleXMLElement, потому что последний нельзя
	 * сериализовывать. Поэтому использует декоратор.
	 *
	 * @var SimpleXMLElement
	 */
	private $xml;

	/**
	 * @since 2.17
	 */
	public function __construct($data, $options = 0, $data_is_url = false, $ns = '',
		$is_prefix = false)
	{
		if ($data instanceof SimpleXMLElement)
		{
			$this->xml = $data;
		}
		else
		{
			$this->xml = new SimpleXMLElement($data, $options, $data_is_url, $ns, $is_prefix);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * @see SimpleXMLElement::__toString()
	 */
	public function __toString()
	{
		return $this->xml->__toString();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @since 2.17
	 */
	public function __get($name)
	{
		$value = $this->xml->$name;
		$value = $this->convertValue($value);
		return $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * @since 2.17
	 */
	public function __set($name, $value)
	{
		$this->xml->$name = $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * @since 2.17
	 */
	public function __call($method, $args)
	{
		$value = call_user_func_array(array($this->xml, $method), $args);
		$value = $this->convertValue($value);
		return $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * @since 2.17
	 */
	public function offsetExists($offset)
	{
		return isset($this->xml[$offset]);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @since 2.17
	 */
	public function offsetGet($offset)
	{
		$value = $this->xml[$offset];
		$value = $this->convertValue($value);
		return $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * @since 2.17
	 */
	public function offsetSet($offset, $value)
	{
		$this->xml[$offset] = $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * @since 2.17
	 */
	public function offsetUnset($offset)
	{
		unset($this->xml[$offset]);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @see Serializable::serialize()
	 */
	public function serialize()
	{
		return serialize($this->xml->asXML());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @see Serializable::unserialize()
	 */
	public function unserialize($value)
	{
		$this->xml = new SimpleXMLElement(unserialize($value));
	}
	//-----------------------------------------------------------------------------

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
		$tags = $this->xml->$locale;
		if (count($tags))
		{
			return strval($tags[0]);
		}

		$locale = substr($locale, 0, 3);
		$tags = $this->xml->xpath('//*[starts-with(name(), "' . $locale . '")]');
		if (count($tags))
		{
			return strval($tags[0]);
		}

		$locale = Eresus_Config::get('eresus.cms.locale.default', 'ru_RU');
		$tags = $this->xml->$locale;
		if (count($tags))
		{
			return strval($tags[0]);
		}

		return '';
	}
	//-----------------------------------------------------------------------------

	/**
	 * Оборачивает все значения SimpleXMLElement в Eresus_XML_Element
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 *
	 * @since 2.17
	 */
	private function convertValue($value)
	{
		if (is_array($value))
		{
			foreach ($value as &$element)
			{
				$element = $this->convertValue($element);
			}
		}
		elseif ($value instanceof SimpleXMLElement)
		{
			$value = new self($value);
		}
		return $value;
	}
	//-----------------------------------------------------------------------------

}
