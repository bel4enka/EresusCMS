<?php
/**
 * ${product.title} ${product.version}
 *
 * Файловый менеджер
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
 * @package EresusCMS
 * @subpackage UI
 *
 * $Id$
 */


/**
 * Файловый менеджер
 *
 * @package EresusCMS
 * @subpackage UI
 */
class EresusFileManager
{
	/**
	 * Объект пользвоательского интерфейса
	 *
	 * @var AdminUI
	 */
	private $ui;

	/**
	 * Корневая директория менеджера
	 *
	 * Задаётся относительно корня сайта
	 *
	 * @var string
	 */
	protected $rootFolder;

	/**
	 * Конструктор
	 *
	 * @param AdminUI $ui          объект пользовательского интерфейса
	 * @param string  $rootFolder  корневая директория менеджера (относительно корня сайта)
	 *
	 * @return EresusFileManager
	 */
	public function __construct(AdminUI $ui, $rootFolder = '/data')
	{
		$this->ui = $ui;
		$this->rootFolder = $rootFolder;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает разметку файлового менеджера
	 *
	 * @return string  HTML
	 */
	public function render()
	{
		$connector = new elFinderConnector();
		$html = $connector->getDataBrowser();
		return $html;
	}
	//-----------------------------------------------------------------------------
}


