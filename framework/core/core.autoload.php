<?php
/**
 * Eresus Core
 *
 * @version 0.1.3
 *
 * Class autoloading table
 *
 * @copyright 2007-2009, Eresus Project, http://eresus.ru/
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
 * @package Kernel
 * @internal
 * @ignore
 *
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 *
 * $Id$
 */

return array(

	/* Console */

	/* DB */
	'DB' => 'DB/DB.php',
	'DBSettings' => 'DB/DB.php',
	'DBRuntimeException' => 'DB/DB.php',
	'DBQueryException' => 'DB/DBQueryException.php',

	/* File */
	'WindowsFS' => 'File/FS/WindowsFS.php',

	/* Misc */
	'Registry' => 'Misc/Registry.php',

	/* Template */
	'Template' => 'Template/Template.php',
	'TemplateFile' => 'Template/Template.php',

	/* WWW */
	'HTTP' => 'WWW/HTTP.php',
	'HttpRequest' => 'WWW/HTTP.php',
	'HttpResponse' => 'WWW/HttpResponse.php',
	'HttpHeaders' => 'WWW/HttpHeaders.php',
	'HttpHeader' => 'WWW/HttpHeaders.php',

	/* Applications */
	'EresusApplication' => 'EresusApplication.php',

);