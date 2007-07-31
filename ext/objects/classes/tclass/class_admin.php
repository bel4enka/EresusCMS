<?php

class tclass_admin {

	var $class;
	var $vars;

	var $templates= array(); //шаблоны
	var $itemsperpage= array(); //число элементов на странцу для дочерних объектов-массивов
	var $sortby= array(); //можно задать поле сортировки по умолчанию для дочерних объектов-массивов
	var $desc= array(); //и порядок сортировки по умолчанию
	var $usercond= array(); //пользовательские условия для загрузки массивов дочерних объектов

	function tclass_admin($class= null)
	{
		if (!isset($class)) $class= substr(get_class($this), 0, -6);

		$this->class= $class;
		loadclass($class);
		$this->vars= get_class_vars($class);
	}



	/**
	 * Функция сохраняет в базе данных элемент базовых свойств класса, переданных в $values
	 * Если $values['id'] не указан, элемент добавляется в таблицу и функция возвращает id добавленного
	 * элмента. Если $values['id'] указан, обновляет соответствующий элемент.
	 *
	 * @param массив_базовых_свойств $values
	 * @param id_владельца $ownerid
	 * @param класс_владельца $ownerclass
	 * @return id_добавленного_элемента
	 */
	function store(&$values)
	{
		$set= array();
		foreach ($this->vars as $varname=>$vartype) if (($varname != 'id') && isset($values[$varname]) && $this->storable($varname)) {
			$varvalue= $values[$varname];
			if (sqlize($vartype, $varvalue)) $set[]= "`$varname`='$varvalue'";
		}
		$set= implode(',', $set);

		$table= $GLOBALS['db']->prefix.$this->class;
		if (!isset($values['id'])) {
			$GLOBALS['db']->query("INSERT INTO `$table` SET $set");
			return $GLOBALS['db']->getInsertedID();
		} else {
			$GLOBALS['db']->query("UPDATE `$table` SET $set WHERE `id`='".$values['id']."'");
			return $values['id'];
		}
	}

	/**
	 * Возвращает текущее значение auto_increment
	 *
	 * @return auto_increment
	 */
	function next_auto_increment()
	{
		$table= $GLOBALS['db']->prefix.$this->class;
		$h= $GLOBALS['db']->query("SHOW TABLE STATUS LIKE '$table'");
		$status= mysql_fetch_assoc($h);
		return $status['Auto_increment'];
	}


	function up(&$values, $usercond= null)
	{
		$table= $GLOBALS['db']->prefix.$this->class;
		$whereclause= '(`name`=@name) AND (`ownerid`=@ownerid) AND (`ownerclass`=@ownerclass)';
		if (isset($usercond)) $whereclause.= " AND ($usercond)";
		$whereclause.= " AND (`tmp`='0')";

	  	$GLOBALS['db']->query("SELECT @name:=`name`,@ownerid:=`ownerid`,@ownerclass:=`ownerclass`,@position:=`position`  FROM `$table` WHERE (`id`='".$values['id']."')");
	  	$GLOBALS['db']->query("SELECT @maxpos:=MAX(`position`) FROM `$table` WHERE $whereclause");
  		$GLOBALS['db']->query("UPDATE `$table` SET `position`=@position WHERE (@position<@maxpos) AND (`position`=@position+1) AND $whereclause");
  		$GLOBALS['db']->query("UPDATE `$table` SET `position`=@position+1 WHERE (@position<@maxpos) AND (`id`='".$values['id']."')");
	}
	function down(&$values, $usercond= null)
	{
		$table= $GLOBALS['db']->prefix.$this->class;
		$whereclause= '(`name`=@name) AND (`ownerid`=@ownerid) AND (`ownerclass`=@ownerclass)';
		if (isset($usercond)) $whereclause.= "AND ($usercond)";
		$whereclause.= " AND (`tmp`='0')";

	  	$GLOBALS['db']->query("SELECT @name:=`name`,@ownerid:=`ownerid`,@ownerclass:=`ownerclass`,@position:=`position`  FROM `$table` WHERE (`id`='".$values['id']."')");
  		$GLOBALS['db']->query("UPDATE `$table` SET `position`=@position WHERE (@position>1) AND (`position`=@position-1) AND $whereclause");
  		$GLOBALS['db']->query("UPDATE `$table` SET `position`=@position-1 WHERE (@position>1) AND (`id`='".$values['id']."')");
	}
	function delete(&$values, $usercond= null, $owner= null)
	{
  		//Удаляем все дочерние объекты
		foreach ($this->vars as $varname => $vartype) if (!isintrinsic($varname) && !isbasic($vartype) && isclass($vartype))
		//Если переменная - массив дочерних объектов
		if (is_array($vartype)) {
			if (empty($vartype)) continue;
			$vartype= $vartype[0];
			$ctl=& loadclass($vartype);
			$adm=& loadclass($vartype.'_admin');

			$children= $ctl->listrestore($varname, $values['id'], $this->class);
			foreach ($children as $child) $adm->delete($child, null, $values);
		}
		//Если переменная дочерний объект
		else {
			$ctl=& loadclass($vartype);
			$adm=& loadclass($vartype.'_admin');

			$child= $ctl->restore(null, $varname, $values['id'], $this->class);
			if (!empty($child)) $adm->delete($child, null, $values);
		}

		//Удаляем сам объект
		$table= $GLOBALS['db']->prefix.$this->class;
		if (!$values['tmp']) {
			$whereclause= '(`name`=@name) AND (`ownerid`=@ownerid) AND (`ownerclass`=@ownerclass)';
			if (isset($usercond)) $whereclause.= "AND ($usercond)";
			$whereclause.= " AND (`tmp`='0')";

	  		$GLOBALS['db']->query("SELECT @name:=`name`,@ownerid:=`ownerid`,@ownerclass:=`ownerclass`,@position:=`position`  FROM `$table` WHERE (`id`='".$values['id']."')");
  			$GLOBALS['db']->query("DELETE FROM `$table` WHERE `id`='".$values['id']."'");
  			$GLOBALS['db']->query("UPDATE `$table` SET `position`=`position`-1 WHERE (`position`>@position) AND $whereclause");
		}
		else $GLOBALS['db']->query("DELETE FROM `$table` WHERE `id`='".$values['id']."'");
	}
	/**
	 * Изменяет значение булевской переменной $varname на противоположное
	 *
	 * @param unknown_type $values
	 * @param unknown_type $varname
	 */
	function toggle(&$values, $varname)
	{
		if (!isset($this->vars[$varname])) return false;

		$table= $GLOBALS['db']->prefix.$this->class;
	  	$GLOBALS['db']->query("UPDATE `$table` SET `$varname`= !`$varname` WHERE `id`='".$values['id']."'");
	  	$values[$varname]= !$values[$varname];
	  	return true;
	}

