/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Клиентские скрипты файлового менеджера
 *
 * @copyright 2010, Eresus Project, http://eresus.ru/
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
 * $Id: layout.css 918 2010-06-10 04:44:59Z mk $
 */


/**
 * Обработчик кликов по панели папок
 *
 * @param Event e
 * @return false;
 */
function fmFoldersPaneClick(e)
{
	var nodeName = e.target.nodeName.toLowerCase();

	switch (true)
	{
		case nodeName == 'a':
			fmFolderNameClick(e);
		break;
	}

	return false;
}
//-----------------------------------------------------------------------------

/**
 * Обработчик клика по имени папки
 *
 * @param Event e
 * @return false;
 */
function fmFolderNameClick(e)
{
	var folderName = jQuery(e.target).text();

	fmLoadFileList(folderName);

	return false;
}
//-----------------------------------------------------------------------------

/**
 * Загружает список файлов в указанной папке
 *
 * @param String folder
 * @return void;
 */
function fmFolderNameClick(folder)
{
	var placeholder = document.createElement('div');
	var filesPane = jQuery("#FileManager div.fm-files-pane");
	var filesPaneContent = jQuery(".content", filesPane).eq(0);
	jQuery(placeholder).
		addClass('placeholder').
		css('top', filesPaneContent.offset().top).
		css('width', filesPaneContent.outerWidth()).
		css('height', filesPaneContent.outerHeight());
	filesPane.get(0).appendChild(placeholder);
}
//-----------------------------------------------------------------------------


jQuery(document).ready(function ()
{
	jQuery("#FileManager div.fm-folders-pane").click(fmFoldersPaneClick);
});
