<?php
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# CMS Eresus™
# © 2005-2006, ProCreat Systems
# Web: http://procreat.ru
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class THtml_mod extends TContentPlugin {
  var $name = 'html_mod';
  var $type = 'client,content,ondemand';
  var $title = 'HTML (мод.)';
  var $version = '2.03m';
  var $description = 'HTML страница (модиф.)';
  var $settings = array (
    'template' => '',
    'root' => 'images/',
  );
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # Внутренние функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function update()
  {
    global $db, $page;

    $item = $db->selectItem('pages', "`id`='".arg('update')."'");
    $item['content'] = decodeOptions($item['content']);
    $item['content']['content'] = arg('content');
    $item['content']['info'] = arg('info');
    $item['content']['image'] = arg('image');
    $item['content'] = encodeOptions($item['content']);
    #print_r($item);
    $db->updateItem('pages', $item, "`id`='".$item['id']."'");
    goto($page->url());
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # Административные функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function renderForm($form, $values)
  {
    global $page, $Eresus;
    
    $width  = 100;
    $height = 100;
    $page->styles .= strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') ?
      "
        .html_imagelist div {overflow: scroll; width: ".$width."px; white-space: nowrap;}
        .html_imagelist img {cursor: pointer;}
        .html_selected_cell {width: ".$width."px; padding-right: 2em;}
        .html_selected {width: ".$width."px; height: ".$height."px; background-color: #fff; line-height: ".$height."px; text-align: center;}
        .html_selected_cell * {border: solid 2px #007acc;}
      " :
      "
        .html_imagelist div {overflow: scroll; white-space: nowrap; padding-top: 2px;}
        .html_imagelist img {cursor: pointer;}
        .html_imagelist img:hover {outline: solid 2px #f80;}
        .html_selected_cell {width: ".$width."px; padding-right: 2em;}
        .html_selected {width: ".$width."px; height: ".$height."px; background-color: #fff; line-height: ".$height."px; text-align: center;}
        .html_selected_cell * {outline: solid 2px #007acc;}
      ";
    $ie_id = uniqid('ie_bug_fix_');
    $page->scripts .= "

      function html_ie_fix()
      {
        var Node = document.getElementById('$ie_id');
        if (Node) {
          Node.style.width = Node.parentNode.offsetWidth+'px';
        } else window.setTimeout('html_ie_fix()', 100);
      }
      
      if (iBrowser['Engine'] == 'IE') window.setTimeout('html_ie_fix()', 100);
    
      function html_select(formName, inputName, image)
      {
        var Selected = image.parentNode.parentNode.offsetParent.rows[0].cells[0];
        Selected.innerHTML = '';
        var Clone = image.cloneNode(false);
        Clone.removeAttribute('title');
        Clone.removeAttribute('onclick');
        Selected.appendChild(Clone);
        document.forms[formName][inputName].value = image.alt;
      }
    ";
    for($i=0; $i<count($form['fields']); $i++) if ($form['fields'][$i]['type'] == 'imagelist') {
      $form['fields'][$i]['type'] = 'text';
      $form['fields'][$i]['value'] = '';
      $list = glob($Eresus->fdata.$this->settings['root'].'*.*');
      $image = '<img src="'.$Eresus->data.'%s" width="'.$width.'" height="'.$height.'" alt="%s" title="Выбрать %s" onclick="html_select(\''.$form['name'].'\', \''.$form['fields'][$i]['name'].'\', this)" />';
      for ($j=0; $j<count($list); $j++) $form['fields'][$i]['value'] .= sprintf($image, substr($list[$j], strlen($Eresus->fdata)), basename($list[$j]), basename($list[$j])).' ';
      $current = isset($values[$form['fields'][$i]['name']]) ? $values[$form['fields'][$i]['name']] : '';
      $form['fields'][] = array('type'=>'hidden', 'name'=>$form['fields'][$i]['name'], 'value' => $current);
      $current = empty($current) ? '<div class="html_selected">Пусто</div>' : sprintf($image, $this->settings['root'].$current, $current, $current);
      $form['fields'][$i]['value'] = 
        '<table width="100%"><tr>'.
        '<td class="html_selected_cell">'.$current.'</td>'.
        '<td class="html_imagelist"><div id="'.$ie_id.'">'.$form['fields'][$i]['value'].'</div></td>'.
        '</tr></table>';
      break;
    }
    $result = $page->renderForm($form, $values);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminRenderContent()
  {
    global $db, $request;

    if (isset($request['arg']['update'])) $this->update($request['arg']['update']);
    else {
      $item = $db->selectItem('pages', "`id`='".$request['arg']['section']."'");
      $item['options'] = decodeOptions($item['options']);
      $item['content'] = decodeOptions($item['content']);
      $form = array(
        'name' => 'contentEditor',
        'caption' => 'Текст страницы',
        'width' => '100%',
        'fields' => array (
          array ('type' => 'hidden','name' => 'update', 'value'=>$item['id']),
          array ('type' => 'imagelist','name' => 'image'),
          array ('type' => 'html','name' => 'info','height' => '200px'),
          array ('type' => 'html','name' => 'content','height' => '400px'),
        ),
        'buttons'=> array('ok', 'reset'),
      );
      $result = $this->renderForm($form, $item['content']);
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function settings()
  {
    global $page;

    $form = array(
      'name' => 'settings',
      'caption' => $this->title.' '.$this->version,
      'width' => '500px',
      'fields' => array (
        array('type'=>'hidden','name'=>'update', 'value'=>$this->name),
        array('type'=>'memo','name'=>'template','label'=>'Шаблон','height'=>'10'),
    ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $result = $page->renderForm($form, $this->settings);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function replaceMacros($template, $item)
  {
    global $Eresus;
    
    $result = $template;
    $result = preg_replace('/{%\$\((.*)\)\?(.*):(.*)}/Ue', '$item[\'$1\']?\'$2\':\'$3\'', $result);
    $result = stripslashes($result);
    $item['image'] = $Eresus->data.$this->settings['root'].$item['image'];
    $result = parent::replaceMacros($result, $item);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientRenderContent()
  {
    global $request, $page;
    
    if (isset($page->topic)) $page->httpError('404');
    $result = decodeOptions($page->content);
    if (!isset($result['image'])) $result['image'] = '';
    $result = $this->replaceMacros($this->settings['template'], $result);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>