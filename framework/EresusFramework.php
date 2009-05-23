<?php
/**
 * Eresus Framework
 *
 * @version 0.0.1
 *
 * Init module
 *
 * @copyright 2007-2008, Eresus Project, http://eresus.ru/
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
 * @author  Mikhail Krasilnikov <mk@procreat.ru>
 *
 * $Id$
 */

/**
 * Path to Core
 */
if ( !defined('ERESUS_CORE_ROOT') ) define('ERESUS_CORE_ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'core');
set_include_path(get_include_path() . PATH_SEPARATOR . ERESUS_CORE_ROOT);

/**
 * Init Core
 */
include_once 'EresusCore.php';

EresusClassAutoloader::add('framework.autoload');
