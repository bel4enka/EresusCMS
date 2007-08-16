<?php
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# LoginBox (CMS Eresus™ Plugin)
# © 2006, ProCreat Systems
# Web: http://procreat.ru
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TLoginBox extends TContentPlugin {
  var
    $name = 'loginbox',
    $title = 'LoginBox',
    $type = 'client,content',
    $version = '1.00b',
    $description = 'Форма авторизации',
    $settings = array(
      'tmplForm' => '',
      'tmplInfo' => '$(userName)',
    );
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # Стандартные функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function TLoginBox()
  # производит регистрацию обработчиков событий
  {
  global $plugins;

    parent::TPlugin();
    $plugins->events['clientOnPageRender'][] = $this->name;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # Клиентские функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientRenderContent()
  {
    global $request, $page, $session, $db;

    if ($request['arg']['action']=='remind') {
      $item = $db->selectItem('users', "`mail`='".strtolower($request['arg']['mail'])."'");
      if (is_null($item)) {
        ErrorMessage('ОШИБКА! Пользователя с таким e-mail '.$request['arg']['mail'].' не найдено!');
        goto($request['path']);
        exit;
      }
      $item['active'] = true;
      srand ((double) microtime() * 1000000);
      for($i=0; $i<7; $i++) $pswd .= sprintf("%c",rand(97,122));
      $item['hash'] = md5($pswd);
      $item['lastVisit'] = gettime();
      $db->updateItem('users', $item, "`id`='".$item['id']."'");
      $message = "Здравствуйте, \$(userName).<br><br>\nДля Вашей учетной записи на сайте \"\$(siteName)\" сгенерирован новый пароль.<br>\nВаш логин: <strong>\$(userLogin)</strong><br>\nВаш пароль: <strong>\$(userPassword)</strong>";
      $message = str_replace(
        array(
          '$(userName)',
          '$(userLogin)',
          '$(userPassword)',
        ),
        array(
         $item['name'],
         $item['name'],
         $pswd,
        ),
      $message);
      $message = $page->replaceMacros($message);
      sendNotify("Восстановление пароля:\n  Имя: ".$item['name']."\n  e-mail: ".$item['mail']);
      sendMail($item['mail'], 'Восстановление пароля', $message, true);
      $session['message'] = 'Новый пароль был выслан на адрес '.$item['mail'];
      goto($request['path']);
      exit;
    } else {
      $form = array (
        'name' => 'remind',
        'caption' => 'Восстановление пароля',
        'width' => '400px',
        'fields' => array (
          array ('type' => 'hidden', 'name' => 'action', 'value' => 'remind'),
          array('type'=>'edit','name'=>'mail','label'=>'e-mail','maxlength'=>32,'width'=>'100%'),
        ),
        'buttons' => array('ok'),
      );
      $result = $page->renderForm($form);
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # Административные функции
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function settings()
  {
  global $page;

    $form = array(
      'name'=>'SettingsForm',
      'caption' => $this->title.' '.$this->version,
      'width' => '500px',
      'fields' => array (
        array('type'=>'hidden','name'=>'update', 'value'=>$this->name),
        array('type'=>'memo','name'=>'tmplForm','label'=>'Шаблон формы ввода логина/пароля','height'=>'10'),
        array('type'=>'text','value' => 'Атрибут action формы может иметь любое значение. Обязательными полями являются: <b>action</b> со значением login, <b>user</b> - имя пользователя и <b>password</b> - пароль пользователя.'),
        array('type'=>'memo','name'=>'tmplInfo','label'=>'Шаблон блока информации о пользователе','height'=>'10'),
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
    global $user, $request, $page;

    if ($user['auth']) $result = $this->settings['tmplInfo'];
    else $result = $this->settings['tmplForm'];
    $text = str_replace('$(plgLoginBox)', $result, $text);
    return $text;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>