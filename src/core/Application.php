<?php
/**
 * ${product.title}
 *
 * Абстрактное приложение
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
 * Абстрактное приложение
 *
 * @package Eresus
 * @since 2.17
 */
abstract class Eresus_Application
{
	/**
	 * Версия CMS
	 *
	 * @var string
	 * @see getVersion()
	 * @since 2.17
	 */
	private $version = '${product.version}';

	/**
	 * Корневая директория приложения
	 *
	 * @var string
	 * @see getRootDir()
	 * @since 2.17
	 */
	private $rootDir;

	/**
	 * Возвращает версию приложения
	 *
	 * @return string
	 * @uses $version
	 */
	public function getVersion()
	{
		return $this->version;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает корневую директорию приложения
	 *
	 * Полный файловый путь к директории приложения без финального слеша.
	 *
	 * @return string  корневая директория приложения
	 *
	 * @since 2.17
	 */
	public function getRootDir()
	{
		if (!$this->rootDir)
		{
			$this->rootDir = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..');
			if (DIRECTORY_SEPARATOR != '/')
			{
				$this->rootDir = str_replace($this->rootDir, DIRECTORY_SEPARATOR, '/');
			}
		}
		return $this->rootDir;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Основной метод приложения
	 *
	 * @return int  Код завершения для консольных вызовов
	 */
	abstract public function main();
	//-----------------------------------------------------------------------------

	/**
	 * Читает настройки
	 *
	 * @throws DomainException  если файл настроек содержит ошибки
	 */
	protected function initConf()
	{
		$filename = $this->getRootDir() . '/cfg/main.php';
		if (file_exists($filename))
		{
			$config = file_get_contents($filename);
			if (substr($config, 0, 5) == '<?php')
			{
				$config = substr($config, 5);
			}
			elseif (substr($config, 0, 2) == '<?')
			{
				$config = substr($config, 2);
			}
			$result = @eval($config);
			if ($result === false)
			{
				throw new DomainException('Error parsing cfg/main.php');
			}
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Включает, если надо, средства отладки
	 *
	 * @return void
	 *
	 * @since 2.17
	 */
	protected function initDebugTools()
	{
		if (Eresus_Config::get('eresus.cms.debug'))
		{
			//TODO
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Настраивает часовой пояс
	 *
	 * @return void
	 *
	 * @since 2.17
	 */
	protected function initTimezone()
	{
		if (Eresus_Config::get('eresus.cms.timezone'))
		{
			date_default_timezone_set(Eresus_Config::get('eresus.cms.timezone'));
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Инициализирует локаль
	 *
	 * @return void
	 *
	 * @since 2.17
	 */
	protected function initLocale()
	{
		Eresus_Kernel::sc()->setService('i18n', new Eresus_i18n($this->getRootDir() . '/lang'));

		if ($locale = Eresus_Config::get('eresus.cms.locale.default'))
		{
			Eresus_Kernel::sc()->i18n->setLocale($locale);
			setlocale(LC_ALL, $locale);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Инициализирует шаблонизатор
	 *
	 * @return void
	 *
	 * @since 2.17
	 */
	protected function initTemplateEngine()
	{
		Eresus_Config::set('dwoo.templateDir', $this->getRootDir());
		Eresus_Config::set('dwoo.compileDir', $this->getRootDir() . '/var/cache/templates');
		Eresus_Template::setGlobalValue('container', Eresus_Kernel::sc());
	}
	//-----------------------------------------------------------------------------

	/**
	 * Инициализация БД
	 *
	 * Настраивает "ленивое" соединение с БД.
	 *
	 * @throws DomainException если в настройках не указан параметр "eresus.cms.dsn"
	 *
	 * @return void
	 *
	 * @uses Eresus_Config::get()
	 * @uses Doctrine::autoload()
	 * @uses Doctrine_Core::modelsAutoload()
	 * @uses Doctrine_Manager::connection()
	 * @uses Doctrine_Manager::getInstance()
	 * @uses Doctrine_Manager::setAttribute()
	 * @uses Doctrine_Core::loadModels()
	 */
	protected function initDB()
	{
		/**
		 * Подключение Doctrine
		 */
		include $this->getRootDir() . '/core/Doctrine.php';
		spl_autoload_register(array('Doctrine', 'autoload'));
		spl_autoload_register(array('Doctrine_Core', 'modelsAutoload'));

		$dsn = Eresus_Config::get('eresus.cms.dsn');
		if (!$dsn)
		{
			throw new DomainException(i18n('Не установлен параметр настройки "eresus.cms.dsn"'));
		}

		Doctrine_Manager::connection($dsn);

		$manager = Doctrine_Manager::getInstance();
		$manager->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, true);
		$manager->setAttribute(Doctrine_Core::ATTR_VALIDATE, Doctrine_Core::VALIDATE_ALL);

		$prefix = Eresus_Config::get('eresus.cms.dsn.prefix');
		if ($prefix)
		{
			$manager->setAttribute(Doctrine_Core::ATTR_TBLNAME_FORMAT, $prefix . '%s');
		}

		Doctrine_Core::loadModels(dirname(__FILE__) . '/Entity');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Инициализирует работу с плагинами
	 *
	 * @return void
	 *
	 * @since 2.17
	 */
	protected function initPlugins()
	{
		$plugins = new Eresus_Plugins;
		$plugins->init();
		// Обратная совместимость. FIXME Удалить
		if (isset($GLOBALS['Eresus']))
		{
			$GLOBALS['Eresus']->plugins = $plugins;
		}
		Eresus_Kernel::sc()->setService('plugins', $plugins);
	}
	//-----------------------------------------------------------------------------

}
