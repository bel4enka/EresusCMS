<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Главный модуль
 *
 * @copyright 2004, ProCreat Systems, http://procreat.ru/
 * @copyright 2007, Eresus Project, http://eresus.ru/
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
 * $Id: main.php 1030 2010-08-23 20:18:15Z mk $
 */

/**
 * Класс информации о плагине
 *
 * @package EresusCMS
 */
class PluginInfo extends Doctrine_Record
{
	public function setTableDefinition()
	{
		$this->setTableName('plugins');
		$this->hasColumn('name', 'string', 32, array('primary' => true));
		$this->hasColumn('active', 'boolean');
		$this->hasColumn('content', 'boolean');
		$this->hasColumn('settings', 'clob');
		$this->hasColumn('title', 'string', 64);
		$this->hasColumn('version', 'string', 16);
		$this->hasColumn('description', 'string', 255);
	}
	//-----------------------------------------------------------------------------
}
