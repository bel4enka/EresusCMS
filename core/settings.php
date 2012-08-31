<?
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# ������� ���������� ��������� Eresus�
# ������ 2.00
# � 2004-2006, ProCreat Systems
# http://procreat.ru/
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# ������������ ������� ���������� ������
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TSettings {
  var $access = ADMIN;
  var $tabs = array(
    'width'=>'120px',
    'items'=>array(
      array('caption'=>admSettingsMain, 'name'=>'section', 'value'=>'main'),
      array('caption'=>admSettingsMail, 'name'=>'section', 'value'=>'mail'),
      array('caption'=>admSettingsFiles, 'name'=>'section', 'value'=>'files'),
      array('caption'=>admSettingsOther, 'name'=>'section', 'value'=>'other'),
    )
  );
  var $notify = '';
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function mkstr($name, $caption, $type='string', $options=array())
  {
    global $request, $locale;
    
    $result = "  define('".(isset($options['locale'])?($options['locale']?$locale['prefix']:''):'').$name."', ";
    $quot = "'";
    $value = (isset($request['arg'][$name]))?$request['arg'][$name]:constant($name);
    if (isset($options['nobr']) && $options['nobr']) $value = str_replace(array("\n", "\r"), ' ', $value);
    if (isset($options['savebr']) && $options['savebr']) {
      $value = addcslashes($value, "\n\r");
      $value = str_replace('\\','\\\\', $value);
      $quot = '"';
    }
    if ($value != constant($name)) $this->notify .= '<strong>'.$caption.':</strong> "'.constant($name).'" &rarr; "'.$value."\"\n";
    switch ($type) {
      case 'string': $value = $quot.$value.$quot; break;
      case 'bool': $value = empty($value)?'false':'true'; break;
      case 'int': if (empty($value)) $value = 0; break;
    }
    $result .= $value.");\n";
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function update()
  { 
    global $request;
    
    $settings = "<?\n";                                                                       

    $settings .= $this->mkstr('siteName', admConfigSiteName, 'string', array('locale'=>true));
    $settings .= $this->mkstr('siteTitle', admConfigSiteTitle, 'string', array('locale'=>true));
    $settings .= $this->mkstr('siteTitleReverse', admConfigTitleReverse, 'bool');
    $settings .= $this->mkstr('siteKeywords', admConfigSiteKeywords, 'string', array('nobr'=>true));
    $settings .= $this->mkstr('siteDescription', admConfigSiteDescription, 'string', array('locale'=>true, 'nobr'=>true));
    $settings .= $this->mkstr('mailFromAddr', admConfigMailFromAddr, 'string', array('locale'=>true));
    $settings .= $this->mkstr('mailFromName', admConfigMailFromName, 'string', array('locale'=>true));
    $settings .= $this->mkstr('mailFromOrg', admConfigMailFromOrg, 'string', array('locale'=>true));
    $settings .= $this->mkstr('mailReplyTo', admConfigMailReplyTo, 'string', array('locale'=>true));
    $settings .= $this->mkstr('mailCharset', admConfigMailCharset, 'string', array('locale'=>true));
    $settings .= $this->mkstr('mailFromSign', admConfigMailSign, 'string', array('locale'=>true,'savebr'=>true));
    $settings .= $this->mkstr('sendNotifyTo', admConfigSendNotifyTo, 'string');
    $settings .= $this->mkstr('filesOwnerSetOnUpload', admConfigFilesOwnerSetOnUpload, 'bool');
    $settings .= $this->mkstr('filesOwnerDefault', admConfigFilesOwnerDefault, 'string');
    $settings .= $this->mkstr('filesModeSetOnUpload', admConfigFilesModeSetOnUpload, 'bool');
    $settings .= $this->mkstr('filesModeDefault', admConfigFilesModeDefault, 'string');
    $settings .= $this->mkstr('contentTypeDefault', admConfigDefaultContentType, 'string');
    $settings .= $this->mkstr('pageTemplateDefault', admConfigDefaultPageTamplate, 'string');

    $settings .= "?>";
    $fp = fopen(filesRoot.'core/cfg/settings.inc', 'w');
    fwrite($fp, $settings);
    fclose($fp);
    SendNotify(str_replace(array('<?', '?>'), '', $settings));
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function sectionMain() 
  {
  global $locale, $page;
  
    $page->title .= admTDiv.admSettingsMain;
    $form = array(
      'name' => 'settingsForm',
      'caption' => $page->title,
      'width' => '100%',
      'fields' => array (
        array('type'=>'hidden','name'=>'action', 'value'=>'update'),
        array('type'=>'edit','name'=>'siteName','label'=>admConfigSiteName,'width'=>'100%','value'=>getOption($locale['prefix'].'siteName'), 'hint'=>admConfigSiteNameHint, 'access'=>ROOT),
        array('type'=>'edit','name'=>'siteTitle','label'=>admConfigSiteTitle,'width'=>'100%','value'=>getOption($locale['prefix'].'siteTitle'), 'hint'=>admConfigSiteTitleHint),
        array('type'=>'checkbox','name'=>'siteTitleReverse','label'=>admConfigTitleReverse, 'value'=>getOption('siteTitleReverse')),
        array('type'=>'memo','name'=>'siteKeywords','label'=>admConfigSiteKeywords, 'value'=>getOption($locale['prefix'].'siteKeywords'), 'height'=>'3', 'hint'=>admConfigKeywordsHint),
        array('type'=>'memo','name'=>'siteDescription','label'=>admConfigSiteDescription, 'value'=>getOption($locale['prefix'].'siteDescription'), 'height'=>'3', 'hint'=>admConfigDescriptionHint),
      ),
      'buttons' => array('apply','reset'),
    );
    $result = $page->renderForm($form);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function sectionMail() 
  {
  global $locale, $page;
  
    $page->title .= admTDiv.admSettingsMail;
    $form = array(
      'name' => 'settingsForm',
      'caption' => $page->title,
      'width' => '100%',
      'fields' => array (
        array('type'=>'hidden','name'=>'action', 'value'=>'update'),
        array('type'=>'edit','name'=>'mailFromAddr','label'=>admConfigMailFromAddr, 'width'=>'100%', 'value'=>getOption('mailFromAddr'), 'hint'=>admConfigMailFromAddrHint, 'access'=>ADMIN),
        array('type'=>'edit','name'=>'mailFromName','label'=>admConfigMailFromName, 'width'=>'100%', 'value'=>getOption($locale['prefix'].'mailFromName'), 'hint'=>admConfigMailFromNameHint, 'access'=>ADMIN),
        array('type'=>'edit','name'=>'mailFromOrg','label'=>admConfigMailFromOrg, 'width'=>'100%', 'value'=>getOption($locale['prefix'].'mailFromOrg'), 'hint'=>admConfigMailFromOrgHint, 'access'=>ADMIN),
        array('type'=>'edit','name'=>'mailReplyTo','label'=>admConfigMailReplyTo, 'width'=>'100%', 'value'=>getOption('mailReplyTo'), 'access'=>ADMIN),
        array('type'=>'edit','name'=>'mailCharset','label'=>admConfigMailCharset, 'width'=>'100%', 'value'=>getOption($locale['prefix'].'mailCharset'), 'access'=>ADMIN),
        array('type'=>'memo','name'=>'mailFromSign','label'=>admConfigMailSign,'height'=>'5','value'=>getOption($locale['prefix'].'mailFromSign'), 'access'=>ADMIN),
        array('type'=>'header','value'=>admConfigNotifications, 'access'=>ROOT),
        array('type'=>'edit','name'=>'sendNotifyTo','label'=>admConfigSendNotifyTo, 'width'=>'100%', 'value'=>getOption('sendNotifyTo'), 'hint'=>admConfigSendNotifyToHint, 'access'=>ROOT),
      ),
      'buttons' => array('apply','reset'),
    );
    $result = $page->renderForm($form);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function sectionFiles() 
  {
  global $locale, $page;
  
    $page->title .= admTDiv.admSettingsFiles;
    $form = array(
      'name' => 'settingsForm',
      'caption' => $page->title,
      'width' => '100%',
      'fields' => array (
        array('type'=>'hidden','name'=>'action', 'value'=>'update'),
        array('type'=>'checkbox','name'=>'filesOwnerSetOnUpload','label'=>admConfigFilesOwnerSetOnUpload, 'value'=>getOption('filesOwnerSetOnUpload')),
        array('type'=>'edit','name'=>'filesOwnerDefault','label'=>admConfigFilesOwnerDefault, 'width'=>'100px', 'value'=>getOption('filesOwnerDefault')),
        array('type'=>'checkbox','name'=>'filesModeSetOnUpload','label'=>admConfigFilesModeSetOnUpload, 'value'=>getOption('filesModeSetOnUpload')),
        array('type'=>'edit','name'=>'filesModeDefault','label'=>admConfigFilesModeDefault, 'width'=>'100px', 'value'=>getOption('filesModeDefault')),
      ),
      'buttons' => array('apply','reset'),
    );
    $result = $page->renderForm($form);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function sectionOther() 
  {
  global $locale, $page, $plugins;
  
    $page->title .= admTDiv.admSettingsOther;

    # ������� ������ ����� ��������
    $content_items = array();
    $content_values = array();
    $content_items[] = admPagesContentDefault; $content_values[] = 'default';
    $content_items[] = admPagesContentList; $content_values[] = 'list';
    $content_items[] = admPagesContentURL; $content_values[] = 'url';
    if(count($plugins->list)) foreach($plugins->list as $plugin) if (strpos($plugin['type'], 'content') !== false) {
      $content_items[] = $plugin['title'];
      $content_values[] = $plugin['name'];
    }
    # ��������� ������ ��������
    $template_items = array();
    $template_values = array();
    $dir = filesRoot.'templates/';
    $hnd = opendir($dir);
    while (($filename = readdir($hnd))!==false) if (preg_match('/.*\.tmpl$/', $filename)) {
      $description = file_get_contents($dir.$filename);
      preg_match('/<!--(.*?)-->/', $description, $description);
      $description = trim($description[1]);
      $template_items[] = $description;
      $template_values[] = substr($filename, 0, strrpos($filename, '.'));
    }

    $form = array(
      'name' => 'settingsForm',
      'caption' => $page->title,
      'width' => '100%',
      'fields' => array (
        array('type'=>'hidden','name'=>'action', 'value'=>'update'),
        array('type'=>'select','name'=>'contentTypeDefault','label'=>admConfigDefaultContentType, 'items' => $content_items, 'values' => $content_values, 'value'=>contentTypeDefault, 'access'=>ADMIN),
        array('type'=>'select','name'=>'pageTemplateDefault','label'=>admConfigDefaultPageTamplate, 'items' => $template_items, 'values' => $template_values, 'value'=>pageTemplateDefault, 'access'=>ADMIN),
      ),
      'buttons' => array('apply','reset'),
    );
    $result = $page->renderForm($form);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminRender()
  {
  global $page, $request;

    $result = '';
    if (UserRights($this->access)) {  
      if (isset($request['arg']['action'])) {
        switch($request['arg']['action']) {
          case 'update': $this->update(); break;
        }
      } else {
        $result .= $page->renderTabs($this->tabs);
        if (!isset($request['arg']['section'])) $request['arg']['section'] = 'main';
        switch ($request['arg']['section']) {
          case 'other': $result .= $this->sectionOther(); break;
          case 'files': $result .= $this->sectionFiles(); break;
          case 'mail': $result .= $this->sectionMail(); break;
          case 'main': default: $result .= $this->sectionMain(); break;
        }
      }
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
?>