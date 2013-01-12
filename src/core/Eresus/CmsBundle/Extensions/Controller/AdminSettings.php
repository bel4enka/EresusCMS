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

namespace Eresus\CmsBundle\Extensions\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Eresus_Kernel;

/**
 * Контроллер диалога настройки плагина
 *
 * @package Eresus
 * @since 4.00
 */
class AdminSettings extends AbstractController
{
    /**
     * Возвращает true, если у плагина есть диалог настроек
     * @return bool
     * @since 4.00
     */
    public function isAvailable()
    {
        return file_exists(Eresus_Kernel::app()->getFsRoot() . $this->getPlugin()->path .
            '/Resources/views/' . str_replace(':', '/', $this->getDialogTemplateName()));
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
            $settings = $req->request->get('settings', array());
            foreach ($settings as $key => $value)
            {
                $this->plugin->settings[$key] = $value;
            }
            /** @var \Eresus\CmsBundle\Extensions\Registry $extensions */
            $extensions = $this->get('extensions');
            $extensions->update($this->plugin);
        }
        $vars = array(
            'plugin' => $this->plugin,
        );

        $contents = $this->renderView($this->getPlugin()->getBundle()->getName() . ':'
            . $this->getDialogTemplateName(), $vars);

        $vars = array(
            'plugin' => $this->plugin,
            'contents' => $contents,
        );

        return $this->renderView('CmsBundle:Extensions:AdminSettingsDialog.html.twig', $vars);
    }

    /**
     * Возвращает имя файла шаблона диалога (в папке Resources/views)
     *
     * @return string
     * @since 4.00
     */
    protected function getDialogTemplateName()
    {
        return 'AdminSettings:Dialog.html.twig';
    }
}

