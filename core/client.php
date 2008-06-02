<?
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# Система управления контентом Eresus™
# Версия 2.08
# © 2004-2007, ProCreat Systems
# http://procreat.ru/
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# Интерфейс посетителя
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
define('CLIENTUI', true);

# Подключаем ядро системы #
if (file_exists('core/kernel.php')) include_once('core/kernel.php'); else {
  echo "<h1>Fatal error</h1>\n<strong>Kernel not available!</strong><br />\nThis error can take place during site update.<br />\nPlease try again later.";
  exit;
}

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function __macroConst($matches) {
  return constant($matches[1]);
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function __macroVar($matches) {
  $result = $GLOBALS[$matches[2]];
  if (!empty($matches[3])) @eval('$result = $result'.$matches[3].';');
  return $result;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# КЛАСС "СТРАНИЦА"
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TClientUI {
  var $dbItem = array(); # Информация о страниуе из БД
  var $id = -1; # Идентификатор страницы
  var $name = ''; # Имя страницы
  var $owner = 0; # Идентификатор родительской страницы
  var $title = ''; # Заголовок страницы
  var $section = array(); # Массив заголовков страниц
  var $caption = ''; # Название страницы
  var $hint = ''; # Подсказка с описанием страницы
  var $description = ''; # Описание страницы
  var $keywords = ''; # Описание страницы
  var $access = GUEST; # Базовый уровень доступа к странице
  var $visible = true; # Видимость страницы
  var $type = 'default'; # Тип страницы
  var $content = ''; # Контент страницы
  var $options = array(); # Опции страницы
  var $html; # HTML структура страницы
  var $plugin; # Плагин контента
  var $headers; # Заголовки ответа сервера
  var $scripts = ''; # Скрипты
  var $styles = ''; # Стили
  var $subpage = 0; # Стили
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # ВНУТРЕННИЕ ФУНКЦИИ
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function replaceMacros($text)
  # Подставляет значения макросов
  {
  global $user;
  
  $section = $this->section;
  if (siteTitleReverse) $section = array_reverse($section);
  $section = strip_tags(implode($section, option('siteTitleDivider')));
  
  $result = str_replace(
      array(
        '$(httpHost)',
        '$(httpPath)',
        '$(httpRoot)',
        '$(styleRoot)',
        '$(dataRoot)',
        
        '$(siteName)',
        '$(siteTitle)',
        '$(siteKeywords)',
        '$(siteDescription)',
        
        '$(pageId)',
        '$(pageName)',
        '$(pageTitle)',
        '$(pageCaption)',
        '$(pageHint)',
        '$(pageDescription)',
        '$(pageKeywords)',
        '$(pageAccessLevel)',
        '$(pageAccessName)',

        '$(sectionTitle)',
      ),
      array(
        httpHost, 
        httpPath, 
        httpRoot, 
        styleRoot,
        dataRoot,
        
        siteName,
        siteTitle,
        siteKeywords,
        siteDescription,
        
        $this->id,
        $this->name,
        $this->title,
        $this->caption,
        $this->hint,
        $this->description,
        $this->keywords,
        $this->access,
        constant('ACCESSLEVEL'.$this->access),
        $section,
      ),
      $text
    );
    $result = preg_replace_callback('/\$\(const:(.*?)\)/i', '__macroConst', $result);
    $result = preg_replace_callback('/\$\(var:(([\w]*)(\[.*?\]){0,1})\)/i', '__macroVar', $result);
    $result = preg_replace('/\$\(\w+(:.*?)*?\)/', '', $result);
    return $result;
  } 
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function loadPage()
  {
  global $KERNEL, $db, $plugins, $request, $user;

    # Поиск текущей страницы
    # В БД обязана быть страница с именем 'main'
    $name = 'main';
    $tmp = $db->selectItem('pages', "`name`='main' AND `owner`='0' AND `access`>='".($user['auth']?$user['access']:GUEST)."' AND `active`='1'");
    $url = '';
    if ($tmp == null) $this->httpError(404); else {
      $item = $tmp;
      $tmp['id'] = 0;
      $this->section[] = $item['title'];
      # Парсим командную строку
      if (count($request['params'])) do {
        $tmp = $db->selectItem('pages', "`name`='".$request['params'][0]."' AND `owner`='".$tmp['id']."' AND `access`>=".($user['auth']?$user['access']:GUEST)."");
        if ($tmp != null) {
          if (!$tmp['active']) $this->httpError(404);
          $item = $tmp;
          $url .= $item['name'].'/';
          $plugins->clientOnURLSplit($item, $url);
          $this->section[] = $item['title'];
          array_shift($request['params']);
        }
      } while (($tmp != null) && count($request['params']));
      $request['path'] = httpRoot.$url;
      #if (empty($this->content['section'])) $this->content['section'] = $item['caption'];
      #else $this->content['section'] = implode(' &raquo; ', $this->content['section']);
    }
    return $item;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # ОБЩИЕ ФУНКЦИИ
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function init()
  # Проводит инициализацию страницы
  {
  global $db, $user, $plugins, $request;

    $plugins->preload(array('client'),array('ondemand'));
    $plugins->clientOnStart();
    
    $item = $this->loadPage();
    if (!is_null($item)) {
      if (count($request['params'])) {
        if (preg_match('/p[\d]+/i', $request['params'][0])) $this->subpage = substr(array_shift($request['params']), 1);
        if (count($request['params'])) $this->topic = array_shift($request['params']);
      }
      $this->dbItem = $item;
      $this->id = $item['id'];
      $this->name = $item['name'];
      $this->owner = $item['owner'];
      $this->title = $item['title'];
      $this->description = $item['description'];
      $this->keywords = $item['keywords'];
      $this->caption = $item['caption'];
      $this->hint = $item['hint'];
      $this->access = $item['access'];
      $this->visible = $item['visible'];
      $this->type = $item['type'];
      $this->template = $item['template'];
      $this->created = $item['created'];
      $this->updated = $item['updated'];
      $this->content = $item['content'];
      $this->scripts = '';
      $this->styles = '';
    }
    $this->options = decodeOptions($item['options']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function Error404()
  {
    $this->httpError(404);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function httpError($code)
  {
  global $KERNEL;
  
    if (isset($KERNEL['ERROR'])) return;
    $ERROR = array(
      '400' => array('response' => 'Bad Request'),
      '401' => array('response' => 'Unauthorized'),
      '402' => array('response' => 'Payment Required'),
      '403' => array('response' => 'Forbidden'),
      '404' => array('response' => 'Not Found'),
      '405' => array('response' => 'Method Not Allowed'),
      '406' => array('response' => 'Not Acceptable'),
      '407' => array('response' => 'Proxy Authentication Required'),
      '408' => array('response' => 'Request Timeout'),
      '409' => array('response' => 'Conflict'),
      '410' => array('response' => 'Gone'),
      '411' => array('response' => 'Length Required'),
      '412' => array('response' => 'Precondition Failed'),
      '413' => array('response' => 'Request Entity Too Large'),
      '414' => array('response' => 'Request-URI Too Long'),
      '415' => array('response' => 'Unsupported Media Type'),
      '416' => array('response' => 'Requested Range Not Satisfiable'),
      '417' => array('response' => 'Expectation Failed'),
    );
  
    Header($_SERVER['SERVER_PROTOCOL'].' '.$code.' '.$ERROR[$code]['response']);

    if (defined('HTTP_CODE_'.$code)) $message = constant('HTTP_CODE_'.$code);
    else $message = $ERROR[$code]['response'];

    $this->section = array(siteTitle, $message);
    $this->title = $message;
    $this->description = '';
    $this->keywords = '';
    $this->caption = $message;
    $this->hint = '';
    $this->access = GUEST;
    $this->visible = true;
    $this->type = 'default';
    if (file_exists(filesRoot.'templates/std/'.$code.'.tmpl')) {
      $this->template = 'std/'.$code;
      $this->content = '';
    } else {
      $this->template = 'default';
      $this->content = '<h1>HTTP ERROR '.$code.': '.$message.'</h1>';
    }
    $KERNEL['ERROR'] = true;
    $this->render();
    exit;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function url($dummy=null)
  {
    global $request;
    
    $pos = strpos($request['url'], '?');
    $result = ($pos === false) ? $request['url'] : substr($request['url'], 0, $pos);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function clientURL($id)
  # Функция возвращает HTTP путь к странице с идентификатором $id
  {
    global $db;
    
    $result = '';
    $item = $db->selectItem('pages', "`id`='".$id."'");
    while (!is_null($item)) {
      $result = $item['name'].'/'.$result;
      $item = $db->selectItem('pages', "`id`='".$item['owner']."'");
    }
    if ($result == 'main/') $result = '';
    $result = httpRoot.$result;
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function render()
  # Отправляет созданную страницу пользователю.
  {
  global $KERNEL, $plugins, $session, $request;

    if (isset($request['arg']['HTTP_ERROR'])) $this->httpError($request['arg']['HTTP_ERROR']);
    # Отрисовываем контент
    $content = $plugins->clientRenderContent();
    $this->updated = mktime(substr($this->updated, 11, 2), substr($this->updated, 14, 2), substr($this->updated, 17, 2), substr($this->updated, 5, 2), substr($this->updated, 8, 2), substr($this->updated, 0, 4));
    #if ($this->updated < 0) $this->updated = 0;
    #$this->headers[] = 'Last-Modified: ' . gmdate('D, d M Y H:i:s', $this->updated) . ' GMT';
    $this->headers[] = 'Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT';
    $template = filesRoot.'templates/'.$this->template.'.tmpl';
    if (file_exists($template)) $template = StripSlashes(file_get_contents($template)); else {
      $template = filesRoot.'templates/default.tmpl';
      if (file_exists($template)) $template = StripSlashes(file_get_contents($template)); else CMSError('File not found', 'Open file '.$template, __FILE__, __LINE__);
    }
    $this->template = trim(substr($template, strpos($template, "\n")));
    $content = $plugins->clientOnContentRender($content);

    if (isset($session['msg']['information']) && count($session['msg']['information'])) {
      $messages = '';
      foreach($session['msg']['information'] as $message) $messages .= InfoBox($message);
      $content = $messages.$content;
      $session['msg']['information'] = array();
    }
    /*OBSOLETE:BEGIN */
    if (!empty($session['message'])) {
      $content = InfoBox($session['message']).$content;
      $session['message'] = '';
    }
    /*OBSOLETE:END */
    if (isset($session['msg']['errors']) && count($session['msg']['errors'])) {
      $messages = '';
      foreach($session['msg']['errors'] as $message) $messages .= ErrorBox($message);
      $content = $messages.$content;
      $session['msg']['errors'] = array();
    }
    /*OBSOLETE:BEGIN */
    if (!empty($session['errorMessage'])) {
      $content = ErrorBox($session['errorMessage']).$content;
      $session['errorMessage'] = '';
    }
    /*OBSOLETE:END */
    $result = str_replace('$(Content)', $content, $this->template);
    
    if (!empty($this->styles)) {
      $styles = "<style type=\"text/css\">\n  ".str_replace("\n", "\n  ", trim($this->styles))."\n</style>\n";
      $result = preg_replace('|(.*)</head>|i', '$1'.$styles."\n</head>", $result);
    }

    $result = $plugins->clientOnPageRender($result);

    if (!empty($this->scripts)) $this->scripts = "  <script type=\"text/javascript\">\n  ".str_replace("\n", "\n    ", trim($this->scripts))."\n  </script>\n";
    $this->scripts =
      '  <script type="text/javascript">'."\n".
      "  //<!-- <![CDATA[\n".
      "    var iBrowser = new Array();\n".
      "    iBrowser['UserAgent'] = navigator.userAgent.toLowerCase();\n".
      "    if ((iBrowser['UserAgent'].indexOf('msie') != -1) && (iBrowser['UserAgent'].indexOf('opera') == -1) && (iBrowser['UserAgent'].indexOf('webtv') == -1)) iBrowser['Engine'] = 'IE';\n".
      "    if (iBrowser['UserAgent'].indexOf('gecko') != -1) iBrowser['Engine'] = 'Gecko';\n".
      "    if (iBrowser['UserAgent'].indexOf('opera') != -1) iBrowser['Engine'] = 'Opera';\n".
      "    if (iBrowser['UserAgent'].indexOf('safari') != -1) iBrowser['Engine'] = 'Safari';\n".
      "    if (iBrowser['UserAgent'].indexOf('konqueror') != -1) iBrowser['Engine'] = 'Konqueror';\n".
      "    iBrowser['UserAgent'] = navigator.userAgent;\n".
      "  //]] -->".
      "  </script>\n".
      $this->scripts;
    $result = preg_replace('|(.*)</head>|i', '$1'.$this->scripts."\n</head>", $result);
    # Замена макросов
    $result = $this->replaceMacros($result);

    if (count($this->headers)) foreach ($this->headers as $header) Header($header);
    
    if (!DEBUG_MODE) ob_start('ob_gzhandler');
    echo $result;
    if (!DEBUG_MODE) ob_end_flush();
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function pages($pagesCount, $itemsPerPage, $reverse = false)
  # Выводит список подстраниц для навигации по ним
  {
  global $request;

    if ($pagesCount>1) {
      $at_once = option('clientPagesAtOnce');
      if (!$at_once) $at_once = 10;
      
      $side_left = '';
      $side_right = '';
      
      $for_from = $reverse ? $pagesCount : 1;
      $default = $for_from;
      $for_to = $reverse ? 0 : $pagesCount+1;
      $for_delta = $reverse ? -1 : 1;

      # Если количество страниц превышает AT_ONCE
      if ($pagesCount > $at_once) {
        if ($reverse) { # Если установлен обратный порядок страниц
          if ($this->subpage < ($pagesCount - (integer)($at_once / 2))) $for_from = ($this->subpage + (integer)($at_once / 2));
          if ($this->subpage < (integer)($at_once / 2)) $for_from = $at_once;
          $for_to = $for_from - $at_once;
          if ($for_to < 0) {$for_from += abs($for_to); $for_to = 0;}
          if ($for_from != $pagesCount) $side_left = "<a href=\"".$request['path']."\" title=\"".strLastPage."\">&nbsp;&laquo;&nbsp;</a>";
          if ($for_to != 0) $side_right = "<a href=\"".$request['path']."p1/\" title=\"".strFirstPage."\">&nbsp;&raquo;&nbsp;</a>";
        } else { # Если установлен прямой порядок страниц
          if ($this->subpage > (integer)($at_once / 2)) $for_from = $this->subpage - (integer)($at_once / 2); 
          if ($pagesCount - $this->subpage < (integer)($at_once / 2) + (($at_once % 2)>0)) $for_from = $pagesCount - $at_once+1;
          $for_to = $for_from + $at_once;
          if ($for_from != 1) $side_left = "<a href=\"".$request['path']."\" title=\"".strFirstPage."\">&nbsp;&laquo;&nbsp;</a>";
          if ($for_to < $pagesCount) $side_right = "<a href=\"".$request['path']."p".$pagesCount."/\" title=\"".strLastPage."\">&nbsp;&raquo;&nbsp;</a>";
        }
      }
      $result = '<div class="pages">'.strPages;
      $result .= $side_left;
      for ($i = $for_from; $i != $for_to; $i += $for_delta) 
        if ($i == $this->subpage) $result .= '<span class="selected">&nbsp;'.$i.'&nbsp;</span>';
          else $result .= '<a href="'.$request['path'].($i==$default?'':'p'.$i.'/').'">&nbsp;'.$i.'&nbsp;</a>';
      $result .= $side_right;
      $result .= "</div>\n";
      return $result;
    } 
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function renderForm($form, $values=null)
  { 
  global $request;
  
    $result = '';
    $hidden = '';
    $body = '';
    $validator = '';
    $html = false;
    $file = false;
    if (empty($form['name'])) ErrorMessage(errFormHasNoName);
    if (count($form['fields'])) foreach($form['fields'] as $item) {
      if ((!isset($item['access'])) || (UserRights($item['access']))) {
        if (isset($item['label'])) $label = !empty($item['hint']) ? '<span class="hint" title="'.$item['hint'].'">'.$item['label'].'</span>': $item['label']; else $label = '';
        if (isset($item['pattern'])) $validator .= "if (!form.".$item['name'].".value.match(".$item['pattern'].")) {\nalert('".(empty($item['errormsg'])?sprintf(errFormPatternError, $item['name'], $item['pattern']):$item['errormsg'])."');\nresult = false;\nform.".$item['name'].".select();\n} else ";
        $value = 
          isset($item['value'])
            ? $item['value']
            : (isset($item['name']) && isset($values[$item['name']])
                ? $values[$item['name']] 
                : (isset($item['default'])
                    ? $item['default']
                    : ''
                  )
              );
        $width = isset($item['width'])?' style="width: '.$item['width'].';"':'';
        $disabled = isset($item['disabled']) && $item['disabled']?' disabled':'';
        $extra = isset($item['extra'])?' '.$item['extra']:'';
        $comment = isset($item['comment'])?' '.$item['comment']:'';
        switch(strtolower($item['type'])) {
          case 'hidden': 
            if (empty($item['name'])) ErrorMessage(sprintf(errFormFieldHasNoName, $item['type'], $form['name']));
            $hidden .= '<input type="hidden" name="'.$item['name'].'" value="'.$value.'" />'."\n";
          break;
          case 'divider': $body .= "<tr><td colspan=\"2\"><hr></td></tr>\n"; break;
          case 'text': $body .= '<tr><td colspan="2" class="formText"'.$extra.'>'.$value."</td></tr>\n"; break;
          case 'header': $body .= '<tr><th colspan="2" class="formHeader">'.$value."</th></tr>\n"; break;
          case 'edit': 
            if (empty($item['name'])) ErrorMessage(sprintf(errFormFieldHasNoName, $item['type'], $form['name']));
            $body .= '<tr><td class="formLabel">'.$label.'</td><td><input type="text" name="'.$item['name'].'" value="'.EncodeHTML($value).'"'.(empty($item['maxlength'])?'':' maxlength="'.$item['maxlength'].'"').$width.$disabled.$extra.' />'.$comment."</td></tr>\n"; break;
          break;
          case 'password': 
            if (empty($item['name'])) ErrorMessage(sprintf(errFormFieldHasNoName, $item['type'], $form['name']));
            $body .= '<tr><td class="formLabel">'.$label.'</td><td><input type="password" name="'.$item['name'].'"'.(empty($item['maxlength'])?'':' maxlength="'.$item['maxlength']).'"'.$width.$extra.' />'.$comment."</td></tr>\n";
            if (isset($item['equal'])) $validator .= "if (form.".$item['name'].".value != form.".$item['equal'].".value) {\nalert('".errFormBadConfirm."');\nresult = false;\nform.".$item['name'].".value = '';\nform.".$item['equal'].".value = ''\nform.".$item['equal'].".select();\n} else ";
          break;
          case 'select': 
            if (empty($item['name'])) ErrorMessage(sprintf(errFormFieldHasNoName, $item['type'], $form['name']));
            $body .= '<tr><td class="formLabel">'.$label.'</td><td><select name="'.$item['name'].'"'.$width.$disabled.$extra.'>'."\n";
            if (!isset($item['items']) && isset($item['values'])) $item['items'] = $item['values'];
            for($i = 0; $i < count($item['items']); $i++) {
              if (isset($item['values'])) $value = $item['values'][$i]; else $value = $i;
              $body .= '<option value="'.$value.'" '.($value == (isset($values[$item['name']]) ? $values[$item['name']] : (isset($item['value'])?$item['value']:'')) ? 'selected' : '').">".$item['items'][$i]."</option>\n";
            }
            $body .= '</select>'.$comment."</td></tr>\n";
          break;
          case 'listbox':
            if (empty($item['name'])) ErrorMessage(sprintf(errFormFieldHasNoName, $item['type'], $form['name']));
            $body .= '<tr><td class="formLabel">'.$label.'</td><td><select multiple name="'.$item['name'].'"'.$width.(isset($item['height'])?' size="'.$item['height'].'"':'').$disabled.$extra.">\n";
            if (!isset($item['items']) && isset($item['values'])) $item['items'] = $item['values'];
            for($i = 0; $i< count($item['items']); $i++) {
              if (isset($item['values'])) $value = $item['values'][$i]; else $value = $i;
              $body .= '<option value="'.$value.'" '.(count($values) && in_array($value, $values[$item['name']]) ? 'selected' : '').">".$item['items'][$i]."</option>\n";
            }
            $body .= '</select>'.$comment."</td></tr>\n";
          break;
          case 'checkbox': 
            if (empty($item['name'])) ErrorMessage(sprintf(errFormFieldHasNoName, $item['type'], $form['name']));
            $body .= '<tr><td>&nbsp;</td><td><input type="checkbox" name="'.$item['name'].'" value="'.($value ? $value : true).'" '.($value ? 'checked' : '').$disabled.$extra.' style="background-color: transparent; border-style: none; margin:0px;" /><span style="vertical-align: baseline"> '.$label."</span></td></tr>\n"; 
          break;
          case 'memo': 
            if (empty($item['name'])) ErrorMessage(sprintf(errFormFieldHasNoName, $item['type'], $form['name']));
            $body .= '<tr><td colspan="2">'.(empty($label)?'':'<span class="formLabel">'.$label.'</span><br />').'<textarea name="'.$item['name'].'" cols="40" rows="'.(empty($item['height'])?'1':$item['height']).'" '.$width.$disabled.$extra.' >'.EncodeHTML($value)."</textarea></td></tr>\n"; 
          break;
          case 'file': 
            if (empty($item['name'])) ErrorMessage(sprintf(errFormFieldHasNoName, $item['type'], $form['name']));
            $body .= '<tr><td class="formLabel">'.$label.'</td><td><input type="file" name="'.$item['name'].'" size="'.$item['width'].'"'.$disabled." />".$comment."</td></tr>\n";
            $file = true;
          break;
          default: ErrorMessage(sprintf(errFormUnknownType, $item['type'], $form['name']));
        }
      }
    }
    $this->scripts .= "
      function ".$form['name']."Submit()
      {
        var result = true;
        var form = document.forms.namedItem('".$form['name']."');
        ".(empty($validator)?'':$validator)."
        if (result) {
          var controls = form.elements;
          var count = controls.length;
          for (var i=0; i < count; i++) if (controls[i].type == 'checkbox') {
            var control = document.createElement('input');
            control.type = 'hidden';
            control.name = controls[i].name;
            control.value = controls[i].checked?controls[i].value:0;
            controls[i].name = '';
            form.appendChild(control);
          }
        }
        return result;
      }
    ";
    #if (!empty($validator)) $this->scripts .= "function ".$form['name']."Submit(strForm)\n{\nvar result = true;\n".$validator.";\nreturn result;\n}\n\n";
    $result .=
      "<div style=\"width: ".$form['width']."\" class=\"form\">\n".
      "<form ".(empty($form['name'])?'':'id="'.$form['name'].'" ')."action=\"".(empty($form['action'])?$request['path'].execScript:$form['action'])."\" method=\"post\"".(empty($validator)?'':' onSubmit="return '.$form['name'].'Submit();"').($file?' enctype="multipart/form-data"':'').">\n".
      "<div class=\"hidden\"><input type=\"hidden\" name=\"submitURL\" value=\"".$this->url()."\" />".
      $hidden."</div>\n".
      "<table>\n".
      (empty($form['caption'])?'':"<tr><th colspan=\"2\">".$form['caption']."</th></tr>\n").
      "<colgroup><col width=\"0*\" /><col width=\"100%\" /></colgroup>\n".
      $body.
      "<tr><td colspan=\"2\" class=\"buttons\"><br />".
      (in_array('ok', $form['buttons'])?'<input type="submit" class="button" value="OK" /> ':'').
      (array_key_exists('ok', $form['buttons'])?'<input type="submit" class="button" value="'.$form['buttons']['ok'].'" /> ':'').
      (in_array('reset', $form['buttons'])?'<input type="reset" class="button" value="'.strReset.'" /> ':'').
      (array_key_exists('reset', $form['buttons'])?'<input type="reset" class="button" value="'.$form['buttons']['reset'].'" /> ':'').
      (in_array('cancel', $form['buttons'])?'<input type="button" class="button" value="'.strCancel.'" onclick="javascript:history.back();" />':'').
      (array_key_exists('cancel', $form['buttons'])?'<input type="button" class="button" value="'.$form['buttons']['cancel'].'" onclick="javascript:history.back();" />':'').
      "</td></tr>\n".
      "</table>\n</form></div>\n";
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function buttonAddItem($caption = '', $value = '')
  {
  global $request;
    return '<form class="contentButton" action="'.$request['url'].execScript.'" method="get"><input type="hidden" name="action" value="'.(empty($value)?'add':$value).'"><input type="submit" value="'.(empty($caption) ? strAdd : $caption).'" class="contentButton" /></form>';
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function buttonBack($caption = '', $url='')
  {
  global $request;
    return '<form class="contentButton" action="" method="get"><input type="button" value="'.(empty($caption) ? strReturn : $caption).'" class="contentButton" onClick="'.(empty($url)?'javascript:history.back();':"window.location='".$url."'").'" /></form>';
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function button($caption, $url, $name='', $value='')
  {
  global $request;

    $result = '<form class="contentButton" action="'.$url.'" method="get">';
    if (!empty($name)) $result .= '<input type="hidden" name="'.$name.'" value="'.$value.'" />';
    $result .= '<input type="submit" value="'.$caption.'" class="contentButton" onClick="window.location=\''.$url.'\'" /></form>';
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

$page = new TClientUI;
$page->init();
$page->render();
?>
