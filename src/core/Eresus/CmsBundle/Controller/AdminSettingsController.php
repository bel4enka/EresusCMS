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

/**
 * Контроллер настроек сайта
 *
 * @since 4.0.0
 */
class AdminSettingsController extends AdminAbstractController
{
    /**
     * @return Response
     * @since 4.0.0
     */
    public function indexAction()
    {
        $form = $this->createFormBuilder($this->get('cms')->getGlobals())
            ->add('siteName', 'text', array('required' => true, 'max_length' => 30))
            ->add('siteTitle', 'textarea', array('required' => true))
            ->add('siteTitleReverse', 'checkbox')
            ->add('siteTitleDivider', 'text', array('required' => true, 'max_length' => 10))
            ->add('siteKeywords', 'textarea')
            ->add('siteDescription', 'textarea')
            ->add('mailFromAddr', 'text', array('required' => true))
            ->add('mailFromName', 'text')
            ->add('mailFromOrg', 'text')
            ->add('mailReplyTo', 'text')
            ->add('mailFromSign', 'textarea')
            ->getForm();
        $data = array(
            'form' => $form->createView()
        );
        return $this->render('CmsBundle:Settings:dialog.html.twig', $data);
    }
}

