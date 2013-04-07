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
     * Уровень доступа к этому модулю
     * @var int
     */
    public $access = ADMIN;

    /**
     * Возвращает разметку интерфейса или готовый ответ
     *
     * @param Request $request
     *
     * @return Response|string
     */
    public function adminRender(Request $request)
    {
        if (UserRights($this->access))
        {
            switch ($request->get('action'))
            {
                case 'add':
                    $result = $this->create($request);
                    break;
                case 'up':
                    $result = $this->moveUp($request);
                    break;
                case 'down':
                    $result = $this->moveDown($request);
                    break;
                case 'create':
                    $result = $this->create($request);
                    break;
                case 'move':
                    $result = $this->move($request);
                    break;
                case 'delete':
                    $result = $this->delete($request);
                    break;
                default:
                    if ($request->get('id') != null)
                    {
                        $result = $this->edit($request);
                    }
                    else
                    {
                        $result = $this->sectionIndex();
                    }
            }
        }
        else
        {
            $result = '';
        }
        return $result;
    }

    /**
     * Перемещает страницу из одной ветки в другую
     *
     * @param Request $request
     *
     * @return Response|string
     */
    private function move(Request $request)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var \Eresus\CmsBundle\Repository\SectionRepository $repo */
        $repo = $em->getRepository('CmsBundle:Section');
        /** @var Section $section */
        $section = $repo->find($request->get('id'));

        if ($request->getMethod() == 'POST')
        {
            /** @var Section $newParent */
            $newParent = $request->request->get('target')
                ? $em->find('CmsBundle:Section', $request->request->get('target'))
                : null /*$repo->getPseudoRoot()*/;

            /*
             * Проверяем, нет ли в разделе назначения раздела с таким же именем и вычисляем
             * новый порядковый номер
             */
            $section->position = 0;
            if ($newParent && $newParent->children)
            {
                foreach ($newParent->children as $child)
                {
                    /** @var Section $child */
                    if ($child->name == $section->name)
                    {
                        ErrorMessage('В разделе назначения уже есть раздел с таким же именем!');
                        return new RedirectResponse($_SERVER['HTTP_REFERER']);
                    }
                    if ($child->position <= $section->position)
                    {
                        $section->position = $child->position + 1;
                    }
                }
            }
            $section->parent = $newParent;
            $em->flush();
            return new RedirectResponse(Eresus_Kernel::app()->getPage()->url(array('id'=>'')));
        }
        else
        {
            return $this->renderView('CmsBundle:Sections:Move.html.twig',
                array('root' => $repo->getRoot(), 'section' => $section));

        }
    }

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

