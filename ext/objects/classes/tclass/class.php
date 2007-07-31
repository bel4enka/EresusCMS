<?php

class tclass {
	var $id= 'ID';
	var $name= 'VARCHAR(31)';

	var $ownerid= 'INT UNSIGNED';
	var $ownerclass= 'VARCHAR(31)';

	var $position= 'INT UNSIGNED';

	var $tmp= 'BOOL';
	var $tmp_created= 'INT UNSIGNED';


	function tclass()
	{
		//Если класс может иметь только один объект, определяем id этого объекта, если он уже создан
		if (array_key_exists('singleton_id', get_class_vars(get_class($this)))) {
			$values= $this->restore(null, null, null, null, null, 'id');
			if (!empty($values)) $this->singleton_id= $values['id'];
		}
	}

	/**
	 * Функция возвращает true, если класс содрежит только базовые свойства.
	 * Если объект класс имеет дочерние объекты, то возвращает false.
	 * Это используется при создании нового объекта класса:
	 * Если класс содержит только базовые свойства, то создание временного объекта не требуется.
	 *
	 * @return bool
	 */
	function simple()
	{
		$vars= get_class_vars(get_class($this));
		foreach ($vars as $varname=>$vartype) if (!isbasic($vartype)) return false;
		return true;
	}

	/**
	 * Функция возвращает ассоциативный массив базовых свойств объекта класса с идентификатором $id
	 * или если $id не указан, то с именем $name, id родителя $ownerid и классом родителя $ownerclass
	 * Фактически просто считывает мз таблицы в базе данных строчку с соответствующим условием.
	 *
	 * @param идентифиактор $id
	 * @return массив_базовых_свойств
	 */
	function restore($id, $name=null, $ownerid= null, $ownerclass=null, $usercond= null, $fields= null, $tmp=false)
	{
		$class= get_class($this);
		$vars= get_class_vars($class);

		$table= $GLOBALS['db']->prefix.$class;
		if (isset($id)) $whereclause= "`$table`.`id`='$id'";
		else {
			$whereclause= array();
			if (isset($name)) $whereclause[]= "(`$table`.`name`='$name')";
			if (isset($ownerid)) $whereclause[]= "(`$table`.`ownerid`='$ownerid')";
			if (isset($ownerclass)) $whereclause[]= "(`$table`.`ownerclass`='$ownerclass')";
			if (isset($usercond)) $whereclause[]= "($usercond)";
			$whereclause[]= "(`$table`.`tmp`='".($tmp? "1": "0")."')";
			$whereclause= implode(' AND ', $whereclause);
		}
		if (empty($fields)) $fields= "`$table`.*";

		$result= $GLOBALS['db']->query("SELECT $fields FROM `$table` WHERE $whereclause ORDER BY `$table`.`position` DESC");
		$row= mysql_fetch_assoc($result);

		$values= array();
		if (!empty($row)) foreach ($vars as $varname=>$vartype) if (isset($row[$varname]) && unsqlize($vartype, $row[$varname]))
			$values[$varname]= $row[$varname];

		return $values;
	}

	/**
	 * Функция загружает массив элементов из базы данных на основе $name, $ownerid, $ownerclass
	 *
	 * @param unknown_type $name
	 * @param unknown_type $ownerid
	 * @param unknown_type $ownerclass
	 * @return unknown
	 */
	function listrestore($name= null, $ownerid= null, $ownerclass= null, $sortfield='position', $desc=true, $offset= null, $limit= null, $usercond= null, $fields= null, $tmp= false)
	{
		$class= get_class($this);
		$vars= get_class_vars($class);

		$table= $GLOBALS['db']->prefix.$class;
		$whereclause= array();
		if (isset($name)) $whereclause[]= "(`$table`.`name`='$name')";
		if (isset($ownerid)) $whereclause[]= "(`$table`.`ownerid`='$ownerid')";
		if (isset($ownerclass)) $whereclause[]= "(`$table`.`ownerclass`='$ownerclass')";
		if (isset($usercond)) $whereclause[]= "($usercond)";
		$whereclause[]= "(`$table`.`tmp`='".($tmp? "1": "0")."')";
		$whereclause= implode(' AND ', $whereclause);
		$desc= $desc? 'DESC': '';
		$limit= isset($offset)
				?(isset($limit)? "LIMIT $offset,$limit":"LIMIT $offset,18446744073709551615")
				:(isset($limit)? "LIMIT $limit":'');

		if (empty($fields)) $fields= '*';

		$result= $GLOBALS['db']->query("SELECT $fields FROM `$table` WHERE $whereclause ORDER BY `$table`.`$sortfield` $desc $limit");

		$listvalues= array();
		while ($row= mysql_fetch_assoc($result)) {
			$values= array();
			if (!empty($row)) foreach ($vars as $varname=>$vartype) if (isset($row[$varname]) && unsqlize($vartype, $row[$varname]))
				$values[$varname]= $row[$varname];
			$listvalues[]= $values;
		}

		return $listvalues;
	}

