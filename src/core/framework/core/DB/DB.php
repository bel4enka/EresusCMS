<?php
/**
 * Eresus Core
 *
 * @version 0.1.3
 *
 * Database abstraction layer
 *
 * @copyright 2007, Eresus Project, http://eresus.ru/
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
 * @package DB
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 *
 * $Id: DB.php 524 2010-06-05 12:53:45Z mk $
 */

/**
 * DB module settings
 *
 * @package DB
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 * @deprecated since 0.1.3
 */
class DBSettings
{
	private static $dsn;
	/**
	 * Set DSN
	 *
	 * @param string $dsn
	 */
	public static function setDSN($dsn)
	{
		eresus_log(__METHOD__, LOG_NOTICE, "Use of deprecated function");
		self::$dsn = $dsn;
		DB::lazyConnection($dsn);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Set code page
	 *
	 * @param string $codepage
	 */
	public static function setCodepage($codepage)
	{
		eresus_log(__METHOD__, LOG_NOTICE, "Use of deprecated function");
		DB::lazyConnection(self::$dsn . '?charset=' . $codepage);
	}
	//-----------------------------------------------------------------------------
}



/**
 * DB Runtime Exception
 *
 * @package DB
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 */
class DBRuntimeException extends EresusRuntimeException {}



/**
 * DB Query Exception
 *
 * @package DB
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 */
class DBQueryException extends DBRuntimeException
{
	/**
	 * Creates new exception object
	 *
	 * @param ezcQuery  $query [optional]    Problem query
	 * @param string    $message [optional]  Error message
	 * @param Exception $previous [optional] Previous exception
	 */
	function __construct($query = null, $message = null, $previous = null)
	{
		if ($query instanceof ezcQuery) {

			$insider = new DBQueryInsider;
			$query->doBind($insider);
			$query = $insider->subst($query);
		}

		if (is_null($message))
			$message = 'Database query failed';

		if (!is_null($previous))
			$query = $previous->getMessage() . ': ' . $query;

		parent::__construct($query, $message, $previous);
	}
	//-----------------------------------------------------------------------------

}



/**
 * Database interface
 *
 * @package DB
 */
class DB implements ezcBaseConfigurationInitializer
{
	/**
	 * Список DSN "ленивых" соединений
	 * @var array(mixed => string)
	 */
	private static $lazyConnectionDSNs = array();

	/**
	 * Connects to DB
	 *
	 * @param string $dsn              Connection DSN string
	 * @param string $name [optional]  Optional connection name
	 * @return ezcDbHandler
	 * @throws DBRuntimeException
	 */
	public static function connect($dsn, $name = false)
	{
		eresus_log(__METHOD__, LOG_DEBUG, '("%s", %s)', $dsn, $name);

		try
		{
			$db = ezcDbFactory::create($dsn);
		}
			catch (Exception $e)
		{
			throw new DBRuntimeException("Can not connect to '$dsn'", "Database connection failed", $e);
		}

		$db->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);

		if (substr($dsn, 0, 5) == 'mysql' && preg_match('/charset=(.*?)(&|$)/', $dsn, $m))
		{
			$db->query("SET NAMES {$m[1]}");
		}

