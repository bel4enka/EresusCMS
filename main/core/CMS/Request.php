<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Обрабатываемый запрос
 *
 * @copyright 2011, Eresus Project, http://eresus.ru/
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
 * @package Core
 *
 * $Id$
 */

/**
 * Обрабатываемый запрос
 *
 * @package Core
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
		return $this->getHttpMessage()->getRequestMethod() == 'GET';
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
		return $this->getHttpMessage()->getRequestMethod() == 'POST';
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает запрошенный хост
	 *
	 * @return string
	 *
	 * @since 2.16
	 * @see http://api.symfony.com/2.0/Symfony/Component/HttpFoundation/Request.html#getHost()
	 */
	public function getHost()
	{
		return  Eresus_Config::get('eresus.cms.http.host', $this->request->getRequestHost());
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
	 * @since 2.16
	 * @see http://api.symfony.com/2.0/Symfony/Component/HttpFoundation/Request.html#getPathInfo()
	 * @see getBasePath()
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
	 * @since 2.16
	 * @see http://api.symfony.com/2.0/Symfony/Component/HttpFoundation/Request.html#getBasePath()
	 * @see getPathInfo()
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
	 * Этот метод ялвяется аналогом current() и возвращает текущий параметр в очереди.
	 *
	 * @return string
	 *
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
	 * Разбивает папки на массив параметров
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	protected function splitParams()
	{
		$this->params = explode('/', $this->getBasePath());
		// Т. к. basePath начинается со слеша, перый элемент массива всегда пустой. Удаляем его.
		array_shift($this->params);
		reset($this->params);
	}
	//-----------------------------------------------------------------------------
}
