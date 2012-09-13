<?php
/**
 * ${product.title}
 *
 * Родительский класс веб-интерфейсов
 *
 * @version ${product.version}
 * @copyright ${product.copyright}
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
 */

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Родительский класс веб-интерфейсов
 *
 * @package Eresus
 */
class Eresus_WebPage extends Controller
{
	/**
	 * Идентификатор текущего раздела
	 *
	 * @var int
	 */
	public $id = 0;

	/**
	 * Заголовок страницы
	 *
	 * @var string
	 */
	public $title;

	/**
	 * HTTP-заголовки ответа
	 *
	 * @var array
	 */
	public $headers = array();

	/**
	 * Описание секции HEAD
	 *
	 * -	meta-http - мета-теги HTTP-заголовков
	 * -	meta-tags - мета-теги
	 * -	link - подключение внешних ресурсов
	 * -	style - CSS
	 * -	script - Скрипты
	 * -	content - прочее
	 *
	 * @var array
	 */
	protected $head = array (
		'meta-http' => array(),
		'meta-tags' => array(),
		'link' => array(),
		'style' => array(),
		'scripts' => array(),
		'content' => '',
	);

	/**
	 * Наполнение секции <body>
	 *
	 * @var array
	 */
	protected $body = array(
		'scripts' => array(),
	);

	/**
	 * Значения по умолчанию
	 * @var array
	 */
	protected $defaults = array(
		'pageselector' => array(
			'<div class="pages">$(pages)</div>',
			'&nbsp;<a href="$(href)">$(number)</a>&nbsp;',
			'&nbsp;<b>$(number)</b>&nbsp;',
			'<a href="$(href)">&larr;</a>',
			'<a href="$(href)">&rarr;</a>',
		),
	);

	/**
	 * Конструктор
	 *
	 * @return Eresus_WebPage
	 */
	// @codeCoverageIgnoreStart
	public function __construct()
	{
	}
	// @codeCoverageIgnoreEnd
	//-----------------------------------------------------------------------------

	/**
	 * Установить мета-тег HTTP-заголовка
	 *
	 * Добавляет или изменяет мета-тег <meta http-equiv="$httpEquiv" content="$content" />
	 *
	 * @param string $httpEquiv  Имя заголовка HTTP
	 * @param string $content  	  Значение заголовка
	 *
	 * @since 2.10
	 */
	public function setMetaHeader($httpEquiv, $content)
	{
		$this->head['meta-http'][$httpEquiv] = $content;
	}
	//------------------------------------------------------------------------------

	/**
	 * Установка мета-тега
	 *
	 * @param string $name     Имя тега
	 * @param string $content  Значение тега
	 *
	 * @since 2.10
	 */
	public function setMetaTag($name, $content)
	{
		$this->head['meta-tags'][$name] = $content;
	}
	//------------------------------------------------------------------------------

	/**
	 * Подключение CSS-файла
	 *
	 * @param string $url    URL файла
	 * @param string $media  Тип носителя
	 *
	 * @since 2.10
	 */
	public function linkStyles($url, $media = '')
	{
		/* Проверяем, не добавлен ли уже этот URL  */
		for ($i = 0; $i < count($this->head['link']); $i++)
		{
			if ($this->head['link'][$i]['href'] == $url)
			{
				return;
			}
		}

		$item = array('rel' => 'StyleSheet', 'href' => $url, 'type' => 'text/css');

		if (!empty($media))
		{
			$item['media'] = $media;
		}

		$this->head['link'][] = $item;
	}
	//------------------------------------------------------------------------------

	/**
	 * Встраивание CSS
	 *
	 * @param string $content  Стили CSS
	 * @param string $media    Тип носителя
	 *
	 * @since 2.10
	 */
	public function addStyles($content, $media = '')
	{
		$content = preg_replace(array('/^(\s)+/m', '/^(\S)/m'), array('		', '	\1'), $content);
		$content = rtrim($content);
		$item = array('content' => $content);
		if (!empty($media))
		{
			$item['media'] = $media;
		}
		$this->head['style'][] = $item;
	}
	//------------------------------------------------------------------------------

