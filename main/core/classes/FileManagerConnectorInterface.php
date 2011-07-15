<?php
/**
 * {Описание}
 *
 * @version 1.00
 *
 * @copyright 2011, Михаил Красильников, <mihalych@vsepofigu.ru>
 * @copyright 2011, ООО «Два слона», http://dvaslona.ru/
 * @copyright 2011, Eresus Project, http://eresus.ru/
 * @license http://www.gnu.org/licenses/gpl.txt GPL License 3
 * @license Только для внутреннего использования
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
 * @package {пакет}
 * @author Михаил Красильников <mihalych@vsepofigu.ru>
 *
 * $Id$
 */

/**
* Интерфейс коннектора файлового менеджера
*
* @package CoreExtensionsAPI
* @since 2.16
*/
interface FileManagerConnectorInterface
{
	/**
	 * Метод должен возвращать разметку для файлового менеджера директории data.
	 *
	 * @return string HTML
	 *
	 * @since 2.16
	 */
	public function getDataBrowser();
	//-----------------------------------------------------------------------------
}



