<?php
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
		$result = false;
		$filename = $this->tablename($tables);
		if ($filename) {
			$rows = file($filename);
			$fieldlist = explode('|', array_shift($rows));
			$result = array();
			for($i = 0; $i < count($rows); $i++) {
				$values = explode('|', $rows[$i]);
				$item = array();
				for($j = 0; $j < count($fieldlist); $j++) $item[$fieldlist[$j]] = $values[$j];
				if ($this->condition_check($condition, $item)) $result[] = $item;
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