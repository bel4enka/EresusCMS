<?php
/**
 * ${product.title}
 *
 * Модуль интернационализации.
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
 * Служба интернационализации
 *
 * Файлы локализации должны располагаться в папке «lang» и называться «код_локали.php». Например:
 * «lang/ru.php», «lang/en_US.php».
 *
 * <b>Примеры</b>
 *
 * <code>
 * $i18n = Eresus_i18n::getInstance();
 * $i18n->setLocale('en_US');
 * echo $i18->getText('Привет, мир!'); // Может вывести, например, "Hello world!"
 * </code>
 *
 * И в шаблонах:
 *
 * <code>
 * <div>{{ i18n('Привет, мир!') }}</div>
 * </code>
 *
 * @package Eresus
 */
class Eresus_I18n
{
	/**
	 * Экземпляр-одиночка
	 *
	 * @var Eresus_I18n
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
	 * Возвращает экземпляр-одиночку
	 *
	 * @return Eresus_I18n
	 */
	static public function getInstance()
	{
		if (!self::$instance)
		{
			self::$instance = new Eresus_I18n(Eresus_Kernel::app()->getFsRoot() . '/lang');
		}

		return self::$instance;
	}

	/**
	 * Конструктор
	 *
	 * @param string $path  Путь к файлам локализации
	 * @return Eresus_I18n
	 */
	public function __construct($path)
	{
		$this->path = $path;
	}

	/**
	 * Возвращает текущую локаль
	 *
	 * @return string
	 *
	 * @since 3.01
	 */
	public function getLocale()
	{
		return $this->locale;
	}

	/**
	 * Выбор локали
	 *
	 * @param string $locale
	 *
	 * @throws InvalidArgumentException  если код локали не в фомрате xx или xx_XX
	 *
	 * @return void
	 */
	public function setLocale($locale)
	{
		if (!preg_match('/^[a-z]{2}(_[A-Z]{2})?$/', $locale))
		{
			throw new InvalidArgumentException('Invalid locale code: ' . $locale);
		}
		$this->locale = $locale;
		/** @noinspection PhpIncludeInspection */
		include_once $this->path . '/' . $this->locale . '.php';
	}

	/**
	 * Возвращает текст в заданной локали
	 *
	 * @param string $key      Ключ искомой строки
	 * @param string $context  Контекст (пока не используется)
	 * @return string
	 */
	public function getText($key, /** @noinspection PhpUnusedParameterInspection */
		$context = null)
	{
		if (defined($key))
		{
			return constant($key);
		}

		return $key;
	}
}
