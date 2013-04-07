<?php
/**
 * Контроллер настроек сайта
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

namespace Eresus\CmsBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Eresus\CmsBundle\HTTP\Request;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\Yaml\Yaml;

/**
 * Контроллер настроек сайта
 *
 * @since 4.0.0
 */
class AdminSettingsController extends AdminAbstractController
{
    /**
     * @param Request $request
     *
     * @return Response
     *
     * @since 4.0.0
     */
    public function indexAction(Request $request)
    {
        $globals = $this->get('cms')->getGlobals();
        $form = $this->createFormBuilder($globals)
            ->add('siteName', 'text', array('required' => true, 'max_length' => 30))
            ->add('siteTitle', 'textarea', array('required' => true))
            ->add('siteTitleReverse', 'checkbox', array('required' => false, 'value' => true))
            ->add('siteTitleDivider', 'text', array('required' => true, 'max_length' => 10))
            ->add('siteKeywords', 'textarea', array('required' => false))
            ->add('siteDescription', 'textarea', array('required' => false))
            ->add('mailFromAddr', 'email', array('required' => true))
            ->add('mailFromName', 'text', array('required' => false))
            ->add('mailFromOrg', 'text', array('required' => false))
            ->add('mailReplyTo', 'email', array('required' => false))
            ->add('mailFromSign', 'textarea', array('required' => false))
            ->add('filesModeSetOnUpload', 'checkbox', array('required' => false, 'value' => true))
            ->add('filesModeDefault', 'text', array('required' => true))
            ->getForm();

        if ($request->isMethod('POST'))
        {
            $form->bind($request);
            if ($form->isValid())
            {
                /** @var FileLocator $locator */
                $locator = $this->container->get('config_locator');
                $filename = $locator->locate('global.yml');
                file_put_contents($filename, Yaml::dump($form->getData(), 2));

                return $this->redirect($this->generateUrl('admin.settings'));
            }
        }

        $vars = $this->createTemplateVars();
        $vars['form'] = $form->createView();

        return $this->render('EresusCmsBundle:Settings:Main.html.twig', $vars);
    }

    /**
     * Возвращает массив переменных для подстановки в шаблон
     *
     * @return array
     *
     * @since 4.0.0
     */
    private function createTemplateVars()
    {
        return array(
            'mainMenuRoute' => 'admin.settings',
        );
    }
}

