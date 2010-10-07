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
 * @package EresusCMS
 *
 * $Id$
 */

/**
 * Родительский класс веб-интерфейсов
 *
 * @package EresusCMS
 */
class WebPage
{
	/**
	 * Идентификатор текущего раздела
	 *
	 * @var int
	 */
	public $id = 0;

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
		'script' => array(),
		'content' => '',
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
	 * @return WebPage
	 */
	public function __construct()
	{
	}
	//-----------------------------------------------------------------------------

	/**
	 * Установить мета-тег HTTP-заголовка
	 *
	 * Добавляет или изменяет мета-тег <meta http-equiv="$httpEquiv" content="$content" />
	 *
	 * @param string $httpEquiv  Имя заголовка HTTP
	 * @param string $content  	  Значение заголовка
	 */
	public function setMetaHeader($httpEquiv, $content)
	{
		$this->head['meta-http'][$httpEquiv] = $content;
	}
	//------------------------------------------------------------------------------

	/**
	 * Установка мета-тега
	 *
	 * @param string $name  		 Имя тега
	 * @param string $content  Значение тега
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
	 */
	public function linkStyles($url, $media = '')
	{
		/* Проверяем, не добавлен ли уже этот URL  */
		for ($i = 0; $i < count($this->head['link']); $i++)
			if ($this->head['link'][$i]['href'] == $url)
				return;

		$item = array('rel' => 'StyleSheet', 'href' => $url, 'type' => 'text/css');

		if (!empty($media))
			$item['media'] = $media;

		$this->head['link'][] = $item;
	}
	//------------------------------------------------------------------------------

	/**
	 * Встраивание CSS
	 *
	 * @param string $content  Стили CSS
	 * @param string $media 	  Тип носителя
	 */
	public function addStyles($content, $media = '')
	{
		$content = preg_replace(array('/^(\s)+/m', '/^(\S)/m'), array('		', '	\1'), $content);
		$content = rtrim($content);
		$item = array('content' => $content);
		if (!empty($media))
			$item['media'] = $media;
		$this->head['style'][] = $item;
	}
	//------------------------------------------------------------------------------

	/**
	 * Подключение клиентского скрипта
	 *
	 * @param string $url   URL скрипта
	 * @param string $type  Тип скрипта
	 */
	public function linkScripts($url, $type = 'javascript')
	{
		for ($i = 0; $i < count($this->head['script']); $i++)
		if (
			isset($this->head['script'][$i]['src']) &&
			$this->head['script'][$i]['src'] == $url
		)
			return;

		if (strpos($type, '/') === false)
			switch (strtolower($type))
			{
				case 'emca': $type = 'text/emcascript'; break;
				case 'javascript': $type = 'text/javascript'; break;
				case 'jscript': $type = 'text/jscript'; break;
				case 'vbscript': $type = 'text/vbscript'; break;
				default: return;
			}

		$this->head['script'][] = array('type' => $type, 'src' => $url);
	}
	//------------------------------------------------------------------------------

	/**
	 * Добавление клиентских скриптов
	 *
	 * @param string $content  Код скрипта
	 * @param string $type     Тип скрипта
	 */
	public function addScripts($content, $type = 'javascript')
	{
		if (strpos($type, '/') === false)
			switch (strtolower($type))
			{
				case 'emca': $type = 'text/emcascript'; break;
				case 'javascript': $type = 'text/javascript'; break;
				case 'jscript': $type = 'text/jscript'; break;
				case 'vbscript': $type = 'text/vbscript'; break;
				default: return;
			}

		$content = preg_replace(array('/^(\s)+/m', '/^(\S)/m'), array('		', '	\1'), $content);
		$this->head['script'][] = array('type' => $type, 'content' => $content);
	}
	//------------------------------------------------------------------------------

