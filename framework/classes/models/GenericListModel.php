<?php
/**
 * Eresus Framework
 *
 * @version 0.0.1
 *
 * Generic list model
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
 * @author  Mikhail Krasilnikov <mk@procreat.ru>
 *
 * $Id: GenericListModel.php 175 2009-05-28 08:52:37Z mekras $
 */


/**
 * Generic list model
 *
 * @package Framework
 * @subpackage Models
 *
 * @author mekras
 *
 */
class GenericListModel extends MvcListModel {

	/**
	 * Database table name
	 * @var string
	 */
	protected $dbTable;

	/**
	 * Primary key name
	 * @var mixed
	 */
	protected $dbKey = 'id';

	/**
	 * Count list size
	 *
	 * @see main/AbstractListModel#internalSize()
	 */
	protected function internalSize()
	{
		elog(array(get_class($this), __METHOD__), LOG_DEBUG, '()');
		$q = DB::createSelectQuery();

		$q->select('count(`'.$this->dbTable.'`.`'.$this->dbKey.'`) as `count`')
		->where($this->where($q));
		$this->fromTables($q);

		$result = DB::fetch($q);
		$this->size = $result['count'];
	}
	//-----------------------------------------------------------------------------

	/**
	 * Load list elements
	 */
	protected function internalLoad()
	{
		if ($this->dbTable) {
			elog(array(get_class($this), __METHOD__), LOG_DEBUG, '()');

			$q = DB::createSelectQuery();

			$this->fields($q);
			$this->fromTables($q);
			$q->where($this->where($q));
			$this->limit($q);
			$this->orderBy($q);

			$this->items = DB::fetchAll($q);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * FROM statement
	 *
	 * @param ezcQuery $q
	 * @return ezcQuery
	 */
	protected function fromTables($q)
	{
		return $q->from($this->dbTable);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Select fields
	 *
	 * @param ezcQuery $q
	 * @return ezcQuery
	 */
	protected function fields($q)
	{
		return $q->select('*');
	}
	//-----------------------------------------------------------------------------

	/**
	 * LIMIT statement
	 *
	 * @param ezcQuery $query
	 * @return ezcQuery
	 * @deprecated
	 */
	protected function mkLIMIT($query)
	{
		return $this->limit($query);
	}
	//-----------------------------------------------------------------------------

	/**
	 * WHERE statement
	 *
	 * @param ezcQuery $query
	 * @return string
	 */
	protected function where($query)
	{
		$e = $query->expr;

		if (count($this->filter)) {

			$asserts = array();
			foreach ($this->filter as $key => $value) {

				if (!is_object($value)) $value = new EqualModelFilter($value);
				elseif ( ! ($value instanceof ModelFilterInterface))
				throw new EresusRuntimeException('Key "' . $key . '" filter object '.get_class($value).' is not an implementation of a ModelFilterInterface', 'Invalid object');

				if ($this->dbTable) $key = $this->dbTable.'.'.$key;
				$asserts []= $value->value($key, $e, $query);

			}

			$where = $e->lAnd($asserts);
			return $where;

		}

		return '1';
	}
	//-----------------------------------------------------------------------------

	/**
	 * LIMIT statement
	 *
	 * @param ezcQuerySelect $query
	 * @return ezcQuerySelect
	 */
	protected function limit($query)
	{
		if ($this->pageSize) {
			if ($this->pageIndex > 1) {
				$offset = ($this->pageIndex - 1) * $this->size;
				return $query->limit($this->pageSize, $offset);
			}
			return $query->limit($this->pageSize);
		}
		return $query;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ORDER BY statement
	 *
	 * @param ezcQuerySelect $query
	 * @return ezcQuerySelect
	 */
	protected function orderBy($query)
	{
		return $query;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get response rows
	 * @param ezcQuery $query
	 * @return array
	 * @deprecated
	 */
	protected function fetchAll($query)
	{
		elog(array(get_class($this), __METHOD__), LOG_DEBUG, '()');
		return DB::fetchAll($query);
	}
	//-----------------------------------------------------------------------------

}

/**
 * Interface of ListModel filters
 *
 * @package Framework
 * @subpackage Models
 *
 * @author mekras
 */
interface ModelFilterInterface {

	/**
	 * Define value
	 * @param mixed $value
	 */
	public function __construct($value);

	/**
	 * Get filter value
	 *
	 * @param string             $column
	 * @param ezcQueryExpression $e
	 * @param ezcQuerySelect     $q
	 *
	 * @return string
	 */
	public function value($column, $e, $q);
}

/**
 * Equal filter
 *
 * @package Framework
 * @subpackage Models
 *
 * @author mekras
 */
class EqualModelFilter implements ModelFilterInterface {

	/**
	 * Filter value
	 * @var mixed
	 */
	protected $value;

	/**
	 * Define value
	 * @param mixed $value
	 */
	public function __construct($value)
	{
		$this->value = $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get filter value
	 *
	 * @param string             $column
	 * @param ezcQueryExpression $e
	 * @param ezcQuerySelect     $q
	 *
	 * @return string
	 */
	public function value($column, $e, $q)
	{
		return $e->eq($column, $q->bindValue($this->value));
	}
	//-----------------------------------------------------------------------------

}

/**
 * 'Not equal' filter
 *
 * @package Framework
 * @subpackage Models
 *
 * @author mekras
 */
class NotEqualModelFilter extends EqualModelFilter {

	/**
	 * Get filter value
	 *
	 * @param string             $column
	 * @param ezcQueryExpression $e
	 * @param ezcQuerySelect     $q
	 *
	 * @return string
	 */
	public function value($column, $e, $q)
	{
		return $e->neq($column, $q->bindValue($this->value));
	}
	//-----------------------------------------------------------------------------

}

/**
 * 'Set' filter
 *
 * @package Framework
 * @subpackage Models
 *
 * @author mekras
 */
class SetModelFilter extends EqualModelFilter {

	/**
	 * Get filter value
	 *
	 * @param string             $column
	 * @param ezcQueryExpression $e
	 * @param ezcQuerySelect     $q
	 *
	 * @return string
	 */
	public function value($column, $e, $q)
	{
		return $e->in($column, $this->value);
	}
	//-----------------------------------------------------------------------------

}