<?php
/**
 * ${product.title}
 *
 * Обрабатываемый запрос
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
 * Обрабатываемый запрос
 *
 * Является обёрткой для {@link Eresus_HTTP_Request} и добавляет к нему логику предметной области.
 *
 * @package Eresus
 * @since 2.16
 */
class Eresus_CMS_Request
{
	/**
	 * Сообщение HTTP
	 *
	 * @var Eresus_HTTP_Request
	 */
	protected $request;

	/**
	 * Префикс корневого URL относительно корня домена
	 *
	 * @var string
	 * @since 2.16
	 */
	protected $rootPrefix;

	/**
	 * Корневой URL
	 *
	 * @var string
	 */
	protected $rootURL;

	/**
	 * Очередь папок-параметров
	 *
	 * @var array|null
	 */
	protected $params = null;

	/**
	 * Экземпляр-одиночка
	 *
	 * @var Eresus_CMS_Request
	 */
	private static $instance;

	/**
	 * Возвращает экземпляр-одиночку запроса
	 *
	 * @return Eresus_CMS_Request
	 *
	 * @uses Eresus_HTTP_Request::fromEnv()
	 * @uses Eresus_WebServer::getInstance()
	 * @uses Eresus_WebServer::getDocumentRoot()
	 * @uses Eresus_Kernel::app()
	 * @uses Eresus_CMS::getRootDir()
	 * @since 2.16
	 */
	public static function getInstance()
	{
		if (!self::$instance)
		{
			$req = Eresus_HTTP_Request::fromEnv();

			$docRoot = Eresus_WebServer::getInstance()->getDocumentRoot();
			$prefix = Eresus_Kernel::app()->getRootDir();
			$prefix = substr($prefix, strlen($docRoot));

			self::$instance = new Eresus_CMS_Request($req, $prefix);
		}
		return self::$instance;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Создаёт запрос на основе окружения приложения
	 *
	 * @param Eresus_HTTP_Message $message  запрос HTTP
	 * @param string              $prefix   префикс корневого URL относительно корня домена (должен
	 *                                      начинаться со слеша и не иметь слеша на конце)
	 *
	 * @return Eresus_CMS_Request
	 *
	 * @since 2.16
	 * @uses Eresus_URI
	 */
	public function __construct(Eresus_HTTP_Request $message, $prefix)
	{
		$this->request = $message;
		$this->rootPrefix = $prefix;
		$this->rootURL = new Eresus_URI($this->request->getUri());
		$this->rootURL->setPath($this->rootPrefix);
		$this->rootURL->setQuery(null);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проксирует запрос к объекту Eresus_HTTP_Message
	 *
	 * @param string $method
	 * @param array  $args
	 *
	 * @return mixed
	 *
	 * @since 2.16
	 */
	public function __call($method, $args)
	{
		if (!method_exists($this->request, $method))
		{
			throw new BadMethodCallException('Call of unknown method ' . get_class($this) . '::' .
				$method);
		}
		return call_user_func_array(array($this->request, $method), $args);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает объект Eresus_HTTP_Message
	 *
	 * @return Eresus_HTTP_Message
	 *
	 * @since 2.16
	 */
	public function getHttpMessage()
	{
		return $this->request;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяет, является ли запрос запросом POST
	 *
	 * @return bool
	 *
	 * @since 2.16
	 */
	public function isGET()
	{
		return $this->getHttpMessage()->getMethod() == 'GET';
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяет, является ли запрос запросом POST
	 *
	 * @return bool
	 *
	 * @since 2.16
	 */
	public function isPOST()
	{
		return $this->getHttpMessage()->getMethod() == 'POST';
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает запрошенный хост
	 *
	 * Значение может быть переопределено через параметр «eresus.cms.http.host» (см. {@link
	 * Eresus_Config}.
	 *
	 * @return string
	 *
	 * @link http://api.symfony.com/2.0/Symfony/Component/HttpFoundation/Request.html#getHost()
	 * @since 2.16
	 */
	public function getHost()
	{
		return Eresus_Config::get('eresus.cms.http.host', $this->request->getHost());
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает корневой префикс
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	public function getRootPrefix()
	{
		return $this->rootPrefix;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает корневой URL
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	public function getRootURL()
	{
		return $this->rootURL;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает часть запроса, соответствующую пути от корня сайта
	 *
	 * Возвращает часть запроса, соответствующую пути от корня сайта. Всегда начинается с "/".
	 *
	 * @return string
	 *
	 * @link http://api.symfony.com/2.0/Symfony/Component/HttpFoundation/Request.html#getPathInfo()
	 * @see getBasePath()
	 * @since 2.16
	 */
	public function getPathInfo()
	{
		$path = $this->request->getPath();
		if ($this->rootPrefix)
		{
			$path = substr($path, strlen($this->rootPrefix));
		}
		return $path;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает путь к текущей виртуальной директории относительно корня сайта
	 *
	 * Примеры:
	 *
	 * - Для "….org/" будет ""
	 * - Для "….org/dir/" будет "/dir"
	 * - Для "….org/dir/file.ext" будет "/dir"
	 *
	 * @return string
	 *
	 * @link http://api.symfony.com/2.0/Symfony/Component/HttpFoundation/Request.html#getBasePath()
	 * @see getPathInfo()
	 * @since 2.16
	 */
	public function getBasePath()
	{
		$path = $this->getPathInfo();
		if (substr($path, -1) == '/')
		{
			$path = substr($path, 0, -1);
		}
		else
		{
			$path = dirname($path);
			if ($path == '/')
			{
				$path = '';
			}
		}
		return $path;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает текущий параметр из запроса
	 *
	 * Eresus_CMS_Request рассматривает последовательность папок в запросе как очередь параметров.
	 * Механизм работы с ними, похож на механизм работы с массивами при помощи reset(), next(),
	 * current() и т. д.
	 *
	 * Этот метод ялвяется аналогом {@link current() current()} и возвращает текущий параметр в
	 * очереди.
	 *
	 * @return string|false
	 *
	 * @see getNextParam()
	 * @since 2.16
	 */
	public function getParam()
	{
		if (is_null($this->params))
		{
			$this->splitParams();
		}
		return current($this->params);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает следующий параметр из запроса
	 *
	 * Этот метод ялвяется аналогом {@link next() next()} и возвращает следующий параметр в очереди.
	 *
	 * @return string|false
	 *
	 * @see getParam()
	 * @since 2.16
	 */
	public function getNextParam()
	{
		if (is_null($this->params))
		{
			$this->splitParams();
		}
		return next($this->params);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Разбивает папки на массив параметров
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	protected function splitParams()
	{
		$this->params = explode('/', $this->getBasePath());
		// Т. к. basePath начинается со слеша, первый элемент массива всегда пустой. Удаляем его.
		array_shift($this->params);
		reset($this->params);
	}
	//-----------------------------------------------------------------------------
}
