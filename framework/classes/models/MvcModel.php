<?php
/**
 * Eresus Framework
 *
 * @version 0.0.1
 *
 * MVC Model
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
 * $Id: MvcModel.php 153 2009-05-18 11:39:14Z mekras $
 */

/**
 * MVC Model
 *
 * Model is an abstraction layer between some data source and application.
 * So it must be created by application with data source specification.
 *
 * @package Framework
 * @subpackage Models
 *
 * @author mekras
 */
class MvcModel {

	/**
	 * Raw entity data
	 *
	 * @var array
	 */
	protected $raw;

	/**
	 * getters cache
	 *
	 * @var array
	 */
	protected $getCache = array();

	/**
	 * Indication of model changes
	 *
	 * @var bool
	 */
	protected $modified = false;

	/**
	 * Get value from $this->raw
	 *
	 * @param string $property  Property name
	 *
	 * @return mixed
	 */
	protected function getRaw($property)
	{
		elog(__METHOD__, LOG_DEBUG, '(%s)', $property);
		if (isset($this->raw[$property])) {
			$value = $this->raw[$property];
			elog(__METHOD__, LOG_DEBUG, 'return: %s', is_object($value) ? get_class($value) : $value);
			return $value;
		}

		throw new EresusRuntimeException("Property '$property' not set", 'Property not exists');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Set value in $this->raw
	 *
	 * @param string $property  Property name
	 * @param mixed  $value     New value
	 */
	protected function setRaw($property, $value)
	{
		if (is_null($this->raw)) $this->raw = array();
		elog(__METHOD__, LOG_DEBUG, '(%s, %s)', $property, is_object($value) ? get_class($value) : $value);
		$this->raw[$property] = $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get value from $this->getCache
	 *
	 * @param string $property  Property name
	 *
	 * @return mixed
	 */
	protected function getGetCache($property)
	{
		elog(__METHOD__, LOG_DEBUG, '(%s)', $property);
		if (isset($this->getCache[$property])) {
			$value = $this->getCache[$property];
			elog(__METHOD__, LOG_DEBUG, 'return: %s', is_object($value) ? get_class($value) : $value);
			return $value;
		}
		elog(__METHOD__, LOG_DEBUG, 'not cached');
		return null;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Set value in $this->getCache
	 *
	 * @param string $property  Property name
	 * @param mixed  $value     New value
	 */
	protected function setGetCache($property, $value)
	{
		if (is_null($this->getCache)) return;
		elog(__METHOD__, LOG_DEBUG, '(%s, %s)', $property, is_object($value) ? get_class($value) : $value);
		$this->getCache[$property] = $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Raw data update
	 *
	 * Model calls this method to reload raw data
	 *
	 * Descedants must override this method to read data from external source,
	 * put them to $this->raw and clear $this->modified flag.
	 *
	 */
	protected function internalRead() {}
	//-----------------------------------------------------------------------------

	/**
	 * Raw data save
	 *
	 * Model calls this method to store raw data
	 *
	 * Descedants must override this method to write data from $this->raw to
	 * external storage and clear $this->modified flag.
	 */
	protected function internalWrite() {}
	//-----------------------------------------------------------------------------

	/**
	 * Remove entity
	 *
	 * Model calls this method to remove entity from external source
	 *
	 * Descedants must override this method
	 */
	protected function internalDelete() {}
	//-----------------------------------------------------------------------------

	/**
	 * Get property
	 *
	 * Descedants can define two types of getter methods which will be called to
	 * determine a value of a requested property.
	 *
	 * 1. Basic getter (cacheable)
	 * Methods with name 'get' + [propertyName], e.g. to get property 'someProperty'
	 * method must have name 'getSomeProperty'. Result of call will be stored in
	 * $this->raw.
	 *
	 * 2. Virtual value (non-cachebale)
	 * Methods with name 'virtual' + [propertyName], e.g. 'virtualSomeProperty'
	 *
	 * @param string $property  Property name
	 * @return mixed
	 */
	public function __get($property)
	{
		elog(__METHOD__, LOG_DEBUG, '%s::%s', get_class($this), $property);

		/* 1. Try virtual getter */
		$getter = 'virtual' . $property;
		if (method_exists($this, $getter)) {
			$value = $this->$getter();
			elog(__METHOD__, LOG_DEBUG, 'Virtual: %s', is_object($value) ? get_class($value) : $value);
			return $value;
		}

		/* 2. Try getters cache */
		if (! is_null($value = $this->getGetCache($property))) {
			elog(__METHOD__, LOG_DEBUG, 'From getters cache: %s', is_object($value) ? get_class($value) : $value);
			return $value;
		}

		/* 3. Read from data source */
		if (is_null($this->raw)) $this->internalRead();

		/* 4. Try getter */
		$getter = 'get' . $property;
		if (method_exists($this, $getter)) {
			$value = $this->$getter();
			$this->setGetCache($property, $value);
			elog(__METHOD__, LOG_DEBUG, 'Getter: %s', is_object($value) ? get_class($value) : $value);
			return $value;
		}

		/* 5. Try raw data */
		try {

			$value = $this->getRaw($property);
			elog(__METHOD__, LOG_DEBUG, 'From raw: %s', is_object($value) ? get_class($value) : $value);
			return $value;

		} catch (Exception $e) {}

		elog(__METHOD__, LOG_DEBUG, 'null');
		return null;
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
		elog(__METHOD__, LOG_DEBUG, '%s::%s = %s', get_class($this), $property, var_export($value, true));

		$setter = 'set' . $property;
		if (method_exists($this, $setter)) {

			elog(__METHOD__, LOG_DEBUG, 'Using setter');
			$this->$setter($value);

		} else {

			elog(__METHOD__, LOG_DEBUG, 'Setting raw value');
			$this->setRaw($property, $value);
			$this->modified = true;

		}

		elog(__METHOD__, LOG_DEBUG, 'modified');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Constructor
	 * @param array $raw [optional] Optional raw entity data
	 */
	public function __construct($raw = null)
	{
		$this->raw = $raw;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Destructor
	 *
	 * Save model changes.
	 */
	public function __destruct()
	{
		if ( ! $this->isModified()) return;
		if ( ! $this->raw) return;
		$this->internalWrite();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Reset model state from data source
	 */
	public function reset()
	{
		$this->internalRead();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Remove entity from data source
	 */
	public function delete()
	{
		$this->internalDelete();
		$this->cancelChanges();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Do not save model changes
	 */
	public function cancelChanges()
	{
		$this->raw = null;
		$this->modified = false;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Indicate chamges in model
	 * @return bool
	 */
	public function isModified()
	{
		return $this->modified;
	}
	//-----------------------------------------------------------------------------

}
