<?php
/**
 * Eresus Core
 *
 * @version 0.1.0
 *
 * Database abstraction layer
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
 * @subpackage DB
 * @author  Mikhail Krasilnikov <mk@procreat.ru>
 *
 * $Id: DB.php 159 2009-05-21 07:59:07Z mekras $
 */

/**
 * Database interface
 *
 * @package Core
 * @subpackage DB
 *
 * @author mekras
 *
 */
class DB {

	/**
	 * Instance to use for testing
	 * @var object
	 */
	private static $testInstance;

	/**
	 * Set test instance
	 * @param object $instance
	 */
	public static function setTestInstance($instance)
	{
		self::$testInstance = $instance;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get connection
	 * @return object
	 */
	private static function getInstance()
	{
		if (self::$testInstance) return self::$testInstance;

		return ezcDbInstance::get();
	}
	//-----------------------------------------------------------------------------

	/**
	 * SELECT
	 * @return ezcQuerySelect
	 */
	public static function createSelectQuery()
	{
		$db = self::getInstance();
		return $db->createSelectQuery();
	}
	//-----------------------------------------------------------------------------

	/**
	 * UPDATE
	 * @return ezcQueryUpdate
	 */
	public static function createUpdateQuery()
	{
		$db = self::getInstance();
		return $db->createUpdateQuery();
	}
	//-----------------------------------------------------------------------------

	/**
	 * INSERT
	 * @return ezcQueryInsert
	 */
	public static function createInsertQuery()
	{
		$db = self::getInstance();
		return $db->createInsertQuery();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Delete
	 * @return ezcQueryDelete
	 */
	public static function createDeleteQuery()
	{
		$db = self::getInstance();
		return $db->createDeleteQuery();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Execute query
	 * @param ezQuery $query
	 * @return mixed
	 */
	public static function execute($query)
	{
		$stmt = $query->prepare();

		try {

			elog(__METHOD__, LOG_DEBUG, 'Query "%s"', $query);
			$result = $stmt->execute();

		} catch (Exception $e) {

			Core::handleException(new EresusRuntimeException($stmt->queryString, 'Database query failed'));
			$result = false;

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
		elog(__METHOD__, LOG_DEBUG, 'Query "%s"', $query->getQuery());
		$stmt = $query->prepare();

		try {

			$stmt->execute();

		} catch (Exception $e) {

			throw new EresusRuntimeException($query->getQuery(), $e->getMessage(), $e);

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
		elog(__METHOD__, LOG_DEBUG, 'Query "%s"', $query->getQuery());
		$stmt = $query->prepare();

		try {

			$stmt->execute();

		} catch (Exception $e) {

			throw new EresusRuntimeException($query->getQuery(), $e->getMessage(), $e);

		}

		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}
	//-----------------------------------------------------------------------------

}



/**
 * Lazy database connection init
 *
 * @package global
 * @internal
 * @ignore
 */
class EresusLazyDatabaseConfiguration implements ezcBaseConfigurationInitializer
{
	public static function configureObject( $instance )
	{
		switch ( $instance ) {
			case false:
				$app = Core::app();

				if (Registry::exists('core.db.dsn')) {

					$dsn = Registry::get('core.db.dsn');
					$codepage = Registry::exists('core.db.codepage') ? Registry::get('core.db.codepage') : null;

				} elseif ($app) { # FIXME: Deprecated

					$dsn = $app->getOpt('database', 'dsn');
					if (is_null($dsn)) throw new EresusRuntimeException(get_class($app) . '::getOpt returned NULL', 'DB connection not configured.');
					$codepage = $app->getOpt('database', 'codepage');

				} else return null;

				$db = ezcDbFactory::create($dsn);

				#FIXME Next line may be valid only for MySQL
				try {
					if ($codepage) $db->query('SET NAMES ' . $codepage);
				} catch (Exception $e) {}

				return $db;
		}
	}
}

if (!Core::testMode()) ezcBaseInit::setCallback('ezcInitDatabaseInstance', 'EresusLazyDatabaseConfiguration');