		ezcDbInstance::set($db, $name);
		return $db;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Configures lazy connection to DB
	 *
	 * @param string $dsn              Connection DSN string
	 * @param string $name [optional]  Optional connection name
	 * @return void
	 */
	public static function lazyConnection($dsn, $name = false)
	{
		eresus_log(__METHOD__, LOG_DEBUG, '("%s", %s)', $dsn, $name);
		self::$lazyConnectionDSNs[$name] = $dsn;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Returns connection handler
	 *
	 * @param string $name [optional]  Optional connection name
	 * @return ezcDbHandler
	 */
	public static function getHandler($name = false)
	{
		return ezcDbInstance::get($name);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Sets connection handler
	 *
	 * @param ezcDbHandler $handler
	 * @param string       $name [optional]  Optional connection name
	 *
	 */
	public static function setHandler(ezcDbHandler $handler, $name = false)
	{
		ezcDbInstance::set($handler, $name);
	}
	//-----------------------------------------------------------------------------

	/**
	 * eZ Components lazy init
	 *
	 * @param bool|string $name  Connection name
	 * @return ezcDbHandler
	 * @internal
	 * @ignore
	 */
	public static function configureObject($name)
	{
		eresus_log(__METHOD__, LOG_DEBUG, '(%s)', $name);

		if (!isset(self::$lazyConnectionDSNs[$name]))
			throw new DBRuntimeException('DSN for lazy connection "'.$name.'" not found');

		$dsn = self::$lazyConnectionDSNs[$name];
		$db = self::connect($dsn, $name);

		return $db;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Set test instance
	 * @param object $instance
	 * @deprecated since 0.1.3 in favor of DB::setHandler
	 */
	public static function setTestInstance($instance)
	{
		eresus_log(__METHOD__, LOG_NOTICE, "Use of deprecated function");
		self::setHandler($instance);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get connection
	 *
	 * @return object
	 * @deprecated since 0.1.3 in favor of DB::getHandler
	 */
	public static function getInstance()
	{
		eresus_log(__METHOD__, LOG_NOTICE, "Use of deprecated function");
		return self::getHandler();
	}
	//-----------------------------------------------------------------------------

	/**
	 * SELECT
	 * @return ezcQuerySelect
	 * @deprecated since 0.1.3 in favor of DB::getHandler()->createSelectQuery
	 */
	public static function createSelectQuery()
	{
		eresus_log(__METHOD__, LOG_NOTICE, "Use of deprecated function");
		$db = self::getInstance();
		return $db->createSelectQuery();
	}
	//-----------------------------------------------------------------------------

	/**
	 * UPDATE
	 * @return ezcQueryUpdate
	 * @deprecated since 0.1.3 in favor of DB::getHandler()->createUpdateQuery
	 */
	public static function createUpdateQuery()
	{
		eresus_log(__METHOD__, LOG_NOTICE, "Use of deprecated function");
		$db = self::getInstance();
		return $db->createUpdateQuery();
	}
	//-----------------------------------------------------------------------------

	/**
	 * INSERT
	 * @return ezcQueryInsert
	 * @deprecated since 0.1.3 in favor of DB::getHandler()->createInsertQuery
	 */
	public static function createInsertQuery()
	{
		eresus_log(__METHOD__, LOG_NOTICE, "Use of deprecated function");
		$db = self::getInstance();
		return $db->createInsertQuery();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Delete
	 * @return ezcQueryDelete
	 * @deprecated since 0.1.3 in favor of DB::getHandler()->createDeleteQuery
	 */
	public static function createDeleteQuery()
	{
		eresus_log(__METHOD__, LOG_NOTICE, "Use of deprecated function");
		$db = self::getInstance();
		return $db->createDeleteQuery();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Execute query
	 *
	 * @param ezcQuery $query
	 *
	 * @throws DBQueryException
	 *
	 * @return mixed
	 */
	public static function execute($query)
	{
		try {

			$stmt = $query->prepare();
			if (LOG_DEBUG) {
				$insider = new DBQueryInsider;
				$query->doBind($insider);
				$s = $insider->subst($query);
				eresus_log(__METHOD__, LOG_DEBUG, 'Query "%s"', $s);
			}
			$result = $stmt->execute();

		} catch (Exception $e) {

			throw new DBQueryException($query, null, $e);

		}

		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Fetch row from DB response
	 * @param ezcQuery $query
	 * @return array
	 */
	public static function fetch($query)
	{
		if (LOG_DEBUG) {
			$insider = new DBQueryInsider;
			$query->doBind($insider);
			$s = $insider->subst($query);
			eresus_log(__METHOD__, LOG_DEBUG, 'Query "%s"', $s);
		}
		$stmt = $query->prepare();

		try {

			$stmt->execute();

		} catch (Exception $e) {

			throw new DBQueryException($query, null, $e);

		}

		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get response rows
	 * @param ezcQuery $query
	 * @return array
	 */
	public static function fetchAll($query)
	{
		if (LOG_DEBUG) {
			$insider = new DBQueryInsider;
			$query->doBind($insider);
			$s = $insider->subst($query);
			eresus_log(__METHOD__, LOG_DEBUG, 'Query "%s"', $s);
		}

		$stmt = $query->prepare();

		try {

			$stmt->execute();

		} catch (Exception $e) {

			throw new DBQueryException($query, null, $e);

		}

		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}
	//-----------------------------------------------------------------------------

}



/**
 * Query Insider
 *
 * Internal class for substitution in doBind method to get values
 * set with bindValue or bindParam methods.
 *
 * @package DB
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 * @internal
 * @ignore
 *
 */
class DBQueryInsider extends PDOStatement {

	/**
	 * Values
	 * @var array
	 */
	protected $values = array();

	/**
	 * Bind value
	 *
	 * @param paramno
	 * @param param
	 * @param type[optional]
	 */
	public function bindValue($paramno, $param, $type = null)
	{
		switch ($type) {

			case PDO::PARAM_BOOL:
			break;

			case PDO::PARAM_INT:
			break;

			case PDO::PARAM_STR:
				$param = is_null($param) ?
					'NULL' :
					"'" . addslashes($param) . "'";
			break;
		}

		$this->values[$paramno] = $param;

	}
	//-----------------------------------------------------------------------------

	/**
	 * Bind param
	 *
	 * @param paramno
	 * @param param
	 * @param type[optional]
	 * @param maxlen[optional]
	 * @param driverdata[optional]
	 */
	public function bindParam($paramno, &$param, $type = null, $maxlen = null, $driverdata = null)
	{
		$this->bindValue($paramno, $param, $type);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Substitute values in query
	 *
	 * @param string $query
	 * @return string
	 */
	public function subst($query)
	{
		foreach ($this->values as $key => $value)
			$query = preg_replace("/$key(\s|,|$)/", "$value$1", $query);

		return $query;
	}
	//-----------------------------------------------------------------------------

}

ezcBaseInit::setCallback('ezcInitDatabaseInstance', 'DB');
