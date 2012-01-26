<?php
/**
 * ${product.title}
 *
 * Шаблон
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



if (!class_exists('Dwoo', false))
{
	/**
	 * Подключаем механизм автозагрузки классов Dwoo.
	 */
	include dirname(__FILE__) . '/Dwoo/dwooAutoload.php';
}



/**
 * Шаблон
 *
 * <b>{@link http://wiki.dwoo.org/index.php/Main_Page Синтаксис шаблонов}</b>
 *
 * <b>Настройка</b>
 *
 * Templte использует {@link Eresus_Config::get()} для чтения конфигурации:
 *
 * - <b>dwoo.templateDir</b> — Корневая директория шаблонов
 * - <b>dwoo.compileDir</b> — Директория для размещения скомпилированных шаблонов
 *
 * @package Eresus
 * @since 2.17
 */
class Eresus_Template
{
	/**
	 * Объект Dwoo
	 * @var Dwoo
	 */
	protected static $dwoo;

	/**
	 * Файл шаблона
	 * @var Dwoo_Template_File
	 */
	protected $file;

	/**
	 * Глобальные переменные для подстановки во все шаблоны
	 *
	 * @var array
	 */
	private static $globals = array();

	/**
	 * Читает шаблон из файла
	 *
	 * @param string $filename
	 *
	 * @return Eresus_Template|null
	 *
	 * @since 2.17
	 */
	public static function fromFile($filename)
	{
		$tmpl = new self();
		$tmpl->loadFromFile($filename);
		if (!$tmpl->file)
		{
			return null;
		}
		return $tmpl;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает глобальную переменную для подстановки во все шаблоны
	 *
	 * @param string $name
	 * @param mixed  $value
	 */
	public static function setGlobalValue($name, $value)
	{
		self::$globals[$name] = $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает глобальную переменную
	 *
	 * @param string $name
	 * @return null|mixed
	 */
	public static function getGlobalValue($name)
	{
		return isset(self::$globals[$name]) ? self::$globals[$name] : null;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Удаляет глобальную переменную
	 *
	 * @param string $name
	 */
	public static function removeGlobalValue($name)
	{
		if (isset(self::$globals[$name]))
		{
			unset(self::$globals[$name]);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Конструктор
	 */
	public function __construct()
	{
		if (!self::$dwoo)
		{
			$compileDir = $this->detectCompileDir();
			self::$dwoo = new Dwoo($compileDir);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Компилирует шаблон
	 *
	 * @param array $data  данные для подстановки
	 *
	 * @return string
	 */
	public function compile($data = null)
	{
		if (!$this->file)
		{
			throw new InvalidArgumentException('No template file specified');
		}

		if (is_null($data))
		{
			$data = array();
		}
		$data['globals'] = self::$globals;

		try
		{
			$result = self::$dwoo->get($this->file, $data);
		}
		catch (Exception $e)
		{
			$status = ob_get_status();
			/*
			 * См. http://php.net/manual/function.ob-get-level.php#52945
			 */
			if (count($status) && $status['name'] != 'default output handler')
			{
				ob_end_clean();
			}
			Eresus_Logger::exception($e);
			$result = '[template error]';
		}

		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает корневую директорию шаблонов
	 *
	 * @return string
	 */
	protected function detectTemplateDir()
	{
		$templateDir = Eresus_Config::get('dwoo.templateDir', '');

		return $templateDir;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает директорию для размещения скомпилированных шаблонов
	 *
	 * @return string
	 */
	protected function detectCompileDir()
	{
		$compileDir = Eresus_Config::get('dwoo.compileDir', '');

		return $compileDir;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Загружает файл шаблона
	 *
	 * @param string $filename  имя файла шаблона
	 *
	 * @return void
	 */
	protected function loadFromFile($filename)
	{
		$templateDir = $this->detectTemplateDir();
		$fullname = $templateDir . '/' . $filename;
		if (file_exists($fullname))
		{
			$this->file = new Dwoo_Template_File($fullname, null, $filename, $filename);
		}
		else
		{
			$this->file = null;
			Eresus_Logger::log(__METHOD__, LOG_ERR, 'Template file "%s" not found', $fullname);
		}
	}
	//-----------------------------------------------------------------------------

}
