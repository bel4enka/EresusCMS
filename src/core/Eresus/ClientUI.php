<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2004, Михаил Красильников <mihalych@vsepofigu.ru>
 * @copyright 2007, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Михаил Красильников <mihalych@vsepofigu.ru>
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
 * @package Eresus
 *
 * $Id$
 */

/**
 * Признак клиентского интерфейса
 *
 * @var bool
 */
define('CLIENTUI', true);

/**
 * Страница клиентского интерфейса
 *
 * @package Eresus
 */
class Eresus_ClientUI extends Eresus_WebPage
{
	public $dbItem = array(); # Информация о странице из БД
	public $name = ''; # Имя страницы
	public $owner = 0; # Идентификатор родительской страницы
	public $section = array(); # Массив заголовков страниц
	public $caption = ''; # Название страницы
	public $hint = ''; # Подсказка с описанием страницы
	public $description = ''; # Описание страницы
	public $keywords = ''; # Ключевые слова страницы
	public $access = GUEST; # Базовый уровень доступа к странице
	public $visible = true; # Видимость страницы
	public $type = 'default'; # Тип страницы
	public $content = ''; # Контент страницы
	public $options = array(); # Опции страницы
	public $Document; # DOM-интерфейс к странице
	public $plugin; # Плагин контента
	public $scripts = ''; # Скрипты
	public $styles = ''; # Стили
	public $subpage = 0; # Подстраница списка элементов

	/**
	 * Идентификатор объекта контента
	 *
	 * Объект контента (или «топик») – это статья, новость, фотография или другой объект в разделе,
	 * содержащим список таких однотипных объектов.
	 *
	 * В $topic помещается элемент массива {@link $Eresus::$request}}['params'] , следующий после
	 * адреса текущего раздела и номера подстраницы списка (если он есть). Если такого элемента в
	 * массиве нет, то $topic будет равен false.
	 *
	 * Примеры:
	 *
	 * - http://exmaple.org/articles/p2/123/ — $topic равен «123».
	 * - http://exmaple.org/articles/123/ — $topic равен «123».
	 * - http://exmaple.org/articles/ — $topic равен «false».
	 * - http://exmaple.org/articles/123/file?key=value — $topic равен «123».
	 * - http://exmaple.org/articles/file?key=value — $topic равен «false».
	 *
	 * @var string|bool
	 * @since 2.10
	 */
	public $topic = false;

	//------------------------------------------------------------------------------

	/**
	 * Конструктор
	 *
	 * @access  public
	 */
	function __construct()
	{
	}
	//------------------------------------------------------------------------------

