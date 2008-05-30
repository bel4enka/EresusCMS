<?php

function test_db_select_sort($a, $b)
{
	global $Eresus;

	$result = 0;

	foreach($Eresus->db->order as $field => $asc) {
		if ($a[$field] == $b[$field]) continue;
		$result = ($a[$field] < $b[$field]) ? -1 : 1;
		if (!$asc) $result *= -1;
	}
	return $result;
}
//-----------------------------------------------------------------------------

class test_db {
	var $functions = array();
	function tablename($table)
	{
		$result = realpath(dirname(__FILE__).'/../../../db/'.str_replace('`', '', $table).'.csv');
		return $result;
	}
	//-----------------------------------------------------------------------------
	function sql_find_in_set()
	{
		$what = func_get_arg(0);
		$where = func_get_arg(1);
		$where = preg_replace('/\s/', '', $where);
		$where = explode(',', $where);
		return in_array($what, $where);
	}
	//-----------------------------------------------------------------------------
	function sqlFunction($function, $arguments)
	{
		$result = false;
		if (in_array($function, $this->functions)) {
			$function = "sql_$function";
			$arguments = explode(', ', $arguments);
			for($i = 0; $i < count($arguments); $i++) {
				$arguments[$i] = trim($arguments[$i]);
				if (substr($arguments[$i], 0, 1) == "'" && substr($arguments[$i], -1) == "'")
					$arguments[$i] = substr($arguments[$i], 1, -1);
			}
			$result = call_user_func_array(array($this, $function), $arguments);
		}
		return $result;
	}
	//-----------------------------------------------------------------------------
	function sqlSubstituteValues($condition, $values)
	{
		foreach($values as $key => $value) $condition = str_replace("`$key`", $value, $condition);
		return $condition;
	}
	//-----------------------------------------------------------------------------
	function sqlSubstituteFunctions($condition, $values)
	{
		$_condition = strtolower($condition);
		foreach($this->functions as $function) if (($p = strpos($_condition, $function)) !== false)  {
			$arguments = substr($condition, $p + strlen($function));
			$arguments = substr($arguments, 1, strpos($arguments, ')')-1);
			$replace = $this->sqlFunction($function, $arguments);
			$condition = substr_replace($condition, $replace, $p, strlen($function) + strlen($arguments) + 2);
		}
		return $condition;
	}
	//-----------------------------------------------------------------------------
	function condition_check($condition, $record)
	{
		if (empty($condition)) return true;

		$condition = $this->sqlSubstituteValues($condition, $record);
		$condition = $this->sqlSubstituteFunctions($condition, $record);

		$condition = preg_replace(
			array('/(?<!<|>)=/'),
			array('=='),
			$condition
		);

		$result = eval("return $condition;");

		return $result;
	}
	//-----------------------------------------------------------------------------
	function init()
	{
		$list = get_class_methods($this);
		foreach($list as $function) if (strpos($function, 'sql_') === 0) $this->functions[] = substr($function, 4);
	}
	//-----------------------------------------------------------------------------
	function select($tables, $condition = '', $order = '', $fields = '', $lim_rows = 0, $lim_offset = 0, $group = '', $distinct = false)
	{
		if (is_bool($fields) || $fields == '1' || $fields == '0' || !is_numeric($lim_rows)) {
			# Обратная совместимость c mysql 1.2.x
			$desc = $fields;
			$fields = $lim_rows ? $lim_rows : '*';
			$lim_rows = $lim_offset;
			$lim_offset = $group;
			$group = $distinct;
			$distinct = func_num_args() == 9 ? func_get_arg(8) : false;
		}

		$result = false;
		$filename = $this->tablename($tables);
		if ($filename) {
			$rows = file($filename);
			$fieldlist = explode('|', trim(array_shift($rows)));
			$result = array();
			for($i = 0; $i < count($rows); $i++) {
				$values = explode('|', trim($rows[$i]));
				$item = array();
				for($j = 0; $j < count($fieldlist); $j++) $item[$fieldlist[$j]] = $values[$j];
				if ($this->condition_check($condition, $item)) $result[] = $item;
			}

			/*
			 * Сортировка
			 */
			if ($order) {
				$_order = explode(',', str_replace('`', '', $order));
				$this->order = array();
				foreach($_order as $value) {
					$f = substr($value, 0, 1);
					switch ($f) {
						case '+': $this->order[substr($value, 1)] = true; break;
						case '-': $this->order[substr($value, 1)] = false; break;
						default: $this->order[$value] = true;
					}
				}
				usort($result, test_db_select_sort);
			}
			/*
			 * Учёт смещения
			 */
			if ($lim_offset) {
				$result = array_slice($result, $lim_offset);
			}
			/*
			 * Учёт ограничения по количеству
			 */
			if ($lim_rows) {
				$result = array_slice($result, 0, $lim_rows);
			}
			/*
			 * Выбор отдельных полей
			 */
			if ($fields) {

			}

		} else FatalError("Table '$tables' not found.");
		return $result;
	}
	//-----------------------------------------------------------------------------
	function fields($table)
	{
		$result = false;
		$filename = $this->tablename($table);
		if ($filename) {
			$rows = file($filename);
			$result = explode('|', array_shift($rows));
		} else FatalError("Table '$tables' not found.");
		return $result;
	}
	//-----------------------------------------------------------------------------
}
?>