<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Служба роутинга АИ
 *
 * @copyright 2010, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mk@procreat.ru>
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
 * @package EresusCMS
 *
 * $Id$
 */

/**
 * Служба роутинга АИ
 *
 * Служба производит разбор URL и позволяет загружать и выполнять запрошенные модули и методы.
 *
 * <b>Формат URL</b>
 * /admin/[модуль/[метод/]][ключ1/значение1/[...]]
 *
 * здесь:
 *
 * - "модуль" - имя модуля. Модули располагаются в admin/modules/имя_модуля
 * - "метод" - метод модуля. В классе модуля соответствующий метод должен иметь префикс "action"
 * - "ключ/значение" - произвольное количество дополнительных параметров
 *
 * @package EresusCMS
 * @since 2.16
 */
class AdminRouteService implements ServiceInterface
{
	/**
	 * Экземпляр-одиночка
	 *
	 * @var AdminRouteService
	 */
	private static $instance = null;

	/**
	 * HTTP запрос
	 *
	 * @var HttpRequest
	 */
	private $request;

	/**
	 * Имя модуля
	 *
	 * @var string
	 */
	private $moduleName = '';

	/**
	 * Объект модуля
	 *
	 * @var AdminModule
	 */
	private $module = null;

	/**
	 * Имя метода (действия)
	 *
	 * @var string
	 */
	private $actionName = 'index';

	/**
	 * Параметры
	 *
	 * @var array
	 */
	private $params = array();

	/**
	 * Возвращает экземпляр класса
	 *
	 * @return AdminRouteService
	 *
	 * @since 2.16
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new self();
		}
		return self::$instance;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Инициализация службы
	 *
	 * Метод должен вызываться после определения корня сайта и вызова HttpRequest::setLocalRoot
	 *
	 * @param HttpRequest $request
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function init(HttpRequest $request)
	{
		$this->moduleName = '';
		$this->module = null;
		$this->actionName = '';
		$this->params = array();

		$parts = explode('/', $request->getLocal());

		// Отбрасываем первый элемент, потому что он всегда пустой
		array_shift($parts);
		// Отбрасываем второй элемент, потому что он всегда "admin"
		array_shift($parts);

		// Отбрасываем аргументы "?..."
		if (substr(end($parts), 0, 1) == '?')
		{
			array_pop($parts);
		}

		// Имя модуля
		$part = array_shift($parts);
		if (!$part)
		{
			return;
		}
		$this->moduleName = $part;

		// Имя метода (действия)
		$part = array_shift($parts);
		if (!$part)
		{
			return;
		}
		$this->actionName = $part;

		for ($i = 0; $i < count($parts); $i += 2)
		{
			$key = $parts[$i];
			$value = isset($parts[$i+1]) ? $parts[$i+1] : null;
			$this->params[$key] = $value;
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Вызывает запрошенный модуль и возвращает результат
	 *
	 * @return string|false  HTML
	 *
	 * @since 2.16
	 */
	public function call()
	{
		$action = $this->getAction();
		if (is_null($action))
		{
			return false;
		}

		return call_user_func($action, $this->params);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает объект модуля
	 *
	 * Метод ищет модуль {@see $moduleName}, загружает и возвращает экземпляр класса модуля.
	 *
	 * @throws PageNotFoundException  если нет файла модуля
	 * @throws LogicException  если нет класса модуля или этот класс не ялвяется потомком AdminModule
	 *
	 * @return AdminModule|null
	 *
	 * @since 2.16
	 */
	public function getModule()
	{
		if ($this->module)
		{
			return $this->module;
		}

		if (empty($this->moduleName))
		{
			return null;
		}
		else
		{
			/* Ищем файл модуля */
			$path = Core::app()->getFsRoot() . '/admin/modules/' . $this->moduleName . '.php';
			if (!is_file($path))
			{
				EresusLogger::log(__METHOD__, LOG_WARNING, 'File "%s" not found', $path);
				throw new PageNotFoundException;
			}
			include $path;

			/* Ищем класс модуля */
			$className = $this->moduleName . 'Module';
			if (!class_exists($className, false))
			{
				throw new LogicException(sprintf('Class "%s" not found in "%s"', $className, $path));
			}

			$module = new $className();
			if (!($module instanceof AdminModule))
			{
				throw new LogicException(sprintf('Class "%s" not a descendant of AdminModule', $className));
			}
		}

		$this->module = $module;
		return $module;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает callback для запрошенного метода
	 *
	 * @throws PageNotFoundException  если запрошенного метода нет в модуле
	 *
	 * @return callback
	 *
	 * @since 2.16
	 */
	public function getAction()
	{
		$module = $this->getModule();
		if (is_null($module))
		{
			return null;
		}

		if (empty($this->actionName))
		{
			$action = 'actionIndex';
		}
		else
		{
			$action = 'action' . $this->actionName;
		}

		if (!method_exists($module, $action))
		{
			EresusLogger::log(__METHOD__, LOG_WARNING, 'Method "%s" not found in "%s"', $action,
				get_class($module));
			throw new PageNotFoundException;
		}

		$callback = array($module, $action);

		return $callback;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Скрываем конструктор
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	private function __construct()
	// @codeCoverageIgnoreStart
	{
	}
	// @codeCoverageIgnoreEnd
	//-----------------------------------------------------------------------------

	/**
	 * Блокируем клонирование
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	private function __clone()
	// @codeCoverageIgnoreStart
	{
	}
	// @codeCoverageIgnoreEnd
	//-----------------------------------------------------------------------------
}
