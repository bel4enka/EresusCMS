<?php
/**
 * Eresus Framework
 *
 * @version 0.0.1
 *
 * MVC Application
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
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 *
 * $Id$
 */

/**
 * Abstract MVC application
 * @package Framework
 * @author  mekras
 */
abstract class MvcApplication extends EresusApplication {

	/**
	 * Class autoloading
	 * @param $className
	 */
	public function autoload($className)
	{

		$className = preg_replace('/^(.*(Model|View|Controller))$/', '$2s/$1', $className);

		try {

			$this->load('classes/'.$className);

		} catch (Exception $e) {}

	}
	//-----------------------------------------------------------------------------
}