	/**
	 * Функция текущие обновляет базовые свойства класса $values значениями из $newvalues
	 * и сохраняет их в базе данных
	 *
	 * @param текущие_значения $values
	 * @param новые_значения $newvalues
	 * @return id_добавленного_элемента
	 */
	function update(&$values, &$newvalues, $usercond= null)
	{
		foreach ($this->vars as $varname=>$vartype)
			if (!isintrinsic($varname) && isbasic($vartype))
				if (isset($newvalues[$varname])) $values[$varname]= $newvalues[$varname];
				//elseif (($vartype == 'BOOL') || ($vartype == 'BOOLEAN')) $values[$varname]= '';

		return $this->store($values);
	}

	/**
	 * Функция создает новый объект с именем name, владельцем $ownerid и $ownerclass,
	 * с значениями базовых свойств $newvalues и сохраняет его в базе данных.
	 * Возвращает id добавленного элемента.
	 * Свойство position вычисляется как максималное из всех элементов с параметрами $name, $ownerid, $ownerclass
	 * плюс один.
	 *
	 * @param unknown_type $name
	 * @param unknown_type $ownerid
	 * @param unknown_type $ownerclass
	 * @param unknown_type $newvalues
	 * @return unknown
	 */
	function create(&$values, &$newvalues, $usercond= null, $tmp= false)
	{
		//Если класс простой и объект создается сразу (без времменного состояния), то вычисляем
		//новое значение для поля position сразу.
		//Если сначала создается временный объект, то значение для position будет вычисленно
		//при вызове функции функции create второй раз (после нажатия ОК или Применить).
		//Тогда объект переводится из временного состояния в созданный
		$values['position']= $tmp? 0: $this->maxposition($values['name'], $values['ownerid'], $values['ownerclass'], $usercond);

		if ($tmp) {
			$values['tmp']= 1;
			$values['tmp_created']= time();
			$this->cleanup();
		}
		else $values['tmp']= 0;

		foreach ($this->vars as $varname=>$vartype)
			if (!isintrinsic($varname) && isbasic($vartype))
				if (isset($newvalues[$varname])) $values[$varname]= $newvalues[$varname];
				//elseif (($vartype == 'BOOL') || ($vartype == 'BOOLEAN')) $values[$varname]= '';

		return $this->store($values);
	}

	/**
	 * Функция удаляет устаревшие временные объекты
	 *
	 */
	function cleanup()
	{
		$ctl=& loadclass($this->class);
		$expires= time()-30*60; //30 минут
		$garbage= $ctl->listrestore(null, null, null, 'position', true, null, null, "`tmp_created` < '$expires'", null, 1);
		foreach ($garbage as $junk) $this->delete($junk, null, array());
	}

	/**
	 * Определяет значение для поля position как максимальное из имеющихся +1 для нового добавляемого
	 * объекта
	 *
	 * @param unknown_type $name
	 * @param unknown_type $ownerid
	 * @param unknown_type $ownerclass
	 * @param unknown_type $usercond
	 * @return position
	 */
	function maxposition($name, $ownerid, $ownerclass, $usercond= null)
	{
		$table= $GLOBALS['db']->prefix.$this->class;
		$whereclause= "(`name`='$name') AND (`ownerid`='$ownerid') AND (`ownerclass`='$ownerclass')";
		if (isset($usercond)) $whereclause.= " AND ($usercond)";
		$whereclause.=" AND (`tmp`='0')";

		$result= $GLOBALS['db']->query("SELECT MAX(position) FROM `$table` WHERE $whereclause");
		list($position)= mysql_fetch_row($result);
		return ++$position;
	}


	/**
	 * Функции устанавливают значения по умолчанию для базовых свойств
	 *
	 * @param unknown_type $values
	 */
	function defaults($values=array())
	{
		$vars= $this->vars;
		foreach ($vars as $varname=>$vartype) if (!isintrinsic($varname))
			$values[$varname]= default_value($vartype);
		return $values;
	}

	function goback(&$values)
	{
		goto(backurl());
	}

	function goself(&$values)
	{
		if (!empty($values['id'])) goto(selfurl(array('id'=>$values['id'], 'action__'=>(isset($_GET['action__'])? $_GET['action__']: ''))));
	}


