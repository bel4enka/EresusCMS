<?php
/**
* Eresus™ 2
*
* Библиотека для работы с HTML-формами
*
* @author: Mikhail Krasilnikov <mk@procreat.ru>
* @version: 0.0.1
* @modified: 2007-08-03
*/


/**
* HTML-форма
*/
class TForm {
  var $form;
  var $values;
  var $hidden = '';
  var $onsubmit = '';
  var $validator = '';
  var $file = false;    # Признок наличия полей типа file
  var $html = false;    # Признак наличия WYSIWYG редакторов
  var $syntax = false;  # Признако наличия полей с подсветкой синтаксиса
  /**
  * Конструктор
  *
  * @param  array  $form    Описание формы
  * @param  array  $values  Значения полей по умолчанию (необязательно)
  */
  function TForm($form, $values=array())
  {
    $this->form = $form;
    $this->values = $values;
  }
  //------------------------------------------------------------------------------
  /**
  * Подготоваливает поле формы для дальнейшей обработки
  *
  * @access  private
  *
  * @param  &array  $item  Описание поле
  */
  function field_prep(&$item)
  {
    $item['type'] = strtolower($item['type']);
    # Метка
    if (!isset($item['label'])) $item['label'] = '';
    # Подсказка
    if (isset($item['hint'])) $item['label'] = '<span class="hint" title="'.$item['hint'].'">'.$item['label'].'</span>';
    # Маска значения
    if (isset($item['pattern']) && isset($item['name'])) 
      $this->validator .= "
        if (!form.".$item['name'].".value.match(".$item['pattern'].")) {
          alert('".(isset($item['errormsg'])?sprintf(errFormPatternError, $item['name'], $item['pattern']):$item['errormsg'])."');
          result = false;
          form.".$item['name'].".select();
        } else ";
    # Значение
    $item['value'] = isset($item['value']) ? $item['value']
      : (isset($item['name']) && isset($this->values[$item['name']]) ? $this->values[$item['name']] 
      : (isset($item['default']) ? $item['default']
      : '' )
    );
    # ID
    if (!isset($item['id'])) $item['id'] = '';
    # Элемент отключен
    if (!isset($item['disabled'])) $item['disabled'] = '';
    # Комментарий
    if (!isset($item['comment'])) $item['comment'] = '';
    # Стили
    $item['style'] = isset($item['style']) ? explode(';', $item['style']) : array();
    # Классы
    $item['class'] = isset($item['class']) ? explode(' ', $item['class']) : array();
    # Дополнительно
    if (!isset($item['extra'])) $item['extra'] = '';
  }
  //------------------------------------------------------------------------------
  /**
  * Отрисовывает атрибуты элемента
  *
  * @access  private
  *
  * @param  array  $item  Элемент
  *
  * @return  string  Отрисованные атрибуты
  */
  function attrs($item)
  {
    $result = '';
    if ($item['id']) $result .= ' id="'.$item['id'].'"';
    if ($item['disabled']) $result .= ' disabled="disabled"';
    if (count($item['class'])) $result .= ' class="'.implode(' ', $item['class']).'"';
    # Ширина
    if (isset($item['width'])) $item['style'][] = 'width: '.$item['width'];
    # Стили
    if (count($item['style'])) $result .= ' style="'.implode(';', $item['style']).'"';
    $result .= ' '.$item['extra'];
    return $result;
  }
  //------------------------------------------------------------------------------
  /**
  * Раделитель
  *
  * @access  private
  *
  * @param  array  $item  Описание поля
  *
  * @return  string  Отрисованное поле
  */
  function render_divider($item)
  {
    $result = "<tr><td colspan=\"2\"><hr class=\"formDivider\"></td></tr>\n";
    return $result;
  }
  //------------------------------------------------------------------------------
  /**
  * Текст
  *
  * @access  private
  *
  * @param  array  $item  Описание поля
  *
  * @return  string  Отрисованное поле
  */
  function render_text($item)
  {
    $result = '<tr><td colspan="2" class="formText"'.$this->attrs($item).'>'.$item['value']."</td></tr>\n";
    return $result;
  }
  //------------------------------------------------------------------------------
  /**
  * Подзаголовок
  *
  * @access  private
  *
  * @param  array  $item  Описание поля
  *
  * @return  string  Отрисованное поле
  */
  function render_header($item)
  {
    $result = '<tr><th colspan="2" class="formHeader"'.$this->attrs($item).'>'.$item['value']."</th></tr>\n";
    return $result;
  }
  //------------------------------------------------------------------------------
  /**
  * <input type="hidden" />
  *
  * @access  private
  *
  * @param  array  $item  Описание поля
  *
  * @return  string  Отрисованное поле
  */
  function render_hidden($item)
  {
    if ($item['name'] === '') ErrorMessage(sprintf(errFormFieldHasNoName, $item['type'], $this->form['name']));
    $this->hidden .= '<input type="hidden" name="'.$item['name'].'" value="'.$item['value'].'" />'."\n";
    return '';
  }
  //------------------------------------------------------------------------------
  /**
  * <input type="text" />
  *
  * @access  private
  *
  * @param  array  $item  Описание поля
  *
  * @return  string  Отрисованное поле
  */
  function render_edit($item)
  {
    if ($item['name'] === '') ErrorMessage(sprintf(errFormFieldHasNoName, $item['type'], $this->form['name']));
    $result = '<tr><td class="formLabel">'.$item['label'].'</td><td><input type="text" name="'.$item['name'].'" value="'.EncodeHTML($item['value']).'"'.(empty($item['maxlength'])?'':' maxlength="'.$item['maxlength'].'"').$this->attrs($item).'>'.$item['comment']."</td></tr>\n";
    return $result;
  }
  //------------------------------------------------------------------------------
  /**
  * <input type="password" />
  *
  * @access  private
  *
  * @param  array  $item  Описание поля
  *
  * @return  string  Отрисованное поле
  */
  function render_password($item)
  {
    if ($item['name'] === '') ErrorMessage(sprintf(errFormFieldHasNoName, $item['type'], $this->form['name']));
    $result = '<tr><td class="formLabel">'.$item['label'].'</td><td><input type="text" name="'.$item['name'].'" value="'.EncodeHTML($item['value']).'"'.(empty($item['maxlength'])?'':' maxlength="'.$item['maxlength'].'"').$this->attrs($item).'>'.$item['comment']."</td></tr>\n";
    if (isset($item['equal'])) $this->validator .= "if (form.".$item['name'].".value != form.".$item['equal'].".value) {\nalert('".errFormBadConfirm."');\nresult = false;\nform.".$item['name'].".value = '';\nform.".$item['equal'].".value = ''\nform.".$item['equal'].".select();\n} else ";
    return $result;
  }
  //------------------------------------------------------------------------------
  /**
  * <input type="checkbox" />
  *
  * @access  private
  *
  * @param  array  $item  Описание поля
  *
  * @return  string  Отрисованное поле
  */
  function render_checkbox($item)
  {
    if ($item['name'] === '') ErrorMessage(sprintf(errFormFieldHasNoName, $item['type'], $this->form['name']));
    $result = '<tr><td><input type="hidden" name="'.$item['name'].'" value="" /></td><td><input type="checkbox" name="'.$item['name'].'" value="'.($item['value'] ? $item['value'] : true).'" '.($item['value'] ? 'checked' : '').$this->attrs($item).' style="background-color: transparent; border-style: none; margin:0px;"><span style="vertical-align: baseline"> '.$item['label']."</span></td></tr>\n";
    return $result;
  }
  //------------------------------------------------------------------------------
  /**
  * <select>
  *
  * @access  private
  *
  * @param  array  $item  Описание поля
  *
  * @return  string  Отрисованное поле
  */
  function render_select($item)
  {
    if ($item['name'] === '') ErrorMessage(sprintf(errFormFieldHasNoName, $item['type'], $this->form['name']));
    $result = '<tr><td class="formLabel">'.$item['label'].'</td><td><select name="'.$item['name'].'"'.$this->attrs($item).'>'."\n";
    if (!isset($item['items']) && isset($item['values'])) $item['items'] = $item['values'];
    for($i = 0; $i< count($item['items']); $i++) {
      if (isset($item['values'])) $value = $item['values'][$i]; else $value = $i;
      $result .= '<option value="'.$value.'" '.($value == (isset($this->values[$item['name']]) ? $this->values[$item['name']] : (isset($item['value'])?$item['value']:'')) ? 'selected' : '').">".$item['items'][$i]."</option>\n";
    }
    $result .= '</select>'.$item['comment']."</td></tr>\n";
    return $result;
  }
  //------------------------------------------------------------------------------
  /**
  * <select multiple>
  *
  * @access  private
  *
  * @param  array  $item  Описание поля
  *
  * @return  string  Отрисованное поле
  */
  function render_listbox($item)
  {
    if ($item['name'] === '') ErrorMessage(sprintf(errFormFieldHasNoName, $item['type'], $this->form['name']));
    $result = '<tr><td class="formLabel">'.$item['label'].'</td><td><select multiple name="'.$item['name'].'[]"'.(isset($item['height'])?' size="'.$item['height'].'"':'').$this->attrs($item).">\n";
    if (!isset($item['items']) && isset($item['values'])) $item['items'] = $item['values'];
    for($i = 0; $i< count($item['items']); $i++) {
      if (isset($item['values'])) $value = $item['values'][$i]; else $value = $i;
      $result .= '<option value="'.$value.'" '.(count($this->values) && in_array($value, $this->values[$item['name']]) ? 'selected' : '').">".$item['items'][$i]."</option>\n";
    }
    $result .= '</select>'.$item['comment']."</td></tr>\n";
    return $result;
  }
  //------------------------------------------------------------------------------
  /**
  * <textarea></textarea>
  *
  * @access  private
  *
  * @param  array  $item  Описание поля
  *
  * @return  string  Отрисованное поле
  */
  function render_memo($item)
  {
    if ($item['name'] === '') ErrorMessage(sprintf(errFormFieldHasNoName, $item['type'], $this->form['name']));
    if (empty($item['width'])) $item['width'] = '100%';
    if (strpos($item['width'], '%') === false) {
      $cols = $item['width'];
      $item['width'] = '';
    } else $cols = '50';
    if (isset($item['syntax'])) {
      if (!$item['id']) $item['id'] = $this->form['name'].'_'.$item['name'];
      $item['class'][] = 'codepress';
      $item['class'] = array_merge($item['class'], explode(' ', $item['syntax']));
      $this->onsubmit .= 
        "\n    form.".$item['name'].".value = ".$item['id'].".getCode();\n".
        "    form.".$item['name'].".disabled = false;\n";
      $this->syntax = true;
    }
    $result = '<tr><td colspan="2">'.(empty($item['label'])?'':'<span class="formLabel">'.$item['label'].'</span><br />').'<textarea name="'.$item['name'].'" cols="'.$cols.'" rows="'.(empty($item['height'])?'3':$item['height']).'" '.$this->attrs($item).'>'.EncodeHTML($item['value'])."</textarea></td></tr>\n"; 
    return $result;
  }
  //------------------------------------------------------------------------------
  /**
  * <textarea html>
  *
  * @access  private
  *
  * @param  array  $item  Описание поля
  *
  * @return  string  Отрисованное поле
  */
  function render_html($item)
  {
    global $page;
    if ($item['name'] === '') ErrorMessage(sprintf(errFormFieldHasNoName, $item['type'], $this->form['name']));
    $value = isset($values[$item['name']]) ? $values[$item['name']] : (isset($item['value'])?$item['value']:'');
    $result = '<tr><td colspan="2">'.$item['label'].'<br /><textarea name="wyswyg_'.$item['name'].'" id="wyswyg_'.$item['name'].'" style="width: 100%; height: '.$item['height'].';">'.str_replace('$(httpRoot)', httpRoot, EncodeHTML($value)).'</textarea></td></tr>'."\n";
    $page->htmlEditors[] = 'wyswyg_'.$item['name'];
    return $result;
  }
  //------------------------------------------------------------------------------
  /**
  * <input type="file" />
  *
  * @access  private
  *
  * @param  array  $item  Описание поля
  *
  * @return  string  Отрисованное поле
  */
  function render_file($item)
  {

    if ($item['name'] === '') ErrorMessage(sprintf(errFormFieldHasNoName, $item['type'], $this->form['name']));
    $result = '<tr><td class="formLabel">'.$item['label']."</td><td><input type=\"file\" name=\"".$item['name']."\" size=\"".$item['width']."\"".$this->attrs($item).">".$item['comment']."</td></tr>\n";
    $this->file = true;
    return $result;
  }
  //------------------------------------------------------------------------------
  /**
  * Создание HTML-кода
  *
  * @access  public
  *
  * @return  string  HTML-код формы
  */
  function render()
  {
    global $page, $request;
      
    $result = '';     # Выходной код
    $hidden = '';     # Скрытые поля???
    $body = '';       # Тело таблицы-формы
  
    if (empty($this->form['name'])) $result .= ErrorBox(errFormHasNoName);
    if (count($this->form['fields'])) foreach($this->form['fields'] as $item) {
      # Проверяем права доступа к элементу
      if ((!isset($item['access'])) || (UserRights($item['access']))) {
        $this->field_prep($item);
        $control = 'render_'.$item['type'];
        #if (method_exists($this, $control)) $result .= call_user_func(array($this, $control), $item);
        if (method_exists($this, $control)) {
          $result .= $this->$control($item);
        }
        else ErrorMessage(sprintf(errFormUnknownType, $item['type'], $this->form['name']));
      }
    }
    $this->onsubmit .= $this->validator;
    if (!empty($this->onsubmit)) $page->scripts .= "
      function ".$this->form['name']."Submit()
      {
        var result = true;
        var form = document.forms.namedItem('".$this->form['name']."');
        ".$this->onsubmit.";
        return result;
      }
    ";
    if ($this->syntax) $page->head .= '<script src="'.httpRoot.'core/codepress/codepress.js" type="text/javascript"></script>'."\n";
    $referer = isset($request['arg']['sub_id'])?$page->url(array('sub_id'=>'')):$page->url(array('id'=>''));
    $this->hidden .= '<input type="hidden" name="submitURL" value="'.$referer.'">';
    $this->hidden = "<div class=\"hidden\">{$this->hidden}</div>";
    $result = 
      "<form ".(empty($this->form['name'])?'':'name="'.$this->form['name'].'" ')."action=\"".$page->url()."\" method=\"post\"".(empty($this->onsubmit)?'':' onsubmit="return '.$this->form['name'].'Submit();"').($this->file?' enctype="multipart/form-data"':'').">\n".
      $this->hidden.
      "<table width=\"100%\">\n".
      "<tr><td style=\"height: 0px; font-size: 0px; padding: 0px;\">".img('style/dot.gif')."</td><td style=\"width: 100%; height: 0px; font-size: 0px; padding: 0px;\">".img('style/dot.gif')."</td></tr>\n".
      $result.
      "<tr><td colspan=\"2\" align=\"center\"><br />".
      (!isset($this->form['buttons']) || in_array('ok', $this->form['buttons'])?"<input type=\"submit\" class=\"button\" value=\"".strOk."\"> ":''). # onClick=\"formOKClick('".$form['name']."')\"> ":'').
      (!isset($this->form['buttons']) || in_array('apply', $this->form['buttons'])?"<input type=\"submit\" class=\"button\" value=\"".strApply."\" onClick=\"formApplyClick('".$this->form['name']."')\"> ":'').
      (isset($this->form['buttons']) && in_array('reset', $this->form['buttons'])?"<input type=\"reset\" class=\"button\" value=\"".strReset."\"> ":'').
      (!isset($this->form['buttons']) || in_array('cancel', $this->form['buttons'])?"<input type=\"button\" class=\"button\" value=\"".strCancel."\" onclick=\"javascript:history.back();\">":'').
      "</td></tr>\n".
      "</table>\n</form>\n";
      
    return $result;
  }
  //------------------------------------------------------------------------------
}

/**
* Отрисовывает форму на основе массива
*
* @access  public
*
* @param  array  $form    Описание формы
* @param  array  $values  Значения полей по умолчанию (необязательно)
*
* @return  string  HTML-код формы
*/
function form($form, $values=array())
{
  $Form = new TForm($form, $values);
  $result = $Form->render();
  return $result;
}
//------------------------------------------------------------------------------
?>