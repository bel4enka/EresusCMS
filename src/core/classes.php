<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2004, ProCreat Systems, http://procreat.ru/
 * @copyright 2007, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 *
 * ������ ��������� �������� ��������� ����������� ������������. ��
 * ������ �������������� �� �/��� �������������� � ������������ �
 * ��������� ������ 3 ���� (�� ������ ������) � ��������� ����� �������
 * ������ ����������� ������������ �������� GNU, �������������� Free
 * Software Foundation.
 *
 * �� �������������� ��� ��������� � ������� �� ��, ��� ��� ����� ���
 * ��������, ������ �� ������������� �� ��� ������� ��������, � ���
 * ����� �������� ��������� ��������� ��� ������� � ����������� ���
 * ������������� � ���������� �����. ��� ��������� ����� ���������
 * ���������� ������������ �� ����������� ������������ ��������� GNU.
 *
 * �� ������ ���� �������� ����� ����������� ������������ ��������
 * GNU � ���� ����������. ���� �� �� �� ��������, �������� �������� ��
 * <http://www.gnu.org/licenses/>
 *
 * @package Eresus
 *
 * $Id$
 */


/**
 * ������ ��������
 *
 * ������ ��� �������� �������������� ������ � ����� �������.
 *
 * @package Eresus
 */
class EresusSourceParseException extends EresusRuntimeException {};


/**
 * ������ � ���������
 *
 * @package Eresus
 */
class Plugins
{
	/**
	 * ������ ���� ��������
	 *
	 * @var array
	 * @todo ������� private
	 */
	public $list = array();

	/**
	 * ������ ��������
	 *
	 * @var array
	 * @todo ������� private
	 */
	public $items = array();

	/**
	 * ������� ������������ �������
	 *
	 * @var array
	 * @todo ������� private
	 */
	public $events = array();

	/**
	 * �����������
	 */
	public function __construct()
	{
		$this->loadActive();
	}
	//-----------------------------------------------------------------------------

