<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Коннектор для elFinder
 *
 * @copyright 2010, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mihalych@vsepofigu.ru>
 *
 * Данная программа является свободным программным обеспечением. Вы
 * вправе распространять ее и/или модифицировать в соответствии с
 * условиями версии 3 либо (по вашему выбору) с условиями более поздней
 * версии Стандартной Общественной Лицензии GNU, опубликованной Free
 * Software Foundation.
 *
 * Мы распространяем эту программу в надежде на то, что она будет вам
 * полезной, однако НЕ ПРЕДОСТАВЛЯЕМ НА НЕЕ НИКАКИХ ГАРАНТИЙ, в том
 * числе ГАРАНТИИ ТОВАРНОГО СОСТОЯНИЯ ПРИ ПРОДАЖЕ и ПРИГОДНОСТИ ДЛЯ
 * ИСПОЛЬЗОВАНИЯ В КОНКРЕТНЫХ ЦЕЛЯХ. Для получения более подробной
 * информации ознакомьтесь со Стандартной Общественной Лицензией GNU.
 *
 * Вы должны были получить копию Стандартной Общественной Лицензии
 * GNU с этой программой. Если Вы ее не получили, смотрите документ на
 * <http://www.gnu.org/licenses/>
 *
 * @package CoreExtensionsAPI
 *
 * $Id: eresus-connector.php 749 2010-02-06 20:48:06Z mk $
 */

/**
 * Класс-коннектор
 *
 * @package CoreExtensionsAPI
 */
class elFinderConnector extends EresusExtensionConnector implements FileManagerConnectorInterface
{
	/**
	 * Показывает, подготовлено ли окружение для elFinder
	 *
	 * @var bool
	 * @see prepare()
	 */
	private $prepared = false;

	/**
	 * Метод возвращает разметку для файлового менеджера директории data.
	 *
	 * @return string HTML
	 *
	 * @since 2.16
	 */
	public function getDataBrowser()
	{
		$this->prepare();

		$GLOBALS['page']->addScripts($this->getInitScript('data'), 'defer');
		return '<div id="filemanager"></div>';
	}
	//-----------------------------------------------------------------------------

