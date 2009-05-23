<?php
/**
 * Eresus Framework
 *
 * @version 0.0.1
 *
 * Generic model
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
 * @subpackage MVC
 * @author  Mikhail Krasilnikov <mk@procreat.ru>
 *
 * $Id: GenericModel.php 157 2009-05-20 13:07:28Z mekras $
 */

/**
 * Generic SQL-based model
 *
 * @package Framework
 * @subpackage MVC
 *
 */
class GenericModel extends MvcModel {

	/**
	 * If this is a new entity?
	 *
	 * @var bool
	 */
	protected $new = false;

	/**
	 * Database table name
	 * @var string
	 */
	protected $dbTable;

	/**
	 * Database table primary key field name
	 * @var string
	 */
	protected $dbKey = 'id';

	/**
	 * Entity ID
	 * @var mixed
	 */
	protected $dbId;

	/**
	 * Constructor
	 *
	 * @param mixed $source [optional]  Optional source data wich can be:
	 *    1. null or ommited. In this case model will be treated as a new
	 *       entity
	 *    2. array representing raw entity data
	 *    3. In other cases $source will be treated as an entity ID.
	 */
	public function __construct($source = null)
	{
		parent::__construct();

		switch (true) {

			case is_null($source):
				$this->raw = array();
				$this->new = true;
			break;

			case is_array($source):
				$this->raw = $source;
			break;

			default:
				$this->dbId = $source;
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Destructor
	 *
	 * Save model changes.
	 */
	public function __destruct()
	{
		if ($this->isNew()) $this->internalCreate();
		else parent::__destruct();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Property getting
	 *
	 * @param string $property  Property name
	 * @return mixed
	 */
	public function __get($property)
	{

		if ($this->isNew() && $property == $this->dbKey) $this->internalCreate();

		$value = parent::__get($property);

		return $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * WHERE statement
	 *
	 * @param ezcQuery $query  Query object
	 * @return string
	 */
	protected function where($query)
	{
		if ($this->dbKey && $this->dbId) {

			$e = $query->expr;
			return $e->eq($this->dbKey, $query->bindValue($this->dbId));

		}

		return '1';
	}
	//-----------------------------------------------------------------------------

	/**
	 * WHERE statement
	 *
	 * @param ezcQuery $query  Query object
	 * @return string
	 *
	 * @deprecated
	 */
	protected function mkWHERE($query)
	{
		return $this->where($query);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Fetch row from DB response
	 * @param ezcQuery $query
	 * @return array
	 *
	 * @deprecated
	 */
	protected function fetch($query)
	{
		return DB::fetch($query);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Check if reading allowed
	 *
	 * Reading not allowed if
	 * * dbTable property not set
	 * * dbKey property not set
	 * * dbId property not set
	 *
	 * @return bool
	 */
	protected function internalReadAllowed()
	{
		if (! $this->dbTable) return false;
		if (! $this->dbKey) return false;
		if (! $this->dbId) return false;
		return true;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Read model data from data source
	 */
	protected function internalRead()
	{
		if (! $this->internalReadAllowed()) return;

		$q = DB::createSelectQuery();

		$q->select('*')
			->from($this->dbTable)
			->where($this->where($q));

		$this->raw = DB::fetch($q);

		if (!$this->raw)
			throw new EresusRuntimeException('Query "' . $q . '" returns empty result', 'Object not found');

		$this->modified = false;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Check if writing allowed
	 *
	 * Writing not allowed if
	 * * model not modified
	 * * dbTable property not set
	 * * dbKey property not set
	 * * dbId property not set
	 *
	 * @return bool
	 */
	protected function internalWriteAllowed()
	{
		if (! $this->isModified()) return false;
		if (! $this->dbTable) return false;
		if (! $this->dbKey) return false;
		if (! $this->dbId) return false;
		return true;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Raw data save
	 *
	 * Model calls this method to store raw data
	 *
	 */
	protected function internalWrite()
	{
		if (! $this->internalWriteAllowed()) return;

		$q = DB::createUpdateQuery();

		$q = $q->update($this->dbTable)->where($this->where($q));

		foreach($this->raw as $column => $value)
			$q = $q->set($column, $q->bindValue($value));

		DB::execute($q);
		$this->modified = false;

	}
	//-----------------------------------------------------------------------------

	/**
	 * Check if creating allowed
	 *
	 * Creation not allowed if
	 * * model not new
	 * * dbTable property not set
	 * * dbKey property not set
	 *
	 * @return bool
	 */
	protected function internalCreateAllowed()
	{
		if (! $this->isNew()) return false;
		if (! $this->dbTable) return false;
		if (! $this->dbKey) return false;
		if (! $this->raw) return false;
		return true;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Entity create
	 *
	 */
	protected function internalCreate()
	{
		if (! $this->internalCreateAllowed()) return;

		$q = DB::createInsertQuery();

		$q = $q->insertInto($this->dbTable);

		foreach($this->raw as $column => $value)
			$q = $q->set($column, $q->bindValue($this->$column));

		DB::execute($q);

		$db = ezcDbInstance::get();
		$id = $db->lastInsertId();
		if ($id) $this->raw[$this->dbKey] = $id;

		$this->new = false;
		$this->modified = false;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Check if deleting allowed
	 *
	 * Deleting not allowed if
	 * * model is new
	 * * dbTable property not set
	 * * dbKey property not set
	 *
	 * @return bool
	 */
	protected function internalDeleteAllowed()
	{
		if ($this->isNew()) return;
		if (! $this->dbTable) return;
		if (! $this->dbKey) return;
		return true;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Remove entity
	 *
	 * Model calls this method to remove entity from external source
	 *
	 * @see build/core/AbstractModel#internalDelete()
	 */
	protected function internalDelete()
	{
		if (! $this->internalDeleteAllowed()) return;

		$q = DB::createDeleteQuery();

		$q->deleteFrom($this->dbTable)
			->where($this->where($q));

		DB::execute($q);

		$this->modified = false;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Indicate if model does not stored in DB
	 *
	 * @return bool
	 */
	public function isNew()
	{
		return $this->new;
	}
	//-----------------------------------------------------------------------------

}
