<?php
/**
 * Абстрактный контроллер АИ
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

namespace Eresus\Controller\Admin;

use Eresus\Controller\Controller;
use Eresus\Templating\TemplateManager;
use Symfony\Component\HttpFoundation\Request;

/**
 * Абстрактный контроллер АИ
 *
 * @internal
 * @since 3.01
 */
abstract class AdminController extends Controller
{
    /**
     * Возвращает отрисованное на основе указанного шаблона представление данных
     *
     * @param string $templateName
     * @param array  $templateVars
     *
     * @return string
     *
     * @since 3.01
     */
    protected function renderView($templateName, array $templateVars = array())
    {
        /** @var TemplateManager $templates */
        $templates = $this->get('templates');
        $template = $templates->getAdminTemplate($templateName);
        return $template->compile($templateVars);
    }
}

