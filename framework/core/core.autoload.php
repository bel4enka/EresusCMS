<?php
/**
 * Eresus Core
 *
 * @version 0.1.0
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
 * @package Core
 * @internal
 * @ignore
 *
 * @author  Mikhail Krasilnikov <mk@procreat.ru>
 *
 * $Id$
 */

return array(

	/* Console */

	/* DB */
	'DB' => 'DB/DB',

	/* File */
	'WindowsFS' => 'File/FS/WindowsFS',

	/* Misc */
	'Registry' => 'Misc/Registry',

	/* Template */
	'Template' => 'Template/Template',
	'TemplateFile' => 'Template/Template',

	/* WWW */
	'HTTP' => 'WWW/HTTP',
	'HttpRequest' => 'WWW/HTTP',
	'HttpResponse' => 'WWW/HttpResponse',
	'HttpHeaders' => 'WWW/HttpHeaders',
	'HttpHeader' => 'WWW/HttpHeaders',

);