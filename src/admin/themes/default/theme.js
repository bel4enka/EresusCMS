/**
 * Скрипты темы Default.
 *
 * @version ${product.version}
 * @copyright ${product.copyright}
 * @license ${license.uri} ${license.name}
 * @author Михаил Красильников <m.krasilnikov@yandex.ru>
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

$(document).on('click', '.section-menu a[data-action="toggle"]', function (e)
{
    e.preventDefault();
    var control = $(e.currentTarget);
    control.blur();
    var menu = control.closest('.menu__item').children('.menu');
    var icon = $('.control__icon', control);
    /* TODO Сделать изменение alt */
    if (icon.hasClass('control__icon_img_expl'))
    {
        menu.show();
        icon.removeClass('control__icon_img_expl').addClass('control__icon_img_fold');
    }
    else if (icon.hasClass('control__icon_img_fold'))
    {
        menu.hide();
        icon.removeClass('control__icon_img_fold').addClass('control__icon_img_expl');
    }
});
