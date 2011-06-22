<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Модуль интернационализации
 *
 * @copyright 2004, Eresus Project, http://eresus.ru/
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
 * @package i18n
 *
 * $Id$
 */


/**
 * Служба интернационализации
 *
 * @package i18n
 */
class Eresus_i18n
{

	/**
	 * Экземпляр-одиночка
	 *
	 * @var Eresus_i18n
	 */
	static private $instance;

	/**
	 * Путь к файлам локализации
	 * @var string
	 */
	private $path;

	/**
	 * Локаль
	 * @var string
	 */
	private $locale;

	/**
	 * Строковые данные
	 *
	 * @var array
	 */
	private $data = array();

	/**
	 * Возвращает экземпляр-одиночку
	 *
	 * @return Eresus_i18n
	 *
	 * @uses $instance
	 * @uses Eresus_Kernel::app()
	 * @uses Eresus_Kernel::getRootDir()
	 */
	static public function getInstance()
	{
		if (!self::$instance)
		{
			self::$instance = new self(Eresus_Kernel::app()->getRootDir() . '/lang');
		}

		return self::$instance;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает текущую локаль
	 *
	 * @return string
	 *
	 * @uses $locale
	 */
	public function getLocale()
	{
		return $this->locale;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Выбор локали
	 *
	 * @param string $locale
	 *
	 * @return void
	 *
	 * @uses $locale
	 */
	public function setLocale($locale)
	{
		$this->locale = $locale;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает текст в заданной локали
	 *
	 * @param string $text     Искомый текст
	 * @param string $context  Контекст
	 *
	 * @return string
	 *
	 * @uses localeLazyLoad()
	 * @uses $data
	 * @uses $locale
	 */
	public function get($text, $context = null)
	{
		$this->localeLazyLoad();

		if (isset($this->data[$this->locale]))
		{
			if ($context && isset($this->data[$this->locale]['messages'][$context]))
			{
				$messages = $this->data[$this->locale]['messages'][$context];
			}
			else
			{
				$messages = $this->data[$this->locale]['messages']['global'];
			}
			if (isset($messages[$text]))
			{
				return $messages[$text];
			}
		}

		return $text;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Конструктор
	 *
	 * @param string $path  Путь к файлам локализации
	 * @return Eresus_i18n
	 */
	private function __construct($path)
	{
		$this->path = $path;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Ленивая загрузка файла локали
	 *
	 * @return void
	 *
	 * @since 2.16
	 * @uses $data
	 * @uses $locale
	 * @uses $path
	 */
	private function localeLazyLoad()
	{
		if (!isset($this->data[$this->locale]))
		{
			$filename = $this->path . '/' . $this->locale . '.php';
			if (file_exists($filename))
			{
				$this->data[$this->locale] = include $filename;
			}
			else
			{
				Eresus_Logger::log(__METHOD__, LOG_WARNING, 'Can not load language file "%s"', $filename);
			}
		}
	}
	//-----------------------------------------------------------------------------

}


/**
 * Сокращение для "Eresus_i18n::getInstance()->getText()"
 *
 * @since 2.16
 */
function i18n($text, $context = null)
{
	return Eresus_i18n::getInstance()->get($text, $context);
}
//-----------------------------------------------------------------------------
