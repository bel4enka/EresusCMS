<?php
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# CMS Eresus� 2.00
# � 2007, ProCreat Systems
# http://procreat.ru/
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TSpamPrevent extends TPlugin {
  var $name = 'spamprevent';
  var $type = 'client';
  var $title = 'SpamPrevent';
  var $version = '1.02';
  var $description = '������ E-mail ������� �� ����-�������';
  var $settings = array(
    'href_method' => 'onmouseover',
    'href_fake_email' => 'abuse@spamcop.net',
    'text_method' => 'entity',
  );
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ����������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function TSpamPrevent()
  # ���������� ����������� ������������ �������
  {
    global $plugins;

    parent::TPlugin();
    $plugins->events['clientOnPageRender'][] = $this->name;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ���������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientInstallScripts()
  {
    global $page;
    $page->scripts .= "
      function ".$this->name."ActionCnange(oSender)
      {
        //var oForm = document.getElementById(sFormName);
        var Row = oSender.parentNode.offsetParent.rows[oSender.parentNode.parentNode.rowIndex+1];
        oSender.form.actionValue.disabled = oSender.value == 'none';
        switch (oSender.value) {
          case 'none': Row.cells[0].innerHTML = ''; break;
          case 'action': Row.cells[0].innerHTML = 'URL'; break;
          case 'mailto': Row.cells[0].innerHTML = 'E-mail'; break;
        }
      }
    ";
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ���������������� �������
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
        array('type'=>'text', 'value'=>'SpamPrevent �������� ��� ������ e-mail �� ��������� ����� �������, ����� ������ �� �� �������, ���������� ���� ������� ��� ��������.'),
        array('type'=>'header', 'value'=>'������ ������� � �������'),
        array('type'=>'select', 'name' => 'href_method', 'label' => '�����', 'items' => array('(�� ������������ ������)', 'JavaScript - ����������� ����� ������ ��� ��������� ����'), 'values' => array('none', 'onmouseover')),
        array('type'=>'edit', 'name' => 'href_fake_email', 'label' => '��������� �����', 'width' => '100%'),
        array('type'=>'header', 'value'=>'������ ������� � ������'),
        array('type'=>'select', 'name' => 'text_method', 'label' => '�����', 'items' => array('(�� ������������ ������)', '�������������� ������� ������ � ����.����'), 'values' => array('none', 'entity')),
      ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $result = $page->renderForm($form, $this->settings);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ����������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientOnPageRender($text)
  {
    global $page;

    define('local_chars', '\d\w!#$%&\'*+\-\/=?^_`{|}~');
    define('local_part', '['.local_chars.']['.local_chars.'.]{0,63}');
    define('server_part', '[\d\w][\d\w\-]+\.[\d\w\-.]{2,}');
    if ($this->settings['href_method'] != 'none') {
      preg_match_all('/<a\s+.*href="mailto:(.+)"(.*)>/Ui', $text, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
      $delta = 0;
      for($i = 0; $i < count($matches); $i++) {
        switch ($this->settings['href_method']) {
          case 'onmouseover':
            $text = substr_replace($text, $this->settings['href_fake_email'], $matches[$i][1][1]+$delta, strlen($matches[$i][1][0]));
            $delta += strlen($this->settings['href_fake_email']) - strlen($matches[$i][1][0]);
            $mail = chunk_split('mailto:'.$matches[$i][1][0], mt_rand(3, 6), "'+'");
            $code = ' onmouseover="this.href=\''.$mail.'\'"';
            $text = substr_replace($text, $code, $matches[$i][2][1]+$delta, 0);
            $delta += strlen($code);
          break;
        }
      }
    }
    if ($this->settings['text_method'] != 'none') {
      preg_match_all('/(mailto:|[^'.local_chars.'])('.local_part.'@'.server_part.')/i', $text, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
      $delta = 0;
      for($i = 0; $i < count($matches); $i++) if (!preg_match('/mailto:/i', $matches[$i][0][0])) {
        switch ($this->settings['text_method']) {
          case 'entity':
            $replace = '';
            for($j = 0; $j < strlen($matches[$i][2][0]); $j++) $replace .= '&#'.ord($matches[$i][2][0]{$j}).';';
            $text = substr_replace($text, $replace, $matches[$i][2][1]+$delta, strlen($matches[$i][2][0]));
            $delta += strlen($replace) - strlen($matches[$i][2][0]);
          break;
        }
      }
    }
    return $text;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>