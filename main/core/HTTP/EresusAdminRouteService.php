<?php
/**
 * ${product.title} ${product.version}
 *
 * Служба роутинга АИ
 *
 * @copyright 2010, Eresus Project, http://eresus.ru/
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
 * @package EresusCMS
 *
 * $Id$
 */

/**
 * Служба роутинга АИ
 *
 * Служба производит разбор URL и позволяет загружать и выполнять запрошенные контроллеры и методы.
 *
 * <b>Формат URL</b>
 * /admin/[контроллер/[метод/]][ключ1/значение1/[...]]
 *
 * здесь:
 *
 * - "контроллер" - имя контроллера. Контроллеры располагаются в admin/controllers/имя_контроллера
 * - "метод" - метод. В классе контроллера соответствующий метод должен иметь префикс "action"
 * - "ключ/значение" - произвольное количество дополнительных параметров
 *
 * @package EresusCMS
 * @since 2.16
 */
class EresusAdminRouteService implements ServiceInterface
{
	/**
	 * Экземпляр-одиночка
	 *
	 * @var EresusAdminRouteService
	 */
	private static $instance = null;

	/**
	 * HTTP запрос
	 *
	 * @var HttpRequest
	 */
	private $request;

	/**
	 * Имя контроллера
	 *
	 * @var string
	 */
	private $controllerName = '';

	/**
	 * Объект контероллера
	 *
	 * @var EresusAdminController
	 */
	private $controller = null;

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
	 * @return EresusAdminRouteService
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
		$this->controllerName = '';
		$this->controller = null;
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

		// Имя контроллера
		$part = array_shift($parts);
		if (!$part)
		{
			return;
		}
		$this->controllerName = $part;

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
	 * Вызывает запрошенный контроллер и возвращает результат
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
	 * Возвращает объект контроллера
	 *
	 * Метод ищет модуль {@see $controllerName}, загружает и возвращает экземпляр класса контроллера.
	 *
	 * @throws PageNotFoundException  если нет файла контроллера
	 * @throws LogicException  если нет класса контроллера или этот класс не ялвяется потомком EresusAdminController
	 *
	 * @return EresusAdminController|null
	 *
	 * @since 2.16
	 */
	public function getController()
	{
		if ($this->controller)
		{
			return $this->controller;
		}

		if (empty($this->controllerName))
		{
			return null;
		}
		else
		{
			/* Ищем файл контроллера */
			$path = Core::app()->getFsRoot() . '/admin/controllers/' . $this->controllerName . '.php';
			if (!is_file($path))
			{
				EresusLogger::log(__METHOD__, LOG_WARNING, 'File "%s" not found', $path);
				throw new PageNotFoundException;
			}
			include $path;

			/* Ищем класс контроллера */
			$className = 'Eresus' . $this->controllerName . 'Controller';
			if (!class_exists($className, false))
			{
				throw new LogicException(sprintf('Class "%s" not found in "%s"', $className, $path));
			}

			$controller = new $className();
			if (!($controller instanceof EresusAdminController))
			{
				throw new LogicException(sprintf('Class "%s" not a descendant of EresusAdminController',
					$className));
			}
		}

		$this->controller = $controller;
		return $controller;
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
		$controller = $this->getController();
		if (is_null($controller))
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

		if (!method_exists($controller, $action))
		{
			EresusLogger::log(__METHOD__, LOG_WARNING, 'Method "%s" not found in "%s"', $action,
				get_class($controller));
			throw new PageNotFoundException;
		}

		$callback = array($controller, $action);

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
	// @codeCoverageIgnoreStart
	private function __construct()
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
	// @codeCoverageIgnoreStart
	private function __clone()
	{
	}
	// @codeCoverageIgnoreEnd
	//-----------------------------------------------------------------------------
}
