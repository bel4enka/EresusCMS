<?php
/**
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
 */

namespace Eresus\CmsBundle\Extensions\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Eresus_Kernel;
use Eresus\CmsBundle\Kernel;

/**
 * Контроллер диалога настройки плагина
 *
 * @since 4.00
 */
class AdminSettingsController extends AbstractController
{
    /**
     * Возвращает true, если у плагина есть диалог настроек
     * @return bool
     * @since 4.00
     */
    public function isAvailable()
    {
        /** @var Kernel $kernel */
        $kernel = $this->get('kernel');
        return file_exists($kernel->getRootDir() . $this->getPlugin()->path .
            '/Resources/views/' . str_replace(':', '/', $this->getDialogTemplateName()));
    }

    /**
     * Основное действие
     *
     * Создаёт диалог настройки и сохраняет изменения, сделанные пользователем
     *
     * @param Request $request
     *
     * @return Response
     * @since 4.00
     */
    public function mainAction(Request $request)
    {
        $values = $this->plugin->settings->toArray();
        $form = $this->createFormBuilder($values);
        foreach ($values as $key => $value)
        {
            switch (true)
            {
                case is_bool($value):
                    $type = 'checkbox';
                    break;
                default:
                    $type = null;
            }
            $form->add($key, $type, array('required' => false));
        }
        $form = $form->getForm();

        if ('POST' === $request->getMethod())
        {
            $form->bind($request);
            $values = $form->getData();
            if ($form->isValid())
            {
                foreach ($this->plugin->settings->getKeys() as $key)
                {
                    $this->plugin->settings[$key] = $values[$key];
                }
                /** @var \Eresus\CmsBundle\Extensions\Registry $extensions */
                $extensions = $this->get('extensions');
                $extensions->update($this->plugin);
                $this->redirect($this->generateUrl('admin.plugins.config',
                    array('id' => $this->plugin->namespace)));
            }
        }

        $vars = array(
            'plugin' => $this->plugin,
            'form' => $form->createView()
        );

        $template = $this->getPlugin()->getBundle()->getName() . ':'
            . $this->getDialogTemplateName();

        return $this->renderView($template, $vars);
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