	/**
	 * Функция создает таблицу в базе данных для хранения базовых свойств класса
	 *
	 */
	function createsqltable()
	{
		$cols= array();
		foreach ($this->vars as $varname=>$vartype) if ((sqlize($vartype, $tmp) && $this->storable($varname)))
			$cols[]= "`$varname` $vartype";
		$cols[]= 'PRIMARY KEY (`id`)';
		$cols[]= 'KEY (`name`, `ownerid`, `ownerclass`)';
		$cols= implode(',', $cols);

		$table= $GLOBALS['db']->prefix.$this->class;
		$GLOBALS['db']->query("CREATE TABLE IF NOT EXISTS `$table` ($cols) ENGINE=MYISAM");
	}

	/**
	 * Функция инсталлирует класс, создавая соответствующие таблицы в базе данных
	 *
	 */
	function install()
	{
		$this->createsqltable();
	}

	/**
	 * Если базовое свойство $varname класса не надо сохранать в базе данных,
	 * функция дожна возвращать false
	 *
	 * @param unknown_type $varname
	 * @return unknown
	 */
	function storable($varname) { return true; }

	/**
	 * Функция возвращает описание переменной класса с именем $varname
	 * Если $varname=null то возвращает описание класса
	 *
	 * @param unknown_type $varname
	 * @return unknown
	 */
	function describe($varname=null)
	{
		if (!isset($varname)) return 'Класс';
		switch ($varname) {
			case 'id': return 'Идентификатор объекта';
			case 'name': return 'Имя объекта';
			case 'ownerid': return 'Идентификатор владельца';
			case 'ownerclass': return 'Класс владельца';
			case 'position': return 'Позиция';
			default: return $varname;
		}
	}

	/**
	 * Функция возвращает форму редактирования базовых свойств класса
	 * Форма представляет собой следующий ассоциативный массив:
	 *   name - имя формы (желательно уникальное. В общем случае на странице может быть несколько форм)
	 *   caption - заголовок формы.
	 *   buttons - массив. значениями могут быть ok, apply или back - соответствующие кнопки
	 *   fields - поля формы.
	 * Поле формы представляет собой следующий ассоциативный массив:
	 *   type - тип поля редактирования. edit, memo, html, checkbox, select, listbox.
	 *          custom - пользовательский тип. Означает, что будет вызвана функция formfield у класса,
	 *          который вызывет функцию form(). При этоп в vartype передается реальный тип переменной
	 *   label - понятно
	 *   comment - ясно
	 *   и прочая хуйня типа width, height и т.д.
	 *
	 * @param unknown_type $values
	 * @return unknown
	 */
	function form(&$values)
	{
		if (!isset($values['id'])) $values= $this->defaults($values);
		if (isset($values['id'])) $caption= 'Редактирование <span style="color: orange">'.$values['name'].'</span> типа <span style="color: yellow">'.$this->class.'</span> (id:'.$values['id'].')';
		else $caption= 'Создание <span style="color: orange">'.$values['name'].'</span> типа <span style="color: yellow">'.$this->class.'</span>';
		$form= array(
			'name' => "editform_".$this->class.(isset($values['id'])?$values['id']:'new'),
			'caption' => $caption,
			'width' => '70%',
			'buttons' => array('ok', 'apply', 'back')
		);

		$fields= array();
		foreach ($this->vars as $varname => $vartype) if (!isintrinsic($varname) && $this->storable($varname))
		switch ($vartype) {
			case 'ID':
				$fields[$varname]= array('type'=>'edit', 'name'=>$varname, 'label'=>$this->describe($varname)); break;
			case 'BOOL': case 'BOOLEAN':
				$fields[$varname]= array('type'=>'checkbox', 'name'=>$varname, 'label'=>$this->describe($varname)); break;
			case 'UNSIGNED INT': case 'INT UNSIGNED': case 'UNSIGNED TINYINT': case 'TINYINT UNSIGNED': case 'UNSIGNED SMALLINT': case 'SMALLINT UNSIGNED':
				$fields[$varname]= array('type'=>'edit', 'name'=>$varname, 'label'=>$this->describe($varname), 'pattern'=>'/^\s*\d+\s*$/', 'errormsg'=>'Значение поля "'.$this->describe($varname).'" не является целым беззнаковым числом!'); break;
			case 'INT': case 'TINYINT': case 'SMALLINT':
				$fields[$varname]= array('type'=>'edit', 'name'=>$varname, 'label'=>$this->describe($varname), 'pattern'=>'/^\s*-?\d+\s*$/', 'errormsg'=>'Значение поля "'.$this->describe($varname).'" не является целым числом!'); break;
			case 'FLOAT': case 'DOUBLE':
				$fields[$varname]= array('type'=>'edit', 'name'=>$varname, 'label'=>$this->describe($varname), 'pattern'=>'/^\s*-?\d+(\.\d+)?\s*$/', 'errormsg'=>'Значение поля "'.$this->describe($varname).'" не является числом с плавающей точкой!'); break;
			case 'PASSWORD':
				$fields[$varname]= array('type'=>'password', 'name'=>$varname, 'label'=>$this->describe($varname), 'width'=>'90%', 'maxlength'=>31); break;
			case 'VARCHAR(31)':
				$fields[$varname]= array('type'=>'edit', 'name'=>$varname, 'label'=>$this->describe($varname), 'width'=>'90%', 'maxlength'=>31); break;
			case 'VARCHAR(63)':
				$fields[$varname]= array('type'=>'edit', 'name'=>$varname, 'label'=>$this->describe($varname), 'width'=>'90%', 'maxlength'=>63); break;
			case 'VARCHAR(127)':
				$fields[$varname]= array('type'=>'edit', 'name'=>$varname, 'label'=>$this->describe($varname), 'width'=>'90%', 'maxlength'=>127); break;
			case 'VARCHAR(255)':
				$fields[$varname]= array('type'=>'edit', 'name'=>$varname, 'label'=>$this->describe($varname), 'width'=>'90%', 'maxlength'=>255); break;
			case 'TEXT':
				$fields[$varname]= array('type'=>'memo', 'name'=>$varname, 'label'=>$this->describe($varname), 'height'=>8); break;
			case 'HTML':
				$fields[$varname]= array('type'=>'html', 'name'=>$varname, 'label'=>$this->describe($varname), 'height'=>'400px'); break;
			case 'DATE':
				$fields[$varname]= array('type'=>'edit', 'name'=>$varname, 'label'=>$this->describe($varname), 'width'=>'30%', 'maxlength'=>10, 'pattern'=>'/^\d\d\d\d-\d\d-\d\d$/', 'errormsg'=>$this->describe($varname).' имеет неправильный формат даты. Формат должен быть YYYY-MM-DD!'); break;
			case 'DATETIME':
				$fields[$varname]= array('type'=>'edit', 'name'=>$varname, 'label'=>$this->describe($varname), 'width'=>'30%', 'maxlength'=>19, 'pattern'=>'/^\d\d\d\d-\d\d-\d\d \d\d:\d\d:\d\d$/', 'errormsg'=>$this->describe($varname).' имеет неправильный формат даты/времени. Формат должен быть YYYY-MM-DD HH:MM:SS!'); break;
			default:
				if (!is_array($vartype) && !isclass($vartype))
					$fields[$varname]= array('type'=>'custom', 'name'=>$varname, 'vartype'=>$vartype);
		}
		$form['fields']= $fields;
		return $form;
	}

