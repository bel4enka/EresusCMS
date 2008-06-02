<?
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# Система управления контентом Eresus™
# Версия 2.09
# © 2004-2007, ProCreat Systems
# http://procreat.ru/
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# Управление учетными записями пользователей
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TUsers {
  var
    $access = ADMIN,
    $itemsPerPage = 30,
    $pagesDesc = false;
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function checkMail($mail)
  {
  global $session;

    $host = substr($mail, strpos($mail, '@')+1);
    $ip = gethostbyname($host);
    if ($ip == $host) {
      $session['errorMessage'] = sprintf(errNonexistedDomain, $host);
      return false;
    }
    return true;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function notifyMessage($new, $old=null)
  {
    $result = '';
    if (is_null($old)) {
      $result .= admUsersName.": ".$new['name']."\n";
      $result .= admUsersLogin.": ".$new['login']."\n";
      $result .= admAccessLevel.": ".constant('ACCESSLEVEL'.$new['access'])."\n";
      $result .= admUsersMail.": ".$new['mail']."\n";
    } else {
      $result = "ID ".$new['id']." - <strong>".$old['name']."</strong>\n".admChanges.":\n";
      if ($new['name'] != $old['name']) $result .= admUsersName.": ".$old['name']." &rarr; ".$new['name']."\n";
      if ($new['login'] != $old['login']) $result .= admUsersLogin.": ".$old['login']." &rarr; ".$new['login']."\n";
      if ($new['active'] != $old['active']) $result .= admUsersAccountState.": ".($old['active']?strYes:strNo)." &rarr; ".($new['active']?strYes:strNo)."\n";
      if ($new['loginErrors'] != $old['loginErrors']) $result .= admUsersLoginErrors.": ".$old['loginErrors']." &rarr; ".$new['loginErrors']."\n";
      if ($new['access'] != $old['access']) $result .= admAccessLevel.": ".constant('ACCESSLEVEL'.$old['access'])." &rarr; ".constant('ACCESSLEVEL'.$new['access'])."\n";
      if ($new['mail'] != $old['mail']) $result .= admUsersMail.": ".$old['mail']." &rarr; ".$new['mail']."\n";
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function check_for_root($item)
  {
    return ($item['access'] != ROOT);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function check_for_edit($item)
  {
  global $user;
    return (($item['access'] != ROOT)||($user['id'] == $item['id'])) && UserRights(ADMIN);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function toggle()
  {
  global $db, $page, $request;

    $item = $db->selectItem('users', "`id`='".$request['arg']['toggle']."'");
    $item['active'] = !$item['active'];
    $db->updateItem('users', $item, "`id`='".$request['arg']['toggle']."'");
    SendNotify(($item['active']?admActivated:admDeactivated).': '.$item['name']);
    goto($page->url());
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function update()
  {
  global $db, $page, $request, $session, $user;

    $item = $db->selectItem('users', "`id`='".$request['arg']['update']."'");
    $old = $item;
    foreach ($item as $key => $value) if (isset($request['arg'][$key])) $item[$key] = $request['arg'][$key];
    $item['active'] = $request['arg']['active'] || ($user['id'] == $item['id']);
    if ($this->checkMail($item['mail'])) {
      $db->updateItem('users', $item, "`id`='".$request['arg']['update']."'");
      SendNotify($this->notifyMessage($item, $old));
    };
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function insert()
  {
  global $db, $page, $request;

    $fields = $db->fields('users');
    foreach ($fields as $field) if (isset($request['arg'][$field])) $item[$field] = $request['arg'][$field];
    if ($request['arg']['pswd1'] == $request['arg']['pswd2']) $item['hash'] = md5($request['arg']['pswd1']); else Logout();
    if ($this->checkMail($item['mail'])) {
      $db->insert('users', $item); 
      SendNotify(admUsersAdded.': '.$this->notifyMessage($item), '', false, '', $page->url(array('action'=>'')));
    };
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function delete()
  {
  global $db, $page, $request;

    $item = $db->selectItem('users', "`id`='".$request['arg']['delete']."'");
    $db->delete('users', "`id`='".$request['arg']['delete']."'");
    SendNotify(admDeleted.': '.$this->notifyMessage($item));
    goto($page->url());
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function password()
  {
  global $db, $page, $request;

    $item = $db->selectItem('users', "`id`='".$request['arg']['password']."'");
    if ($request['arg']['pswd1'] == $request['arg']['pswd2']) $item['hash'] = md5($request['arg']['pswd1']); else exit;
    $db->updateItem('users', $item, "`id`='".$request['arg']['password']."'");
    SendNotify(admUsersPasswordChanged.': '.$item['name']);
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function edit()
  {
  global $db, $page, $request, $user;

    $item = $db->selectItem('users', "`id`='".$request['arg']['id']."'");
    $form = array(
      'name' => 'UserForm',
      'caption' => admUsersChangeUser.' №'.$item['id'],
      'width' => '400px',
      'fields' => array (
        array('type'=>'hidden','name'=>'update', 'value'=>$item['id']),
        array('type'=>'edit','name'=>'name','label'=>admUsersName,'maxlength'=>32,'width'=>'100%','value'=>$item['name'], 'pattern'=>'/.+/', 'errormsg'=>admUsersNameInvalid),
        array('type'=>'edit','name'=>'login','label'=>admUsersLogin,'maxlength'=>16,'width'=>'100%','value'=>$item['login'], 'pattern'=>'/^[a-z\d_]+$/', 'errormsg'=>admUsersLoginInvalid, 'access'=>ADMIN),
        array('type'=>'select','name'=>'access','label'=>admAccessLevel, 'values'=>array('2','3','4'),'items'=>array(ACCESSLEVEL2, ACCESSLEVEL3, ACCESSLEVEL4), 'value'=>$item['access'], 'disabled'=>$item['access'] == ROOT, 'access'=>ADMIN),
        array('type'=>'checkbox','name'=>'active','label'=>admUsersAccountState,'value'=>$item['active'], 'access'=>ADMIN),
        array('type'=>'edit','name'=>'loginErrors','label'=>admUsersLoginErrors,'maxlength'=>2,'width'=>'30px','value'=>$item['loginErrors'], 'access'=>ADMIN),
        array('type'=>'edit','name'=>'mail','label'=>admUsersMail,'maxlength'=>32,'width'=>'100%','value'=>$item['mail'], 'pattern'=>'/^[\w]+[\w\d_\.\-]+@[\w\d\-]{2,}\.[a-z]{2,5}$/i', 'errormsg'=>admUsersMailInvalid, 'access'=>ADMIN),
      ),
      'buttons' => array(UserRights($this->access)?'ok':'', 'apply', 'cancel'),
    );
    
    $pswd = array(
      'name' => 'PasswordForm',
      'caption' => admUsersChangePassword,
      'width' => '400px',
      'fields' => array (
        array('type'=>'hidden','name'=>'password', 'value'=>$item['id']),
        array('type'=>'password','name'=>'pswd1','label'=>admUsersPassword,'maxlength'=>32,'width'=>'100%'),
        array('type'=>'password','name'=>'pswd2','label'=>admUsersConfirmation,'maxlength'=>32,'width'=>'100%', 'equal'=>'pswd1', 'errormsg'=>admUsersConfirmInvalid),
      ),
      'buttons' => array(UserRights($this->access)?'ok':'apply', 'cancel'),
    );
    
    $result = $page->renderForm($form)."<br />\n".$page->renderForm($pswd);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function create()
  {
  global $db, $page;

    $form = array(
      'name'=>'UserForm',
      'caption' => admUsersCreate,
      'width' => '400px',
      'fields' => array (
        array('type'=>'hidden','name'=>'action','value'=>'insert'),
        array('type'=>'edit','name'=>'name','label'=>admUsersName,'maxlength'=>32,'width'=>'100%', 'pattern'=>'/.+/', 'errormsg'=>admUsersNameInvalid),
        array('type'=>'edit','name'=>'login','label'=>admUsersLogin,'maxlength'=>16,'width'=>'100%', 'pattern'=>'/^[a-z0-9_]+$/i', 'errormsg'=>admUsersLoginInvalid),
        array('type'=>'select','name'=>'access','label'=>admAccessLevel, 'width'=>'100%','values'=>array('2','3','4'),'items'=>array(ACCESSLEVEL2,ACCESSLEVEL3,ACCESSLEVEL4), 'value'=>USER),
        array('type'=>'checkbox','name'=>'active','label'=>admUsersAccountState,'value'=>'1'),
        array('type'=>'divider'),
        array('type'=>'password','name'=>'pswd1','label'=>admUsersPassword,'maxlength'=>32,'width'=>'100%'),
        array('type'=>'password','name'=>'pswd2','label'=>admUsersConfirmation,'maxlength'=>32,'width'=>'100%', 'equal'=>'pswd1', 'errormsg'=>admUsersConfirmInvalid),
        array('type'=>'divider'),
        array('type'=>'edit','name'=>'mail','label'=>admUsersMail,'maxlength'=>32,'width'=>'100%'),
      ),
      'buttons'=>array('ok', 'cancel')
    );
    
    $result = $page->renderForm($form);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminRender()
  {
  global $db, $page, $request, $user;

    $result = '';
    $granted = false;
    if (UserRights($this->access)) $granted = true; else {
      if ($request['arg']['id'] == $user['id']) {
        if (empty($request['arg']['password']) || ($request['arg']['password'] == $user['id'])) $granted = true;
        if (empty($request['arg']['update']) || ($request['arg']['update'] == $user['id'])) $granted = true;
      }
    }  
    if ($granted) {
      if (isset($request['arg']['update'])) $this->update();
      elseif (isset($request['arg']['password'])  && (!isset($request['arg']['action']) || ($request['arg']['action'] != 'login'))) $this->password();
      elseif (isset($request['arg']['toggle'])) $this->toggle();
      elseif (isset($request['arg']['delete'])) $this->delete();
      elseif (isset($request['arg']['id'])) $result = $this->edit();
      elseif (isset($request['arg']['action'])) switch($request['arg']['action']) {
        case 'create': $result = $this->create(); break;
        case 'insert': $this->insert(); break;
      } else {
        $table = array (
          'name' => 'users',
          'key'=>'id',
          'itemsPerPage' => 20,
          'columns' => array(
            array('name' => 'id', 'caption' => 'ID', 'align' => 'right', 'width' => '40px'),
            array('name' => 'name', 'caption' => admUsersName, 'align' => 'left'),
            array('name' => 'access', 'caption' => admUsersAccessLevelShort, 'align' => 'center', 'width' => '70px', 'replace' => array (
              '1' => '<span style="font-weight: bold; color: red;">ROOT</span>',
              '2' => '<span style="font-weight: bold; color: red;">admin</span>',
              '3' => '<span style="font-weight: bold; color: blue;">editor</span>',
              '4' => 'user'
            )),
            array('name' => 'login', 'caption' => admUsersLogin, 'align' => 'left'),
            array('name' => 'mail', 'caption' => admUsersMail, 'align' => 'center', 'macros'=>true, 'value'=>'<a href="mailto:$(mail)">$(mail)</a>'),
            array('name' => 'lastVisit', 'caption' => admUsersLastVisitShort, 'align' => 'center', 'width' => '140px'),
            array('name' => 'loginErrors', 'caption' => admUsersLoginErrorsShort, 'align' => 'center', 'replace' => array (
              '0' => '',
            )),
          ),
          'controls' => array (
            'delete' => 'check_for_root',
            'edit' => 'check_for_edit',
            'toggle' => 'check_for_root',
          ),
          'tabs' => array(
            'width'=>'180px',
            'items'=>array(
             array('caption'=>admUsersCreate, 'name'=>'action', 'value'=>'create')
            )
          )
        );
        $result = $page->renderTable($table);
      }
      return $result;
    }
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
?>