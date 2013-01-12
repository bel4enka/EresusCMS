<?php

namespace Eresus\Html\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultContentAdminController extends Controller
{
    public function indexAction()
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        //$em = $this->getDoctrine()->getManager();
        /** @var \Eresus\CmsBundle\Entity\Section $section */
        /*$section = $em->find('CmsBundle:Section', $request->get('id'));
        $form = $this->getForm($section);

        if ($request->getMethod() == 'POST')
        {
            $form->bind($request);
            if ($form->isValid())
            {
                $em->flush();
                return $this->redirect(Eresus_Kernel::app()->getPage()->url(array('id' => '')));
            }
        }

        // Вычисляем адрес страницы
        $urlAbs = Eresus_Kernel::app()->getPage()->clientURL($section->id);*/

        return $this->renderView('EresusHtmlBundle:DefaultContentAdmin:Edit.html.twig', array());
    }

}