	# Подставляет значения макросов
	function replaceMacros($text)
	{
		$section = $this->section;
		if (siteTitleReverse)
		{
			$section = array_reverse($section);
		}
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
	//------------------------------------------------------------------------------

	/**
	 * Отрисовка переключателя страниц
	 *
	 * @param int     $total      Общее количество страниц
	 * @param int     $current    Номер текущей страницы
	 * @param string  $url        Шаблон адреса для перехода к подстранице.
	 * @param array   $templates  Шаблоны оформления
	 * @return string
	 */
	function pageSelector($total, $current, $url = null, $templates = null)
	{
		if (is_null($url))
		{
			$url = $this->url().'p%d/';
		}
		$Templates = new Eresus_Templates();
		$defaults = explode('---', $Templates->get('PageSelector', 'std'));
		if (!is_array($templates))
		{
			$templates = array();
		}
		for ($i=0; $i < 5; $i++)
		{
			if (!isset($templates[$i]))
			{
				$templates[$i] = $defaults[$i];
			}
		}
		$result = parent::pageSelector($total, $current, $url, $templates);
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * Производит разбор URL и загрузку соответствующего раздела
	 *
	 * @return  array|bool  Описание загруженного раздела или false если он не найден
	 */
	private function loadPage()
	{
		$result = false;
		$main_fake = false;
		if (!count(Eresus_CMS::getLegacyKernel()->request['params']) ||
			Eresus_CMS::getLegacyKernel()->request['params'][0] != 'main')
		{
			array_unshift(Eresus_CMS::getLegacyKernel()->request['params'], 'main');
			$main_fake = true;
		}
		reset(Eresus_CMS::getLegacyKernel()->request['params']);
		$item['id'] = 0;
		$url = '';
		do
		{
			$items = Eresus_CMS::getLegacyKernel()->sections->children($item['id'],
				Eresus_CMS::getLegacyKernel()->user['auth'] ?
					Eresus_CMS::getLegacyKernel()->user['access'] : GUEST, Eresus_Sections::SECTIONS_ACTIVE);
			$item = false;
			for ($i=0; $i<count($items); $i++)
			{
				if ($items[$i]['name'] == current(Eresus_CMS::getLegacyKernel()->request['params']))
				{
					$result = $item = $items[$i];
					if ($item['id'] != 1 || !$main_fake)
					{
						$url .= $item['name'].'/';
					}
					Eresus_CMS::getLegacyKernel()->plugins->clientOnURLSplit($item, $url);
					$this->section[] = $item['title'];
					next(Eresus_CMS::getLegacyKernel()->request['params']);
					array_shift(Eresus_CMS::getLegacyKernel()->request['params']);
					break;
				}
			}
			if ($item && $item['id'] == 1 && $main_fake)
			{
				$item['id'] = 0;
			}
		}
		while ($item && current(Eresus_CMS::getLegacyKernel()->request['params']));
		Eresus_CMS::getLegacyKernel()->request['path'] =
		Eresus_CMS::getLegacyKernel()->request['path'] = Eresus_CMS::getLegacyKernel()->root . $url;
		if ($result)
		{
			$result = Eresus_CMS::getLegacyKernel()->sections->get($result['id']);
		}
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проводит инициализацию страницы
	 */
	public function init()
	{
		Eresus_CMS::getLegacyKernel()->plugins->clientOnStart();

		$item = $this->loadPage();
		if ($item)
		{
			if (count(Eresus_CMS::getLegacyKernel()->request['params']))
			{
				if (preg_match('/p[\d]+/i', Eresus_CMS::getLegacyKernel()->request['params'][0]))
				{
					$this->subpage = substr(array_shift(Eresus_CMS::getLegacyKernel()->request['params']), 1);
				}

				if (count(Eresus_CMS::getLegacyKernel()->request['params']))
				{
					$this->topic = array_shift(Eresus_CMS::getLegacyKernel()->request['params']);
				}
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
			$this->options = $item['options'];
		}
		else
		{
			$this->httpError(404);
		}
	}
	//-----------------------------------------------------------------------------

	function Error404()
	{
		$this->httpError(404);
	}
	//-----------------------------------------------------------------------------

	function httpError($code)
	{
		global $KERNEL;

		if (isset($KERNEL['ERROR']))
		{
			return;
		}
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

		if (defined('HTTP_CODE_'.$code))
		{
			$message = constant('HTTP_CODE_'.$code);
		}
		else
		{
			$message = $ERROR[$code]['response'];
		}

		$this->section = array(siteTitle, $message);
		$this->title = $message;
		$this->description = '';
		$this->keywords = '';
		$this->caption = $message;
		$this->hint = '';
		$this->access = GUEST;
		$this->visible = true;
		$this->type = 'default';
		if (file_exists(filesRoot.'templates/std/'.$code.'.html'))
		{
			$this->template = 'std/'.$code;
			$this->content = '';
		}
		else
		{
			$this->template = 'default';
			$this->content = '<h1>HTTP ERROR '.$code.': '.$message.'</h1>';
		}
		$KERNEL['ERROR'] = true;
		$this->render();
		exit;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Отправляет созданную страницу пользователю.
	 */
	public function render()
	{
		if (arg('HTTP_ERROR'))
		{
			$this->httpError(arg('HTTP_ERROR', 'int'));
		}
		# Отрисовываем контент
		$content = Eresus_CMS::getLegacyKernel()->plugins->clientRenderContent();
		$templates = new Eresus_Templates;
		$this->template = $templates->get($this->template);
		$content = Eresus_CMS::getLegacyKernel()->plugins->clientOnContentRender($content);

		if (
			isset(Eresus_CMS::getLegacyKernel()->session['msg']['information']) &&
			count(Eresus_CMS::getLegacyKernel()->session['msg']['information'])
		)
		{
			$messages = '';
			foreach (Eresus_CMS::getLegacyKernel()->session['msg']['information'] as $message)
			{
				$messages .= InfoBox($message);
			}
			$content = $messages.$content;
			Eresus_CMS::getLegacyKernel()->session['msg']['information'] = array();
		}
		if (
			isset(Eresus_CMS::getLegacyKernel()->session['msg']['errors']) &&
			count(Eresus_CMS::getLegacyKernel()->session['msg']['errors'])
		)
		{
			$messages = '';
			foreach (Eresus_CMS::getLegacyKernel()->session['msg']['errors'] as $message)
			{
				$messages .= ErrorBox($message);
			}
			$content = $messages.$content;
			Eresus_CMS::getLegacyKernel()->session['msg']['errors'] = array();
		}
		$result = str_replace('$(Content)', $content, $this->template);

		# FIX: Обратная совместимость
		if (!empty($this->styles))
		{
			$this->addStyles($this->styles);
		}

		$result = Eresus_CMS::getLegacyKernel()->plugins->clientOnPageRender($result);

		// FIXME: Обратная совместимость
		if (!empty($this->scripts))
		{
			$this->addScripts($this->scripts);
		}

		$result = preg_replace('|(.*)</head>|i', '$1'.$this->renderHeadSection()."\n</head>", $result);

		# Замена макросов
		$result = $this->replaceMacros($result);

		if (count($this->headers))
		{
			foreach ($this->headers as $header)
			{
				header($header);
			}
		}

		$result = Eresus_CMS::getLegacyKernel()->plugins->clientBeforeSend($result);
		if (!Eresus_CMS::getLegacyKernel()->conf['debug']['enable'])
		{
			ob_start('ob_gzhandler');
		}
		echo $result;
		if (!Eresus_CMS::getLegacyKernel()->conf['debug']['enable'])
		{
			ob_end_flush();
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Выводит список подстраниц для навигации по ним
	 *
	 * @param int  $pagesCount
	 * @param int  $itemsPerPage
	 * @param bool $reverse
	 *
	 * @return string
	 */
	function pages($pagesCount, $itemsPerPage, $reverse = false)
	{
		$eresus = Eresus_CMS::getLegacyKernel();

		if ($pagesCount>1)
		{
			$at_once = option('clientPagesAtOnce');
			if (!$at_once)
			{
				$at_once = 10;
			}

			$side_left = '';
			$side_right = '';

			$for_from = $reverse ? $pagesCount : 1;
			$default = $for_from;
			$for_to = $reverse ? 0 : $pagesCount+1;
			$for_delta = $reverse ? -1 : 1;

			# Если количество страниц превышает AT_ONCE
			if ($pagesCount > $at_once)
			{
				# Если установлен обратный порядок страниц
				if ($reverse)
				{
					if ($this->subpage < ($pagesCount - (integer) ($at_once / 2)))
					{
						$for_from = ($this->subpage + (integer) ($at_once / 2));
					}
					if ($this->subpage < (integer) ($at_once / 2))
					{
						$for_from = $at_once;
					}
					$for_to = $for_from - $at_once;
					if ($for_to < 0)
					{
						$for_from += abs($for_to);
						$for_to = 0;
					}
					if ($for_from != $pagesCount)
					{
						$side_left = "<a href=\"".$eresus->request['path']."\" title=\"".strLastPage.
							"\">&nbsp;&laquo;&nbsp;</a>";
					}
					if ($for_to != 0)
					{
						$side_right = "<a href=\"".$eresus->request['path']."p1/\" title=\"".strFirstPage.
							"\">&nbsp;&raquo;&nbsp;</a>";
					}
				}
				# Если установлен прямой порядок страниц
				else
				{
					if ($this->subpage > (integer) ($at_once / 2))
					{
						$for_from = $this->subpage - (integer) ($at_once / 2);
					}
					if ($pagesCount - $this->subpage < (integer) ($at_once / 2) + (($at_once % 2)>0))
					{
						$for_from = $pagesCount - $at_once+1;
					}
					$for_to = $for_from + $at_once;
					if ($for_from != 1)
					{
						$side_left = "<a href=\"".$eresus->request['path']."\" title=\"".strFirstPage.
							"\">&nbsp;&laquo;&nbsp;</a>";
					}
					if ($for_to < $pagesCount)
					{
						$side_right = "<a href=\"".$eresus->request['path']."p".$pagesCount."/\" title=\"".
							strLastPage."\">&nbsp;&raquo;&nbsp;</a>";
					}
				}
			}
			$result = '<div class="pages">'.strPages;
			$result .= $side_left;
			for ($i = $for_from; $i != $for_to; $i += $for_delta)
			{
				if ($i == $this->subpage)
				{
					$result .= '<span class="selected">&nbsp;'.$i.'&nbsp;</span>';
				}
				else
				{
					$result .= '<a href="'.$eresus->request['path'].($i==$default?'':'p'.$i.'/').
						'">&nbsp;'.$i.'&nbsp;</a>';
				}
			}
			$result .= $side_right;
			$result .= "</div>\n";
			return $result;
		}
		else
		{
			return '';
		}
	}
	//-----------------------------------------------------------------------------
}