	/**
	 * Функция возвращает число элементов массива на основе $name, $ownerid и $ownerclass
	 *
	 * @param unknown_type $name
	 * @param unknown_type $ownerid
	 * @param unknown_type $ownerclass
	 * @return unknown
	 */
	function count($name= null, $ownerid= null, $ownerclass= null, $usercond= null, $tmp=false)
	{
		$table= $GLOBALS['db']->prefix.get_class($this);
		$whereclause= array();
		if (isset($name)) $whereclause[]= "(`name`='$name')";
		if (isset($ownerid)) $whereclause[]= "(`ownerid`='$ownerid')";
		if (isset($ownerclass)) $whereclause[]= "(`ownerclass`='$ownerclass')";
		if (isset($usercond)) $whereclause[]= "($usercond)";
		$whereclause[]= "(`$table`.`tmp`='".($tmp? "1": "0")."')";
		$whereclause= implode(' AND ', $whereclause);

		$result= $GLOBALS['db']->query("SELECT COUNT(*) FROM `$table` WHERE $whereclause");
		list($count)= mysql_fetch_row($result);
		return $count;
	}

	/**
	 * Функция загружает одну "страницу" массива эелементов.
	 * Страница - $page. Число элементов на страницу - $itemsperpage
	 * При этом вычисляется $pages - общее число страниц, чтобы можно было его потом использовать
	 * при отрисовке переключателя страниц. Поэтому $pages передается как ссылка.
	 *
	 * @param unknown_type $name
	 * @param unknown_type $ownerid
	 * @param unknown_type $ownerclass
	 * @param unknown_type $sortfield
	 * @param unknown_type $desc
	 * @param unknown_type $page
	 * @param unknown_type $pages
	 * @param unknown_type $itemsperpage
	 * @return unknown
	 */
	function pagerestore($name= null, $ownerid= null, $ownerclass= null, $sortfield= 'position', $desc= true, &$page, &$pages, $itemsperpage= 0, $usercond= null, $fields= null, $tmp= false, $reversepaging= false)
	{
		if (empty($itemsperpage)) return $this->listrestore($name, $ownerid, $ownerclass, $sortfield, $desc, null, null, $usercond, $fields, $tmp);

		$count= $this->count($name, $ownerid, $ownerclass, $usercond, $tmp);
		$pages= ceil($count/$itemsperpage);
		if ($reversepaging) $page= isset($page)? ($pages-$page-1): 0;
		if ($page > $pages-1) $page= $pages-1;
		if ($page < 0) $page= 0;
		if ($pages < 2) return $this->listrestore($name, $ownerid, $ownerclass, $sortfield, $desc, null, null, $usercond, $fields, $tmp);

		$offset= $page*$itemsperpage;
		return $this->listrestore($name, $ownerid, $ownerclass, $sortfield, $desc, $offset, $itemsperpage, $usercond, $fields, $tmp);
	}

	/**
	 * Отрисовывает переключатель страниц (клиентская сторона)
	 *
	 * @param unknown_type $page
	 * @param unknown_type $pages
	 * @param unknown_type $template
	 */
	/*
	function pagesswitch($url, $page, $pages, $template= null)
	{
		if ($pages < 2) return '';

		// LOL here. admtemplates не подгружаются с клиентской стороны ...
		if (!isset($template)) $template=& $GLOBALS['admtemplates']['pagesswitch'];

		$result= '';
		for ($i= 0; $i < $pages; $i++)
			if ($i == $page) $result.= '<span class="selected">&nbsp;'.($i+1).'&nbsp;</span>';
			else $result.= '<a href="'.$url.($i?("p$i/"):'').'">&nbsp;'.($i+1).'&nbsp;</a>';
		return str_replace('$(pages)', $result, $template);
	}
	*/


	function pagesswitch($url, $page, $pages, $template= null, $atonce=5)
	{
		if ($pages < 2) return '';

		/*- LOL here. admtemplates не подгружаются с клиентской стороны ... -*/
		if (!isset($template)) $template=& $GLOBALS['admtemplates']['pagesswitch'];

		$left= ''; $right= '';
		if ($pages <= $atonce) { $l= 0; $r= $pages-1; }
		else {
			$l= $page - floor($atonce/2);
			$r= $l+$atonce-1;

			if ($l <= 0) { $l= 0; $r= $atonce-1; }
			else $left= '<a href="'.$url.'">&laquo;</a>';

			if ($r >= $pages-1)  { $r= $pages-1; $l= $pages-$atonce; }
			//else $right= '<a href="'.$url.'p'.($pages-1).'/">&raquo;</a>';
			else $right= '<a href="'.$url.'p0/">&raquo;</a>'; // <- обратный порядок страниц
		}

		$result= '';
		for ($i= $l; $i <= $r; $i++)
			if ($i == $page) $result.= '<span class="selected">&nbsp;'.($i+1).'&nbsp;</span>';
			else $result.= '<a href="'.$url.($i?("p".($pages-$i-1)."/"):'').'">&nbsp;'.($i+1).'&nbsp;</a>';

		$result= $left.$result.$right;

		return str_replace('$(pages)', $result, $template);
	}

	/**
	 * Отрисовывает содержимое на клентской стороне
	 *
	 * @param unknown_type $values
	 * @param unknown_type $params
	 * @param unknown_type $url
	 */
	function render(&$values, $settings= array())
	{

	}

}

?>