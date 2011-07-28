<?php
/**
 * ${product.title} ${product.version}
 *
 * Служба по работе с шаблонами
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
 * @package Service
 *
 * $Id$
 */

/**
 * Служба по работе с шаблонами
 *
 * @package Service
 * @since 2.16
 */
class Eresus_Service_Templates
{
	/**
	 * Экземпляр-одиночка
	 *
	 * @var Eresus_Service_Templates
	 */
	private static $instance = null;

	/**
	 * Реестр разделов
	 *
	 * Хранит объекты всех разделов сайта для ускорения работы с ними
	 *
	 * @var array
	 */
	private $registry = array();

	/**
	 * Индекс разделов
	 *
	 * Содержит ссылки на разделы по разным признакам
	 *
	 * @var array
	 */
	private $index = array();

	/**
	 * Возвращает экземпляр класса
	 *
	 * @return Eresus_Service_Templates
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
	 * Возвращает шаблон
	 *
	 * @param string $name    имя файла шаблона (см. $source) и без расширения
	 * @param string $module  модуль
	 *
	 * @return Eresus_Template
	 *
	 * @since 2.16
	 */
	public function get($name, $module = null)
	{
		switch ($module)
		{
			case null:
				$path = 'templates';
			break;

			case 'core':
				$path = 'core/templates';
			break;

			default:
				throw new LogicException('Not implemented');
		}
		$tmpl = Eresus_Template::fromFile($path . '/' . $name . '.html');
		return $tmpl;
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
