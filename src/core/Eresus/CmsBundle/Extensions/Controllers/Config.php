<?php
/**
 * ${product.title}
 *
 * Контроллер диалога настройки плагина
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
 */

namespace Eresus\CmsBundle\Extensions\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Eresus_Kernel;
use Eresus_CMS;

/**
 * Контроллер диалога настройки плагина
 *
 * @package Eresus
 * @since 4.00
 */
class Config extends AbstractController
{
    /**
     * Возвращает true, если у плагина есть диалог настроек
     * @return bool
     * @since 4.00
     */
    public function isAvailable()
    {
        return file_exists(Eresus_Kernel::app()->getFsRoot() . $this->getDialogTemplateFilename());
    }

    /**
     * Основное действие
     *
     * @param Request $req
     *
     * @return Response
     * @since 4.00
     */
    public function mainAction(Request $req)
    {
        if ('POST' === $req->getMethod())
        {
            $settings = $req->request->get('settings');
            foreach ($settings as $key => $value)
            {
                $this->plugin->settings[$key] = $value;
            }
            Eresus_CMS::getLegacyKernel()->plugins->update($this->plugin);
        }
        $vars = array(
            'plugin' => $this->plugin,
            'form' => array('action' => 'admin.php', 'method' => 'post')
        );
        return $this->renderView($this->getDialogTemplateFilename(), $vars);
    }

    /**
     * Возвращает путь к файлу шаблона диалога относительно корня сайта
     *
     * @return string
     * @since 4.00
     */
    protected function getDialogTemplateFilename()
    {
        return '/plugins/' . str_replace('\\', '/', $this->plugin->namespace) .
            '/Resources/views/AdminConfigDialog.html.twig';
    }
}

