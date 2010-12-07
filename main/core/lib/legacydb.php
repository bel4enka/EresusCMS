<?php
/**
 * ${product.title} ${product.version}
 *
 * Библиотека для обеспечения обратной совместимости с mysql
 *
 * @copyright 2010, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 *
 * Данная программа является свободным программным обеспечением. Вы
 * вправе распространять ее и/или модифицировать в соответствии с
 * условиями версии 3 либо (по вашему выбору) с условиями более поздней
 * версии Стандартной Общественной Лицензии GNU, опубликованной Free
 * Software Foundation.
 *
 * Мы распространяем эту программу в надежде на то, что она будет вам
 * полезной, однако НЕ ПРЕДОСТАВЛЯЕМ НА НЕЕ НИКАКИХ ГАРАНТИЙ, в том
 * числе ГАРАНТИИ ТОВАРНОГО СОСТОЯНИЯ ПРИ ПРОДАЖЕ и ПРИГОДНОСТИ ДЛЯ
 * ИСПОЛЬЗОВАНИЯ В КОНКРЕТНЫХ ЦЕЛЯХ. Для получения более подробной
 * информации ознакомьтесь со Стандартной Общественной Лицензией GNU.
 *
 * Вы должны были получить копию Стандартной Общественной Лицензии
 * GNU с этой программой. Если Вы ее не получили, смотрите документ на
 * <http://www.gnu.org/licenses/>
 *
 * @package EresusCMS
 *
 * $Id$
 */

/**
 * Класс, имитирующий старую библиотеку MySQL
 *
 * @package EresusCMS
 */
class LegacyDB
{
	/**
	 * Префикс таблиц
	 * @var string
	 * @deprecated
	 */
	public $prefix;

	/**
	 * Вести лог запросов
	 * @var bool
	 */
	public $logQueries = false;

	/**
	 * Если TRUE (по умолчанию) в случае ошибки скрипт будет прерван и показано сообщение об ошибке
	 *
	 * @var bool
	 */
	public $error_reporting = true;

	/**
	 * @var ezcDbSchema
	 */
	private $dbSchema = null;