	//Нахуй ваще эта функция?
	function formfield(&$form, &$values) { return; }

	/**
	 * Функция отрисовывает элементы редактирования класса
	 *
	 * @param массив_базовых_свойств $values
	 * @return форма
	 */
	function render(&$values, $action=null)
	{
		//Обработка ajax-запросов
		if (isset($_GET['ajax'])) {
			$childname= strtok($_GET['ajax_id'], '_');
			XMLAjaxResponse('redraw', $_GET['ajax_id'], $this->renderchild($childname, null, $values));
		}

		if (!isset($values['id']) || ($action == 'edit')) return form($this->form($values), $values, $this->class);

		$template= isset($this->templates[0])?$this->templates[0]:$GLOBALS['admtemplates']['template'];

		$layout= array('objects'=>'');
		if (strpos($template, '$(form)') !== false) $layout['form']= form($this->form($values), $values, $this->class);

		if (isset($values['id'])) $this->renderchildren($layout, $values);
		$layout['backref']= backurl();

		if ($values['ownerclass'] != 'root') $layout['ownerref']=objecturl($values['ownerid'], $values['ownerclass'], null, null, null, false);
		else $layout['ownerref']= '';

		if (isset($values['id'])) {
			$layout['viewref']= objecturl($values['id'], $this->class);
			$layout['editref']= objecturl($values['id'], $this->class).'&action__=edit';
		}

		$template= rm($template, $values);

		return rm($template, $layout);
	}

	/**
	 * Функция отрисовывает html представления дочерних объектов
	 *
	 * @param unknown_type $html
	 * @param unknown_type $values
	 */
	function renderchildren(&$layout, &$values)
	{
		foreach ($this->vars as $varname => $vartype) if (!isintrinsic($varname) && !isbasic($vartype)) {
			//ajax-wrapper
			$layout[$varname]=
				'<div id="'.$varname.'_'.$this->class.'_'.$values['id'].'">'.
					$this->renderchild($varname, $vartype, $values).
				'</div>';
			$layout['objects'].= $layout[$varname];
		}
	}

	/**
	 * Отрисовывает дочерний объект
	 *
	 * @param unknown_type $layout
	 * @param unknown_type $values
	 */
	function renderchild($varname, $vartype= null, &$values)
	{
		if (!isset($vartype)) $vartype= $this->vars[$varname];

		//Если переменная - массив дочерних объектов, отрисовываем список объектов с помощью
		// thumbslist дочернего класса
		if (is_array($vartype)) {
			if (empty($vartype)) return;
			$vartype= $vartype[0];
			$ctl=& loadclass($vartype);
			$adm=& loadclass($vartype.'_admin');

			$uid= $varname.'_'.$this->class.'_'.$values['id'];
			if (isset($_GET["s-$uid"])) $sortby= $_GET["s-$uid"];
			else $sortby= isset($this->sortby[$varname])? $this->sortby[$varname]: 'position';

			if (isset($_GET["d-$uid"])) $desc= ($_GET["d-$uid"])? 1: 0;
			else $desc= isset($this->desc[$varname])? $this->desc[$varname]: 1;
			$_GET["d-$uid"]= $desc;

			$itemsperpage= isset($this->itemsperpage[$varname])?$this->itemsperpage[$varname]:0;
			$page= isset($_GET["p-$uid"])?$_GET["p-$uid"]:0;
			$pages= 0;
			$usercond= isset($this->usercond[$varname])?$this->usercond[$varname]:null;
			$childvalues= $ctl->pagerestore($varname, $values['id'], $this->class, $sortby, $desc, $page, $pages, $itemsperpage, $usercond);

			$thumbslist_tmpl= /*isset($this->templates[$varname]['thumbslist'])?$this->templates[$varname]['thumbslist']:*/null;
			$listthumb_tmpl= /*isset($this->templates[$varname]['listthumb'])?$this->templates[$varname]['listthumb']:*/null;
			$sortbar_tmpl= /*isset($this->templates[$varname]['sortbar'])?$this->templates[$varname]['sortbar']:*/null;

			return $adm->thumbslist($varname, $values['id'], $this->class, $childvalues, $thumbslist_tmpl, $listthumb_tmpl, $sortbar_tmpl, $page, $pages);
		}
		//Если переменная дочерний объект - отрисовываем с помощью thumb дочернего класса
		elseif (isclass($vartype)) {
			$ctl=& loadclass($vartype);
			$adm=& loadclass($vartype.'_admin');

			$childvalues= $ctl->restore(null, $varname, $values['id'], $this->class);
			if (empty($childvalues)) $childvalues= array('name'=>$varname, 'ownerid'=>$values['id'], 'ownerclass'=>$this->class);

			return $adm->thumb($childvalues);
		}
	}

