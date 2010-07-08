/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Пресет настроек для TinyMCE
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
 * @package Eresus2
 *
 * $Id$
 */

tinyMCE.init(
{
	/****************************************************************************
	 * Параметры редактора
	 ****************************************************************************/

	/*
	 * Режим превращения textarea в WYSISYG
	 *
	 * - textareas          - Все textarea
	 * - specific_textareas - Выбранные textarea (см. editor_selector)
	 */
	mode : "specific_textareas",

	/*
	 * Класс CSS, который должен быть у textarea для превращения её в WYSIWYG
	 */
	editor_selector : "tinymce_default",

	/*
	 * Язык редактора
	 */
	language: "ru",

	/*
	 * Подключаемые плагины
	 *
	 * - advimage     - Расширенный диалог вставки картинок
	 * - advlist      - Дополнительные опции для списков
	 * - fullscreen   - Позволяет разворачивать редактор на всю страницу.
	 *                  Кнопки: fullscreen
	 * - images       - Менеджер изображений
	 * - inlinepopups - Открывает диалоги во всплывающих слоях, а не в новых окнах
	 * - paste        - Расширенные возможности вставки
	 * - safari       - Исправляет разные проблемы совместимости в Safari
	 * - table        - Работа с таблицами.
	 *                  Кнопки: tablecontrols, table, row_props, cell_props, delete_col, delete_row,
	 *                  delete_table, col_after, col_before, row_after, row_before, split_cells,
	 *                  merge_cells.
	 */
	plugins : "advimage,advlist,fullscreen,images,inlinepopups,paste,safari,table",

	/*
	 * Тема оформления
	 */
	theme : "advanced",

	/*
	 * Расположение панели кнопок относительно области редактирования
	 */
	theme_advanced_toolbar_location : "top",

	/*
	 * Выравнивание кнопок на панели
	 */
	theme_advanced_toolbar_align : "left",

	/*
	 * Расположение строки состояния относительно области редактирования
	 */
	theme_advanced_statusbar_location : "bottom",

	/*
	 * Разрешено ли изменение размера области редактирования
	 */
	theme_advanced_resizing : true,

	/*
	 * Список тегов, отображаемых в списке "Формат"
	 */
	theme_advanced_blockformats: "p,h1,h2,h3,h4,h5,h6",

	theme_advanced_buttons1 :
		"fullscreen,|,"+
		"undo,redo,|,"+
		//"formatselect,styleselect,|,"+
		"formatselect,styleselect,|,"+
		"bold,italic,strikethrough,|,"+
		"bullist,numlist,|,outdent,indent,|,"+
		"blockquote,sub,sup,|,"+
		"justifyleft,justifycenter,justifyright,justifyfull",
	theme_advanced_buttons2 :
		"link,unlink,anchor,images,image,hr,charmap,|,"+
		"tablecontrols,|,"+
		"cut,copy,paste,pastetext,pasteword,|,"+
		"cleanup,code",
	theme_advanced_buttons3 : "",

	/****************************************************************************
	 * Параметры создаваемой разметки
	 ****************************************************************************/

	/*
	 * DOCTYPE, применяемый к документу
	 */
	doctype: '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN">',

	/*
	 * Формат тегов: html или xhtml
	 */
	element_format: "html",

	/*
	 * Файл(ы) стилей, применяемые для оформления контента
	 */
	content_css: window.Eresus.siteRoot + "/style/default.css",

	/*
	 * This option enables you to specify that list elements UL/OL is to be converted to valid XHTML.
	 */
	fix_list_elements: true,

	/*
	 * This option enables you to specify that table elements should be moved outside paragraphs or
	 * other block elements.
	 */
	fix_table_elements: true,

	/*
	 * This option controls if invalid contents should be corrected before insertion in IE. IE has a
	 * bug that produced an invalid DOM tree if the input contents aren't correct so this option tries
	 * to fix this using preprocessing of the HTML string.
	 */
	fix_nesting: true,

	/*
	 * If this option is set to true, all URLs returned from the MCFileManager will be relative from
	 * the specified document_base_url. If it's set to false all URLs will be converted to absolute
	 * URLs.
	 */
	relative_urls: false,

	/*
	 * If this option is enabled the protocol and host part of the URLs returned from the
	 * MCFileManager will be removed. This option is only used if the relative_urls option is set to
	 * false.
	 */
	remove_script_host: false,


	dummy: null
});
