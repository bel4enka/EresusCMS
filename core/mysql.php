<?
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# MYSQL.PHP - Класс для работы с БД MySQL
# @version: 1.20
# @modified: 2007-02-06
# © ProCreat Systems (http://procreat.ru/)
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

# ФУНКЦИИ ОТЛАДКИ (Работают при установленном флаге DEBUG_MODE)
# Устанавливает глобальные переменные 
#  $__MYSQL_QUERY_COUNT - Считает количество запросов к БД
#  $__MYSQL_QUERY_TIME - Считает общее время запросов к БД
#  $__MYSQL_QUERY_LOG - Все сделанные запросы (Необходимо установить флаг TMySQL->logQueries)
if (!defined('DEBUG_MODE')) define('DEBUG_MODE', false);

class TMySQL {
  var $Connection, $name, $prefix,
        $logQueries = false,
        $functionStack = array();
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function functionStackPush($function_name)
  {
    array_push($this->functionStack, $function_name);
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function functionStackPop()
  {
    array_pop($this->functionStack);
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function ErrorMessage($msg = 'Unknown', $task = 'Unknown', $LINE= 'Unknown')
  # Функция выводит сообщение об ошибке MySQL и прекращает работу скрипта
  #   $msg - описание ошибки
  #   $task - операция, при выполнении которой обнаружена ошибка
  #   $LINE - номер строки, в которой обнаружена ошибка (в качестве аргумента, укажите __LINE__)
  {
  global $PHP_SELF;
    if (constant('DEBUG_MODE')) {
      $_stack = '';
      foreach($this->functionStack as $func) $_stack .= "&nbsp;&nbsp;TMySQL.".$func."<br />\n";
      $_stack = "<br />Call stack:<br />\n".$_stack;
    }
    echo "<div align=\"center\">\n".
      "<table width=\"80%\" style=\"background-color: #79c; border-style: solid; border-width: 1; border-color: #acf #025 #025 #acf; font-family: verdana; font-size: 8pt;\">\n".
      "<tr><td bgcolor=\"black\" align=\"center\" style=\"color: yellow; font-weight: bold; border-style: solid; border-width: 1; border-color: #025 #acf #acf #025;\">MySQL Error</td></tr>\n".
      "<tr><td style=\"background-color: #79c; color: white; text-align: left; padding: 10; font-weight: bold; border-style: solid; border-width: 1; border-color: #025 #acf #acf #025;\">".
      "Error: $msg<br /> Action: ".$task."<br /> Adress: ".$PHP_SELF."<br /> Sript file: ".__FILE__."<br /> Line: ". $LINE.$_stack."</td></tr>\n".
      "</table></div>\n";
    exit();
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function init($mysqlHost, $mysqlUser, $mysqlPswd, $mysqlName, $mysqlPrefix='')
  # Открывает соединение с базой данных MySQL и выбирает указанную базу данных.
  {
    if (constant('DEBUG_MODE')) $this->functionStackPush("init('$mysqlHost', '$mysqlUser', '[PASSWORD]', '$mysqlName', '$mysqlPrefix')");
    $this->name = $mysqlName;
    $this->prefix = $mysqlPrefix;
    @$this->Connection = mysql_connect($mysqlHost, $mysqlUser, $mysqlPswd, true);
    if (!$this->Connection) $this->ErrorMessage("Can not connect","Connecting to MySQL server. Check login and password",__LINE__);
    if (defined('LOCALE_CHARSET')) $this->query("SET NAMES '".LOCALE_CHARSET."'", false);
    if (!mysql_select_db($this->name, $this->Connection)) $this->ErrorMessage(mysql_error($this->Connection),"Selecting database \"".$this->name."\"",__LINE__);
    if (constant('DEBUG_MODE')) $this->functionStackPop();
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function query($query, $error_reporting=true)
  # Выполняет запрос к базе данных с которой установленно соединение методом init().
  {
  global $__MYSQL_QUERY_COUNT, $__MYSQL_QUERY_TIME, $__MYSQL_QUERY_LOG;

    if (constant('DEBUG_MODE')) {
      $this->functionStackPush("query('$query', '$error_reporting')");
      $time_start = microtime();
      if ($this->logQueries) $__MYSQL_QUERY_LOG .= $query."\n";
    }
    $result = mysql_query($query, $this->Connection);
    if ($error_reporting && !$result) $this->ErrorMessage(mysql_error($this->Connection),"Query \"".$query."\"",__LINE__);
    if (constant('DEBUG_MODE')) {
      $__MYSQL_QUERY_COUNT++;
      $__MYSQL_QUERY_TIME += microtime() - $time_start;
      $this->functionStackPop();
    }
    return $result;
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function query_array($query, $error_reporting=true)
  # Выполняет запрос к базе данных и возвращает ассоциативный массив значений
  {
  global $__MYSQL_QUERY_COUNT, $__MYSQL_QUERY_TIME, $__MYSQL_QUERY_LOG;

    if (constant('DEBUG_MODE')) {
      $this->functionStackPush("query_array('$query', '$error_reporting')");
    }
    $result = $this->query($query, $error_reporting);
    $values = Array();
    while($row = mysql_fetch_assoc($result)) {
      if (count($row)) foreach($row as $key => $value) $row[$key] = StripSlashes($value);
      $values[] = $row;
    }
    if (constant('DEBUG_MODE')) $this->functionStackPop();
    return $values;
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function select($tables, $condition = '', $order = '', $desc = false, $fields = '', $lim_rows = 0, $lim_offset = 0, $group = '', $distinct = false)
  # Выполняет запрос SELECT к базе данных используя метод query().
  #  $tables - таблицы, из которых требуется сделать выборку (FROM)
  #  $contidion - условие для выборки (WHERE)
  #  $order - поля, по которым следует выполнить сортировку (OREDER BY)
  #  $desc - если равен true, то сортировка идет в обратном порядке
  #  $fields - список полей, которые требуется получить
  #  $lim_rows - кол-во строк для выборки
  #  $lim_offset - первая строка для выборки
  #  $group - поле для группировки
  #  $distinct - усли равно true, то будут выбраны только уникальные значения.
  {
    if (constant('DEBUG_MODE')) $this->functionStackPush("select('$tables', '$condition', '$order', '$desc', '$fields', '$lim_rows', '$lim_offset', '$group', '$distinct')");
    $query = 'SELECT ';
    if ($distinct) $query .= 'DISTINCT ';
    if (!strlen($fields)) $fields = '*';
    $tables = str_replace('`','',$tables);
    $tables = preg_replace('/([\w.]+)/i', '`'.$this->prefix.'$1`', $tables);
    $query .= $fields." FROM ".$tables;
    if (strlen($condition)) $query .= " WHERE ".$condition;
    if (strlen($group)) $query .= " GROUP BY ".$group."";
    if (strlen($order)) {
      $query .= " ORDER BY ".$order;
      if ($desc) $query .= ' DESC';
    }
    if ($lim_rows) {
      $query .= ' LIMIT ';
      if ($lim_offset) $query .= "$lim_offset, ";
      $query .= $lim_rows;
    }

    $result = $this->query_array($query);

    if (constant('DEBUG_MODE')) $this->functionStackPop();
    return $result;
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
  function insert($table, $item)
  # Выполняет запрос INSERT к базе данных используя метод query().
  #  $table - таблица, в которую надо вставтиь запись
  #  $item - ассоциативный массив значений
  {
    if (constant('DEBUG_MODE')) $this->functionStackPush("insert('$table', '$item')");
    $hnd = mysql_list_fields($this->name, $this->prefix.$table, $this->Connection);
    $cols = '';
    $values = '';
    while (($field = @mysql_field_name($hnd, $i++))) if (isset($item[$field])) {
      $cols .= ", `$field`";
      $values .= " , '".mysql_real_escape_string($item[$field])."'";
    }
    $cols = substr($cols, 2);
    $values = substr($values, 2);
    $result = $this->query("INSERT INTO ".$this->prefix.$table." (".$cols.") VALUES (".$values.")");
    if (constant('DEBUG_MODE')) $this->functionStackPop();
    return $result; 
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function update($table, $set, $condition)
  # Выполняет запрос UPDATE к базе данных используя метод query().
  #  $table - таблица, в которую надо внестит изменения
  #  $set - изменяемые значения
  #  $condition - условия для изменения
  {
    if (constant('DEBUG_MODE')) $this->functionStackPush("update('$table', '$set', '$condition')");
    $result = $this->query("UPDATE `".$this->prefix.$table."` SET ".$set." WHERE ".$condition);
    if (constant('DEBUG_MODE')) $this->functionStackPop();
    return $result;
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function delete($table, $condition)
  # Выполняет запрос DELETE к базе данных используя метод query().
  #  $table - таблица, из которой требуется удалить записи
  #  $condition - признаки удаляемых записей
  {
    if (constant('DEBUG_MODE')) $this->functionStackPush("delete('$table', '$condition')");
    $result = $this->query("DELETE FROM `".$this->prefix.$table."` WHERE ".$condition);
    if (constant('DEBUG_MODE')) $this->functionStackPop();
    return $result;
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function fields($table)
  # Возвращает список полей таблицы
  #  $table - таблица, для которой надо получить список полей
  {
    if (constant('DEBUG_MODE')) $this->functionStackPush("fields('$table')");
    $hnd = mysql_list_fields($this->name, $this->prefix.$table, $this->Connection);
    if ($hnd == false) $this->ErrorMessage(mysql_error($this->Connection),"Enumerating fields in \"".$prefix.$table."\"",__LINE__);
    while (($field = @mysql_field_name($hnd, $i++))) $result[] = $field;
    if (constant('DEBUG_MODE')) $this->functionStackPop();
    return $result;
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function selectItem($table, $condition, $fields = '')
  {
    if (constant('DEBUG_MODE')) $this->functionStackPush("selectItem('$table', '$condition', '$fields')");
    if ($table[0] != "`") $table = "`".$table."`";
    $tmp = $this->select($table, $condition, '', false, $fields);
    $tmp = isset($tmp[0])?$tmp[0]:null;
    if (constant('DEBUG_MODE')) $this->functionStackPop();
    return $tmp;
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function updateItem($table, $item, $condition)
  {
    if (constant('DEBUG_MODE')) $this->functionStackPush("updateItem('$table', '$item', '$condition')");
    $hnd = mysql_list_fields($this->name, $this->prefix.$table, $this->Connection);
    if ($hnd === false) $this->ErrorMessage(mysql_error($this->Connection),"Listing fields of \"".$this->dbname.'.'.$this->prefix.$table."\"",__LINE__);
    $values = '';
    $i = 0;
    while (($field = @mysql_field_name($hnd, $i++))) if (isset($item[$field])) $values .= " , `$field`='".mysql_real_escape_string($item[$field])."'";
    $values = substr($values, 2);
    $result = $this->query("UPDATE `".$this->prefix.$table."` SET ".$values." WHERE ".$condition); 
    if (constant('DEBUG_MODE')) $this->functionStackPop();
    return $result;
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function count($table, $condition='', $group='', $rows=false)
  # Возвращает количество записей в таблице используя метод query().
  #  $table - таблица, для которой требуется посчитать кол-во записей
  {
    if (constant('DEBUG_MODE')) $this->functionStackPush("count('$table')");
    $result = $this->query("SELECT count(*) FROM `".$this->prefix.$table."`".(empty($condition)?'':'WHERE '.$condition).(empty($group)?'':' GROUP BY `'.$group.'`'));
    if ($rows) {
      $count = 0;
      while (mysql_fetch_row($result)) $count++;
      $result = $count;
    } else {
      $result = mysql_fetch_row($result);
      $result = $result[0];
    }
    if (constant('DEBUG_MODE')) $this->functionStackPop();
    return $result;
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function getInsertedID()
  {
    return mysql_insert_id($this->Connection);
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function tableStatus($table, $param='')
  {
    $result = $this->query_array("SHOW TABLE STATUS LIKE '".$table."'");
    if (!empty($param)) $result = $result[0][$param];
    return $result;
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
}
?>