	/**
	 * Функция возвращает html представление элемента, которое будет использоваться
	 * в форме редактирования родителя на основе шаблона $template.
	 *
	 * @param unknown_type $values
	 * @param unknown_type $template
	 * @return unknown
	 */
	function thumb(&$values, $template=null)
	{
		if (!isset($template))
			if (isset($this->templates['thumb'])) $template=& $this->templates['thumb'];
		 	else $template=& $GLOBALS['admtemplates']['thumb'];

		$caption= describe($values['ownerclass'], $values['name']);

		if (isset($values['id'])) {
			$values['viewref']= objecturl($values['id'], $this->class);
			$values['viewref_nh']= objecturl($values['id'], $this->class, null, null, null, false);
			$values['editref']= objecturl($values['id'], $this->class).'&action__=edit';
			$values['backref']= backurl();

			$uid= $values['name'].'_'.$values['ownerclass'].'_'.$values['ownerid'];
			if (isset($_GET["s-$uid"])) $sortby= $_GET["s-$uid"]; else $sortby= 'position';
			if (isset($_GET["d-$uid"]) && ($_GET["d-$uid"] == 0)) $desc= 0; else $desc= 1;

			//ajax идентификатор
			$values['ajax_id']= $uid;

			//Для кнопок перемещения сбрасываем сортировку, чтобы при нажатии вверх или вниз
			//выставлялась сортировка по положению
			if ($sortby == 'position') $nosorturl= selfurl(array("s-$uid"=>''));
			else $nosorturl= selfurl(array("s-$uid"=>'',"d-$uid"=>''));

			if ($desc) {
				$values['upref']= objecturl($values['id'],$this->class, null, null, null, true, $nosorturl).'&action__=up';
				$values['downref']= objecturl($values['id'],$this->class, null, null, null, true, $nosorturl).'&action__=down';
			} else {
				$values['downref']= objecturl($values['id'],$this->class, null, null, null, true, $nosorturl).'&action__=up';
				$values['upref']= objecturl($values['id'],$this->class, null, null, null, true, $nosorturl).'&action__=down';
			}

			$values['delref']= $values['viewref'].'&action__=delete';
			$values['edit']= $caption;
		} else {
			$values['editref']= objecturl(null, $this->class, $values['name'], $values['ownerid'], $values['ownerclass']);
			$values['viewref']= objecturl(null, $this->class, $values['name'], $values['ownerid'], $values['ownerclass']);
			$values['edit']= "Создать $caption";
		}


		//Отрисовка галочек для булевских переменных
		if (strpos($template, '$(toggle:') !== false)
			return rm($this->toggles($values, $template), $values);

		return rm($template, $values, $this->class);
	}

	/**
	 * Функция возвращает html представление списка из элементов класса на основе шаблонов
	 *   списка - $listtmpl, элемента списка - $itemtmpl
	 * По умолчанию шаблон $listtmpl - кнопка добавить и список элементов.
	 * Шаблн $itemtmpl - иконка, кнопки переместить вверх, вниз, редактировать, удалить.
	 * см. admtemplates.inc
	 *
	 * @param unknown_type $name
	 * @param unknown_type $ownerid
	 * @param unknown_type $ownerclass
	 * @param unknown_type $valueslist
	 * @param unknown_type $listtmpl
	 * @param unknown_type $itemtmpl
	 * @return unknown
	 */
	function thumbslist($name, $ownerid, $ownerclass, &$valueslist, $listtemplate=null, $itemtemplate=null, $sortbartemplate=null, $page=0, $pages=0)
	{
		if (!isset($listtemplate))
			if (isset($this->templates['thumbslist'])) $listtemplate=& $this->templates['thumbslist'];
			else $listtemplate=& $GLOBALS['admtemplates']['thumbslist'];

		if (!isset($itemtemplate))
			if (isset($this->templates['listthumb'])) $itemtemplate=& $this->templates['listthumb'];
			else $itemtemplate=& $GLOBALS['admtemplates']['listthumb'];

		if (!isset($sortbartemplate))
			if (isset($this->templates['sortbar'])) $sortbartemplate=& $this->templates['sortbar'];
		 	else $sortbartemplate= null;

		$item= array();
		$item['caption']= describe($ownerclass, $name);
		$item['classcaption']= $this->describe();
		$item['addref']= objecturl(null, $this->class, $name, $ownerid, $ownerclass);
		/*do we need this?*/
		if (strpos($listtemplate, '$(rootaddref)') !== false) {
			list($rootid, $rootclass)= root_owner($ownerid, $ownerclass);
			$item['rootaddref']= objecturl(null, $this->class, $name, $rootid, $rootclass);
		}
		$item['backref']= backurl();
		$item['sortbar']= $this->sortbar($sortbartemplate, $name, $ownerid, $ownerclass);
		$item['pagesswitch']= $this->pagesswitch($name, $ownerid, $ownerclass, $page, $pages);
		$item['items']= '';
		//ajax идентификатор
		$item['ajax_id']= $name.'_'.$ownerclass.'_'.$ownerid;
		foreach ($valueslist as $values) $item['items'].= $this->thumb($values, $itemtemplate);

		return rm($listtemplate, $item, $this->class);
	}

