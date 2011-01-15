<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Таблица автозагрузки классов
 *
 * @copyright 2009, Eresus Project, http://eresus.ru/
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

return array(

	/* Domain */
	'Plugins' => 'core/classes/Plugins.php',

	'AdminModule' => 'core/classes/AdminModule.php',
	'EresusExtensionConnector' => 'core/classes/EresusExtensionConnector.php',
	'EresusForm' => 'core/EresusForm.php',
	'I18n' => 'core/i18n.php',
	'PaginationHelper' => 'core/classes/helpers/PaginationHelper.php',
	'WebServer' => 'core/classes/WebServer.php',
	'WebPage' => 'core/classes/WebPage.php',

	/* Службы */
	'AdminRouteService' => 'core/classes/AdminRouteService.php',

	/* AccessControl */
	'EresusAuthService' => 'core/AccessControl/EresusAuthService.php',

	/* DBAL */
	'EresusActiveRecord' => 'core/DBAL/EresusActiveRecord.php',
	'EresusORM' => 'core/DBAL/EresusORM.php',
	'EresusQuery' => 'core/DBAL/EresusQuery.php',

	/* Helpers */
	'EresusCollection' => 'core/Helpers/EresusCollection.php',

	/* Сторонние компоненты */
	'elFinderConnector' => 'ext-3rd/elfinder/eresus-connector.php',
	'elFinder' => 'ext-3rd/elfinder/connectors/php/elFinder.class.php',

	/* Обратная совместимость */
	'TPlugin' => 'core/classes/backward/TPlugin.php',
	'TContentPlugin' => 'core/classes/backward/TContentPlugin.php',
	'TListContentPlugin' => 'core/classes/backward/TListContentPlugin.php',
);