	/**
	 * Подключение клиентского скрипта
	 *
	 * В качестве дополнительных параметров метод может принимать:
	 *
	 * <b>Типы скриптов</b>
	 * - ecma, text/ecmascript
	 * - javascript, text/javascript
	 * - jscript, text/jscript
	 * - vbscript, text/vbscript
	 *
	 * <b>Параметры загрузки скриптов</b>
	 * - async
	 * - defer
	 * - top
	 *
	 * Если скрипту передан параметр defer, то скрипт будет подключён в конце документа, перед
	 * </body>, в противном случае он будет подключён в <head>.
	 *
	 * Если передан аргумент «top», то скрипт будет подключен в самом начале блока скриптов.
	 *
	 * @param string $url        URL скрипта
	 * @param string $param,.../  Дополнительные параметры
	 *
	 * @since 2.10
	 */
	public function linkScripts($url)
	{
		foreach ($this->head['scripts'] as $script)
		{
			if ($script->getAttribute('src') == $url)
			{
				return;
			}
		}

		$script = new Eresus_HTML_ScriptElement($url);

		$args = func_get_args();
		// Отбрасываем $url
		array_shift($args);

		$top = false;

		foreach ($args as $arg)
		{
			switch (strtolower($arg))
			{
				case 'ecma':
				case 'text/ecmascript':
					$script->setAttribute('type', 'text/ecmascript');
				break;

				case 'javascript':
				case 'text/javascript':
					$script->setAttribute('type', 'text/javascript');
				break;

				case 'jscript':
				case 'text/jscript':
					$script->setAttribute('type', 'text/jscript');
				break;

				case 'vbscript':
				case 'text/vbscript':
					$script->setAttribute('type', 'text/vbscript');
				break;

				case 'async':
				case 'defer':
					$script->setAttribute($arg);
				break;

				case 'top':
					$top = true;
				break;
			}
		}

		if ($script->getAttribute('defer'))
		{
			$this->body['scripts'][] = $script;
		}
		else
		{
			if ($top)
			{
				array_unshift($this->head['scripts'], $script);
			}
			else
			{
				$this->head['scripts'][] = $script;
			}
		}
	}

	/**
	 * Встраивает в страницу клиентские скрипты
	 *
	 * <b>Типы скриптов</b>
	 * - ecma, text/ecmascript
	 * - javascript, text/javascript
	 * - jscript, text/jscript
	 * - vbscript, text/vbscript
	 *
	 * <b>Параметры загрузки скриптов</b>
	 * - head - вставить в секцию <head> (по умолчанию)
	 * - body - вставить в секцию <body>
	 *
	 * @param string $code  Код скрипта
	 * @param string $param,...    Дополнительные параметры
	 *
	 * @since 2.10
	 */
	public function addScripts($code)
	{
		$script = new Eresus_HTML_ScriptElement($code);

		$args = func_get_args();
		// Отбрасываем $code
		array_shift($args);

		// По умолчанию помещаем скрипты в <head>
		$body = false;

		foreach ($args as $arg)
		{
			switch (strtolower($arg))
			{
				case 'ecma':
				case 'text/ecmascript':
					$script->setAttribute('type', 'text/ecmascript');
				break;

				case 'javascript':
				case 'text/javascript':
					$script->setAttribute('type', 'text/javascript');
				break;

				case 'jscript':
				case 'text/jscript':
					$script->setAttribute('type', 'text/jscript');
				break;

				case 'vbscript':
				case 'text/vbscript':
					$script->setAttribute('type', 'text/vbscript');
				break;

				case 'body':
					$body = true;
				break;
			}
		}

		if ($body)
		{
			$this->body['scripts'][] = $script;
		}
		else
		{
			$this->head['scripts'][] = $script;
		}
	}
	//------------------------------------------------------------------------------