	/**
	 * Отрисовка секции <head>
	 *
	 * @return string  Отрисованная секция <head>
	 */
	protected function renderHeadSection()
	{
		$result = array();
		/* <meta> теги */
		if (count($this->head['meta-http']))
			foreach($this->head['meta-http'] as $key => $value)
				$result[] = '	<meta http-equiv="'.$key.'" content="'.$value.'" />';

		if (count($this->head['meta-tags']))
			foreach($this->head['meta-tags'] as $key => $value)
				$result[] = '	<meta name="'.$key.'" content="'.$value.'" />';

		/* <link> */
		if (count($this->head['link']))
			foreach($this->head['link'] as $value)
				$result[] = '	<link rel="'.$value['rel'].'" href="'.$value['href'].'" type="'.
					$value['type'].'"'.(isset($value['media'])?' media="'.$value['media'].'"':'').' />';

		/* <script> */
		if (count($this->head['script']))
			foreach($this->head['script'] as $value)
			{
				if (isset($value['content']))
				{
					$value['content'] = trim($value['content']);
					$result[] = "	<script type=\"".$value['type']."\">\n	//<!-- <![CDATA[\n		".
						$value['content']."\n	//]] -->\n	</script>";
				}
				elseif (isset($value['src']))
				{
					$result[] = '	<script src="'.$value['src'].'" type="'.$value['type'].'"></script>';
				}
			}

		/* <style> */
		if (count($this->head['style']))
			foreach($this->head['style'] as $value)
				$result[] = '	<style type="text/css"'.(isset($value['media'])?' media="'.
					$value['media'].'"':'').'>'."\n".$value['content']."\n  </style>";

		$this->head['content'] = trim($this->head['content']);
		if (!empty($this->head['content']))
			$result[] = $this->head['content'];

		$result = implode("\n" , $result);
		return $result;
	}
	//------------------------------------------------------------------------------

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
	 * @param array $args  Установить аргументы
	 * @return string
	 */
	public function url($args = array())
	{
		global $Eresus;

		/* Объединяем аргументы метода и аргументы текущего запроса */
		$args = array_merge($Eresus->request['arg'], $args);

		/* Превращаем значения-массивы в строки, соединяя элементы запятой */
		foreach ($args as $key => $value)
			if (is_array($value))
				$args[$key] = implode(',', $value);

		$result = array();
		foreach ($args as $key => $value)
			if ($value !== '')
				$result []= "$key=$value";

		$result = implode('&amp;', $result);
		$result = $Eresus->request['path'].'?'.$result;
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает клиентский URL страницы с идентификатором $id
	 *
	 * @param int $id  Идентификатор страницы
	 * @return string URL страницы или NULL если раздела $id не существует
	 */
	public function clientURL($id)
	{
		global $Eresus;

		$parents = $Eresus->sections->parents($id);

		if (is_null($parents)) return null;

		array_push($parents, $id);
		$items = $Eresus->sections->get( $parents);

		$list = array();
		for($i = 0; $i < count($items); $i++) $list[array_search($items[$i]['id'], $parents)-1] = $items[$i]['name'];
		$result = $Eresus->root;
		for($i = 0; $i < count($list); $i++) $result .= $list[$i].'/';

		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Отрисовка переключателя страниц
	 *
	 * @param int     $total      Общее количество страниц
	 * @param int     $current    Номер текущей страницы
	 * @param string  $url        Шаблон адреса для перехода к подстранице.
	 * @param array   $templates  Шаблоны оформления
	 * @return string
	 */
	public function pageSelector($total, $current, $url = null, $templates = null)
	{
		global $Eresus;

		$result = '';
		# Загрузка шаблонов
		if (!is_array($templates))
			$templates = array();
		for ($i=0; $i < 5; $i++)
			if (!isset($templates[$i]))
				$templates[$i] = $this->defaults['pageselector'][$i];

		if (is_null($url))
			$url = $Eresus->request['path'].'p%d/';

		$pages = array(); # Отображаемые страницы
		# Определяем номера первой и последней отображаемых страниц
		$visible = 10;
		if ($total > $visible)
		{
			# Будут показаны НЕ все страницы
			$from = floor($current - $visible / 2); # Начинаем показ с текущей минус половину видимых
			if ($from < 1)
				$from = 1; # Страниц меньше 1-й не существует
			$to = $from + $visible - 1; # мы должны показать $visible страниц
			if ($to > $total)
			{ # Но если это больше чем страниц всего, вносим исправления
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
		for($i = $from; $i <= $to; $i++)
		{
			$src['href'] = sprintf($url, $i);
			$src['number'] = $i;
			$pages[] = replaceMacros($templates[$i != $current ? 1 : 2], $src);
		}

		$pages = implode('', $pages);
		if ($from != 1)
			$pages = replaceMacros($templates[3], array('href' => sprintf($url, 1))).$pages;
		if ($to != $total)
			$pages .= replaceMacros($templates[4], array('href' => sprintf($url, $total)));
		$result = replaceMacros($templates[0], array('pages' => $pages));

		return $result;
	}
	//------------------------------------------------------------------------------

}
