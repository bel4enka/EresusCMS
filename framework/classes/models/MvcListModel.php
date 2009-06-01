<?php
/**
 * Eresus Framework
 *
 * @version 0.0.1
 *
 * MVC List Model
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
 * @subpackage Models
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 *
 * $Id: MvcListModel.php 175 2009-05-28 08:52:37Z mekras $
 */

/**
 * MVC Model List
 *
 * It is a meta model.
 *
 * @package Framework
 * @subpackage Models
 *
 * @author mekras
 *
 */
class MvcListModel implements Iterator {

	/**
	 * Specigy an empty list
	 * @var int
	 */
	const EMPTY_LIST = 0;

	/**
	 * Current item index
	 * @var int
	 */
	private $index = 0;

	/**
	 * List page size
	 * @var int
	 */
	protected $pageSize = 0;

	/**
	 * Page number
	 * @var int
	 */
	protected $pageIndex = 1;

	/**
	 * List size (total count)
	 */
	protected $size = null;

	/**
	 * Filter settings
	 * @var array
	 */
	protected $filter = array();

	/**
	 * List items
	 * @var array
	 */
	protected $items = null;

	/**
	 * Constructor
	 *
	 * Constructs model list.
	 *
	 * There are two ways to create list:
	 * 1. Reading from external data source
	 * 2. Specify elements manualy
	 *
	 * To go the first way just call constructor without arguments. After it
	 * you can user filtering to specify wich elements you whant to see in list.
	 *
	 * For the second way you must call constructor with one or more arguments,
	 * where each argument represents a raw element data.
	 *
	 * Special argument value AbstractListMode::EMPTY_LIST can be used to create
	 * list with no elements;
	 *
	 * @param array|int $item1  Element 1 raw data
	 * ...
	 * @param array     $itemN  Element N raw data
	 */
	public function __construct()
	{
		$args = func_get_args();

		if (count($args)) {

			if (reset($args) == self::EMPTY_LIST)
				$this->items = array();

			else
				$this->items = $args;
				$this->size = count($this->items);
		}
	}
	//-----------------------------------------------------------------------------

	/**
   * Returns current element
   * @return mixed
	 */
	public function current()
	{
		return $this->item($this->index);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Returns current index
	 * @return scalar
	 */
	public function key()
	{
		return $this->index;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Goes to next element
	 */
	public function next()
	{
		$this->index++;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Rewind internal pointer
	 */
	public function rewind()
	{
		$this->index = 0;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Check for element
	 * @return bool
	 */
	public function valid()
	{
		if ($this->index < 0) return false;
		if ($this->index >= $this->count()) return false;
		return true;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Load raw element data
	 *
	 * Model calls this method to reload raw data
	 *
	 * Descedants must override this method to read data from external source,
	 * put them to $this->items.
	 *
	 */
	protected function internalLoad() {}
	//-----------------------------------------------------------------------------

	/**
	 * Get real list size
	 */
	protected function internalSize() {}
	//-----------------------------------------------------------------------------

	/**
	 * Element factory
	 *
	 * @param array $raw  Raw model data
	 * @return AbstractModel
	 *
	 * @see AbstractListModel::item
	 */
	protected function itemFactory($raw)
	{
		$className = str_replace('List', '', get_class($this));
		return new $className($raw);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get property
	 *
	 * @param string $property  Property name
	 * @return mixed
	 */
	public function __get($property)
	{
		elog(array(get_class($this), __METHOD__), LOG_DEBUG, '%s::%s', get_class($this), $property);

		switch (true) {

			case $property == 'count': return $this->count();
			case $property == 'size': return $this->size();
			case substr($property, 0, 6) == 'filter':
				$getter = 'get' . $property;
				elog(array(get_class($this), __METHOD__), LOG_DEBUG, 'Calling getter: %s', $getter);
				return $this->$getter();
			break;

		}

		throw new EresusPropertyNotExistsException($property, get_class($this));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Set property
	 *
	 * @param string $property  Property name
	 * @param mixed  $value     Property value
	 */
	public function __set($property, $value)
	{
		switch (true) {

			case substr($property, 0, 6) == 'filter':
				$setter = 'set' . $property;
				$this->$setter($value);
			break;

		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Magic method for setting/getting list params
	 *
	 * getFilterXXX / setFilterXXX - Gets or sets filtering by property 'XXX'.
	 * Setting this value to null removes filtering by this property.
	 *
	 * @param string $name       Method name
	 * @param array  $arguments  Arguments
	 * @return mixed
	 */
	public function __call($name, $arguments)
	{
		elog(array(get_class($this), __METHOD__), LOG_DEBUG, '%s::%s()', get_class($this), $name);
		switch (true) {

			case strncasecmp($name, 'getfilter', 9) == 0:
				$property = substr($name, 9);
				$property{0} = strtolower($property{0});
				return isset($this->filter[$property]) ? $this->filter[$property] : null;
			break;

			case strncasecmp($name, 'setfilter', 9) == 0:
				$property = substr($name, 9);
				$property{0} = strtolower($property{0});
				$this->filter[$property] = reset($arguments);
			break;

			default: throw new EresusMethodNotExistsException($name, get_class($this));

		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Set page size
	 *
	 * @param int $value
	 */
	public function setPageSize($value)
	{
		$this->pageSize = $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Set page index
	 *
	 * @param int $value
	 */
	public function setPageIndex($value)
	{
		$this->pageIndex = $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get page size
	 *
	 * getPageSize() may be greater than count()
	 *
	 * @return int
	 */
	public function getPageSize()
	{
		return $this->pageSize;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get page index
	 *
	 * @return int
	 */
	public function getPageIndex()
	{
		return $this->pageIndex;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Count list size
	 * @return int
	 */
	public function size()
	{
		if (is_null($this->size)) $this->internalSize();
		return $this->size;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Count elements on current page
	 * @return int
	 */
	public function count()
	{
		if (is_null($this->items)) $this->internalLoad();
		return count($this->items);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get list element
	 *
	 * @param int $index  Element index
	 * @return AbstractModel
	 */
	public function item($index)
	{
		if (is_null($this->items)) $this->internalLoad();

		if (! isset($this->items[$index])) return null;

		return $this->itemFactory($this->items[$index]);
	}
	//-----------------------------------------------------------------------------

}
