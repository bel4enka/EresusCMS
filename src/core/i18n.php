<?php
/**
 * ${product.title}
 *
 * Модуль интернационализации
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
 * Служба интернационализации
 *
 * Файлы локализации должны располагаться в папке «lang» и называться «код_локали.php». Например:
 * «lang/ru_RU.php».
 *
 * <b>Примеры</b>
 *
 * <code>
 * $i18n = Eresus_i18n::getInstance();
 * $i18n->setLocale('en_US');
 * echo $i18->getText('Привет, мир!'); // Может вывести, например, "Hello world!"
 * </code>
 *
 * Можно использовать сокращённый вызов метода:
 *
 * <code>
 * echo i18n('Привет, мир!');
 * </code>
 *
 * И в шаблонах:
 *
 * <code>
 * <div>{i18n('Привет, мир!')}</div>
 * </code>
 *
 * @package Eresus
 * @since 2.17
 */
class Eresus_i18n
{
	/**
	 * Экземпляр-одиночка
	 *
	 * @var Eresus_i18n
	 * @since 2.17
	 */
	static private $instance;

	/**
	 * Путь к файлам локализации
	 *
	 * @var string
	 * @since 2.17
	 */
	private $path;

	/**
	 * Локаль
	 *
	 * @var string
	 * @since 2.17
	 */
	private $locale;

	/**
	 * Строковые данные
	 *
	 * @var array
	 * @since 2.17
	 */
	private $data = array();

	/**
	 * Возвращает экземпляр-одиночку
	 *
	 * @return Eresus_i18n
	 *
	 * @since 2.17
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
	 * @since 2.17
	 */
	public function getLocale()
	{
		return $this->locale;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Выбор локали
	 *
	 * @param string $locale  код локали (ru_RU, en_US, …)
	 *
	 * @throws InvalidArgumentException  если код локали не в фомрате xx_XX
	 *
	 * @return void
	 *
	 * @since 2.17
	 */
	public function setLocale($locale)
	{
		if (!preg_match('/^[a-z]{2}_[A-Z]{2}$/', $locale))
		{
			throw new InvalidArgumentException('Invalid locale code: ' . $locale);
		}
		$this->locale = $locale;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Загружает языковой файл для указанной локали
	 *
	 * @param string $locale
	 *
	 * @return void
	 *
	 * @since 2.17
	 */
	public function loadLocale($locale)
	{
		if (!array_key_exists($locale, $this->data))
		{
			$filename = $this->path . '/' . $locale . '.php';
			if (file_exists($filename))
			{
				$this->data[$locale] = include $filename;
			}
			else
			{
				// FIXME
				//Eresus_Logger::log(__METHOD__, LOG_WARNING, 'Can not load language file "%s"', $filename);
			}
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает текст в заданной локали
	 *
	 * В качестве контекста желательно использовать имя класса (__CLASS__), в котором расположен
	 * вызов метода.
	 *
	 * Если для указанного контекста не найден перевод, поиск продолжится в глобальном контексте.
	 * Если и там перевод отсутствует, то будет показан исходный текст ($text).
	 *
	 * @param string $text     искомый текст
	 * @param string $context  контекст
	 *
	 * @return string
	 *
	 * @since 2.17
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
	 * Транслитерирует текст
	 *
	 * @param string $source  транслитерируемый текст
	 * @param string $locale  локаль текста (по умолчанию текущая)
	 *
	 * @return string
	 *
	 * @since 2.17
	 */
	public function translit($source, $locale = null)
	{
		$str = preg_replace('/\W/u', '', $source);
		if (null === $locale)
		{
			$locale = $this->locale;
		}

		if (!array_key_exists($locale, $this->data))
		{
			$this->loadLocale($locale);
		}

		/*
		 * Если в языковом файле есть таблица транслитерации, используем её. Иначе попробуем iconv
		 */
		if (isset($this->data[$locale]['translit']))
		{
			$table =& $this->data[$locale]['translit'];
			$strlen = mb_strlen($str);
			$result = '';
			for ($i = 0; $i < $strlen; $i++)
			{
				$char = mb_substr($str, $i, 1);
				$result .= isset($table[$char]) ? $table[$char] : '';
			}
		}
		else
		{
			$result = iconv('utf-8', 'ascii//TRANSLIT', $str);
		}
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Конструктор
	 *
	 * @param string $path  путь к файлам локализации
	 *
	 * @return Eresus_i18n
	 *
	 * @since 2.17
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
	 * @since 2.17
	 */
	private function localeLazyLoad()
	{
		if (!$this->locale)
		{
			$this->locale = Eresus_Config::get('eresus.cms.locale.default', 'ru_RU');
		}
		$this->loadLocale($this->locale);
	}
	//-----------------------------------------------------------------------------

}


/**
 * Сокращение для «{@link Eresus_i18n::get() Eresus_i18n::getInstance()->get()}»
 *
 * @param string $text     искомый текст
 * @param string $context  контекст
 *
 * @return string
 *
 * @since 2.17
 */
function i18n($text, $context = null)
{
	return Eresus_i18n::getInstance()->get($text, $context);
}
//-----------------------------------------------------------------------------
