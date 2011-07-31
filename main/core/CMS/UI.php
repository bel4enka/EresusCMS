<?php
/**
 * ${product.title}
 *
 * Абстрактный интерфейс CMS
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
 * Абстрактный интерфейс CMS
 *
 * @package Eresus
 * @since 2.16
 */
abstract class Eresus_CMS_UI
{
	/**
	 * Экземпляр-одиночка
	 *
	 * @var Eresus_CMS_UI
	 */
	private static $instance;

	/**
	 * Создаваемый документ
	 *
	 * @var Eresus_HTML_Document
	 * @since 2.16
	 */
	protected $document;

	/**
	 * Возвращает, при необходимости создавая, объект интерфейса
	 *
	 * @param string $className имя класса, потомка Eresus_CMS_UI
	 *
	 * @throws LogicException  если аргумент $className опущен и экземпляр Eresus_CMS_UI не был создан
	 *                         ранее или если $className не потомок Eresus_CMS_UI
	 * @return Eresus_CMS_UI
	 *
	 * @since 2.16
	 */
	public static function getInstance($className = null)
	{
		if (!self::$instance)
		{
			if (!$className)
			{
				throw new LogicException('No instance of Eresus_CMS_UI exists and no class name given');
			}
			$instance = new $className;
			if (!($instance instanceof self))
			{
				throw new LogicException('Given class is not a descendant of Eresus_CMS_UI');
			}
			self::$instance = $instance;
		}
		return self::$instance;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Конструктор
	 *
	 * @return Eresus_CMS_UI
	 *
	 * @uses Eresus_HTML_Document
	 * @since 2.16
	 */
	public function __construct()
	{
		$this->document = new Eresus_HTML_Document();
		Eresus_Template::setGlobalValue('document', $this->document);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обрабатывает запрос и возвращает ответ
	 *
	 * @return Eresus_CMS_Response
	 *
	 * @since 2.16
	 */
	abstract public function process();
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает создаваемый документ
	 *
	 * @return Eresus_HTML_Document
	 *
	 * @since 2.16
	 */
	public function getDocument()
	{
		return $this->document;
	}
	//-----------------------------------------------------------------------------
}