	/**
	 * Подключает библиотеку JavaScript
	 *
	 * При множественном вызове метода, библиотека будет подключена только один раз.
	 *
	 * Доступные библиотеки:
	 *
	 * - jquery — {@link http://jquery.com/ jQuery}
	 * - modernizr — {@link http://modernizr.com/ Modernizr}
	 * - webshim — {@link http://afarkas.github.com/webshim/demos/ Webshim}
	 * - webshims — устаревший синоним для webshim
	 *
	 * Аргументы для библиотеки jquery:
	 *
	 * - ui — jQuery UI
	 * - cookie — jQuery.Cookie
	 *
	 * @param string $library  имя библиотеки
	 * @param ...              дополнительные аргументы
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function linkJsLib($library)
	{
		$args = func_get_args();
		array_shift($args);
		$root = Eresus_CMS::getLegacyKernel()->root;
		switch ($library)
		{
			case 'jquery':
				if (in_array('ui', $args))
				{
					$this->linkScripts($root . 'core/jquery/jquery-ui.min.js', 'top');
				}
				if (in_array('cookie', $args))
				{
					$this->linkScripts($root . 'core/jquery/jquery.cookie.js', 'top');
				}
				$this->linkScripts($root . 'core/jquery/jquery.min.js', 'top');
			break;

			case 'modernizr':
				$this->linkScripts($root . 'core/js/modernizr/modernizr.min.js', 'top');
			break;

			case 'webshim':
			case 'webshims':
				$this->linkScripts($root . 'core/js/webshim/polyfiller.js', 'top');
				$this->linkJsLib('modernizr');
				$this->linkJsLib('jquery');
				$this->addScripts('jQuery.webshims.polyfill();');
			break;
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Отрисовка секции <head>
	 *
	 * @return string  Отрисованная секция <head>
	 */
	public function renderHeadSection()
	{
		$result = array();
		/* <meta> теги */
		if (count($this->head['meta-http']))
		{
			foreach ($this->head['meta-http'] as $key => $value)
			{
				$result[] = '	<meta http-equiv="'.$key.'" content="'.$value.'" />';
			}
		}

		if (count($this->head['meta-tags']))
		{
			foreach ($this->head['meta-tags'] as $key => $value)
			{
				$result[] = '	<meta name="'.$key.'" content="'.$value.'" />';
			}
		}

		/* <link> */
		if (count($this->head['link']))
		{
			foreach ($this->head['link'] as $value)
			{
				$result[] = '	<link rel="'.$value['rel'].'" href="'.$value['href'].'" type="'.
					$value['type'].'"'.(isset($value['media'])?' media="'.$value['media'].'"':'').' />';
			}
		}

		/*
		 * <script>
		 */
		foreach ($this->head['scripts'] as $script)
		{
			/** @var Eresus_HTML_ScriptElement $script */
			$result[] = $script->getHTML();
		}

		/* <style> */
		if (count($this->head['style']))
		{
			foreach ($this->head['style'] as $value)
			{
				$result[] = '	<style type="text/css"'.(isset($value['media'])?' media="'.
					$value['media'].'"':'').'>'."\n".$value['content']."\n  </style>";
			}
		}

		$this->head['content'] = trim($this->head['content']);
		if (!empty($this->head['content']))
		{
			$result[] = $this->head['content'];
		}

		$result = implode("\n" , $result);
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * Отрисовка секции <body>
	 *
	 * @return string  HTML
	 */
	protected function renderBodySection()
	{
		$result = array();
		/*
		 * <script>
		 */
		foreach ($this->body['scripts'] as $script)
		{
			/** @var Eresus_HTML_ScriptElement $script */
			$result[] = $script->getHTML();
		}

		$result = implode("\n" , $result);
		return $result;
	}

	/**
	 * Строит URL GET-запроса на основе переданных аргументов
	 *
	 * URL будет состоять из двух частей:
	 * 1. Адрес текущего раздела ($Eresus->request['path'])
	 * 2. key=value аргументы
	 *
	 * Список аргументов составляется объединением списка аргументов текущего запроса
	 * и элементов массива $args. Элементы $args имеют приоритет над аргументами текущего
	 * запроса.
	 *
	 * Если значение аргумента - пустая строка, он будет удалён из запроса.
	 *
	 * Если значение аргумента – массив, то его элементы будут объединены в строку через запятую.
	 *
	 * <b>Пример</b>
	 *
	 * Обрабатывается запрос: http://example.com/page/?name=igor&second_name=orlov&date=18.11.10
	 *
	 * <code>
	 * $args = array(
	 *   'second_name' => 'zotov',
	 *   'date' => '',
	 *   'age' => 31,
	 *   'flags' => array('new', 'customer', 'discount'),
	 * );
	 * return $page->url($args);
	 * </code>
	 *
	 * Этот код:
	 *
	 * - Оставит ''name'' нетронутым, потому что его нет в $args
	 * - Заменит значение ''second_name''
	 * - Удалит аргумент ''date''
	 * - Добавит числовой аргумент ''age''
	 * - Добавит массив ''flags''
	 *
	 * Получится:
	 *
	 * http://example.com/page/?name=igor&second_name=zotov&age=31&flags=new,customer,discount
	 *
	 * @param array $args  Установить аргументы
	 *
	 * @return string
	 *
	 * @since 2.10
	 */
	public function url($args = array())
	{
		/* Объединяем аргументы метода и аргументы текущего запроса */
		$args = array_merge(Eresus_CMS::getLegacyKernel()->request['arg'], $args);

		/* Превращаем значения-массивы в строки, соединяя элементы запятой */
		foreach ($args as $key => $value)
		{
			if (is_array($value))
			{
				$args[$key] = implode(',', $value);
			}
		}

		$result = array();
		foreach ($args as $key => $value)
		{
			if ($value !== '')
			{
				$result []= "$key=$value";
			}
		}

		$result = implode('&amp;', $result);
		$result = Eresus_CMS::getLegacyKernel()->request['path'] .'?'.$result;
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает клиентский URL страницы с идентификатором $id
	 *
	 * @param int $id  Идентификатор страницы
	 *
	 * @return string URL страницы или NULL если раздела $id не существует
	 *
	 * @since 2.10
	 */
	public function clientURL($id)
	{
		/** @var Eresus_Sections $sections */
		$sections = Eresus_Kernel::get('sections');
		$parents = $sections->parents($id);

		if (is_null($parents))
		{
			return null;
		}

		array_push($parents, $id);
		$items = $sections->get( $parents);

		$list = array();
		for ($i = 0; $i < count($items); $i++)
		{
			$list[array_search($items[$i]['id'], $parents)-1] = $items[$i]['name'];
		}
		$result = Eresus_CMS::getLegacyKernel()->root;
		for ($i = 0; $i < count($list); $i++)
		{
			$result .= $list[$i].'/';
		}

		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Отрисовка переключателя страниц
	 *
	 * @param int     $total      Общее количество страниц
	 * @param int     $current    Номер текущей страницы
	 * @param string  $url        Шаблон адреса для перехода к подстранице
	 * @param array   $templates  Шаблоны оформления
	 *
	 * @return string
	 *
	 * @since 2.10
	 */
	public function pageSelector($total, $current, $url = null, $templates = null)
	{
		# Загрузка шаблонов
		if (!is_array($templates))
		{
			$templates = array();
		}
		for ($i=0; $i < 5; $i++)
		{
			if (!isset($templates[$i]))
			{
				$templates[$i] = $this->defaults['pageselector'][$i];
			}
		}

		if (is_null($url))
		{
			$url = Eresus_CMS::getLegacyKernel()->request['path'].'p%d/';
		}

		$pages = array(); # Отображаемые страницы
		# Определяем номера первой и последней отображаемых страниц
		$visible = 10;
		if ($total > $visible)
		{
			# Будут показаны НЕ все страницы
			$from = floor($current - $visible / 2); # Начинаем показ с текущей минус половину видимых
			if ($from < 1)
			{
				$from = 1; # Страниц меньше 1-й не существует
			}
			$to = $from + $visible - 1; # мы должны показать $visible страниц
			if ($to > $total)
			{
				# Но если это больше чем страниц всего, вносим исправления
				$to = $total;
				$from = $to - $visible + 1;
			}
		}
		else
		{
			# Будут показаны все страницы
			$from = 1;
			$to = $total;
		}
		for ($i = $from; $i <= $to; $i++)
		{
			$src['href'] = sprintf($url, $i);
			$src['number'] = $i;
			$pages[] = replaceMacros($templates[$i != $current ? 1 : 2], $src);
		}

		$pages = implode('', $pages);
		if ($from != 1)
		{
			$pages = replaceMacros($templates[3], array('href' => sprintf($url, 1))).$pages;
		}
		if ($to != $total)
		{
			$pages .= replaceMacros($templates[4], array('href' => sprintf($url, $total)));
		}
		$result = replaceMacros($templates[0], array('pages' => $pages));

		return $result;
	}
	//------------------------------------------------------------------------------

}
