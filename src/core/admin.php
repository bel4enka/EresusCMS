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
 * @package Eresus
 *
 * $Id$
 */


define('ADMINUI', true);

/**
 * Класс представляет страницу административного интерфейса
 *
 * @package Eresus
 */
class TAdminUI extends WebPage
{
	var $module; # Загружаемый модуль
	var $title; # Заголовок страницы
	var $menu; # Меню администратора
	var $extmenu; # Меню раширений
	var $sub; # Уровень вложенности
	var $headers; # Заголовки ответа сервера
	var $options; # Для совместимости с TClientUI

	/**
	 * Тема оформления
	 *
	 * @var Eresus_UI_Admin_Theme
	 */
	protected $uiTheme;

	/**
	 * Констурктор
	 * @return TAdminUI
	 */
	public function __construct()
	{
		global $Eresus;

		eresus_log(__METHOD__, LOG_DEBUG, '()');

		parent::__construct();

		$theme = new Eresus_UI_Admin_Theme(Eresus_Config::get('eresus.cms.admin.theme', 'default'));
		$this->setUITheme($theme);
		Eresus_Template::setGlobalValue('theme', $theme);

		$this->title = i18n('Управление', __CLASS__);
		/* Определяем уровень вложенности */
		do
		{
			$this->sub++;
			$i = strpos($Eresus->request['url'], str_repeat('sub_', $this->sub).'id');
		}
		while ($i !== false);

		$this->sub--;

		/* Создаем меню */
		$this->menu = array(
			array(
				'access'  => EDITOR,
				'caption' => i18n('Управление', __CLASS__),
				'items' => array(
					array('link' => 'pages', 'caption' => i18n('Разделы сайта', __CLASS__),
						'hint' => i18n('Управление структурой разделов', __CLASS__), 'access' => ADMIN),
					array('link' => 'files', 'caption' => i18n('Файловый менеджер', __CLASS__),
						'hint' => i18n('Управление файлами данных', __CLASS__), 'access' => EDITOR),
					array ('link' => 'plgmgr', 'caption' => i18n('Модули расширения', __CLASS__),
						'hint' => i18n('Управление модулями расширения', __CLASS__), 'access' => ADMIN),
					array('link' => 'themes', 'caption' => i18n('Оформление', __CLASS__),
						'hint' => i18n('Управление шаблонами страниц и стилями', __CLASS__), 'access' => ADMIN),
					array('link' => 'users', 'caption' => i18n('Пользователи', __CLASS__),
						'hint' => i18n('Управление пользователями', __CLASS__), 'access' => ADMIN),
					array('link' => 'settings', 'caption' => i18n('Настройки сайта', __CLASS__),
						'hint' => i18n('Основные настройки сайта', __CLASS__), 'access' => ADMIN),
				)
			),
		);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает объект текущей темы оформления
	 * @return Eresus_UI_Admin_Theme
	 */
	public function getUITheme()
	{
		return $this->uiTheme;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает новую тему оформления
	 * @param Eresus_UI_Admin_Theme $theme
	 * @return void
	 */
	public function setUITheme(Eresus_UI_Admin_Theme $theme)
	{
		$this->uiTheme = $theme;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Подставляет значения макросов
	 * @param $text
	 * @return unknown_type
	 */
	function replaceMacros($text)
	{
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
			),
			array(
				httpHost,
				httpPath,
				$GLOBALS['Eresus']->root,
				styleRoot,
				dataRoot,

				siteName,
				siteTitle,
				siteKeywords,
				siteDescription,
			),
			$text
		);
		$result = preg_replace_callback('/\$\(const:(.*?)\)/i', '__macroConst', $result);
		$result = preg_replace_callback('/\$\(var:(([\w]*)(\[.*?\]){0,1})\)/i', '__macroVar', $result);
		$result = preg_replace('/\$\(\w+(:.*?)*?\)/', '', $result);
		return $result;
	}
	//-----------------------------------------------------------------------------

	function url($args = null, $clear = false)
	{
		global $Eresus;

		$basics = array('mod','section','id','sort','desc','pg');
		$result = '';
		if (count($Eresus->request['arg']))
		{
			foreach ($Eresus->request['arg'] as $key => $value)
			{
				if (in_array($key,$basics)|| strpos($key, 'sub_')===0)
				{
					$arg[$key] = $value;
				}
			}
		}
		if (count($args))
		{
			foreach ($args as $key => $value)
			{
				$arg[$key] = $value;
			}
		}
		if (count($arg))
		{
			foreach ($arg as $key => $value)
			{
				if (!empty($value))
				{
					$result .= '&'.$key.'='.$value;
				}
			}
		}
		if (!empty($result))
		{
			$result[0] = '?';
		}
		// См. баг http://bugs.eresus.ru/view.php?id=365
		//$result = str_replace('&', '&amp;', $result);
		$result = $GLOBALS['Eresus']->root . 'admin.php' . $result;
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Добавляет пункт в меню "Расширения"
	 *
	 * @param string $section  Заголовок меню
	 * @param array  $item     Описание добавляемого пункта. Ассоциативный массив:
	 *                         - 'access'   - Минимальный уровень доступа, необходимый чтобы видеть
	 *                                        этот пункт
	 *                         - 'link'     - "адрес", соответствующий этому пункту. В URL будет
	 *                                        подставлен в виде "mod=ext-{link}" (без фигурных скобок)
	 *                         - 'caption'  - Название пункта меню
	 *                         - 'hint'     - Всплывающая подсказка к пункту меню
	 *                         - 'disabled' - Если true - пункт будет видимым, но недоступным
	 *
	 * @return void
	 */
	public function addMenuItem($section, $item)
	{
		$item['link'] = 'ext-'.$item['link'];
		$ptr = null;
		for ($i=0; $i<count($this->extmenu); $i++)
		{
			if ($this->extmenu[$i]['caption'] == $section)
			{
				$ptr = &$this->extmenu[$i];
				break;
			}
		}

		if (is_null($ptr))
		{
			$this->extmenu[] = array(
				'access' => $item['access'],
				'caption' => $section,
				'items' => array()
			);
			$ptr = &$this->extmenu[count($this->extmenu)-1];
		}
		$ptr['items'][] = encodeHTML($item);
		if ($ptr['access'] < $item['access'])
		{
			$ptr['access'] = $item['access'];
		}
	}
	//-----------------------------------------------------------------------------


	function box($text, $class, $caption='')
	{
		$result = "<div".(empty($class)?'':' class="'.$class.'"').">\n".(empty($caption)?'':
			'<span class="'.$class.'Caption">'.$caption.'</span><br />').$text."</div>\n";
		return $result;
	}
	//-----------------------------------------------------------------------------

	function window($wnd)
	{
		$result =
		"<table border=\"0\" class=\"admWindow\"".(empty($wnd['width'])?'':' style="width: '.
		$wnd['width'].';"').">\n".
		(empty($wnd['caption'])?'':"<tr><th>".$wnd['caption']."</th></tr>\n").
		"<tr><td".(empty($wnd['style'])?'':' style="'.$wnd['style'].'"').">".$wnd['body'].
		"</td></tr>\n</table>\n";
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Отрисовывает элемент управления
	 *
	 * @access  public
	 *
	 * @param  string  $type    Тип ЭУ (delete,toggle,move,custom...)
	 * @param  string  $href    Ссылка
	 * @param  string  $custom  Индивидуальные настройки
	 *
	 * @return  string  Отрисованный ЭУ
	 */
	function control($type, $href, $custom = array())
	{
		global $Eresus;

		switch ($type)
		{
			case 'add':
				$control = array(
					'image' => $Eresus->root.'admin/themes/default/img/medium/item-add.png',
					'title' => i18n('Добавить', __CLASS__),
					'alt' => '+',
				);
			break;
			case 'edit':
				$control = array(
					'image' => $Eresus->root.'admin/themes/default/img/medium/item-edit.png',
					'title' => i18n('Изменить', __CLASS__),
					'alt' => '&plusmn;',
				);
			break;
			case 'delete':
				$control = array(
					'image' => $Eresus->root.'admin/themes/default/img/medium/item-delete.png',
					'title' => i18n('Удалить', __CLASS__),
					'alt' => 'X',
					'onclick' => 'return askdel(this)',
				);
			break;
			case 'setup':
				$control = array(
					'image' => $Eresus->root.'admin/themes/default/img/medium/item-config.png',
					'title' => i18n('Свойства', __CLASS__),
					'alt' => '*',
				);
			break;
			case 'move':
				$control = array(
					'image' => $Eresus->root.'admin/themes/default/img/medium/item-move.png',
					'title' => i18n('Переместить', __CLASS__),
					'alt' => '-&gt;',
				);
			break;
			case 'position':
				$control = array(
					'image' => $Eresus->root.'admin/themes/default/img/medium/move-up.png',
					'title' => i18n('Вверх', __CLASS__),
					'alt' => '&uarr;',
				);
				$s = array_pop($href);
				$href = $href[0];
			break;
			case 'position_down':
				$control = array(
					'image' => $Eresus->root.'admin/themes/default/img/medium/move-down.png',
					'title' => i18n('Вниз', __CLASS__),
					'alt' => '&darr;',
				);
			break;
			default:
				$control = array(
					'image' => '',
					'title' => '',
					'alt' => '',
				);
			break;
		}
		foreach ($custom as $key => $value)
		{
			$control[$key] = $value;
		}
		$result = '<a href="'.$href.'"'.(isset($control['onclick'])?' onclick="'.
			$control['onclick'].'"':'').'><img src="'.$control['image'].'" alt="'.$control['alt'].
			'" title="'.$control['title'].'" /></a>';
		if ($type == 'position')
		{
			$result .= ' '.$this->control('position_down', $s, $custom);
		}
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * Отрисовывает кнопки-"вкладки"
	 *
	 * @param array $tabs
	 *
	 * @return string  HTML
	 */
	function renderTabs($tabs)
	{
		global $Eresus, $page;

		if (count($tabs))
		{
			$result = '<div class="legacy-tabs ui-helper-clearfix">';
			$width = empty($tabs['width']) ?
				'' :
				' style="width: ' . $tabs['width'] . '"';
			if (
				isset($tabs['items']) &&
				count($tabs['items'])
			)
			{
				foreach ($tabs['items'] as $item)
				{
					if (isset($item['url']))
					{
						$url = $item['url'];
					}
					else
					{
						$url = $Eresus->request['url'];
						if (isset($item['name']))
						{
							if (($p = strpos($url, $item['name'].'=')) !== false)
							{
								$url = substr($url, 0, $p-1);
							}
							$url .= (strpos($url, '?') !== false ? '&' : '?') . $item['name'].'='.$item['value'];
						}
						else
						{
							$url = $page->url();
						}
					}
					$url = preg_replace('/&(?!amp;)/', '&amp;', $url);
					$result .= '<a'.$width.(isset($item['class'])?' class="'.$item['class'].'"':'').
						' href="'.$url.'">'.$item['caption'].'</a>';
				}
			}
			$result .= "</div>\n";
		}
		return $result;
	}
	//-----------------------------------------------------------------------------

	function renderPages($itemsCount, $itemsPerPage, $pageCount, $Descending = false, $sub_prefix='')
	{
		$prefix = empty($sub_prefix)?str_repeat('sub_', $this->sub):$sub_prefix;
		if ($itemsCount > $itemsPerPage)
		{
			$result = '<div class="admListPages">' . i18n('Страницы: ', __CLASS__);
			if ($Descending)
			{
				$forFrom = $pageCount;
				$forTo = 0;
				$forDelta = -1;
			}
			else
			{
				$forFrom = 1;
				$forTo = $pageCount+1;
				$forDelta = 1;
			}
			$pageIndex = arg($prefix.'pg') ? arg($prefix.'pg', 'int') : $forFrom;
			for ($i = $forFrom; $i != $forTo; $i += $forDelta)
			{
				if ($i == $pageIndex)
				{
					$result .= '<span class="selected">&nbsp;'.$i.'&nbsp;</span>';
				}
				else
				{
					$result .= '<a href="'.$this->url(array($prefix.'pg' => $i)).'">&nbsp;'.$i.'&nbsp;</a>';
				}
			}
			$result .= "</div>\n";
			return $result;
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 *
	 * @param array  $table
	 * @param array  $values
	 * @param string $sub_prefix
	 * @return string
	 */
	function renderTable($table, $values=null, $sub_prefix='')
	{
		global $Eresus;

		$result = '';
		$prefix = empty($sub_prefix) ? str_repeat('sub_', $this->sub) : $sub_prefix;
		$itemsPerPage = isset($table['itemsPerPage']) ?
			$table['itemsPerPage'] :
			(isset($this->module->settings['itemsPerPage']) ?
				$this->module->settings['itemsPerPage'] :
				0);
		$pagesDesc = isset($table['sortDesc']) ? $table['sortDesc'] : false;
		if (isset($table['tabs']) && count($table['tabs']))
		{
			$result .= $this->renderTabs($table['tabs']);
		}
		if (isset($table['hint']))
		{
			$result .= '<div class="admListHint">'.$table['hint']."</div>\n";
		}
		$sortMode = arg($prefix.'sort') ?
			arg($prefix.'sort', 'word') :
			(isset($table['sortMode'])?$table['sortMode']:'');
		$sortDesc = arg($prefix.'desc') ?
			arg($prefix.'desc', 'int') :
			(arg($prefix.'sort')?'':(isset($table['sortDesc'])?$table['sortDesc']:false));
		if (is_null($values))
		{
			$count = $Eresus->db->count($table['name'],
				isset($table['condition'])?$table['condition']:'');
			if ($itemsPerPage)
			{
				$pageCount = ((integer) ($count / $itemsPerPage)+(($count % $itemsPerPage) > 0));
				if ($count > $itemsPerPage)
				{
					$pages = $this->renderPages($count, $itemsPerPage, $pageCount, $pagesDesc, $sub_prefix);
				}
				else
				{
					$pages = '';
				}
				$page = arg($prefix.'pg') ? arg($prefix.'pg', 'int') : ($pagesDesc ? $pageCount : 1);
			}
			else
			{
				$pageCount = $count;
				$pages = '';
				$page = 1;
			}
			$items = $Eresus->db->select(
				$table['name'],
				isset($table['condition'])?$table['condition']:'',
				($sortDesc ? '-' : '').$sortMode,
				'',
				$itemsPerPage,
				($pagesDesc?($pageCount-$page)*$itemsPerPage:($page-1)*$itemsPerPage)
			);
		}
		else
		{
			$items = $values;
		}

		if (isset($pages))
		{
			$result .= $pages;
		}
		$result .= "<table class=\"admList\">\n".
			'<tr><th style="width: 100px;">' . i18n('Управление', __CLASS__) .
			(isset($table['controls']['position'])?' <a href="'.
			$this->url(array($prefix.'sort' => 'position', $prefix.'desc' => '0')).'" title="'.
			i18n('По порядку', __CLASS__) . '">'.
			img('admin/themes/default/img/ard.gif', i18n('По порядку', __CLASS__),
				i18n('По порядку', __CLASS__)).'</a>':'').
			"</th>";
		if (count($table['columns']))
		{
			foreach ($table['columns'] as $column)
			{
				$result .= '<th '.(isset($column['width'])?' style="width: '.$column['width'].'"':'').'>'.
					(arg($prefix.'sort') == $column['name'] ? '<span class="admSortBy">'.
					(isset($column['caption'])?$column['caption']:'&nbsp;').
					'</span>':(isset($column['caption'])?$column['caption']:'&nbsp;')).
					(isset($table['name'])?
					' <a href="'.$this->url(array($prefix.'sort' => $column['name'], $prefix.'desc' => '')).
					'" title="' . i18n('По возрастанию', __CLASS__) . '">'.
					img('admin/themes/default/img/ard.gif', i18n('По возрастанию', __CLASS__),
						i18n('По возрастанию', __CLASS__)).'</a> '.
					'<a href="'.$this->url(array($prefix.'sort' => $column['name'], $prefix.'desc' => '1')).
					'" title="' . i18n('По убыванию', __CLASS__) . '">'.
					img('admin/themes/default/img/aru.gif', i18n('По убыванию', __CLASS__),
						i18n('По убыванию', __CLASS__)). '</a></th>':'');
			}
		}
		$result .= "</tr>\n";
		$url_delete = $this->url(array($prefix.'delete'=>"%s"));
		$url_edit = $this->url(array($prefix.'id'=>"%s"));
		$url_position = $this->url(array($prefix."%s"=>"%s"));
		$url_toggle = $this->url(array($prefix.'toggle'=>"%s"));
		if (count($items))
		{
			foreach ($items as $item)
			{
				$result .= '<tr><td class="ctrl">';

				/* Удаление */
				if (
					isset($table['controls']['delete']) &&
					(
						empty($table['controls']['delete']) ||
						$this->module->$table['controls']['delete']($item)
					)
				)
				{
					$result .= ' <a href="' . sprintf($url_delete, $item[$table['key']]) . '" title="' .
						i18n('Удалить', __CLASS__) . '" onclick="return askdel(this)">' .
						img('admin/themes/default/img/medium/item-delete.png', i18n('Удалить', __CLASS__),
							i18n('Удалить', __CLASS__), 16, 16).
						'</a>';
				}

				/* Изменение */
				if (
					isset($table['controls']['edit']) &&
					(
						empty($table['controls']['edit']) ||
						$this->module->$table['controls']['edit']($item)
					)
				)
				{
					$result .= ' <a href="' . sprintf($url_edit, $item[$table['key']]) . '" title="' .
						i18n('Изменить', __CLASS__) .	'">' .
						img('admin/themes/default/img/medium/item-edit.png', i18n('Изменить', __CLASS__),
							i18n('Изменить', __CLASS__), 16, 16).'</a>';
				}

				/* Вверх/вниз */
				if (
					isset($table['controls']['position']) &&
					(
						empty($table['controls']['position']) ||
						$this->module->$table['controls']['position']($item)
					) &&
					$sortMode == 'position'
				)
				{
					$result .= ' <a href="' . sprintf($url_position, 'up', $item[$table['key']]) .
						'" title="' . i18n('Вверх', __CLASS__) . '">' .
						img('admin/themes/default/img/medium/move-up.png', i18n('Вверх', __CLASS__),
							i18n('Вверх', __CLASS__)).'</a>';
					$result .= ' <a href="' . sprintf($url_position, 'down', $item[$table['key']]) .
						'" title="' . i18n('Вниз', __CLASS__) . '">' .
						img('admin/themes/default/img/medium/move-down.png', i18n('Вниз', __CLASS__),
							i18n('Вниз', __CLASS__)).'</a>';
				}

				/* Активность */
				if (
					isset($table['controls']['toggle']) &&
					(
						empty($table['controls']['toggle']) ||
						$this->module->$table['controls']['toggle']($item)
					)
				)
				{
					$result .= ' <a href="' . sprintf($url_toggle, $item[$table['key']]) . '" title="' .
						($item['active'] ? i18n('Отключить', __CLASS__) : i18n('Включить', __CLASS__)) . '">' .
						img('admin/themes/default/img/medium/item-' . ($item['active'] ? 'active':'inactive').
						'.png', $item['active'] ? i18n('Отключить', __CLASS__) : i18n('Включить', __CLASS__),
						$item['active'] ? i18n('Отключить', __CLASS__) : i18n('Включить', __CLASS__)).'</a>';
				}

				$result .= '</td>';
				# Обрабатываем ячейки данных
				if (count($table['columns']))
				{
					foreach ($table['columns'] as $column)
					{
						$value = isset($column['value']) ?
							$column['value'] :
							(isset($item[$column['name']])?$item[$column['name']]:'');
						if (isset($column['replace']) && count($column['replace']))
						{
							$value = array_key_exists($value, $column['replace']) ?
								$column['replace'][$value] :
								$value;
						}
						if (isset($column['macros']))
						{
							preg_match_all('/\$\((.+)\)/U', $value, $matches);
							if (count($matches[1]))
							{
								foreach ($matches[1] as $macros)
								{
									if (isset($item[$macros]))
									{
										$value = str_replace('$('.$macros.')', encodeHTML($item[$macros]), $value);
									}
								}
							}
						}
						$value = $this->replaceMacros($value);
						if (isset($column['striptags']))
						{
							$value = strip_tags($value);
						}
						if (isset($column['function']))
						{
							switch ($column['function'])
							{
								case 'isEmpty':
									$value = empty($value)? i18n('Да') : i18n('Нет');
								break;
								case 'isNotEmpty':
									$value = empty($value)? i18n('Нет') : i18n('Да');
								break;
								case 'isNull':
									$value = is_null($value)? i18n('Да') : i18n('Нет');
								break;
								case 'isNotNull':
									$value = is_null($value)? i18n('Нет') : i18n('Да');
								break;
								case 'length':
									$value = mb_strlen($value);
								break;
							}
						}
						if (isset($column['maxlength']) && (mb_strlen($value) > $column['maxlength']))
						{
							$value = mb_substr($value, 0, $column['maxlength']).'...';
						}
						$style = '';
						if (isset($column['align']))
						{
							$style .= 'text-align: '.$column['align'].';';
						}
						if (isset($column['wrap']) && !$column['wrap'])
						{
							$style .=  'white-space: nowrap;';
						}
						if (!empty($style))
						{
							$style = " style=\"$style\"";
						}
						$result .= '<td'.$style.'>'.$value.'</td>';
					}
				}
				$result .= "</tr>\n";
			}
		}
		$result .= "</table>\n";
		if (isset($pages))
		{
			$result .= $pages;
		}
		return $result;
	}
	//-----------------------------------------------------------------------------

	function renderForm($form, $values=array())
	{
		$result = '';
		if (isset($form['tabs']))
		{
			$result .= $this->renderTabs($form['tabs']);
		}
		useLib('forms');
		$wnd['caption'] = $form['caption'];
		$wnd['width'] = isset($form['width'])?$form['width']:'';
		$wnd['style'] = 'padding: 0px;';
		$wnd['body'] = form($form, $values);
		$result .= $this->window($wnd);
		return $result;
	}
	//-----------------------------------------------------------------------------

	function renderContent()
	{
		global $Eresus;

		eresus_log(__METHOD__, LOG_DEBUG, '()');

		$req = HTTP::request();
		$result = '';
		if (arg('mod'))
		{
			$module = arg('mod', '/[^\w-]/');
			if (file_exists($GLOBALS['Eresus']->froot . "core/$module.php"))
			{
				include $Eresus->froot . "core/$module.php";
				$class = "T$module";
				$this->module = new $class;
			}
			elseif (substr($module, 0, 4) == 'ext-')
			{
				$name = substr($module, 4);
				$this->module = $Eresus->plugins->load($name);
			}
			else
			{
				ErrorMessage(i18n('Файл не найден') . ': "' . $GLOBALS['Eresus']->froot .
					"core/$module.php'");
			}

			/*
			 * Отрисовка контента плагином
			 */
			if (is_object($this->module))
			{
				if (method_exists($this->module, 'adminRender'))
				{
					try
					{
						$result .= $this->module->adminRender();
					}
					catch (Eresus_CMS_Exception_NotFound $e)
					{
						header('Not Found', true, 404);
						return i18n('Страница не найдена');
					}
					catch (Exception $e)
					{
						if (isset($name))
						{
							$msg = i18n('В расширении "%s" произошла ошибка.');
							$msg = sprintf($msg, $name);
						}
						else
						{
							$msg = i18n('В подсистеме "%s" произошла ошибка.');
							$msg = sprintf($msg, $module);
						}

						Eresus_Logger::exception($e);

						$msg .= '<br />' . $e->getMessage();
						if ($e instanceof EresusRuntimeException || $e instanceof EresusLogicException)
						{
							$msg .= '<br />' . $e->getDescription();
						}
						ErrorMessage($msg);
					}
				}
				else
				{
					$result .= ErrorMessage(sprintf(i18n('Метод "%s" не найден в классе "%s".'),
						'adminRender', get_class($this->module)));
				}
			}
			else
			{
				eresus_log(__METHOD__, LOG_ERR, '$module property is not an object');
				$msg = i18n('Модуль расширения "%s" не установлен или отключен.', __CLASS__);
				ErrorMessage(sprintf($msg, isset($name) ? $name : $module));
			}
		}

		$session =& $Eresus->session;
		$tmpl = Eresus_Template::fromFile('core/templates/message.html');

		if (
			isset($session['msg']['information']) && count($session['msg']['information'])
		)
		{
			$messages = '';
			foreach ($session['msg']['information'] as $message)
			{
				$messages .= $tmpl->compile(array('text' => $message, 'type' => 'info'));
			}
			$result = $messages . $result;
			$session['msg']['information'] = array();
		}
		if (isset($session['msg']['errors']) && count($session['msg']['errors']))
		{
			$messages = '';
			foreach ($session['msg']['errors'] as $message)
			{
				$messages .= $tmpl->compile(array('text' => $message, 'type' => 'error'));
			}
			$result = $messages . $result;
			$session['msg']['errors'] = array();
		}
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Отрисовывает ветку меню
	 *
	 * @param $opened
	 * @param $owner
	 * @param $level
	 */
	private function renderPagesMenu(&$opened, $owner = 0, $level = 0)
	{
		global $Eresus;

		$theme = $this->getUITheme();

		$result = '';
		$items = $Eresus->sections->children($owner, $Eresus->user->access, SECTIONS_ACTIVE);

		if (count($items))
		{
			foreach ($items as $item)
			{
				if (empty($item['caption']))
				{
					$item['caption'] = i18n('(не задано)', __CLASS__);
				}

				if (
					isset($Eresus->request['arg']['section']) &&
					$item['id'] == arg('section')
				)
				{
					$this->title = $item['caption']; # title - массив?
				}

				$sub = $this->renderPagesMenu($opened, $item['id'], $level+1);
				$current = (arg('mod') == 'content') && (arg('section') == $item['id']);

				if ($current)
				{
					$opened = $level;
				}

				// Альтернативный текст
				$alt = '[&nbsp;]';
				// Подсказка
				$title = '';
				// Классы пункта меню
				$classes = array();

				if ($opened == $level + 1)
				{
					$display = 'block';
					$classes []= 'opened';
					$opened--;
				}
				else
				{
					$display = 'none';
				}

				if ($sub)
				{
					$classes []= 'parrent';
					$alt = '[+]';
					$title = 'Развернуть';
				}

				if ($current)
				{
					$classes []= 'current';
				}

				if (!$item['visible'])
				{
					$classes []= 'invisible';
				}

				$classes = implode(' ', $classes);

				$result .=
					'<li' . ($classes ? ' class="' . $classes . '"' : '') . '>' .
						'<img src="' . $GLOBALS['Eresus']->root . $theme->getImage('dot.gif') . '" alt="' .
						$alt . '" title="' . $title . '" /> ' .
						'<a href="' . $GLOBALS['Eresus']->root . 'admin.php?mod=content&amp;section=' .
						$item['id'] .
						'" title="ID: '.$item['id'].' ('.$item['name'].')">'.$item['caption']."</a>\n";

				if (!empty($sub))
				{
					$result .= '<ul style="display: '.$display.';">'.$sub.'</ul>';
				}
			}
		}

		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Отрисовывает меню плагинов и управления
	 *
	 * @return string  HTML
	 */
	private function renderControlMenu()
	{
		global $Eresus;

		//TODO $Eresus->plugins->adminOnMenuRender();

		$menu = '';
		for ($section = 0; $section < count($this->extmenu); $section++)
		{
			if (UserRights($this->extmenu[$section]['access']))
			{
				$menu .= '<div class="header">' . $this->extmenu[$section]['caption'] .
					'</div><div class="content"><ul class="nav-main">';
				foreach ($this->extmenu[$section]['items'] as $item)
				{
					if (
						UserRights(
							isset($item['access']) ? $item['access'] : $this->extmenu[$section]['access']
						) &&
						(!(isset($item['disabled']) && $item['disabled']))
					)
					{
						if ($item['link'] == arg('mod'))
						{
							$this->title = $item['caption'];
						}
						$menu .= '<li class="nav-main-item' .
							($item['link'] == arg('mod') ? ' nav-main-item_state_current' : '') .
							'"><a href="' . $GLOBALS['Eresus']->root . "admin.php?mod=" . $item['link'] .
							"\" title=\"" .	$item['hint'] . "\">" . $item['caption'] . "</a></li>\n";
					}
				}
				$menu .= "</ul></div>\n";
			}
		}

		for ($section = 0; $section < count($this->menu); $section++)
		{
			if (UserRights($this->menu[$section]['access']))
			{
				$menu .=
					'<div class="block__header">' . $this->menu[$section]['caption'] . '</div>' .
					'<ul class="nav-main">';
				foreach ($this->menu[$section]['items'] as $item)
				{
					if (
						UserRights(
							isset($item['access']) ? $item['access'] : $this->menu[$section]['access']
						) &&
						(!(isset($item['disabled']) && $item['disabled']))
					)
					{
						if ($item['link'] == arg('mod'))
						{
							$this->title = $item['caption'];
						}
						$menu .= '<li class="nav-main-item' .
							($item['link'] == arg('mod') ? ' nav-main-item_state_current' : '') .
							'"><a href="' . $GLOBALS['Eresus']->root . "admin.php?mod=" . $item['link'] .
							'" title="' .	$item['hint'] . '" class="nav-main-item__caption">' . $item['caption'] . "</a></li>\n";
					}
				}
				$menu .= "</ul>\n";
			}
		}

		return $menu;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает разметку страницы или отправляет её пользователю.
	 *
	 * @return string
	 */
	public function render()
	{
		/* Проверям права доступа и, если надо, проводим авторизацию */
		if (!UserRights(EDITOR))
		{
			$ctrl = new Eresus_Admin_Controller_Auth(Eresus_Kernel::sc());
			$html = $ctrl->authAction();
		}
		else
		{
			$html = $this->renderUI();
		}
		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Отрисовка интерфейса
	 * @return void
	 */
	private function renderUI()
	{
		global $Eresus;

		eresus_log(__METHOD__, LOG_DEBUG, '()');
		$data = array();

		$data['page'] = $this;
		$data['content'] = $this->renderContent();
		$data['siteName'] = option('siteName');
		$data['body'] = $this->renderBodySection();
		$data['cms'] = array(
			'name' => CMSNAME,
			'version' => CMSVERSION,
			'link' => CMSLINK,
		);
		$opened = -1;
		$data['sectionMenu'] = $this->renderPagesMenu($opened);
		$data['controlMenu'] = $this->renderControlMenu();
		$data['user'] = $Eresus->user;

		$tmpl = Eresus_Template::fromFile('core/templates/page.default.html');
		$html = $tmpl->compile($data);

		if (count($this->headers))
		{
			foreach ($this->headers as $header)
			{
				header($header);
			}
		}

		echo $html;
	}
	//-----------------------------------------------------------------------------
}
