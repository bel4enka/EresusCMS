<?php
/**
 * Eresus Framework
 *
 * @version 0.0.1
 *
 * Class autoloading table
 *
 * @copyright 2007-${year}, Eresus Project, http://eresus.ru/
 * @license http://www.gnu.org/licenses/gpl.txt GPL License 3
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Framework
 * @internal
 * @ignore
 *
 * @author  Mikhail Krasilnikov <mk@procreat.ru>
 *
 * $Id$
 */

return array(

	'MvcApplication' => 'MvcApplication',

	/* Models  */
	'MvcModel' => 'classes/models/MvcModel',
	'MvcListModel' => 'classes/models/MvcListModel',
	'GenericModel' => 'classes/models/GenericModel',
	'GenericListModel' => 'classes/models/GenericListModel',

	/* Views  */
	'MvcView' => 'classes/views/MvcView',
	'GenericView' => 'classes/views/GenericView',

	/* Controllers */
	'MvcController' => 'classes/controllers/MvcController',
	'GenericController' => 'classes/controllers/GenericController',
	'FrontController' => 'classes/controllers/FrontController',

	/* Routes */
	'Router' => 'classes/Routes',
	'Route' => 'classes/Routes',
	'RegExpRoute' => 'classes/Routes',

	'TableDataGateway' => 'classes/TableDataGateway',

);