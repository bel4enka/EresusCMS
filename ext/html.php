<?
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# CMS Eresus™
# © 2005-2006, ProCreat Systems
# Web: http://procreat.ru
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class THtml extends TContentPlugin {
  var $name = 'html';
  var $type = 'client,content,ondemand';
  var $title = 'HTML';
  var $version = '2.02';
  var $description = 'HTML страница';
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # Внутренние функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function update()
  {
  global $db, $page, $request;

    $item = $db->selectItem('pages', "`id`='".$request['arg']['update']."'");
    $item['content'] = $request['arg']['content'];
    $item['options'] = decodeOptions($item['options']);
    $item['options']['allowGET'] = $request['arg']['allowGET'];
    $item['options'] = encodeOptions($item['options']);
    $db->updateItem('pages', $item, "`id`='".$request['arg']['update']."'");
    goto($page->url());
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # Административные функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminRenderContent()
  {
  global $db, $page, $request;

    if (isset($request['arg']['update'])) $this->update($request['arg']['update']);
    else {
      $item = $db->selectItem('pages', "`id`='".$request['arg']['section']."'");
      $item['options'] = decodeOptions($item['options']);
      $form = array(
        'name' => 'contentEditor',
        'caption' => 'Текст страницы',
        'width' => '100%',
        'fields' => array (
          array ('type' => 'hidden','name' => 'update', 'value'=>$item['id']),
          array ('type' => 'html','name' => 'content','height' => '400px', 'value'=>$item['content']),
          array ('type' => 'checkbox','name' => 'allowGET', 'label' => 'Разрешить аргументы GET', 'value'=>$item['options']['allowGET']),
        ),
        'buttons'=> array('ok', 'reset'),
      );
      $result = $page->renderForm($form);
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientRenderContent()
  {
    global $request, $page;
    if (isset($page->topic)) {
      if (!($page->options['allowGET'] && (strpos($page->topic, execScript.'?') === 0))) $page->httpError('404');
    }
    return parent::clientRenderContent();
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>