<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Интерфейс к веб-серверу
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
 * @package AbstractionLayers
 *
 * $Id$
 */

/**
 * Интерфейс к веб-серверу
 *
 * Этот класс - кандидат на перенос в Eresus Core
 *
 * @package AbstractionLayers
 * @since 2.15
 */
class Eresus_WebServer
{
	/**
	 * Экземпляр-одиночка
	 *
	 * @var WebServer
	 * @since 2.15
	 */
	private static $instance;

	/**
	 * Корневая директория веб-сервера (виртуального хоста)
	 *
	 * @var string
	 * @since 2.15
	 */
	private $documentRoot;

	/**
	 * Путь от домена до корня сайта
	 *
	 * @var string
	 * @since 2.16
	 */
	private $prefix;

	/**
	 * Возвращает экземпляр класса
	 *
	 * @return Eresus_WebServer
	 *
	 * @since 2.15
	 */
	public static function getInstance()
	{
		if (!self::$instance)
		{
			self::$instance = new self();
		}
		return self::$instance;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает корневую директорию веб-сервера
	 *
	 * @return string
	 *
	 * @since 2.15
	 */
	public function getDocumentRoot()
	{
		return $this->documentRoot;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает путь от доменного имени до корня сайта
	 *
	 * @return string
	 *
	 * @since 2.15
	 */
	public function getPrefix()
	{
		if (!$this->prefix)
		{
			$DOCUMENT_ROOT = $this->getDocumentRoot();
			$this->prefix = Eresus_Kernel::app()->getRootDir();
			$this->prefix = substr($this->prefix, strlen($DOCUMENT_ROOT));
		}
		return $this->prefix;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает заголовки запроса
	 *
	 * @return array
	 *
	 * @since 2.16
	 */
	public function getRequestHeaders()
	{
		return Eresus_Kernel::isModule() ? apache_request_headers() : array();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Конструктор
	 *
	 * @return Eresus_WebServer
	 *
	 * @since 2.15
	 */
	private function __construct()
	{
		$path = realpath($_SERVER['DOCUMENT_ROOT']);
		if (DIRECTORY_SEPARATOR != '/')
		{
			$path = str_replace($path, DIRECTORY_SEPARATOR, '/');
		}
		$this->documentRoot = $path;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Запрещаем клонирование
	 *
	 * @return void
	 *
	 * @since 2.15
	 */
	private function __clone()
	{
		// @codeCoverageIgnoreStart
	}
	// @codeCoverageIgnoreEnd
	//-----------------------------------------------------------------------------
}
