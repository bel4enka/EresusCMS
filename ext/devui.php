<?php
/**
 * DevUI
 *
 * Eresus 2
 *
 * Developer User Interface - инструменты разработчика 
 *
 * @version 0.01a
 *
 * @copyright   2007, Eresus Group, http://eresus.ru/
 * @license     http://www.gnu.org/licenses/gpl.txt  GPL License 3
 * @maintainer  Mikhail Krasilnikov <mk@procreat.ru>
 * @author      Mikhail Krasilnikov <mk@procreat.ru>
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
 */

class DevUI extends Plugin {
	var $version = '0.01a';
  var $kernel = '2.10b2';
  var $title = 'DevUI';
  var $description = 'Developer UI';
  var $type = 'admin';
 /**
  * Конструктор
  * @return DevUI
  */
  function DevUI()
  {
  	parent::Plugin();
  	$this->listenEvents('adminOnMenuRender');
  }
  //-----------------------------------------------------------------------------
 /**
  * Диалог выполнения скриптов
  *
  * @return string
  */
  function runScripts()
  {
  	global $page;
  	
  	$result = '';
  	if (arg('run')) {
  		ob_start();
  		include(filesRoot.'distrib/'.arg('run'));
  		$result .= ob_get_clean();
  	} else {
	  	$files = glob(filesRoot.'distrib/*.php');
	  	for($i=0; $i<count($files); $i++) {
	  		$result .= '<a href="'.$page->url(array('mode'=>'run', 'run' => basename($files[$i]))).'">'.basename($files[$i]).'</a><br />';
	  	}
  	}
  	return $result;
  }
  //-----------------------------------------------------------------------------
 /**
  * Отрисовывает контент
  * 
  * @return string
  */
  function adminRender()
  {
  	switch(arg('mode')) {
  		case 'run': $result = $this->runScripts(); break;
  		default: $result = '';
  	}
  	return $result;
  }
  //-----------------------------------------------------------------------------
 /**
  * Обработчик события 'adminOnMenuRender'
  *
  */
  function adminOnMenuRender()
  {
    global $page;
  
    $caption = 'DevUI';
    $page->addMenuItem($caption, array ('access'  => EDITOR, 'link'  => $this->name.'&mode=run', 'caption'  => 'Run scripts', 'hint'  => 'Run standalone scripts within the Eresus'));
  }
  //-----------------------------------------------------------------------------
}
//-----------------------------------------------------------------------------

?>