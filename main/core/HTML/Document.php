<?php
/**
 * ${product.title}
 *
 * Документ HTML
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
 *
 * $Id$
 */


/**
 * Документ HTML
 *
 * Этот класс предназначен для создания документов HTML.
 *
 * @package Eresus
 * @since 2.16
 */
class Eresus_HTML_Document
{
	/**
	 * Шаблон документа
	 *
	 * @var array
	 * @see setTemplate()
	 * @since 2.16
	 */
	private $template;

	/**
	 * Переменные для шаблона
	 *
	 * @var array
	 * @see setVar()
	 * @since 2.16
	 */
	private $vars = array();

	/**
	 * Список подключаемых файлов стилей
	 *
	 * @var array
	 * @see linkStylesheet()
	 * @since 2.16
	 */
	private $stylesheets = array();

	/**
	 * Список подключаемых скриптов
	 *
	 * @var array
	 * @see linkScript()
	 * @since 2.16
	 */
	private $scripts = array();

	/**
	 * Устанавливает шаблон документа
	 *
	 * @param string $template  имя шаблона
	 * @param string $module    модуль шаблона
	 *
	 * @return void
	 *
	 * @see Eresus_Template_Service::get()
	 * @since 2.16
	 */
	public function setTemplate($template, $module = null)
	{
		$this->template = array($template, $module);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает переменную для подстановки в шаблон
	 *
	 * @param string $name   имя переменной
	 * @param mixed  $value  значение
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function setVar($name, $value)
	{
		$this->vars[$name] = $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Подключает к документу лист стилей
	 *
	 * @param string $url    адрес листа стилей
	 * @param string $media  тип устройства
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function linkStylesheet($url, $media = '')
	{
		$this->stylesheets[$url] = array('media' => $media);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Подключает к документу скрипт
	 *
	 * @param string $url       адрес скрипта
	 * @param string $attr,...  атрибуты: defer, async
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function linkScript($url)
	{
		$attrs = func_get_args();
		array_shift($attrs);
		$this->scripts[$url] = $attrs;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Собирает документ из составляющих его частей и возвращает в виде строки
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	public function compile()
	{
		$ts = Eresus_Template_Service::getInstance();
		$tmpl = $ts->get($this->template[0], $this->template[1]);
		$html = $tmpl->compile($this->vars);

		$styles = $this->compileStylesheets();
		if ($styles)
		{
			$html = str_replace('</head>', $styles . '</head>', $html);
		}

		$scripts = $this->compileScripts();
		if ($scripts)
		{
			$html = str_replace('</head>', $scripts . '</head>', $html);
		}

		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает разметку для подключения файлов стилей
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	private function compileStylesheets()
	{
		$html = '';
		if (count($this->stylesheets))
		{
			//$req = Eresus_CMS_Request::getInstance(); $req->getRootPrefix()
			foreach ($this->stylesheets as $url => $params)
			{
				// TODO Учесть разметку XHTML
				$html .= '<link rel="stylesheet" href="' . $url . '"' .
					($params['media'] ? ' media="' . $params['media'] . '"' : '') . ">\n";
			}
		}
		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает разметку для подключения файлов скриптов
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	private function compileScripts()
	{
		$html = '';
		if (count($this->scripts))
		{
			//$req = Eresus_CMS_Request::getInstance(); $req->getRootPrefix()
			foreach ($this->scripts as $url => $params)
			{
				// TODO Учесть разметку XHTML
				$html .= '<script src="' . $url . '"' .
					(in_array('async', $params) ? ' async' : '') .
					(in_array('defer', $params) ? ' defer' : '') .
					"></script>\n";
			}
		}
		return $html;
	}
	//-----------------------------------------------------------------------------
}
