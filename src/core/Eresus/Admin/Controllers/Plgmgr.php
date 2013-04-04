<?php
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
 *
 * @package Eresus
 */

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Eresus\CmsBundle\Extensions\Plugin;
use Eresus\CmsBundle\Extensions\Registry;
use Eresus\CmsBundle\AdminUI;

/**
 * Управление модулями расширения
 *
 * @package Eresus
 */
class Eresus_Admin_Controllers_Plgmgr extends Eresus_Admin_Controllers_Abstract
{
    /**
     * Реестр плагинов
     * @var Registry
     * @since 4.00
     */
    private $extensions;

    /**
     * Удаляет плагин
     *
     * @param Request $req
     *
     * @return Response
     *
     * @since 4.00
     */
    public function uninstallAction(Request $req)
    {
        $plugin = $this->extensions->get($req->query->get('id'));
        $this->extensions->uninstall($plugin);
        return new RedirectResponse(Eresus_Kernel::app()->getPage()->url(array('id' => '')));
    }
}

