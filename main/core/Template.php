<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Шаблон
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
 * @package Template
 *
 * $Id$
 */



if (!class_exists('Dwoo', false))
{
	include dirname(__FILE__) . '/Dwoo/dwooAutoload.php';
}



/**
 * Шаблон
 *
 * <b>Настройка</b>
 * Templte использует {@link Eresus_Config::get()} для чтения конфигурации:
 *
 * <b>core.template.templateDir</b>
 * Корневая директория шаблонов
 *
 * <b>core.template.compileDir</b>
 * Директория для размещения скомпилированных шаблонов
 *
 * <b>core.template.charset</b>
 * Кодировка шаблонов
 *
 * @package Template
 */
class Eresus_Template
{
	/**
	 * Объект Dwoo
	 * @var Dwoo
	 */
	protected $dwoo;

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
	 * @return Eresus_Template
	 *
	 * @since 2.16
	 */
	public static function fromFile($filename)
	{
		$tmpl = new self();
		$tmpl->loadFromFile($filename);
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
		$compileDir = $this->detectCompileDir();
		$this->dwoo = new Dwoo($compileDir);

		if (Eresus_Config::get('core.template.charset'))
		{
			$this->dwoo->setCharset(Eresus_Config::get('core.template.charset'));
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
			$result = $this->dwoo->get($this->file, $data);
		}
		catch (Exception $e)
		{
			Eresus_Logger::exception($e);
			$result = '';
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
		$templateDir = Eresus_Config::get('core.template.templateDir', '');

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
		$compileDir = Eresus_Config::get('core.template.compileDir', '');

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
		if (!file_exists($fullname))
		{
			Eresus_Logger::log(__METHOD__, LOG_ERR, 'Template file "%s" not found', $fullname);
		}
		$this->file = new Dwoo_Template_File($fullname, null, $filename, $filename);
	}
	//-----------------------------------------------------------------------------

}