	/**
	 * ������������� ������
	 *
	 * @param string $name  ��� �������
	 *
	 * @return void
	 *
	 * @throws EresusSourceParseException
	 */
	public function install($name)
	{
		global $Eresus;

		eresus_log(__METHOD__, LOG_DEBUG, '("%s")', $name);

		$filename = filesRoot.'ext/'.$name.'.php';
		if (FS::exists($filename))
		{
			$info = Eresus_PluginInfo::loadFromFile($filename);
			/*
			 * ���������� ������ ����� eval ����� ��������� � ���������� ��������� ��������������
			 * ������. ���� � �� ����, ��� ��� ���������.
			 */
			$code = file_get_contents($filename);
			$code = preg_replace('/^\s*<\?php|\?>\s*$/m', '', $code);
			$code = str_replace('__FILE__', "'$filename'", $code);
			ini_set('track_errors', true);
			$valid = eval($code) !== false;
			ini_set('track_errors', false);
			if (!$valid)
			{
				throw new DomainException(
					sprintf('Plugin "%s" is broken: %s', $name, $php_errormsg)
				);
			}

			$className = $name;
			if (!class_exists($className, false) && class_exists('T' . $className, false))
			{
				$className = 'T' . $className; // FIXME: �������� ������������� � �������� �� 2.10b2
			}

			if (class_exists($className, false))
			{
				$this->items[$name] = new $className();
				$this->items[$name]->install();
				$item = $this->items[$name]->__item();
				$item['info'] = serialize($info);
				$Eresus->db->insert('plugins', $item);
			}
			else
			{
				FatalError(sprintf(errClassNotFound, $ClassName));
			}
		}
		else
		{
			eresus_log(__METHOD__, LOG_ERR, 'Can not find main file "%s" for plugin "%s"', $filename,
				$name);
			$msg = I18n::getInstance()->getText('Can not find main file "%s" for plugin "%s"', __CLASS__);
			$msg = sprintf($msg, $filename, $name);
			ErrorMessage($msg);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * �������������� ������
	 *
	 * @param string $name  ��� �������
	 */
	public function uninstall($name)
	{
		global $Eresus;

		if (!isset($this->items[$name]))
		{
			$this->load($name);
		}
		if (isset($this->items[$name]))
		{
			$this->items[$name]->uninstall();
		}
		$item = $Eresus->db->selectItem('plugins', "`name`='".$name."'");
		if (!is_null($item))
		{
			$Eresus->db->delete('plugins', "`name`='".$name."'");
		}
		$filename = filesRoot.'ext/'.$name.'.php';
		#if (file_exists($filename)) unlink($filename);
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#

	/**
	 * ��������� ������ � ���������� ��� ���������
	 *
	 * @param string $name  ��� �������
	 *
	 * @return Plugin|TPlugin|false  ��������� ������� ��� FASLE ���� �� ������� ��������� ������
	 */
	public function load($name)
	{
		eresus_log(__METHOD__, LOG_DEBUG, '("%s")', $name);
		/* ���� ������ ��� ��� �������� ���������� ��������� �� ������� */
		if (isset($this->items[$name]))
		{
			eresus_log(__METHOD__, LOG_DEBUG, 'Plugin "%s" already loaded', $name);
			return $this->items[$name];
		}

		/* ���� ����� ������ �� ���������������, ���������� FASLE */
		if (!isset($this->list[$name]))
		{
			eresus_log(__METHOD__, LOG_DEBUG, 'Plugin "%s" not registered', $name);
			return false;
		}

		// ���� � ����� �������
		$filename = filesRoot . 'ext/' . $name . '.php';

		/* ���� ������ ����� ���, ���������� FASLE */
		if (!file_exists($filename))
		{
			eresus_log(__METHOD__, LOG_ERR, 'Can not find main file "%s" for plugin "%s"', $filename,
				$name);
			return false;
		}

		Core::safeInclude($filename);
		$className = $name;

		/* TODO: �������� ������������� � �������� �� 2.10b2. ���������� � ����� ������� */
		if (!class_exists($className, false) && class_exists('T' . $className))
		{
			$className = 'T' . $className;
		}

		if (!class_exists($className, false))
		{
			eresus_log(__METHOD__, LOG_ERR, 'Main class %s for plugin "%s" not found in "%s"',
				$className, $name, $filename);
			FatalError(sprintf(errClassNotFound, $name));
		}

		// ������� ��������� � ������
		$this->items[$name] = new $className();
		eresus_log(__METHOD__, LOG_DEBUG, 'Plugin "%s" loaded', $name);

		return $this->items[$name];
	}
	//-----------------------------------------------------------------------------

	/**
	 * ��������� �������� �������
	 *
	 * @return stirng  �������
	 */
	function clientRenderContent()
	{
		global $Eresus, $page;

		$result = '';
		switch ($page->type)
		{

			case 'default':
				$plugin = new ContentPlugin;
				$result = $plugin->clientRenderContent();
			break;

			case 'list':
				/* ���� � URL ������� ���-���� ����� ������ �������, ���������� ����� 404 */
				if ($Eresus->request['file'] || $Eresus->request['query'] || $page->subpage || $page->topic)
					$page->httpError(404);

				$subitems = $Eresus->db->select('pages', "(`owner`='".$page->id."') AND (`active`='1') AND (`access` >= '".($Eresus->user['auth'] ? $Eresus->user['access'] : GUEST)."')", "`position`");
				if (empty($page->content)) $page->content = '$(items)';
				useLib('templates');
				$templates = new Templates();
				$template = $templates->get('SectionListItem', 'std');
				if (false === $template)
				{
					$template = '<h1><a href="$(link)" title="$(hint)">$(caption)</a></h1>$(description)';
				}
				$items = '';
				foreach ($subitems as $item)
				{
					$items .= str_replace(
						array(
							'$(id)',
							'$(name)',
							'$(title)',
							'$(caption)',
							'$(description)',
							'$(hint)',
							'$(link)',
						),
						array(
							$item['id'],
							$item['name'],
							$item['title'],
							$item['caption'],
							$item['description'],
							$item['hint'],
							$Eresus->request['url'].($page->name == 'main' && !$page->owner ? 'main/' : '').$item['name'].'/',
						),
						$template
					);
				}
				$result = str_replace('$(items)', $items, $page->content);
			break;

			case 'url':
				HTTP::redirect($page->replaceMacros($page->content));
			break;
			default:
			if ($this->load($page->type)) {
				if (method_exists($this->items[$page->type], 'clientRenderContent'))
					$result = $this->items[$page->type]->clientRenderContent();
				else ErrorMessage(sprintf(errMethodNotFound, 'clientRenderContent', get_class($this->items[$page->type])));
			} else ErrorMessage(sprintf(errContentPluginNotFound, $page->type));
		}
		return $result;
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
	function clientOnStart()
	{
		if (isset($this->events['clientOnStart'])) foreach($this->events['clientOnStart'] as $plugin) $this->items[$plugin]->clientOnStart();
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
	function clientOnURLSplit($item, $url)
	{
		if (isset($this->events['clientOnURLSplit'])) foreach($this->events['clientOnURLSplit'] as $plugin) $this->items[$plugin]->clientOnURLSplit($item, $url);
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
	function clientOnTopicRender($text, $topic = null, $buttonBack = true)
	{
	global $page;
		if (isset($this->events['clientOnTopicRender'])) foreach($this->events['clientOnTopicRender'] as $plugin) $text = $this->items[$plugin]->clientOnTopicRender($text, $topic);
		if ($buttonBack) $text .= '<br /><br />'.$page->buttonBack();
		return $text;
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
	function clientOnContentRender($text)
	{
		if (isset($this->events['clientOnContentRender']))
			foreach($this->events['clientOnContentRender'] as $plugin) $text = $this->items[$plugin]->clientOnContentRender($text);
		return $text;
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
	function clientOnPageRender($text)
	{
		if (isset($this->events['clientOnPageRender']))
			foreach($this->events['clientOnPageRender'] as $plugin) $text = $this->items[$plugin]->clientOnPageRender($text);
		return $text;
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
	function clientBeforeSend($text)
	{
		if (isset($this->events['clientBeforeSend']))
			foreach($this->events['clientBeforeSend'] as $plugin) $text = $this->items[$plugin]->clientBeforeSend($text);
		return $text;
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
	/* function clientOnFormControlRender($formName, $control, $text)
	{
		if (isset($this->events['clientOnFormControlRender'])) foreach($this->events['clientOnFormControlRender'] as $plugin) $text = $this->items[$plugin]->clientOnFormControlRender($formName, $control, $text);
		return $text;
	}*/
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
	function adminOnMenuRender()
	{
		if (isset($this->events['adminOnMenuRender'])) foreach($this->events['adminOnMenuRender'] as $plugin)
			if (method_exists($this->items[$plugin], 'adminOnMenuRender')) $this->items[$plugin]->adminOnMenuRender();
			else ErrorMessage(sprintf(errMethodNotFound, 'adminOnMenuRender', $plugin));
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
 /**
	* ������� ajaxOnRequest
	*/
	function ajaxOnRequest()
	{
		if (isset($this->events['ajaxOnRequest']))
			foreach($this->events['ajaxOnRequest'] as $plugin)
				$this->items[$plugin]->ajaxOnRequest();
	}
	//-----------------------------------------------------------------------------

	/**
	 * ��������� �������� �������
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	private function loadActive()
	{
		$items = $GLOBALS['Eresus']->db->select('plugins', 'active = 1');
		if ($items)
		{
			foreach ($items as &$item)
			{
				$item['info'] = unserialize($item['info']);
				$this->list[$item['name']] = $item;
			}

			/* ��������� ����������� */
			do
			{
				$success = true;
				foreach ($this->list as $plugin => $item)
				{
					foreach ($item['info']->getRequiredPlugins() as $required)
					{
						list ($name, $minVer, $maxVer) = $required;
						if (
							!isset($this->list[$name]) ||
							($minVer && version_compare($this->list[$name]['info']->version, $minVer, '<')) ||
							($maxVer && version_compare($this->list[$name]['info']->version, $maxVer, '>'))
						)
						{
							$msg = 'Plugin "%s" requires plugin %s';
							$requiredPlugin = $name . ' ' . $minVer . '-' . $maxVer;
							eresus_log(__CLASS__, LOG_ERR, $msg, $plugin, $requiredPlugin);
							/*$msg = I18n::getInstance()->getText($msg, $this);
							ErrorMessage(sprintf($msg, $plugin, $requiredPlugin));*/
							unset($this->list[$plugin]);
							$success = false;
						}
					}
				}
			}
			while (!$success);

			/* ��������� ������� */
			foreach ($this->list as $item)
			{
				$this->load($item['name']);
			}
		}
	}
	//-----------------------------------------------------------------------------
}

/* * * * * * * * * * * * * * * * * * * * * * * *
*
*     ������-������ ��� �������� ��������
*
* * * * * * * * * * * * * * * * * * * * * * * */

/**
 * ������������ ����� ��� ���� ��������
 *
 * @package Eresus
 */
class Plugin
{
	/**
	 * ��� �������
	 *
	 * @var string
	 */
	public $name;

	/**
	 * ������ �������
	 *
	 * ������� ������ ����������� ��� ����� ���������
	 *
	 * @var string
	 */
	public $version = '0.00';

	/**
	 * ����������� ������ Eresus
	 *
	 * ������� ����� ����������� ��� ����� ���������
	 *
	 * @var string
	 */
	public $kernel = '2.10b2';

	/**
	 * �������� �������
	 *
	 * ������� ������ ����������� ��� ����� ���������
	 *
	 * @var string
	 */
	public $title = 'no title';

	/**
	 * �������� �������
	 *
	 * ������� ������ ����������� ��� ����� ���������
	 *
	 * @var string
	 */
	public $description = '';

	/**
	 * ��������� �������
	 *
	 * ������� ����� ����������� ��� ����� ���������
	 *
	 * @var array
	 */
	public $settings = array();

	/**
	 * ���������� ������
	 *
	 * /data/���_�������
	 *
	 * @var string
	 */
	protected $dirData;

	/**
	 * URL ������
	 *
	 * @var string
	 */
	protected $urlData;

	/**
	 * ���������� ��������
	 *
	 * /ext/���_�������
	 *
	 * @var string
	 */
	protected $dirCode;

	/**
	 * URL ��������
	 *
	 * @var string
	 */
	protected $urlCode;

	/**
	 * ���������� ����������
	 *
	 * style/���_�������
	 *
	 * @var string
	 */
	protected $dirStyle;

	/**
	 * URL ����������
	 *
	 * @var string
	 */
	protected $urlStyle;

	/**
	 * �����������
	 *
	 * ���������� ������ �������� ������� � ����������� �������� ������
	 *
	 * @uses $Eresus
	 * @uses $locale
	 * @uses FS::isFile
	 * @uses Core::safeInclude
	 * @uses Plugin::resetPlugin
	 */
	public function __construct()
	{
		global $Eresus, $locale;

		$this->name = strtolower(get_class($this));
		if (!empty($this->name) && isset($Eresus->plugins->list[$this->name]))
		{
			$this->settings = decodeOptions($Eresus->plugins->list[$this->name]['settings'], $this->settings);
			# ���� ����������� ������ ������� �������� �� ������������� �����
			# �� ���������� ���������� ���������� ���������� � ������� � ��
			if ($this->version != $Eresus->plugins->list[$this->name]['version'])
				$this->resetPlugin();
		}
		$this->dirData = $Eresus->fdata.$this->name.'/';
		$this->urlData = $Eresus->data.$this->name.'/';
		$this->dirCode = $Eresus->froot.'ext/'.$this->name.'/';
		$this->urlCode = $Eresus->root.'ext/'.$this->name.'/';
		$this->dirStyle = $Eresus->fstyle.$this->name.'/';
		$this->urlStyle = $Eresus->style.$this->name.'/';
		$filename = filesRoot.'lang/'.$this->name.'/'.$locale['lang'].'.php';
		if (FS::isFile($filename))
			Core::safeInclude($filename);
	}
	//------------------------------------------------------------------------------

	/**
	 * ���������� ���������� � �������
	 *
	 * @param  array  $item  ���������� ������ ���������� (�� ��������� null)
	 *
	 * @return  array  ������ ����������, ��������� ��� ������ � ��
	 */
	public function __item($item = null)
	{
		global $Eresus;

		$result['name'] = $this->name;
		$result['content'] = false;
		$result['active'] = is_null($item)? true : $item['active'];
		$result['settings'] = $Eresus->db->escape(is_null($item) ? encodeOptions($this->settings) : $item['settings']);
		$result['title'] = $this->title;
		$result['version'] = $this->version;
		$result['description'] = $this->description;
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * ����������� ��������� � �������������� ������� ��������
	 *
	 * @param string $method  ��� ���������� ������
	 * @param array  $args    ���������� ���������
	 *
	 * @throws EresusMethodNotExistsException
	 */
	public function __call($method, $args)
	{
		throw new EresusMethodNotExistsException($method, get_class($this));
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� URL ���������� ������ �������
	 *
	 * @return string
	 *
	 * @since 2.15
	 */
	public function getDataURL()
	{
		return $this->urlData;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� URL ���������� ������ �������
	 *
	 * @return string
	 *
	 * @since 2.15
	 */
	public function getCodeURL()
	{
		return $this->urlCode;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� URL ���������� ������ �������
	 *
	 * @return string
	 *
	 * @since 2.15
	 */
	public function getStyleURL()
	{
		return $this->urlStyle;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ������ �������� ������� �� ��
	 *
	 * @return bool  ��������� ����������
	 */
	protected function loadSettings()
	{
		global $Eresus;

		$result = $Eresus->db->selectItem('plugins', "`name`='".$this->name."'");
		if ($result)
			$this->settings = decodeOptions($result['settings'], $this->settings);
		return (bool)$result;
	}
	//------------------------------------------------------------------------------

	/**
	 * ���������� �������� ������� � ��
	 *
	 * @return bool  ��������� ����������
	 */
	protected function saveSettings()
	{
		global $Eresus;

		$result = $Eresus->db->selectItem('plugins', "`name`='{$this->name}'");
		$result = $this->__item($result);
		$result['settings'] = $Eresus->db->escape(encodeOptions($this->settings));
		$result = $Eresus->db->updateItem('plugins', $result, "`name`='".$this->name."'");

		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * ���������� ������ � ������� � ��
	 */
	protected function resetPlugin()
	{
		$this->loadSettings();
		$this->saveSettings();
	}
	//------------------------------------------------------------------------------

	/**
	 * ��������, ����������� ��� ����������� �������
	 */
	public function install() {}
	//------------------------------------------------------------------------------

	/**
	 * ��������, ����������� ��� ������������� �������
	 */
	public function uninstall()
	{
		global $Eresus;

		# TODO: ��������� � IDataSource
		$tables = $Eresus->db->query_array("SHOW TABLES LIKE '{$Eresus->db->prefix}{$this->name}_%'");
		$tables = array_merge($tables, $Eresus->db->query_array("SHOW TABLES LIKE '{$Eresus->db->prefix}{$this->name}'"));
		for ($i=0; $i < count($tables); $i++)
			$this->dbDropTable(substr(current($tables[$i]), strlen($this->name)+1));
	}
	//------------------------------------------------------------------------------

	/**
	 * �������� ��� ��������� ��������
	 */
	public function onSettingsUpdate() {}
	//------------------------------------------------------------------------------

	/**
	 * ��������� � �� ��������� �������� �������
	 */
	public function updateSettings()
	{
		global $Eresus;

		foreach ($this->settings as $key => $value)
			if (!is_null(arg($key)))
				$this->settings[$key] = arg($key);
		$this->onSettingsUpdate();
		$this->saveSettings();
	}
	//------------------------------------------------------------------------------

	/**
	 * ������ ��������
	 *
	 * @param  string  $template  ������ � ������� ��������� �������� ������ ��������
	 * @param  mixed   $item      ������������� ������ �� ���������� ��� ����������� ������ ��������
	 *
	 * @return  string  ������������ ������
	 */
	protected function replaceMacros($template, $item)
	{
		$result = replaceMacros($template, $item);
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * �������� ����� ����������
	 *
	 * @param string $name ��� ����������
	 * @return bool ���������
	 */
	protected function mkdir($name = '')
	{
		$result = true;
		$umask = umask(0000);
		# �������� � �������� �������� ���������� ������
		if (!is_dir($this->dirData)) $result = mkdir($this->dirData);
		if ($result) {
			# ������� ���������� ���� "." � "..", � ����� ��������� � ���������� �����
			$name = preg_replace(array('!\.{1,2}/!', '!^/!', '!/$!'), '', $name);
			if ($name) {
				$name = explode('/', $name);
				$root = substr($this->dirData, 0, -1);
				for($i=0; $i<count($name); $i++) if ($name[$i]) {
					$root .= '/'.$name[$i];
					if (!is_dir($root)) $result = mkdir($root);
					if (!$result) break;
				}
			}
		}
		umask($umask);
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * �������� ���������� � ������
	 *
	 * @param string $name ��� ����������
	 * @return bool ���������
	 */
	protected function rmdir($name = '')
	{
		$result = true;
		$name = preg_replace(array('!\.{1,2}/!', '!^/!', '!/$!'), '', $name);
		$name = $this->dirData.$name;
		if (is_dir($name)) {
			$files = glob($name.'/{.*,*}', GLOB_BRACE);
			for ($i = 0; $i < count($files); $i++) {
				if (substr($files[$i], -2) == '/.' || substr($files[$i], -3) == '/..') continue;
				if (is_dir($files[$i])) $result = $this->rmdir(substr($files[$i], strlen($this->dirData)));
				elseif (is_file($files[$i])) $result = filedelete($files[$i]);
				if (!$result) break;
			}
			if ($result) $result = rmdir($name);
		}
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * ���������� �������� ��� �������
	 *
	 * @param string $table  ��������� ��� �������
	 * @return string �������� ��� �������
	 */
	protected function __table($table)
	{
		return $this->name.(empty($table)?'':'_'.$table);
	}
	//------------------------------------------------------------------------------

	/**
	 * �������� ������� � ��
	 *
	 * @param string $SQL �������� �������
	 * @param string $name ��� �������
	 *
	 * @return bool ��������� �����������
	 */
	protected function dbCreateTable($SQL, $name = '')
	{
		global $Eresus;

		$result = $Eresus->db->create($this->__table($name), $SQL);
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * �������� ������� ��
	 *
	 * @param string $name ��� �������
	 *
	 * @return bool ��������� �����������
	 */
	protected function dbDropTable($name = '')
	{
		global $Eresus;

		$result = $Eresus->db->drop($this->__table($name));
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * ���������� ������� �� ������� ��
	 *
	 * @param string	$table				��� ������� (������ �������� - ������� �� ���������)
	 * @param string	$condition		������� �������
	 * @param string	$order				������� �������
	 * @param string	$fields				������ �����
	 * @param int			$limit				������� �� ������ ����� ��� limit
	 * @param int			$offset				�������� �������
	 * @param bool		$distinct			������ ���������� ����������
	 *
	 * @return array|bool  ��������� �������� � ���� ������� ��� FALSE � ������ ������
	 */
	public function dbSelect($table = '', $condition = '', $order = '', $fields = '', $limit = 0,
		$offset = 0, $group = '', $distinct = false)
	{
		global $Eresus;

		$result = $Eresus->db->select($this->__table($table), $condition, $order, $fields, $limit,
			$offset, $group, $distinct);

		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * ��������� ������ �� ��
	 *
	 * @param string $table  ��� �������
	 * @param mixed  $id   	 ������������� ��������
	 * @param string $key    ��� ��������� ����
	 *
	 * @return array �������
	 */
	public function dbItem($table, $id, $key = 'id')
	{
		global $Eresus;

		$result = $Eresus->db->selectItem($this->__table($table), "`$key` = '$id'");

		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * ������� � ������� ��
	 *
	 * @param string $table          ��� �������
	 * @param array  $item           ����������� �������
	 * @param string $key[optional]  ��� ��������� ����. �� ��������� "id"
	 */
	public function dbInsert($table, $item, $key = 'id')
	{
		global $Eresus;

		$result = $Eresus->db->insert($this->__table($table), $item);
		$result = $this->dbItem($table, $Eresus->db->getInsertedId(), $key);

		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * ��������� ������ � ��
	 *
	 * @param string $table      ��� �������
	 * @param mixed  $data       ���������� �������� / ���������
	 * @param string $condition  �������� ���� / ������� ��� ������
	 *
	 * @return bool ���������
	 */
	public function dbUpdate($table, $data, $condition = '')
	{
		global $Eresus;

		if (is_array($data)) {
			if (empty($condition)) $condition = 'id';
			$result = $Eresus->db->updateItem($this->__table($table), $data, "`$condition` = '{$data[$condition]}'");
		} elseif (is_string($data)) {
			$result = $Eresus->db->update($this->__table($table), $data, $condition);
		}

		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * �������� �������� �� ��
	 *
	 * @param string $table  ��� �������
	 * @param mixed  $item   ��������� ������� / �������������
	 * @param string $key    �������� ����
	 *
	 * @return bool ���������
	 */
	public function dbDelete($table, $item, $key = 'id')
	{
		global $Eresus;

		$result = $Eresus->db->delete($this->__table($table), "`$key` = '".(is_array($item)? $item[$key] : $item)."'");

		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * ������� ���������� ������� � ��
	 *
	 * @param string $table      ��� �������
	 * @param string $condition  ������� ��� ��������� � �������
	 *
	 * @return int ���������� �������, ��������������� �������
	 */
	public function dbCount($table, $condition = '')
	{
		global $Eresus;

		$result = $Eresus->db->count($this->__table($table), $condition);

		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * ��������� ���������� � ��������
	 *
	 * @param string $table  ����� ����� �������
	 * @param string $param  ������� ������ ��������� �������
	 *
	 * @return mixed
	 */
	public function dbTable($table, $param = '')
	{
		global $Eresus;

		$result = $Eresus->db->tableStatus($this->__table($table), $param);

		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * ����������� ������������ �������
	 *
	 * @param string $event1  ��� �������1
	 * ...
	 * @param string $eventN  ��� �������N
	 */
	protected function listenEvents()
	{
		global $Eresus;

		for($i=0; $i < func_num_args(); $i++)
			$Eresus->plugins->events[func_get_arg($i)][] = $this->name;
	}
	//------------------------------------------------------------------------------
}

/**
* ������� ����� ��� ��������, ��������������� ��� ��������
*
* @package Eresus
*/
class ContentPlugin extends Plugin
{
	/**
	 * �����������
	 *
	 * ������������� ������ � �������� ������� �������� � ������ ��������� ���������
	 */
	public function __construct()
	{
		global $page;

		parent::__construct();
		if (isset($page))
		{
			$page->plugin = $this->name;
			if (isset($page->options) && count($page->options))
				foreach ($page->options as $key=>$value)
					$this->settings[$key] = $value;
		}
	}
	//------------------------------------------------------------------------------

	/**
	 * ���������� ���������� � �������
	 *
	 * @param  array  $item  ���������� ������ ���������� (�� ��������� null)
	 *
	 * @return  array  ������ ����������, ��������� ��� ������ � ��
	 */
	public function __item($item = null)
	{
		$result = parent::__item($item);
		$result['content'] = true;
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * �������� ��� �������� ������� ������� ����
	 * @param int     $id     ������������� ���������� �������
	 * @param string  $table  ��� �������
	 */
	public function onSectionDelete($id, $table = '')
	{
		if (count($this->dbTable($table)))
			$this->dbDelete($table, $id, 'section');
	}
	//-----------------------------------------------------------------------------

	/**
	 * ��������� ������� �������� � ��
	 *
	 * @param  string  $content  �������
	 */
	public function updateContent($content)
	{
		global $Eresus, $page;

		$item = $Eresus->db->selectItem('pages', "`id`='".$page->id."'");
		$item['content'] = $content;
		$Eresus->db->updateItem('pages', $item, "`id`='".$page->id."'");
	}
	//------------------------------------------------------------------------------

	/**
	* ��������� ������� ��������
	*/
	function adminUpdate()
	{
		$this->updateContent(arg('content', 'dbsafe'));
		HTTP::redirect(arg('submitURL'));
	}
	//------------------------------------------------------------------------------

	/**
	 * ��������� ���������� �����
	 *
	 * @return  string  �������
	 */
	public function clientRenderContent()
	{
		global $Eresus, $page;

		/* ���� � URL ������� ���-���� ����� ������ �������, ���������� ����� 404 */
		if ($Eresus->request['file'] || $Eresus->request['query'] || $page->subpage || $page->topic)
			$page->httpError(404);

		return $page->content;
	}
	//------------------------------------------------------------------------------

	/**
	 * ��������� ���������������� �����
	 *
	 * @return  string  �������
	 */
	public function adminRenderContent()
	{
		global $page, $Eresus;

		if (arg('action') == 'update') $this->adminUpdate();
		$item = $Eresus->db->selectItem('pages', "`id`='".$page->id."'");
		$form = array(
			'name' => 'editForm',
			'caption' => $page->title,
			'width' => '100%',
			'fields' => array (
				array ('type'=>'hidden','name'=>'action', 'value' => 'update'),
				array ('type' => 'memo', 'name' => 'content', 'label' => strEdit, 'height' => '30'),
			),
			'buttons' => array('apply', 'reset'),
		);

		$result = $page->renderForm($form, $item);
		return $result;
	}
	//------------------------------------------------------------------------------
}

/**
 * ������� ����� ���������� ��������� ����������
 *
 * @package Eresus
 */
class EresusExtensionConnector
{
	/**
	 * �������� URL ����������
	 *
	 * @var string
	 */
	protected $root;

	/**
	 * �������� ���� ����������
	 *
	 * @var string
	 */
	protected $froot;

	/**
	 * �����������
	 *
	 * @return EresusExtensionConnector
	 */
	function __construct()
	{
		global $Eresus;

		$name = strtolower(substr(get_class($this), 0, -9));
		$this->root = $Eresus->root.'ext-3rd/'.$name.'/';
		$this->froot = $Eresus->froot.'ext-3rd/'.$name.'/';
	}
	//-----------------------------------------------------------------------------

	/**
	 * �������� ���������� �������
	 *
	 * @param string $text
	 * @return string
	 */
	protected function replaceMacros($text)
	{
		global $Eresus;

		$text = str_replace(
			array(
				'$(httpHost)',
				'$(httpPath)',
				'$(httpRoot)',
				'$(styleRoot)',
				'$(dataRoot)',
			),
			array(
				$Eresus->host,
				$Eresus->path,
				$Eresus->root,
				$Eresus->style,
				$Eresus->data
			),
			$text
		);

		return $text;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ����� ���������� ��� ������������� ������ �������� � ����������
	 *
	 */
	function proxy()
	{
		global $Eresus;

		if (!UserRights(EDITOR))
			die;

		$filename = $Eresus->request['path'] . $Eresus->request['file'];
		$filename = $Eresus->froot . substr($filename, strlen($Eresus->root));

		if (FS::isDir($filename))
		{
			$filename = FS::normalize($filename . '/index.php');
		}

		if (!FS::isFile($filename))
		{
			header('Not found', true, 404);
			die('<h1>Not found.</h1>');
		}

		$ext = strtolower(substr($filename, strrpos($filename, '.') + 1));

		switch (true)
		{
			case in_array($ext, array('png', 'jpg', 'jpeg', 'gif')):
				$info = getimagesize($filename);
				header('Content-type: '.$info['mime']);
				echo file_get_contents($filename);
			break;

			case $ext == 'js':
				header('Content-type: text/javascript');
				$s = file_get_contents($filename);
				$s = $this->replaceMacros($s);
				echo $s;
			break;

			case $ext == 'css':
				header('Content-type: text/css');
				$s = file_get_contents($filename);
				$s = $this->replaceMacros($s);
				echo $s;
			break;

			case $ext == 'html':
			case $ext == 'htm':
				header('Content-type: text/html');
				$s = file_get_contents($filename);
				$s = $this->replaceMacros($s);
				echo $s;
			break;

			case $ext == 'php':
				$Eresus->conf['debug']['enable'] = false;
				restore_error_handler();
				chdir(dirname($filename));
				require $filename;
			break;
		}
	}
	//-----------------------------------------------------------------------------
}



/**
 * ����� ��� ������ � ������������ �������
 *
 * @package Eresus
 */
class EresusExtensions
{
 /**
	* ����������� ����������
	*
	* @var array
	*/
	var $items = array();
 /**
	* ����������� ����� ����������
	*
	* @param string $class     ����� ����������
	* @param string $function  ����������� �������
	* @param string $name      ��� ����������
	*
	* @return mixed  ��� ���������� ��� false ���� ����������� ���������� �� �������
	*/
	function get_name($class, $function, $name = null)
	{
		global $Eresus;

		$result = false;
		if (isset($Eresus->conf['extensions'])) {
			if (isset($Eresus->conf['extensions'][$class])) {
				if (isset($Eresus->conf['extensions'][$class][$function])) {
					$items = $Eresus->conf['extensions'][$class][$function];
					reset($items);
					$result = isset($items[$name]) ? $name : key($items);
				}
			}
		}

		return $result;
	}
	//-----------------------------------------------------------------------------
 /**
	* �������� ����������
	*
	* @param string $class     ����� ����������
	* @param string $function  ����������� �������
	* @param string $name      ��� ����������
	*
	* @return mixed  ��������� ������ EresusExtensionConnector ��� false ���� �� ������� ��������� ����������
	*/
	function load($class, $function, $name = null)
	{
		global $Eresus;

		$result = false;
		$name = $this->get_name($class, $function, $name);

		if (isset($this->items[$name]))
		{
			$result = $this->items[$name];
		}
			else
		{
			$filename = $Eresus->froot.'ext-3rd/'.$name.'/eresus-connector.php';
			if (is_file($filename)) {
				include_once $filename;
				$class = $name.'Connector';
				if (class_exists($class)) {
					$this->items[$name] = new $class();
					$result = $this->items[$name];
				}
			}
		}
		return $result;
	}
	//-----------------------------------------------------------------------------
}