	/**
	 * Открывает соединение сервером данных и выбирает источник
	 *
	 * @param string $server    Сервер данных
	 * @param string $username  Имя пользователя для доступа к серверу
	 * @param string $password  Пароль пользователя
	 * @param string $source    Имя источника данных
	 *
	 * @return bool  Результат соединения
	 * @deprecated
	 */
	public function init($server, $username, $password, $source, $prefix = '')
	{
		eresus_log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
		$e = new Exception();
		eresus_log(__METHOD__, LOG_NOTICE, $e->getTraceAsString());

		return true;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает объект-одиночку схемы БД
	 * @return ezcDbSchema
	 */
	public function getSchema()
	{
		$e = new Exception();
		eresus_log(__METHOD__, LOG_NOTICE, $e->getTraceAsString());

		if (!$this->dbSchema)
		{
			$db = DB::getHandler();
			// FIXME Doctrine
			if ($db->options && $db->options->tableNamePrefix)
			{
				$options = new ezcDbSchemaOptions(array('tableNamePrefix' => $db->options->tableNamePrefix));
				ezcDbSchema::setOptions($options);
			}

			$this->dbSchema = ezcDbSchema::createFromDb($db);
		}

		return $this->dbSchema;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Выполняет запрос к источнику
	 *
	 * @param string $query  Запрос в формате источника
	 * @return mixed  Результат запроса. Тип зависит от источника, запроса и результата
	 * @deprecated
	 */
	public function query($query)
	{
		eresus_log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
		$e = new Exception();
		eresus_log(__METHOD__, LOG_NOTICE, $e->getTraceAsString());

		$db = DB::getHandler();
		$db->exec($query);
		return true;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Выполняет запрос к источнику и возвращает ассоциативный массив значений
	 *
	 * @param  string  $query    Запрос в формате источника
	 * @return  array|bool  Ответ в виде массива или FALSE в случае ошибки
	 * @deprecated
	 */
	public function query_array($query)
	{
		eresus_log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
		$e = new Exception();
		eresus_log(__METHOD__, LOG_NOTICE, $e->getTraceAsString());

		$db = DB::getHandler();
		$stmt = $db->prepare($query);
		if (!$stmt->execute())
			return false;

		$values = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $values;
	}
	//------------------------------------------------------------------------------

	/**
	 * Создание новой таблицы
	 *
	 * @param string $name       Имя таблицы
	 * @param string $structure  Описание структуры
	 * @param string $options    Опции
	 *
	 * @return bool Результат
	 * @deprecated
	 */
	public function create($name, $structure, $options = '')
	{
		eresus_log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
		$e = new Exception();
		eresus_log(__METHOD__, LOG_NOTICE, $e->getTraceAsString());

		$db = DB::getHandler();
		$name = $db->options->tableNamePrefix . $name;
		$query = "CREATE TABLE `$name` ($structure) $options";
		$result = $this->query($query);

		if ($result)
		{
			$db = DB::getHandler();
			$this->dbSchema = ezcDbSchema::createFromDb($db);
		}

		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * Удаление таблицы
	 *
	 * @param string $name       Имя таблицы
	 *
	 * @return bool Результат
	 * @deprecated
	 */
	public function drop($name)
	{
		eresus_log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
		$e = new Exception();
		eresus_log(__METHOD__, LOG_NOTICE, $e->getTraceAsString());

		$db = DB::getHandler();
		$name = $db->options->tableNamePrefix . $name;
		$query = "DROP TABLE `$name`";
		$result = $this->query($query);

		if ($result)
		{
			$db = DB::getHandler();
			$this->dbSchema = ezcDbSchema::createFromDb($db);
		}

		return $result;
	}
	//------------------------------------------------------------------------------

 /**
	* Производит выборку данных из источника
	*
	* @param  string   $tables     Список таблиц из которых проводится выборка
	* @param  string   $condition  Условие для выборки (WHERE)
	* @param  string   $order      Поля для сортировки (ORDER BY)
	* @param  string   $fields     Список полей для получения
	* @param  int      $limit      Максимльное количество получаемых записей
	* @param  int      $offset     Начальное смещение для выборки
	* @param  string   $group      Поле для группировки
	* @param  bool     $distinct   Вернуть только уникальные записи
	*
	* @return  array|bool  Выбранные элементы в виде массива или FALSE в случае ошибки
	* @deprecated
	*/
	public function select($tables, $condition = '', $order = '', $fields = '', $limit = 0, $offset = 0, $group = '', $distinct = false)
	{
		eresus_log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
		$e = new Exception();
		eresus_log(__METHOD__, LOG_NOTICE, $e->getTraceAsString());

		$db = DB::getHandler();
		$q = $db->createSelectQuery();
		$e = $q->expr;

		if (empty($fields))
			$fields = '*';

		if ($distinct)
			$q->selectDistinct($fields);
		else
			$q->select($fields);

		$tables = explode(',', str_replace('`', '', $tables));
		$q->from($tables);

		if ($condition)
			$q->where($condition);

		if (strlen($order))
		{
			$order = explode(',', $order);
			for($i = 0; $i < count($order); $i++)
				switch ($order[$i]{0})
				{
					case '+':
						$q->orderBy(substr($order[$i], 1));
					break;

					case '-':
						$q->orderBy(substr($order[$i], 1), ezcQuerySelect::DESC);
					break;

					default:
						$q->orderBy($order[$i]);
					break;
				}
		}

		if ($limit && $offset)
			$q->limit($limit, $offset);
		elseif ($limit)
			$q->limit($limit);

		if ($group)
			$q->groupBy($group);

		$result = DB::fetchAll($q);

		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Вставка элемента в БД
	 *
	 * @param string $table  Таблица, в которую надо вставтиь элемент
	 * @param array  $item   Ассоциативный массив значений
	 *
	 * @return mixed  Результат выполнения операции
	 * @deprecated
	 */
	public function insert($table, $item)
	{
		eresus_log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
		$e = new Exception();
		eresus_log(__METHOD__, LOG_NOTICE, $e->getTraceAsString());

		$fields = $this->fields($table);
		if (!$table)
			return false;

		$q = DB::getHandler()->createInsertQuery();
		$q->insertInto($table);

		foreach ($fields as $field)
			if (isset($item[$field]))
				$q->set($field, $q->bindValue($item[$field]));

		DB::execute($q);
		return true;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Выполняет обновление информации в источнике
	 *
	 * @param string $table      Таблица
	 * @param mixed  $set        Изменения
	 * @param string $condition  Условие
	 * @return unknown
	 * @deprecated
	 */
	public function update($table, $set, $condition)
	{
		eresus_log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
		$e = new Exception();
		eresus_log(__METHOD__, LOG_NOTICE, $e->getTraceAsString());

		$q = DB::getHandler()->createUpdateQuery();
		$q->update($table)
			->where($condition);

		$set = explode(',', $set);
		foreach ($set as $each)
		{
			list($key, $value) = explode('=', $each);
			$key = str_replace('`', '', trim($key));
			$value = preg_replace('/(^\'|\'$)/', '', trim($value));
			$q->set($key, $q->bindValue($value));
		}

		DB::execute($q);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Выполняет запрос DELETE к базе данных
	 *
	 * @param string $table      таблица, из которой требуется удалить записи
	 * @param string $condition  признаки удаляемых записей
	 * @return mixed
	 * @deprecated
	 */
	public function delete($table, $condition)
	{
		eresus_log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
		$e = new Exception();
		eresus_log(__METHOD__, LOG_NOTICE, $e->getTraceAsString());

		$q = DB::getHandler()->createDeleteQuery();
		$q->deleteFrom($table)
			->where($condition);
		DB::execute($q);
		return null;
	}
	//-----------------------------------------------------------------------------
 /**
	* Получение списка полей таблицы
	*
	* @param string $table            Имя таблицы
	* @param bool   $info [optional]
	* @return array|false  Список полей, с описанием, если $info = true
	*
	* @deprecated с 2.14
	*/
	public function fields($table, $info = false)
	{
		eresus_log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
		$e = new Exception();
		eresus_log(__METHOD__, LOG_NOTICE, $e->getTraceAsString());

		$schm = $this->getSchema()->getSchema();
		if ($schm[$table]->fields)
			return array_keys($schm[$table]->fields);
		else
			return false;
/*		global $Eresus;

		$fields = $this->query_array("SHOW COLUMNS FROM `{$this->prefix}$table`");
		if ($fields) {
			$result = array();
			foreach($fields as $item) {
				if ($info) {
					$result[$item['Field']] = array(
						'name' => $item['Field'],
						'type' => $item['Type'],
						'size' => 0,
						'signed' => false,
						'default' => $item['Default'],
					);
					switch (true) {
						case $item['Type'] == 'text':
							$result[$item['Field']]['size'] = 65535;
						break;
						case $item['Type'] == 'longtext':
							$result[$item['Field']]['type'] = 'text';
							$result[$item['Field']]['size'] = 4294967295;
						break;
						case substr($item['Type'], 0, 3) == 'int':
							$result[$item['Field']]['signed'] = strpos($result[$item['Field']]['type'], 'unsigned') === false;
							$item['Type'] = str_replace(' unsigned', '', $item['Type']);
							$result[$item['Field']]['type'] = 'int';
							$result[$item['Field']]['size'] = substr($item['Type'], 4, -1);
						break;
						case substr($item['Type'], 0, 8) == 'smallint':
							$result[$item['Field']]['signed'] = strpos($result[$item['Field']]['type'], 'unsigned') === false;
							$item['Type'] = str_replace(' unsigned', '', $item['Type']);
							$result[$item['Field']]['type'] = 'int';
							$result[$item['Field']]['size'] = substr($item['Type'], 9, -1);
						break;
						case substr($item['Type'], 0, 7) == 'tinyint':
							$result[$item['Field']]['signed'] = strpos($result[$item['Field']]['type'], 'unsigned') === false;
							$item['Type'] = str_replace(' unsigned', '', $item['Type']);
							$result[$item['Field']]['type'] = 'int';
							$result[$item['Field']]['size'] = substr($item['Type'], 8, -1);
						break;
						case substr($item['Type'], 0, 7) == 'varchar':
							$result[$item['Field']]['type'] = 'string';
							$result[$item['Field']]['size'] = substr($item['Type'], 8, -1);
						break;
					}
				} else $result[] = $item['Field'];
			}
		} else FatalError(mysql_error($this->Connection));
		return $result;*/
	}
	//-----------------------------------------------------------------------------

	/**
	 * Выбрать одну запись из БД
	 *
	 * @param string $table      Имя таблицы
	 * @param string $condition  SQL-условие
	 * @param string $fields     Выбираемые поля
	 * @return array|false
	 * @deprecated
	 */
	public function selectItem($table, $condition, $fields = '')
	{
		eresus_log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
		$e = new Exception();
		eresus_log(__METHOD__, LOG_NOTICE, $e->getTraceAsString());

		$q = DB::getHandler()->createSelectQuery();

		if ($fields == '')
			$fields = '*';

		$q->select($fields)
			->from($table)
			->where($condition)
			->limit(1);

		$item = DB::fetch($q);

		return $item;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обновляет одну запись
	 *
	 * @param string $table
	 * @param array  $item
	 * @param string $condition
	 * @return void
	 * @deprecated
	 */
	public function updateItem($table, $item, $condition)
	{
		eresus_log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
		$e = new Exception();
		eresus_log(__METHOD__, LOG_NOTICE, $e->getTraceAsString());

		$fields = $this->fields($table);
		if (!$table)
			return false;

		$q = DB::getHandler()->createUpdateQuery();
		$q->update($table)
			->where($condition);

		foreach ($fields as $field)
			if (isset($item[$field]))
				$q->set($field, $q->bindValue($item[$field]));

		DB::execute($q);
		return true;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает количество записей в таблице
	 *
	 * @param string $table      таблица, для которой требуется посчитать кол-во записей
	 * @param string $condition
	 * @param string $group
	 * @param bool   $rows
	 * @return int
	 * @deprecated
	 */
	public function count($table, $condition = false, $group = false, $rows = false)
	{
		eresus_log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
		$e = new Exception();
		eresus_log(__METHOD__, LOG_NOTICE, $e->getTraceAsString());

		$q = DB::getHandler()->createSelectQuery();
		$e = $q->expr;

		$q->select($q->alias($e->count('*'), 'count'))
			->from($table);

		if ($condition)
			$q->where($condition);

		if ($group)
			$q->groupBy($group);

		$result = DB::fetchAll($q);
		if ($rows)
			return count($result);
		else
			return intval($result[0]['count']);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает идентификатор последней вставленной записи
	 *
	 * @return mixed
	 * @deprecated
	 */
	public function getInsertedID()
	{
		eresus_log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
		$e = new Exception();
		eresus_log(__METHOD__, LOG_NOTICE, $e->getTraceAsString());

		$db = DB::getHandler();
		return $db->lastInsertId();
	}
	//-----------------------------------------------------------------------------

	/**
	 *
	 * @param unknown_type $table
	 * @param unknown_type $param
	 * @return unknown_type
	 * @deprecated
	 */
	public function tableStatus($table, $param='')
	{
		eresus_log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
		$e = new Exception();
		eresus_log(__METHOD__, LOG_NOTICE, $e->getTraceAsString());

		$result = $this->query_array("SHOW TABLE STATUS LIKE '".$this->prefix.$table."'");
		if ($result) {
			$result = $result[0];
			if (!empty($param)) $result = $result[$param];
		}
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Экранирует потенциально опасные символы
	 *
	 * Начиная с 2.13 метод ничего не делает.
	 *
	 * @param mixed $src  Входные данные
	 * @return mixed
	 * @deprecated
	 */
	public function escape($src)
	{
		eresus_log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
		$e = new Exception();
		eresus_log(__METHOD__, LOG_NOTICE, $e->getTraceAsString());

		return $src;
	}
	//-----------------------------------------------------------------------------
}