	/**
	 * Отрисовывает шаблон с учетом специфики тулбара сортировки.
	 * Короче бля просто заменяет $(sort:name) на соответсвующую надпись с картинкой, которая
	 * подсвечена или нет в зависимости от того, че в $_GET
	 *
	 * @param unknown_type $template
	 * @param unknown_type $name
	 * @param unknown_type $ownerid
	 * @param unknown_type $ownerclass
	 * @return unknown
	 */
	function sortbar($template=null, $name, $ownerid, $ownerclass)
	{
		if (!isset($template)) return '';

		$sortby= 's-'.$name.'_'.$ownerclass.'_'.$ownerid;
		$highlight= isset($_GET[$sortby])?$_GET[$sortby]:'position';

		$descby= 'd-'.$name.'_'.$ownerclass.'_'.$ownerid;
		if (isset($_GET[$descby]) && ($_GET[$descby] == 0)) $desc= 'asc';
		else $desc= 'desc';

		foreach ($this->vars as $varname=>$vartype) {
			if (($varname == $highlight) && ($desc == 'desc'))
				$template= str_replace("$(sortref:$varname)", selfurl(array($sortby=>$varname,$descby=>0)), $template);
			else
				$template= str_replace("$(sortref:$varname)", selfurl(array($sortby=>$varname,$descby=>1)), $template);

			if ($varname == $highlight) $template= str_replace("$(highlight:$varname)", "highlight$desc", $template);
			else $template= str_replace("$(highlight:$varname)", 'nohighlight', $template);
		}
		return $template;
	}

	/**
	 * Отрисовывает переключатель страниц
	 *
	 * @param unknown_type $page
	 * @param unknown_type $pages
	 * @param unknown_type $template
	 */
	function pagesswitch($name, $ownerid, $ownerclass, $page, $pages, $template= null)
	{
		if ($pages < 2) return '';

		$uid= $name.'_'.$ownerclass.'_'.$ownerid;
		if (!isset($template)) $template=& $GLOBALS['admtemplates']['pagesswitch'];
		$result= '';
		for ($i= 0; $i < $pages; $i++)
			if ($i == $page) $result.= '<span class="selected">&nbsp;'.($i+1).'&nbsp;</span>';
			else $result.= '<a href="'.selfurl(array("p-$uid"=>($i?$i:''))).'">&nbsp;'.($i+1).'&nbsp;</a>';
		return str_replace('$(pages)', $result, $template);
	}

	/**
	 * Вместо $(toggle:varname) отрисовывает галочку для переключения varname
	 *
	 * @param unknown_type $values
	 * @param unknown_type $template
	 */
	function toggles(&$values, $template)
	{
		$id= $values['id'];
		foreach ($this->vars as $varname=>$vartype) if (($vartype == 'BOOL') || ($vartype == 'BOOLEAN')) {
			if ($values[$varname])
				$toggle_tmpl= str_replace('$(toggleref)', objecturl($values['id'], $this->class)."&toggle=$varname", $GLOBALS['admtemplates']['toggled_on']);
			else
				$toggle_tmpl= str_replace('$(toggleref)', objecturl($values['id'], $this->class)."&toggle=$varname", $GLOBALS['admtemplates']['toggled_off']);
			$toggle_tmpl= str_replace('$(ajax_id)', $varname.'_'.$this->class.'_'.$id, $toggle_tmpl);
			$template= str_replace("$(toggle:$varname)", $toggle_tmpl, $template);
		}
		return $template;
	}

}//End of TClass_admin



//***************** Admin UI *************************************************************************
/*
function box($content, $style='clear: none;')
{
	return '<div style="'.$style.'">
<div class="objectbox">
<div class="left"><div class="right"><div class="top"><div class="bottom"><div class="topleft"><div class="topright"><div class="bottomleft"><div class="bottomright"><div class="inner">'.
$content.
'</div></div></div></div></div></div></div></div></div></div>
';
}

function window($caption, $body, $style= 'clear: none;')
{
	$content= null;
	if ($caption != '') $content.= '<div class="header">'.$caption.'</div>';
	$content.= '<div class="content">'.$body.'</div>';
	return box($content, $style);
}
*/
/*
function button($caption, $url, $type=null)
{
	switch ($type) {
		case 'edit': $aclass= 'class="edit"'; break;
		default: $aclass= null;
	}

	$button='
<div class="objectbutton"><div class="left"><div class="right"><div class="inner">'.
'	<a href="'.$url.'" '.$aclass.'>'.$caption.'</a>'.
'</div></div></div></div>
';

	return $button;
}
*/
/*
function parambutton($caption, $url, $params)
{
	$form= '<form action="'.$url.'" method="post">';
	foreach ($params as $k=>$v) $form.= '<input type="hidden" name="'.$k.'" value="'.$v.'" />';
	$form.= '<button type="submit">'.$caption.'</button>';
	return $form;
}
*/

