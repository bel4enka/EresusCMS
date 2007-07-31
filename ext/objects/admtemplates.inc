<?php
//Шаблон для отрисовки объекта.
$GLOBALS['admtemplates']['template']= '$(form) $(objects)';


//Шаблон для отображения объекта в родительском. Используется функцией class_admin::thumb()
//Макросы:
//	 editref - путь для редактирования объекта
//  upref - путь для перемещения объекта на позицию вверх (используется при выводе списка объектов)
//  downref - путь для перемещения объекта на позицию вниз
//  delref - путь для удаления объекта
//	 edit - 	текст. Если объект не создан то имеет вид "Создать ИМЯ". Если создан, то просто "ИМЯ", где
//			имя - описание переменной.
$GLOBALS['admtemplates']['thumb']= '
<div class="objectbox">
<div class="left"><div class="right"><div class="top"><div class="bottom"><div class="topleft"><div class="topright"><div class="bottomleft"><div class="bottomright"><div class="inner">

  <div style="text-align: center; width: 90px; padding: 10px 10px 5px 10px;">
    <div><a href="$(viewref)"><img src="$(httpRoot)ext/classes/$(class)/icon.png" alt="" /></a></div>
    <div><a href="$(viewref)">$(edit)</a></div>
  </div>

</div></div></div></div></div></div></div></div></div></div>
';

//Шаблон для отображения массива объектов в родительском. Используется функцией class_admin::thumbslist()
//Макросы:
//	caption - описание переменной массива.
//	children - список элементов массива.
//
$GLOBALS['admtemplates']['thumbslist']= '
<div class="objectbox" style="clear: both;">
<div class="left"><div class="right"><div class="top"><div class="bottom"><div class="topleft"><div class="topright"><div class="bottomleft"><div class="bottomright"><div class="inner">

   <div class="header">$(caption)</div>
   <div class="content">
      <div><a style="color: green; font-weight: bold;" href="$(addref)">+ Добавить $(classcaption)</a></div>
      <div>$(items)</div>
       <div style="clear: both"></div>
   </div>

</div></div></div></div></div></div></div></div></div></div>
';

//Шаблон элемента массива.  Cм шаблон thumb.
$GLOBALS['admtemplates']['listthumb']= '
<div style="text-align: center; width: 90px; padding: 10px 0px 5px 0px; float: left;">
<div>$(id)</div>
<div><a href="$(viewref)"><img src="$(httpRoot)ext/classes/$(class)/icon.png" alt="" /></a></div>
<div>
	<a href="$(upref)"><img src="$(httpRoot)core/img/objects/controls/left.gif" alt="Вверх" /></a>
	<a style="color: #d2ba32;" href="$(viewref)">edit</a>
	<a style="color: #FF0000;" href="$(delref)" onclick="return confirm(\'Действительно удалить?\')">del</a>
	<a style="vertical-align: middle" href="$(downref)"><img src="$(httpRoot)core/img/objects/controls/right.gif" alt="Вниз" /></a>
</div>
</div>
';

$GLOBALS['admtemplates']['pagesswitch']= '<div class="admListPages">Страницы: $(pages)</div>';

$GLOBALS['admtemplates']['toggled_on']= '
	<a href="$(toggleref)" onclick="
		queue_req(\'$(toggleref)&ajax=true\', ajax_handler, \'GET\', null);
		return false;
	">
		<img id="$(ajax_id)" src="$(httpRoot)core/img/objects/controls/on.gif" />
	</a>';
$GLOBALS['admtemplates']['toggled_off']= '
	<a href="$(toggleref)" onclick="
		queue_req(\'$(toggleref)&ajax=true\', ajax_handler, \'GET\', null);
		return false;
	">
		<img id="$(ajax_id)" src="$(httpRoot)core/img/objects/controls/off.gif" />
	</a>';

//Шаблон для вывода формы.
//Макросы:
//	caption - заголовок формы
//	html - html код формы
$GLOBALS['admtemplates']['form_weird']= '
<div class="objectbox" style="width: $(width);">
<div class="left"><div class="right"><div class="top"><div class="bottom"><div class="topleft"><div class="topright"><div class="bottomleft"><div class="bottomright"><div class="inner">

  <div class="header">$(caption)</div>
  <div class="content">$(html)</div>

</div></div></div></div></div></div></div></div></div></div>
';

$GLOBALS['admtemplates']['form']= '
<table class="admWindow" style="width: $(width);">
	<tr><th>$(caption)</th></tr>
  	<tr><td>$(html)</td></tr>
</table>
';

//Шаблон окна
$GLOBALS['admtemplates']['window']= '
<div class="objectbox" style="width: $(width);">
<div class="left"><div class="right"><div class="top"><div class="bottom"><div class="topleft"><div class="topright"><div class="bottomleft"><div class="bottomright"><div class="inner">

  <div class="header">$(header)</div>
  <div class="content">$(content)</div>

</div></div></div></div></div></div></div></div></div></div>
';
?>
