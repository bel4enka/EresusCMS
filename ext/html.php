<?php
/**
 * HTML-страница
 *
 * Eresus 2
 * 
 * Плагин обеспечивает визаульное редактирование текстографических страниц
 *
 * © 2005-2006, ProCreat Systems, http://procreat.ru/
 * © 2007, Eresus Group, http://eresus.ru/
 *
 * @version: 3.00
 * @modified: 2007-09-24
 * 
 * @author: Mikhail Krasilnikov <mk@procreat.ru>
 */

class Html extends ContentPlugin {
  var $version = '3.00a2';
  var $kernel = '2.10b2';
  var $title = 'HTML';
  var $description = 'HTML страница';
  var $type = 'client,content,ondemand';
  /**
   * Обновление контента
   *
   * @param string $content  Новый контент
   */
  function updateContent($content)
  {
		global $Eresus, $page;
	
	  $item = $Eresus->db->selectItem('pages', "`id`='".$page->id."'");
	  $item['content'] = $content;
    $item['options'] = decodeOptions($item['options']);
    $item['options']['allowGET'] = arg('allowGET');
    $item['options'] = encodeOptions($item['options']);
	  $Eresus->db->updateItem('pages', $item, "`id`='".$page->id."'");
  }
	//------------------------------------------------------------------------------
  /**
	 * Отрисовка административной части
	 *
	 * @return  string  Контент
	 */
  function adminRenderContent()
  {
		global $page, $Eresus;
	
	  if (arg('action') == 'update') $this->adminUpdate();
		$item = $Eresus->db->selectItem('pages', "`id`='".$page->id."'");
    $item['options'] = decodeOptions($item['options']); 	 
    $url = $page->clientURL($item['id']);		
	  $form = array(
	    'name' => 'contentEditor',
	    'caption' => $page->title,
	    'width' => '100%',
	    'fields' => array (
	      array ('type'=>'hidden','name'=>'action', 'value' => 'update'),
				array ('type' => 'html','name' => 'content','height' => '400px', 'value'=>$item['content']),
				array ('type' => 'text', 'value' => 'Адрес страницы: <a href="'.$url.'">'.$url.'</a>'),
				array ('type' => 'checkbox','name' => 'allowGET', 'label' => 'Разрешить передавать аргументы в адресе страницы', 'value'=>isset($item['options']['allowGET'])?$item['options']['allowGET']:false),
	     ),
	    'buttons' => array('apply', 'reset'),
	  );
	
	  $result = $page->renderForm($form, $item);
	  return $result;
  }
	//------------------------------------------------------------------------------
  function clientRenderContent()
  {
    global $request, $page;
    if ($page->topic) {
      if (!(isset($page->options['allowGET']) && $page->options['allowGET'] && (strpos($page->topic, execScript.'?') === 0))) $page->httpError(404);
    }
    return parent::clientRenderContent();
  }
	//------------------------------------------------------------------------------
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>