function formfield(&$field, &$form)
{
	if ($field['type'] == 'divider') { $form['html'].= '<tr><td colspan="2"><hr class="admFormDivider"></td></tr>'."\n"; return; }
	if ($field['type'] == 'text') { $form['html'].= '<tr><td colspan="2" class="admFormText">'.$field['value']."</td></tr>\n"; return; }
	if ($field['type'] == 'header') { $form['html'].= '<tr><th colspan="2" class="admFormHeader">'.$field['value']."</th></tr>\n"; return; }

	if (isset($field['label'])) $label = isset($field['hint']) ? '<span class="hint" title="'.$field['hint'].'">'.$field['label'].'</span>': $field['label']; else $label = '';
	if (isset($field['validator'])) $form['validator'].= $field['validator'];
	elseif (isset($field['patterns'])) foreach ($field['patterns'] as $pattern) $form['validator'] .= "if (!document.".$form['name'].".".$field['name'].".value.match(".$pattern.")) {\nalert('".(empty($field['errormsg'])?sprintf(errFormPatternError, $field['name'], $pattern):$field['errormsg'])."');\n return false;\n}\n";
	elseif (isset($field['pattern'])) $form['validator'] .= "if (!document.".$form['name'].".".$field['name'].".value.match(".$field['pattern'].")) {\nalert('".(empty($field['errormsg'])?sprintf(errFormPatternError, $field['name'], $field['pattern']):$field['errormsg'])."');\n return false;\n}\n";

   if (isset($field['purehtml'])) { $form['html'].= $field['purehtml']; return; }

	$width = isset($field['width'])? $field['width']: 'auto';
	$height = isset($field['height'])? $field['height']: 'auto';
	$comment = isset($field['comment'])?' '.$field['comment']:'';
	$disabled = isset($field['disabled']) && $field['disabled']?' disabled':'';
	$extra = isset($field['extra'])?' '.$field['extra']:'';
	$extrastyle= isset($field['style'])? $field['style']: '';

	switch ($field['type']) {
		case 'hidden': $form['hiddenhtml'].= '<div class="admHidden"><input type="hidden" name="'.$field['name'].'" value="'.$field['value'].'"></div>'."\n"; return;
		case 'edit': $form['html'].= '<tr><td class="admFormLabel">'.$label.'</td><td style="width: 100%;"><input type="text" name="'.$field['name'].'" value="'.EncodeHTML($field['value']).'"'.(empty($field['maxlength'])?'':' maxlength="'.$field['maxlength'].'"')."style=\"width: $width; height: $height; $extrastyle\"".$disabled.$extra.'>'.$comment."</td></tr>\n"; return;
		case 'password': $form['html'].= '<tr><td class="admFormLabel">'.$label.'</td><td style="width: 100%;"><input type="password" name="'.$field['name'].'" value="'.EncodeHTML($field['value']).'"'.(empty($field['maxlength'])?'':' maxlength="'.$field['maxlength'].'"')."style=\"width: $width; height: $height; $extrastyle\"".$disabled.$extra.'>'.$comment."</td></tr>\n"; return;
		//Возможность выбора даты с помощью всплывающего календарика
		case 'date':
			if (strpos($GLOBALS['page']->headlinks, '--jsCalendar--') === false) {
				$GLOBALS['page']->headlinks.= '
					<!-- --jsCalendar-- -->
					<link rel="stylesheet" href="'.httpRoot.'ext/objects/jscalendar/css/calendar-eresus.css" />
					<script type="text/javascript" src="'.httpRoot.'ext/objects/jscalendar/calendar.js"></script>
					<script type="text/javascript" src="'.httpRoot.'ext/objects/jscalendar/lang/calendar-ru.js"></script>
					<script type="text/javascript" src="'.httpRoot.'ext/objects/jscalendar/calendar-setup.js"></script>
				';
			}
			$cal_id= $form['name'].'_'.$field['name'];
			$form['html'].= '
				<tr><td class="admFormLabel">'.$label.'</td><td style="width: 100%;">
					<input type="text" id="field_'.$cal_id.'" name="'.$field['name'].'" value="'.EncodeHTML($field['value']).'"'.(empty($field['maxlength'])?'':' maxlength="'.$field['maxlength'].'"')."style=\"width: $width; height: $height;\"".$disabled.$extra.'>'.
//					'<button id="caltrig_'.$cal_id.'">...</button>'.
					'&nbsp;<img id="caltrig_'.$cal_id.'" src="'.httpRoot.'ext/objects/jscalendar/css/img2.gif" style="vertical-align: top; cursor: pointer;" />'.
					$comment."</td></tr>\n";
			$form['after'].= '
				<script type="text/javascript">
					Calendar.setup({
						inputField: "field_'.$cal_id.'",
						ifFormat: "%Y-%m-%d",
						button: "caltrig_'.$cal_id.'"
					});
				</script>
			';
			return;

		case 'radioselect':
			$form['html'].= '<tr><td class="admFormLabel">'.$label.'</td><td style="width: 100%;">'."\n";
			foreach ($field['items'] as $k=>$v)
            	$form['html'].= '<input class="radio" type="radio" name="'.$field['name'].'" value="'.$k.'" '.($k == $field['value']?'checked':'').' />'.$v."\n";
			$form['html'].= "</td></tr>\n";
			return;
		case 'select':
            $form['html'].= '<tr><td class="admFormLabel">'.$label.'</td><td><select name="'.$field['name'].'" style="width:'.$width.'"'. $disabled.$extra.'>'."\n";
            foreach ($field['items'] as $k=>$v)
            	$form['html'].= '<option value="'.$k.'" '.($k == $field['value']?'selected':'').'>'.$v."</option>\n";
            $form['html'].= '</select>'.$comment."</td></tr>\n";
            return;
      //Выбор производится с помощью списка checkbox'ов
		case 'checkselect':
            $form['html'].= '<tr><td class="admFormLabel">'.$label.'</td><td><div class="ListBox">'."\n";
            foreach ($field['items'] as $k=>$l) {
            	$v= !empty($field['value'][$k]);
            	$form['html'].= '
            		<div>
            			<input type="hidden" name="'.$field['name'].'['.$k.']" value="0">
            			<input type="checkbox" name="'.$field['name'].'['.$k.']" value="1" '.($v? 'checked': '').'>'.$l."</input>
            		</div>\n";
            }
            $form['html'].= '</div>'.$comment."</td></tr>\n";
            return;
		case 'checkbox': $form['html'].= '<tr><td>&nbsp;</td><td><input type="hidden" name="'.$field['name'].'" value="0" /><input type="checkbox" name="'.$field['name'].'" value="'.($field['value'] ? $field['value'] : true).'" '.($field['value'] ? 'checked' : '').$disabled.$extra.' style="background-color: transparent; border-style: none; margin:0px; '.$extrastyle.'"><span style="vertical-align: baseline"> '.$label."</span></td></tr>\n"; return;
		//memo - выводится textarea
		case 'memo': $form['html'].= '<tr><td colspan="2">'.(empty($label)?'':'<span class="admFormLabel">'.$label.'</span><br />').'<textarea name="'.$field['name'].'" cols="1" rows="'.(empty($field['height'])?'1':$field['height']).'" '.$disabled.$extra.' style="width: 100%;">'.EncodeHTML($field['value'])."</textarea></td></tr>\n"; return;
		//html - wysiwyg редактор
		case 'html':
            $form['html'].= '<tr><td colspan="2">'.$label.'<br /><textarea name="wyswyg_'.$field['name'].'" id="wyswyg_'.$field['name'].'" style="width: 100%; height: '.$height.';">'.str_replace('$(httpRoot)', httpRoot, EncodeHTML($field['value'])).'</textarea></td></tr>'."\n";
            $GLOBALS['page']->htmlEditors[] = array('id'=>'wyswyg_'.$field['name'], 'bodyId'=>isset($field['bodyId'])? $field['bodyId']: 'Content');
            return;
		case 'file':
			$form['file']= true;
            $form['html'].= '<tr><td class="admFormLabel">'.$label.'</td><td><input type="file" name="'.$field['name'].'" style="width:'.$width.'" '.$disabled.'>'.$comment."</td></tr>\n";
			return;
	}
	return;
}