	/**
	 * (non-PHPdoc)
	 * @see EresusExtensionConnector::proxyUnexistent()
	 *
	 */
	protected function proxyUnexistent($path)
	{
		$file = basename($path, '.php');
		switch ($file)
		{
			case 'databrowser':
				$this->dataConnector();
			break;

			case 'datapopup':
				$this->dataPopup();
			break;

			default:
				parent::proxyUnexistent($path);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Подготавливает окружение для работы elFinder
	 *
	 * @return void
	 *
	 * @since 2.16
	 * @access private
	 */
	protected function prepare()
	{
		if ($this->prepared)
		{
			return;
		}

		$rootURL = $GLOBALS['Eresus']->root . 'ext-3rd/elfinder';
		$GLOBALS['page']->linkScripts($rootURL . '/js/elfinder.full.js', 'defer');
		$GLOBALS['page']->linkScripts($rootURL . '/js/i18n/elfinder.ru.js', 'defer');
		$GLOBALS['page']->linkStyles($rootURL . '/css/elfinder.css');

		$this->prepared = true;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает инициализируеющий JavaScript
	 *
	 * @param string $type  тип барузера (data, style)
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	protected function getInitScript($type)
	{
		return '
			jQuery(document).ready(function()
			{
				jQuery("#filemanager").elfinder(
				{
					url: "' . $GLOBALS['Eresus']->root . 'ext-3rd/elfinder/' . $type . 'browser.php",
					lang: "ru",
					height: "500px",
					places: "",
					toolbar: [
						["back", "realod"],
						["mkdir", "upload"],
						["mkfile", "edit"],
						["open", "info", "rename"],
						["copy", "cut", "paste", "rm"],
						["icons", "list"]
					],
					contextmenu : {
						cwd : ["reload", "delim", "mkdir", "mkfile", "upload", "delim", "paste", "delim", "info"],
						file : ["select", "delim", "copy", "cut", "rm", "delim", "duplicate", "rename"],
						group : ["copy", "cut", "rm", "delim", "delim", "info"]
					}
				})
			});';
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает заготовку для опций elFinder
	 *
	 * @return array
	 *
	 * @since 2.16
	 * @access private
	 */
	protected function getOptions()
	{
		$options = array(
			//'uploadAllow'   => array('images/*'),
			//'uploadDeny'    => array('all'),
			//'uploadOrder'   => 'deny,allow'
			// 'disabled'     => array(),      // list of not allowed commands
			// 'dotFiles'     => false,        // display dot files
			// 'dirSize'      => true,         // count total directories sizes
			// 'fileMode'     => 0666,         // new files mode
			// 'dirMode'      => 0777,         // new folders mode
			// 'mimeDetect'   => 'auto',       // files mimetypes detection method (finfo, mime_content_type, linux (file -ib), bsd (file -Ib), internal (by extensions))
			// 'uploadAllow'  => array(),      // mimetypes which allowed to upload
			// 'uploadDeny'   => array(),      // mimetypes which not allowed to upload
			// 'uploadOrder'  => 'deny,allow', // order to proccess uploadAllow and uploadAllow options
			// 'imgLib'       => 'auto',       // image manipulation library (imagick, mogrify, gd)
			// 'tmbDir'       => '.tmb',       // directory name for image thumbnails. Set to "" to avoid thumbnails generation
			// 'tmbCleanProb' => 1,            // how frequiently clean thumbnails dir (0 - never, 100 - every init request)
			// 'tmbAtOnce'    => 5,            // number of thumbnails to generate per request
			// 'tmbSize'      => 48,           // images thumbnails size (px)
			// 'fileURL'      => true,         // display file URL in "get info"
			// 'dateFormat'   => 'j M Y H:i',  // file modification date format
			// 'logger'       => null,         // object logger
			// 'defaults'     => array(        // default permisions
			// 	'read'   => true,
			// 	'write'  => true,
			// 	'rm'     => true
			// 	),
			// 'perms'        => array(),      // individual folders/files permisions
			// 'debug'        => true,         // send debug to client
			// 'archiveMimes' => array(),      // allowed archive's mimetypes to create. Leave empty for all available types.
			// 'archivers'    => array()       // info about archivers to use. See example below. Leave empty for auto detect
			// 'archivers' => array(
			// 	'create' => array(
			// 		'application/x-gzip' => array(
			// 			'cmd' => 'tar',
			// 			'argc' => '-czf',
			// 			'ext'  => 'tar.gz'
			// 			)
			// 		),
			// 	'extract' => array(
			// 		'application/x-gzip' => array(
			// 			'cmd'  => 'tar',
			// 			'argc' => '-xzf',
			// 			'ext'  => 'tar.gz'
			// 			),
			// 		'application/x-bzip2' => array(
			// 			'cmd'  => 'tar',
			// 			'argc' => '-xjf',
			// 			'ext'  => 'tar.bz'
			// 			)
			// 		)
			// 	)
		);
		return $options;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Коннектор для работы с директорией data
	 *
	 * @return void
	 *
	 * @since 2.16
	 * @access private
	 */
	protected function dataConnector()
	{
		global $Eresus;

		$options = $this->getOptions();
		$options['root'] = $Eresus->froot . 'data/';
		$options['URL'] = $Eresus->root . 'data/';
		$options['rootAlias'] = 'data';

		$fm = new elFinder($options);
		$fm->run();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Всплывающее окно для работы с директорией data
	 *
	 * @return void
	 *
	 * @since 2.16
	 * @access private
	 */
	protected function dataPopup()
	{
		$data = array();
		$data['root'] = preg_replace('~/$~', '', $GLOBALS['Eresus']->root);
		$data['initScript'] = $this->getInitScript('data');

		$tmpl = Eresus_Template::fromFile('ext-3rd/elfinder/popup.html');
		echo $tmpl->compile($data);
		throw new Eresus_ExitException;
	}
	//-----------------------------------------------------------------------------
}


/**
 * Созадёт директорию без учёта umask
 *
 * @param string   $pathname
 * @param int      $mode
 * @param bool     $recursive
 * @param resource $context
 *
 * @return bool
 *
 * @since 2.16
 */
function elfinder_mkdir($pathname, $mode = 0777, $recursive = false, $context = null)
{
	$umask = umask(0000);
	$result = mkdir($pathname, $mode, $recursive);
	umask($umask);
	return $result;
}
//-----------------------------------------------------------------------------