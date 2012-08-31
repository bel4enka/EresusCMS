<?
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# ������� ���������� ��������� Eresus�
# ������ 2.00
# � 2004-2006, ProCreat Systems
# http://procreat.ru/
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# ���������� ���������� �����
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TPages {
  var $access = ADMIN;
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function notifyMessage($new, $old=null)
  {
    $result = '';
    if (is_null($old)) {
      $result .= admPagesName.": ".$new['name']."\n";
      $result .= admPagesTitle.": ".$new['title']."\n";
      $result .= admPagesCaption.": ".$new['caption']."\n";
      $result .= admPagesHint.": ".$new['hint']."\n";
      $result .= admPagesDescription.": ".$new['description']."\n";
      $result .= admPagesKeywords.": ".$new['keywords']."\n";
      $result .= admPagesActive.": ".($new['active']?strYes:strNo)."\n";
      $result .= admPagesVisible.": ".($new['visible']?strYes:strNo)."\n";
      $result .= admAccessLevel.": ".constant('ACCESSLEVEL'.$new['access'])."\n";
      $result .= admPagesTemplate.": ".$new['template']."\n";
      $result .= admPagesContentType.": ".$new['type']."\n";
      $result .= admPagesOptions.": ".$new['options']."\n";
    } else {
      $result = "ID ".$new['id']." - <strong>".$old['caption']."</strong>\n".admChanges.":\n";
      if ($new['name'] != $old['name']) $result .= admPagesName.": ".$old['name']." &rarr; ".$new['name']."\n";
      if ($new['title'] != $old['title']) $result .= admPagesTitle.": ".$old['title']." &rarr; ".$new['title']."\n";
      if ($new['caption'] != $old['caption']) $result .= admPagesCaption.": ".$old['caption']." &rarr; ".$new['caption']."\n";
      if ($new['hint'] != $old['hint']) $result .= admPagesHint.": ".$old['hint']." &rarr; ".$new['hint']."\n";
      if ($new['description'] != $old['description']) $result .= admPagesDescription.": ".$old['description']." &rarr; ".$new['description']."\n";
      if ($new['keywords'] != $old['keywords']) $result .= admPagesKeywords.": ".$old['keywords']." &rarr; ".$new['keywords']."\n";
      if ($new['active'] != $old['active']) $result .= admPagesActive.": ".($old['active']?strYes:strNo)." &rarr; ".($new['active']?strYes:strNo)."\n";
      if ($new['visible'] != $old['visible']) $result .= admPagesVisible.": ".($old['visible']?strYes:strNo)." &rarr; ".($new['visible']?strYes:strNo)."\n";
      if ($new['access'] != $old['access']) $result .= admAccessLevel.": ".constant('ACCESSLEVEL'.$old['access'])." &rarr; ".constant('ACCESSLEVEL'.$new['access'])."\n";
      if ($new['template'] != $old['template']) $result .= admPagesTemplate.": ".$old['template']." &rarr; ".$new['template']."\n";
      if ($new['type'] != $old['type']) $result .= admPagesContentType.": ".$old['type']." &rarr; ".$new['type']."\n";
      if ($new['content'] != $old['content']) $result .= admPagesContent.": ".$old['content']." &rarr; ".$new['content']."\n";
      if ($new['created'] != $old['created']) $result .= admPagesCreated.": ".$old['created']." &rarr; ".$new['created']."\n";
      if ($new['updated'] != $old['updated']) $result .= admPagesUpdated.": ".$old['updated']." &rarr; ".$new['updated']."\n";
      if ($new['options'] != $old['options']) $result .= admPagesOptions.": ".$old['options']." &rarr; ".$new['options']."\n";
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function insert()
  # ������ ����� �������� � ��
  {
  global $db, $page, $request, $session;

    $item = getArgs($db->fields('pages'));
    $temp = $db->selectItem('pages', "(`name`='".$item['name']."') AND (`owner`='".$item['owner']."')");
    if (is_null($temp)) {
      $item['created'] = gettime('Y-m-d H:i:s');
      $item['updated'] = $item['created'];
      $item['options'] = trim($item['options']);
      $item['options'] = (empty($item['options']))?'':encodeOptions(text2array($item['options'], true));
      if (empty($item['position'])) $item['position'] = $db->count('pages', "`owner`='".$item['owner']."'");
      $db->insert('pages', $item);
      $item['id'] = $db->getInsertedId();
      SendNotify($this->notifyMessage($item));
      dbReorderItems('pages', "`owner`='".$request['arg']['owner']."'");
      goto($page->url(array('id'=>$item['id'])));
    } else {
      $session['errorMessage'] = sprintf(errItemWithSameName, $item['name']);
      saveRequest();
      goto($request['referer']);
    }
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function update()
  {
  global $db, $page, $request;

    $item = $db->selectItem('pages', "`id`='".$request['arg']['update']."'");
    $old = $item;
    foreach ($item as $key => $value) if (isset($request['arg'][$key])) $item[$key] = $request['arg'][$key];
    $item['active'] = isset($request['arg']['active'])?$request['arg']['active']:false;
    $item['visible'] = isset($request['arg']['visible'])?$request['arg']['visible']:false;
    $item['options'] = trim($item['options']);
    $item['options'] = (empty($item['options']))?'':encodeOptions(text2array($item['options'], true));
    $item['updated'] = gettime('Y-m-d H:i:s');
    if (isset($request['arg']['updatedAuto'])) $item['updated'] = gettime();
    $db->updateItem('pages', $item, "`id`='".$request['arg']['update']."'");
    SendNotify($this->notifyMessage($item, $old));
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function pagesList($owner = 0, $level = 0)
  # ������� ������ �������
  {
  global $db, $user, $page, $request;
  
    $items = $db->select('`pages`', "(`owner`='".$owner."') AND (`access` >= '".$user['access']."')", "`position`", false, '`id`,`caption`,`description`,`active`');
    $result = '';
    if (count($items)) foreach($items as $item) {
      $item['caption'] = trim($item['caption']);
      if (empty($item['caption'])) $item['caption'] = '---';
      $result .= '<tr><td>'.str_repeat('&nbsp;',$level*2).'<a'.((isset($request['arg']['id']) && ($request['arg']['id'] == $item['id']))?' class="selected"':($item['active']?'':' class="disabled"')).' href="'.$page->url(array('id'=>$item['id'])).'" title="'.$item['description'].'">'.$item['caption'].'</a> ';
      $result .=
        '<a href="'.$page->url(array('up'=>$item['id'])).'" title="'.admUp.'">'.img('core/img/aru.gif', admUp, admUp).'</a> '.
        '<a href="'.$page->url(array('down' => $item['id'])).'" title="'.admDown.'">'.img('core/img/ard.gif', admDown, admDown).'</a> '.
        "</td></tr>\n";
      $result .= $this->pagesList($item['id'], $level+1);
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function selectList($default, $skip=0, $owner = 0, $level = 0)
  {
  global $db, $user;
  
    $items = $db->select('`pages`', "(`owner`='".$owner."') AND (`access` >= '".$user['access']."')", "`position`", false, '`id`,`caption`');
    $result = '';
    if (count($items)) foreach($items as $item) {
      if ($item['id'] != $skip) {
        $item['caption'] = trim($item['caption']);
        if (empty($item['caption'])) $item['caption'] = '---';
        $result .= '<option value="'.$item['id'].'"'.($item['id'] == $default ? ' selected' : '').'>'.str_repeat('&nbsp;', $level*2).$item['caption']."</option>\n";
        $result .= $this->selectList($default, $skip, $item['id'], $level+1);
      }
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function renderPages()
  # ������� ������������ ������ ������� � �������� ����������
  {
  global $page, $request;

    $wnd['caption'] = strPages;
    $wnd['width'] = '100%';
    $wnd['body'] =
      "<table width=\"100%\">\n".
      "<tr><td style=\"padding: 0px;\"><table cellPadding=\"0\" cellSpacing=\"0\">\n".$this->pagesList()."\n</table>\n".
      "<tr><td><hr></td></tr>\n".
      "<tr><td>".
      '<form action="'.httpRoot.'admin.php" method="get">'."\n".
      '<input type="hidden" name="mod" value="pages"><input type="hidden" name="action" value="create"><input type="submit" value="'.admPagesAddInto.'" class="button" style="width: 100%"><br>'."\n".
      "<select name=\"owner\" style=\"width: 100%;\"><option value=\"0\">".admPagesRoot."</option>\n".$this->selectList(isset($request['arg']['id'])?$request['arg']['id']:'')."</select><br>\n</form></td></tr>\n";
    if (isset($request['arg']['id'])) {
      $wnd['body'] .=
        '<tr><td>'.
          '<form action="'.httpRoot.'admin.php" method="post">'.
          '<div class="admHidden"><input type="hidden" name="mod" value="pages"></div>'.
          '<div class="admHidden"><input type="hidden" name="action" value="move"></div>'.
          '<div class="admHidden"><input type="hidden" name="id" value="'.$request['arg']['id'].'"></div>'."\n".
        "<hr><div><input type=\"submit\" value=\"".admPagesMoveTo."\" class=\"button\" style=\"width: 100%\"></div>\n".
        "<div><select name=\"to\" style=\"width: 100%;\"><option value=\"0\">".admPagesRoot."</option>\n".$this->selectList(0, $request['arg']['id'])."</select></div><br>\n</form></td></tr>\n";
      if (UserRights(ADMIN)) $wnd['body'] .= "<tr><td><hr><form action=\"".httpRoot."admin.php\" method=\"post\"><div><input type=\"hidden\" name=\"mod\" value=\"pages\"><input type=\"hidden\" name=\"action\" value=\"delete\"><input type=\"hidden\" name=\"id\" value=\"".$request['arg']['id']."\"><input type=\"submit\" value=\"".admPagesDeleteBrunch."\" class=\"button\" style=\"width: 100%\"></div></form></td></tr>\n";
    }
    $wnd['body'] .= 
      "</table>\n";
    $result = $page->window($wnd);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function moveUp()
  # ������� ���������� �������� ����� � ������
  {
  global $db, $page, $request;
  
    $item = $db->selectItem('pages',"`id`='".$request['arg']['up']."'");
    dbReorderItems('pages', "`owner`='".$item['owner']."'");
    $item = $db->selectItem('pages',"`id`='".$request['arg']['up']."'");
    if ($item['position'] > 0) {
      $temp = $db->selectItem('pages',"(`owner`='".$item['owner']."') AND (`position`='".($item['position']-1)."')");
      $item['position']--;
      $temp['position']++;
      $db->updateItem('pages', $item, "`id`='".$item['id']."'");
      $db->updateItem('pages', $temp, "`id`='".$temp['id']."'");
    }
    goto($page->url());
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function moveDown()
  # ������� ���������� �������� ���� � ������
  {
  global $db, $page, $request;
  
    $item = $db->selectItem('pages',"`id`='".$request['arg']['down']."'");
    dbReorderItems('pages', "`owner`='".$item['owner']."'");
    $item = $db->selectItem('pages',"`id`='".$request['arg']['down']."'");
    if ($item['position'] < $db->count('pages', "`owner`='".$item['owner']."'")) {
      $temp = $db->selectItem('pages',"(`owner`='".$item['owner']."') AND (`position`='".($item['position']+1)."')");
      $item['position']++;
      $temp['position']--;
      $db->updateItem('pages', $item, "`id`='".$item['id']."'");
      $db->updateItem('pages', $temp, "`id`='".$temp['id']."'");
    }
    goto($page->url());
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function move()
  # ���������� �������� �� ����� ����� � ������
  {
  global $db, $page, $request;

    $item = $db->selectItem('pages', "`id`='".$request['arg']['id']."'");
    dbReorderItems('pages', "`owner`='".$item['owner']."'");
    $item['owner'] = $request['arg']['to'];
    $item['position'] = $db->count('pages', "`owner`='".$item['owner']."'");
    $db->updateItem('pages', $item, "`id`='".$request['arg']['id']."'");
    goto($page->url());
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function deleteBrunch($id) {
  global $db;
  
    $items = $db->select('`pages`', "`owner`='".$id."'", '', false, '`id`');
    if (count($items)) foreach($items as $item) $this->deleteBrunch($item['id']);
    $db->delete('pages', "`id`='".$id."'");
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function delete()
  # ������� ��������
  {
  global $db, $page, $request;

    $item = $db->selectItem('pages', "`id`='".$request['arg']['id']."'");
    $this->deleteBrunch($request['arg']['id']);
    dbReorderItems('pages', "`owner`='".$item['owner']."'");
    SendNotify(admDeleted.":\n".$this->notifyMessage($item));
    goto($page->url());
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function loadContentTypes()
  {
    global $plugins;
    $result[0] = array(); $result[1] = array();
    $result[0][] = admPagesContentDefault; $result[1][] = 'default';
    $result[0][] = admPagesContentList; $result[1][] = 'list';
    $result[0][] = admPagesContentURL; $result[1][] = 'url';
    if(count($plugins->list)) foreach($plugins->list as $plugin) if (strpos($plugin['type'], 'content') !== false) {
      $result[0][] = $plugin['title'];
      $result[1][] = $plugin['name'];
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function loadTemplates()
  {
    $result[0] = array();
    $result[1] = array();
    $dir = filesRoot.'templates/';
    $hnd = opendir($dir);
    while (($filename = readdir($hnd))!==false) if (preg_match('/.*\.tmpl$/', $filename)) {
      $description = file_get_contents($dir.$filename);
      preg_match('/<!--(.*?)-->/', $description, $description);
      $description = trim($description[1]);
      $result[0][] = $description;
      $result[1][] = substr($filename, 0, strrpos($filename, '.'));
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function create()
  # ������� ������� ����� ��� ���������� ����� ��������
  {
  global $page, $plugins, $request;

    $content = $this->loadContentTypes();
    $templates = $this->loadTemplates();
    restoreRequest();
    $form = array (
      'name' => 'createPage',
      'caption' => strAdd,
      'width' => '600px',
      'fields' => array (
        array ('type' => 'hidden','name'=>'owner','value'=>$request['arg']['owner']),
        array ('type' => 'hidden','name'=>'action', 'value'=>'insert'),
        array ('type' => 'edit','name' => 'name','label' => admPagesName,'width' => '150px','maxlength' => '32', 'pattern'=>'/[\w]+/', 'errormsg'=>admPagesNameInvalid),
        array ('type' => 'edit','name' => 'title','label' => admPagesTitle,'width' => '100%','maxlength' => '255', 'pattern'=>'/.+/', 'errormsg'=>admPagesTitleInvalid),
        array ('type' => 'edit','name' => 'caption','label' => admPagesCaption,'width' => '100%','maxlength' => '64', 'pattern'=>'/.+/', 'errormsg'=>admPagesCaptionInvalid),
        array ('type' => 'edit','name' => 'hint','label' => admPagesHint,'width' => '100%','maxlength' => '255'),
        array ('type' => 'edit','name' => 'description','label' => admPagesDescription,'width' => '100%','maxlength' => '255'),
        array ('type' => 'edit','name' => 'keywords','label' => admPagesKeywords,'width' => '100%','maxlength' => '255'),
        array ('type' => 'select','name' => 'template','label' => admPagesTemplate, 'items' => $templates[0], 'values' => $templates[1], 'value'=>pageTemplateDefault),
        array ('type' => 'select','name' => 'type','label' => admPagesContentType, 'items' => $content[0], 'values' => $content[1], 'value'=>contentTypeDefault),
        array ('type' => 'checkbox','name' => 'active','label' => admPagesActive, 'value'=>true),
        array ('type' => 'checkbox','name' => 'visible','label' => admPagesVisible, 'value'=>true),
        array ('type' => 'select','name' => 'access','label' => admAccessLevel,'access' => ADMIN,'values'=>array(ADMIN,EDITOR,USER,GUEST),'items' => array (ACCESSLEVEL2,ACCESSLEVEL3,ACCESSLEVEL4,ACCESSLEVEL5), 'value'=>GUEST),
        array ('type' => 'edit','name' => 'position','label' => admPosition,'access' => ADMIN,'width' => '4em','maxlength' => '5'),
        array ('type' => 'memo','name' => 'options','label' => admPagesOptions,'height' => '5')
      ),
      'buttons' => array('ok', 'cancel'),
    );
  
    $result = $page->renderForm($form, $request['arg']);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function edit($id)
  {
    global $db, $page;

    $item = $db->selectItem('pages', "`id`='".$id."'");
    $content = $this->loadContentTypes();
    $templates = $this->loadTemplates();
    # ���������� �����
    $item['options'] = array2text(decodeOptions($item['options']), true);
    $form['caption'] = $item['caption'];
    # ��������� ����� ��������
    $urlAbs = $page->clientURL($item['id']);

    $form = array(
      'name' => 'PageForm',
      'caption' => $item['caption'].' ('.$item['name'].')',
      'width' => '100%',
      'fields' => array (
        array ('type' => 'hidden','name' => 'update', 'value'=>$item['id']),
        array ('type' => 'edit','name' => 'id','label' => admPagesID,'width' => '50px','maxlength' => '5', 'access'=>ROOT),
        array ('type' => 'edit','name' => 'name','label' => admPagesName,'width' => '150px','maxlength' => '32', 'pattern'=>'/[a-z0-9_]+/i', 'errormsg'=>admPagesNameInvalid),
        array ('type' => 'edit','name' => 'title','label' => admPagesTitle,'width' => '100%','maxlength' => '255', 'pattern'=>'/.+/', 'errormsg'=>admPagesTitleInvalid),
        array ('type' => 'edit','name' => 'caption','label' => admPagesCaption,'width' => '100%','maxlength' => '64', 'pattern'=>'/.+/', 'errormsg'=>admPagesCaptionInvalid),
        array ('type' => 'edit','name' => 'hint','label' => admPagesHint,'width' => '100%','maxlength' => '128'),
        array ('type' => 'edit','name' => 'description','label' => admPagesDescription,'width' => '100%','maxlength' => '255'),
        array ('type' => 'edit','name' => 'keywords','label' => admPagesKeywords,'width' => '100%','maxlength' => '255'),
        array ('type' => 'select','name' => 'template','label' => admPagesTemplate, 'items' => $templates[0], 'values' => $templates[1]),
        array ('type' => 'select','name' => 'type','label' => admPagesContentType, 'items' => $content[0], 'values' => $content[1]),
        array ('type' => 'checkbox','name' => 'active','label' => admPagesActive),
        array ('type' => 'checkbox','name' => 'visible','label' => admPagesVisible),
        array ('type' => 'select','name' => 'access','label' => admAccessLevel,'access' => ADMIN,'values'=>array(ADMIN,EDITOR,USER,GUEST),'items' => array (ACCESSLEVEL2,ACCESSLEVEL3,ACCESSLEVEL4,ACCESSLEVEL5)),
        array ('type' => 'edit','name' => 'position','label' => admPosition,'access' => ADMIN,'width' => '4em','maxlength' => '5'),
        array ('type' => 'memo','name' => 'options','label' => admPagesOptions,'height' => '5'),
        array ('type' => 'edit','name' => 'created','label' => admPagesCreated,'access' => ADMIN,'width' => '10em','maxlength' => '19'),
        array ('type' => 'edit','name' => 'updated','label' => admPagesUpdated,'access' => ADMIN,'width' => '10em','maxlength' => '19'),
        array ('type' => 'checkbox','name' => 'updatedAuto','label' => admPagesUpdatedAuto, 'value'=>true),
        array ('type' => 'text', 'value'=>admPagesThisURL.': <a href="'.$urlAbs.'">'.$urlAbs.'</a>'),
      ),
      'buttons' => array('apply', 'reset'),
    );
    $result = $page->renderForm($form, $item);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminRender()
  {
  global $db, $page, $plugins, $request;

    if (UserRights($this->access)) {
      $result = '';
      if (isset($request['arg']['update'])) $this->update();
      elseif (isset($request['arg']['up'])) $this->moveUp();
      elseif (isset($request['arg']['down'])) $this->moveDown();
      elseif (isset($request['arg']['action'])) switch($request['arg']['action']) {
        case 'create': $result = $this->create(); break;
        case 'insert': $this->insert();
        case 'move': $this->move(); break;
        case 'delete': $this->delete(); break;
      } else {
        if (isset($request['arg']['id'])) $result .= $this->edit($request['arg']['id']);
        $result = "<table width=\"100%\"><tr><td valign=\"top\" style=\"width: 30%\">\n".$this->renderPages()."</td>\n<td valign=\"top\">".$result."</td></tr></table>\n";
      }
      return $result;
    }
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
?>