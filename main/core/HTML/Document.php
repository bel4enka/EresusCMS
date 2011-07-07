<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Документ HTML
 *
 * @copyright 2011, Eresus Project, http://eresus.ru/
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
 * @package HTML
 *
 * $Id$
 */


/**
 * Документ HTML
 *
 * @package HTML
 */
class Eresus_HTML_Document
{
	/**
	 * Шаблон документа
	 *
	 * @var array
	 */
	private $template;

	/**
	 * Переменные для шаблона
	 *
	 * @var array
	 */
	private $vars = array();

	/**
	 * Список подключаемых файлов CSS
	 *
	 * @var array
	 */
	private $css = array();

	/**
	 * Устанавлвиает шаблон документа
	 *
	 * @param string $template
	 * @param string $module
	 *
	 * @return void
	 *
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
	 * @param string $name
	 * @param mixed  $value
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
	 * Подключает к документу лист стилей CSS
	 *
	 * @param string $url
	 * @param string $media
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	public function linkCSS($url, $media = '')
	{
		//$this->css[$url] = $media;
		$req = Eresus_Kernel::app()->get('request');
		$html .= '<link rel="stylesheet" href="' . $req->getRootPrefix() . '/' . $url . '"' .
			($media ? ' media="' . $media . '"' : '') .'>';
		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Подключает к документу скрипт JavaScript
	 *
	 * @param string $url
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	public function linkJavaScript($url)
	{
		//$this->css[$url] = $media;
		$req = Eresus_Kernel::app()->get('request');
		$html .= '<script src="' . $req->getRootPrefix() . '/' . $url .
			'" type="text/javascript"></script>';
		return $html;
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
		$vars = $this->vars;

		$ts = Eresus_Service_Templates::getInstance();
		$tmpl = $ts->get($this->template[0], $this->template[1]);
		$html = $tmpl->compile($vars);
		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает разметку для подключения файлов CSS
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	private function compileCSS()
	{
	}
	//-----------------------------------------------------------------------------
}
