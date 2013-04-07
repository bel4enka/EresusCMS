<?php
/**
 * Контроллер управления разделами сайта
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

use Eresus\CmsBundle\Content\ContentTypeRegistry;
use Eresus\CmsBundle\Entity\Section;
use Eresus\CmsBundle\Form\DataTransformer\NullToStringTransformer;
use Eresus\CmsBundle\Form\DataTransformer\OptionsTransformer;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Response;
use Eresus\CmsBundle\HTTP\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Контроллер управления разделами сайта
 *
 * @since 4.0.0
 */
class AdminContentController extends AdminAbstractController
{
    /**
     * Список разделов сайта
     *
     * @return Response
     *
     * @since 4.0.0
     */
    public function indexAction()
    {
        $vars = $this->createTemplateVars();

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var \Eresus\CmsBundle\Repository\SectionRepository $repo */
        $repo = $em->getRepository('CmsBundle:Section');
        $vars['section'] = $repo->getRoot();
        return $this->render('CmsBundle:Content:Index.html.twig', $vars);
    }

    /**
     * Добавление нового раздела
     *
     * @param int     $parent
     * @param Request $request
     *
     * @throws NotFoundHttpException
     *
     * @return Response
     */
    public function addAction($parent, Request $request)
    {
        $vars = array();

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var Section $parent */
        $parent = $em->find('CmsBundle:Section', $parent);
        if (null === $parent)
        {
            throw $this->createNotFoundException();
        }

        $section = new Section();
        $section->parent = $parent;
        $section->description = '';
        $section->keywords = '';
        $section->enabled = true;
        $section->visible = true;
        $section->created = new \DateTime();
        $vars['section'] = $section;

        /** @var ContentTypeRegistry $contentTypeRegistry */
        $contentTypeRegistry = $this->get('content_types');
        $contentTypeDescriptions = array();
        foreach ($contentTypeRegistry->getAll() as $type)
        {
            $contentTypeDescriptions[$type->getId()] = $type->getDescription();
        }
        $vars['contentTypeDescriptions'] = $contentTypeDescriptions;

        $form = $this->getForm($section);

        if ($request->getMethod() == 'POST')
        {
            $form->bind($request);
            if ($form->isValid())
            {
                $section->parent = $parent;
                if (empty($section->title))
                {
                    $section->title = $section->caption;
                }
                $em->persist($section);

                $q = $em->createQuery(
                    'SELECT MAX(s.position) FROM CmsBundle:Section s WHERE s.parent = :parent');
                $q->setParameter('parent', $parent);
                $max = $q->getSingleResult();
                $section->position = $max[1] + 1;

                $em->flush();
                return $this->redirect($this->generateUrl('admin.content'));
            }
        }
        $vars['form'] = $form->createView();
        return $this->render('CmsBundle:Content:Add.html.twig', $vars);
    }

    /**
     * Редактирование раздела
     *
     * @param int $id
     * @param Request $request
     *
     * @throws NotFoundHttpException
     *
     * @return Response
     */
    public function editAction($id, Request $request)
    {
        $vars = $this->createTemplateVars();
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var \Eresus\CmsBundle\Entity\Section $section */
        $section = $em->find('CmsBundle:Section', $id);
        if (null === $section)
        {
            throw $this->createNotFoundException();
        }
        $vars['section'] = $section;

        return $this->render('CmsBundle:Content:Edit.html.twig', $vars);
    }

    /**
     * Изменение свойств раздела
     *
     * @param int $id
     * @param Request $request
     *
     * @throws NotFoundHttpException
     *
     * @return Response
     */
    public function propertiesAction($id, Request $request)
    {
        $vars = $this->createTemplateVars();
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var \Eresus\CmsBundle\Entity\Section $section */
        $section = $em->find('CmsBundle:Section', $id);
        if (null === $section)
        {
            throw $this->createNotFoundException();
        }
        $form = $this->getForm($section);
        $vars['section'] = $section;

        if ($request->getMethod() == 'POST')
        {
            $form->bind($request);
            if ($form->isValid())
            {
                $em->flush();
                return $this->redirect($this->generateUrl('admin.content.properties',
                    array('id' => $section->id)));
            }
        }

        $vars['form'] = $form->createView();

        return $this->render('CmsBundle:Content:Properties.html.twig', $vars);
    }

