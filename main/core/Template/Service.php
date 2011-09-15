<?php
/**
 * ${product.title}
 *
 * Служба по работе с шаблонами
 *
 * @version ${product.version}
 * @copyright ${product.copyright}
 * @license ${license.uri} ${license.name}
 * @author Михаил Красиьников <mihalych@vsepofigu.ru>
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
 * Служба по работе с шаблонами
 *
 * @package Eresus
 * @since 2.16
 */
class Eresus_Template_Service
{
	/**
	 * Экземпляр-одиночка
	 *
	 * @var Eresus_Template_Service
	 */
	private static $instance = null;

	/**
	 * Возвращает экземпляр класса
	 *
	 * @return Eresus_Template_Service
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
	 * Возвращает объект шаблона
	 *
	 * В качестве значения $module надо указать или имя модуля расширения, для получения его шаблона,
	 * либо одно из ключевых слов:
	 *
	 * - core — встроенные шаблоны CMS
	 *
	 * @param string $name    имя файла шаблона без расширения
	 * @param string $module  имя модуля расширения или ключевое слово
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
