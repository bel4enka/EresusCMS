<?
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# CMS Eresus™
# © 2005, ProCreat Systems
# Web: http://procreat.ru
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TMainMenu extends TPlugin {
  var
    $name = 'mainmenu',
    $title = 'MainMenu',
    $type = 'client',
    $version = '2.00a2',
    $description = 'Главное меню сайта',
    $settings = array(
      'root' => 0,
    );
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # Стандартные функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function TMainMenu()
  # производит регистрацию обработчиков событий
  {
  global $plugins;

    parent::TPlugin();
    $plugins->events['clientOnPageRender'][] = $this->name;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # Внутренние функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function pagesBrunch($owner = 0, $level = 0)
  {
  global $db;
    $result = array(array(), array());
    $items = $db->select('`pages`', "(`owner`='".$owner."') AND (`active`='1')", "`position`", false, "`id`,`caption`");
    if (count($items)) foreach($items as $item) {
      $result[0][] = str_repeat('- ', $level).$item['caption'];
      $result[1][] = $item['id'];
      $sub = $this->pagesBrunch($item['id'], $level+1);
      if (count($sub[0])) {
        $result[0] = array_merge($result[0], $sub[0]);
        $result[1] = array_merge($result[1], $sub[1]);
      }
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function menuBrunch($owner = 0, $path = '', $level = 0)
  # Функция строит ветку меню начиная от элемента с id = $owner
  #   $owner - id корневого предка
  #   $path - виртуальный путь к страницам
  #   $level - уровень вложенности
  {
  global $page, $db, $user;
    $result = '';
    $resultup = '';
    if (strpos($path, httpRoot) !== false) $path = substr($path, strlen(httpRoot));
    $items = $db->select('`pages`', "(`access`>='".($user['auth']?$user['access']:GUEST)."')AND (`owner`='".$owner."') AND (`active`='1') AND (`visible` = '1')", "`position`");

    if (count($items)) foreach($items as $item) {
      if ($item['type'] == 'url') {
        $item['options'] = decodeOptions($item['options']);
        $url = $item['options']['url'];
      } else $url = httpRoot.$path.($item['name']=='main'?'':$item['name'].'/');
      if ($level==0)
       $result .= '
        <tr>
         <td width="14" align="left"><img src="/style/msharik.gif" width="9" height="9" alt="" align="middle" style="margin-left:5">
         </td>
         <td class="tdmar">'.'<a href="'.$url.'" title="'.$item['hint'].'" class="menu2">'.$item['caption'].'</a>
         </td>
        </tr>'
       ;
      else
      {
       if ($page->id==$item['owner'])
        $resultup .= '&nbsp;&nbsp;<a href="'.$url.'" title="'.$item['hint'].'">'.$item['caption'].'</a>'."\n";
      }
      if ($level<1)
       $result .= $this->menuBrunch2($item['id'], $path.$item['name'].'/', $level+1, $resultup);
    }

    if ($resultup!='') {
      $qowner="";
      if ($page->owner==0) {
        $qowner=$page->caption;
      }
      else {
        $qow=$db->selectItem('pages', "`id`='".$page->owner."'", 'caption');
        $qowner=$qow['caption'];
      }

//      if (is_array($owik))

      $resultup ='<h2 class=sub_menuh2>'.$qowner.'</h2>'.$resultup.'';
    }
    $result='
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
        <td width="7"></td>
        <td>
     <table width="100%"  border="0" cellspacing="0" cellpadding="0">

           <tr>
                 <td>
                         <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                           <tr>
                                 <td width="6" height="25"><img src="/style/zleft.gif" width="6" height="25" alt=""></td>
                                 <td style="background-image:url(\'/style/zcenter.gif\')" width="10"><img src="/style/zsharik.gif" width="10" height="10" alt="" align="middle"></td>
                                 <td style="background-image:url(\'/style/zcenter.gif\'); padding-left:10;" width="100%" class="menu">РАЗДЕЛЫ</td>
                                 <td><img src="/style/zright.gif" width="6" height="25" alt=""></td>

                           </tr>
                         </table>
                 </td>
             </tr>
             <tr>
                 <td>
                   <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                     <tr>
                           <td width="1" style="background-color:#D9E2E9"></td>

                           <td>
                           <table width="100%"  border="0" cellspacing="0" cellpadding="0">

    '.$result.'
             <tr>
                     <td colspan="2"><table width="100%"  border="0" cellspacing="0" cellpadding="0">

       <tr>
             <td height="23"><img src="/style/mleft.gif" width="3" height="23" alt=""></td>
             <td style="background-image:url(\'/style/mcenter.gif\')" width="100%"></td>
             <td><img src="/style/mright.gif" width="3" height="23" alt=""></td>
       </tr>
     </table>
</td>
</tr>
</table>

</td>
<td width="1" style="background-color:#D9E2E9"></td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>

</table>


    ';
/*    foreach ($page as $k=>$v)
      $result.="$k __ $v<BR>";
      */

    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function menuBrunch2($owner, $path, $level, &$resultup)
  {
  global $page, $db, $user;
    $result = '';
    if (strpos($path, httpRoot) !== false) $path = substr($path, strlen(httpRoot));
    $items = $db->select('`pages`', "(`access`>='".($user['auth']?$user['access']:GUEST)."')AND (`owner`='".$owner."') AND (`active`='1') AND (`visible` = '1')", "`position`");
    if (count($items)) foreach($items as $item) {
      if ($item['type'] == 'url') {
        $item['options'] = decodeOptions($item['options']);
        $url = $item['options']['url'];
      } else $url = httpRoot.$path.($item['name']=='main'?'':$item['name'].'/');
      if ($level==0)
       $result .= '<a href="'.$url.'" title="'.$item['hint'].'">'.$item['caption'].'</a>'."\n";
      else
      {
       if (($page->owner==0)&&($page->id==$item['owner'])||
           ($page->owner!=0)&&($page->owner==$item['owner']))
        $resultup .= '<li><a href="'.$url.'" title="'.$item['hint'].'">'.$item['caption'].'</a>'."</li>\n";

      }
      if ($level<1)
       $result .= $this->menuBrunch2($item['id'], $path.$item['name'].'/', $level+1, $resultup);
    }
    return $result;
  }

  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#

function BokmenuBrunch($owner = 0, $path = '', $level = 0)
  # Функция строит ветку меню начиная от элемента с id = $owner
  #   $owner - id корневого предка
  #   $path - виртуальный путь к страницам
  #   $level - уровень вложенности
  {
  global $page, $db, $user;
    $result = '';
    $resultup = '';
    if (strpos($path, httpRoot) !== false) $path = substr($path, strlen(httpRoot));
    $items = $db->select('`pages`', "(`access`>='".($user['auth']?$user['access']:GUEST)."')AND (`owner`='2') AND (`active`='1') AND (`visible` = '1')", "`position`");
    $counti=0;
    $owik = $db->selectItem('pages', "`id`='2'", 'name');

    if (count($items)) foreach($items as $item) {
      /*if ($item['type'] == 'url') {
        $item['options'] = decodeOptions($item['options']);
        $url = $item['options']['url'];
      } else
      */
      $url = $owik['name'].'/'.$item['name'].'/';
      if ($level==0) {
        if (($counti!=count($items)) && ($counti!=0))
          $result .= '
           <td><img src="/style/razdelitel.gif" border="0" width="2" height="26" alt="" align="top"></td>
            <td class="menu">'.'<a href="/'.$url.'" title="'.$item['hint'].'" class="menu2">'.$item['caption'].'</a>
            </td>';
          else
            $result .= '
            <td class="menu">'.'<a href="/'.$url.'" title="'.$item['hint'].'" class="menu2">'.$item['caption'].'</a>
            </td>';
        $counti++;

       }
      else
      {
       if ($page->id==$item['owner'])
        $resultup .= '&nbsp;&nbsp;<a href="'.$url.'" title="'.$item['hint'].'">'.$item['caption'].'</a>'."\n";
      }
     /* if ($level<1)
       $result .= $this->menuBrunch2($item['id'], $path.$item['name'].'/', $level+1, $resultup);*/
    }

    if ($resultup!='') {
      $qowner="";
      if ($page->owner==0) {
        $qowner=$page->caption;
      }
      else {
        $qow=$db->selectItem('pages', "`id`='".$page->owner."'", 'caption');
        $qowner=$qow['caption'];
      }

     // $resultup ='<h2 class=sub_menuh2>'.$qowner.'</h2>'.$resultup.'';
    }
    $result='<table border="0" cellspacing="0" cellpadding="0" align="left">
    <tr>
    '.$result.'
    </tr>
    </table>
    ';

    return $result;
  }


  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # Административные функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function settings()
  {
  global $page, $db;

    $sections = $this->pagesBrunch();
    array_unshift($sections[0], 'КОРЕНЬ');
    array_unshift($sections[1], 0);
    $form = array(
      'name'=>'SettingsForm',
      'caption' => $this->title.' '.$this->version,
      'width' => '500px',
      'fields' => array (
        array('type'=>'hidden','name'=>'update', 'value'=>$this->name),
        array('type'=>'select','name'=>'root','label'=>'Корневой раздел', 'values'=>$sections[1], 'items'=>$sections[0]),
      ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $result = $page->renderForm($form, $this->settings);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # Обработчики событий
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientOnPageRender($text)
  {
    global $page;
    $text= str_replace('$(plgMainMenu)', ''.$this->menuBrunch($this->settings['root'], $page->clientURL($this->settings['root'])).'', $text);
    $text= str_replace('$(plgBokMenu)', ''.$this->BokmenuBrunch($this->settings['root'], $page->clientURL($this->settings['root'])).'', $text);

    return $text;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>