    /**
     * Перемещает страницу выше в списке
     *
     * @param int $id
     *
     * @return Response
     */
    public function upAction($id)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var Section $section */
        $section = $em->find('CmsBundle:Section', $id);
        if ($section->position > 0)
        {
            $q = $em->createQuery(
                'SELECT s FROM CmsBundle:Section s ' .
                    'WHERE s.parent = :parent AND s.position < :position ' .
                    'ORDER BY s.position DESC'
            );
            $q->setParameter('parent', $section->parent);
            $q->setParameter('position', $section->position);
            $q->setMaxResults(1);
            /** @var Section $swap */
            $swap = $q->getOneOrNullResult();
            if (null !== $swap)
            {
                $pos = $section->position;
                $section->position = $swap->position;
                $swap->position = $pos;
                $em->flush();
            }
        }
        return $this->redirect($this->generateUrl('admin.content'));
    }

    /**
     * Перемещает раздел ниже в списке
     *
     * @param int $id
     *
     * @return Response
     */
    public function downAction($id)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var Section $section */
        $section = $em->find('CmsBundle:Section', $id);
        $q = $em->createQuery(
            'SELECT s FROM CmsBundle:Section s ' .
                'WHERE s.parent = :parent AND s.position > :position ' .
                'ORDER BY s.position ASC'
        );
        $q->setParameter('parent', $section->parent);
        $q->setParameter('position', $section->position);
        $q->setMaxResults(1);
        /** @var Section $swap */
        $swap = $q->getOneOrNullResult();
        if (null !== $swap)
        {
            $pos = $section->position;
            $section->position = $swap->position;
            $swap->position = $pos;
            $em->flush();
        }
        return $this->redirect($this->generateUrl('admin.content'));
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
            'mainMenuRoute' => 'admin.content',
        );
    }

    /**
     * Возвращает форму добавления/изменения
     *
     * @param Section $section  раздел сайта
     *
     * @return Form
     *
     * @since 4.00
     */
    private function getForm(Section $section)
    {
        $null2string = new NullToStringTransformer();
        $builder = $this->createFormBuilder($section);

        // Если это добавление…
        if (null === $section->id)
        {
            /** @var ContentTypeRegistry $contentTypeRegistry */
            $contentTypeRegistry = $this->get('content_types');
            $contentTypes = array();
            $contentTypeDescriptions = array();
            foreach ($contentTypeRegistry->getAll() as $type)
            {
                $contentTypes[$type->getId()] = $type->getTitle();
                $contentTypeDescriptions[$type->getId()] = $type->getDescription();
            }
            $builder
                ->add('type', 'choice', array('label' => 'Выберите тип раздела',
                    'choices' => $contentTypes));
        }

        $builder->add('caption', 'text', array('label'  => 'Название пункта меню'));

        if (null !== $section->parent)
        {
            $builder->add('name', 'text', array('label'  => 'Адрес раздела',
                'attr' => array('pattern' => '^[a-zA-Z0-9_-]+$')));
        }

        $builder
            ->add('title', 'textarea',
                array('label'  => 'Заголовок &lt;title&gt;', 'required' => false))
            ->add($builder->create('description', 'textarea',
                array('label'  => 'Описание (description)', 'required' => false))
                ->addModelTransformer($null2string))
            ->add($builder->create('keywords', 'textarea',
                array('label'  => 'Ключевые слова (keywords)', 'required' => false))
                ->addModelTransformer($null2string))

            ->add('enabled', 'checkbox', array('label'  => 'Включить', 'required' => false))
            ->add('visible', 'checkbox',
                array('label'  => 'Показывать в меню', 'required' => false));
        /*
        ->add('template', 'choice', array('label'  => 'Шаблон',
            'choices' => $templates->enum()))*/

        if (null !== $section->id)
        {
            $builder->add('created', 'datetime', array('label'  => 'Дата и время создания'));
        }

        return $builder->getForm();
    }
}

