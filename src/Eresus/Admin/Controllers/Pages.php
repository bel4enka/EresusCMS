<?php
/**
 * ${product.title}
 *
 * Управление разделами сайта
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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Eresus\CmsBundle\Form\DataTransformer\OptionsTransformer;
use Eresus\CmsBundle\Form\DataTransformer\NullToStringTransformer;
use Eresus\CmsBundle\Entity\Section;
use Eresus\CmsBundle\AdminUI;
use Eresus\CmsBundle\Templates;

/**
 * Управление разделами сайта
 *
 * @package Eresus
 */
class Eresus_Admin_Controllers_Pages extends Eresus_Admin_Controllers_Abstract
{
    /**
     * Удаляет страницу
     *
     * @param Request $request
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return Response
     */
    private function delete(Request $request)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $section = $em->find('CmsBundle:Section', $request->query->getInt('id'));
        if (null === $section)
        {
            throw $this->createNotFoundException();
        }

        $em->remove($section);
        return new RedirectResponse(Eresus_Kernel::app()->getPage()->url(array('id'=>'')));
    }
}