function form($form, $values=array(), $class=null)
{

	//$form['name']= 'form'.mt_rand();
	$form['validator']= null;
	$form['file']= false;
	$form['after']= '';

	if (!isset($form['html'])) $form['html']= null;
	if (!isset($form['hiddenhtml'])) $form['hiddenhtml']= null;

	if (!empty($form['fields'])) foreach($form['fields'] as $field) {
		if (isset($field['name']) && !isset($field['value']))
			if (isset($values[$field['name']])) $field['value']= $values[$field['name']];
			elseif (isset($field['default'])) $field['value']= $field['default'];
			else $field['value']= null;
		//если тип поля custom, то вызываем функцию formfield у соответствующего класса
		if (($field['type'] == 'custom') && isclass($class.'_admin')) {
			$adm=& loadclass($class.'_admin');
			$adm->formfield($field, $form);
		}
		else formfield($field, $form);
	}
	$GLOBALS['page']->scripts.= 'function '.$form['name'].'Submit() {'.$form['validator'].'; return true; }';

	$form['html']= '<form class="admForm" id="'.$form['name'].'" name="'.$form['name'].'" method="post" onsubmit="return '.$form['name'].'Submit()" action="" '.($form['file']?' enctype="multipart/form-data"':'').'>'."\n".
					$form['hiddenhtml'].
					'<table style="width: 100%">'."\n".
					$form['html'].
 			        '<tr><td colspan="2" style="text-align: center;"><br />'.
 			        '<input name="action__" value="OK" type="hidden" />'.
					(isset($form['buttons_html'])? $form['buttons_html']: '').
					((in_array('ok', $form['buttons']))?'<input type="submit" class="button" value="OK"/> ':'').
					((in_array('apply', $form['buttons']))?'<input type="submit" class="button" value="Применить" onclick="document.'.$form['name'].'.action__.value=\'Apply\'" /> ':'').
					((in_array('back', $form['buttons']))?'<a class="button" href="'.backurl().'">Назад</a>':'').
					"</td></tr></table>\n".
					"</form>\n";
	if (isset($form['after'])) $form['html'].= $form['after'];
	return rm($GLOBALS['admtemplates']['form'], array('caption'=>$form['caption'], 'html'=>$form['html'], 'width'=>$form['width']), null, false);
}

function ErrorForm($message, $caption='Ошибка!')
{
	$form= array(
		'caption'=>$caption,
		'name'=>'ErrorForm',
		'width'=>'350px',
		'buttons'=>array('back'),
		'fields'=>array(
			array('type'=>'text', 'value'=>'<div style="color: #F00000; font-weight: bold; text-align: center; ">'.$message.'</div>')
		)
	);
	return $form;
}

?>
