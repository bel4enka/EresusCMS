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
 */

function replaceMacros(sURL)
{
	var macros = new Array();
	macros['httpRoot'] = '$(httpRoot)';
	macros['httpHost'] = '$(httpHost)';
	macros['httpPath'] = '$(httpPath)';
	macros['styleRoot'] = '$(styleRoot)';
	macros['dataRoot'] = '$(dataRoot)';

	function __replace(sMatch, sMacros)
	{
		return macros[sMacros];
	}

	sURL = sURL.replace(/\$\(([^\)]+)\)/, __replace);
	return sURL;
}

function formApplyClick(strForm)
{
	var objForm = document.forms[strForm];
	objForm.submitURL.value = document.URL;
}

/*
 * Запрос подтверждения при удалении
 */
$(document).on('click', 'a[data-action="delete"]',
    /**
     * @param {Event} e
     */
    function (e)
    {
        if (!confirm('Подтверждаете удаление?'))
        {
            e.preventDefault();
        }
    }
);

$(document).on('click', '.sections__icon',
    /**
     * @param {Event} e
     */
    function (e)
    {
        e.preventDefault();
        var icon = $(this);
        var item = icon.closest('.sections__item');
        if (item.hasClass('sections__item_has_children'))
        {
            var children = item.children('.sections__children');
            item.toggleClass('sections__item_state_collapsed');
            var title = icon.attr('title');
            icon.attr('title', icon.attr('data-inverse-title'));
            icon.attr('data-inverse-title', title);
        }
        icon.blur();
    }
);