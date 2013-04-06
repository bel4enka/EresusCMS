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
        $vars = array('parentId' => $parent);
        $section = new Section();
        $section->description = '';
        $section->keywords = '';
        $section->hint = '';
        $section->enabled = true;
        $section->visible = true;
        $section->created = new \DateTime();

        //$null2string = new NullToStringTransformer();

        /** @var ContentTypeRegistry $contentTypeRegistry */
        $contentTypeRegistry = $this->get('content_types');
        $contentTypes = array();
        $contentTypeDescriptions = array();
        foreach ($contentTypeRegistry->getAll() as $type)
        {
            $contentTypes[$type->getId()] = $type->getTitle();
            $contentTypeDescriptions[$type->getId()] = $type->getDescription();
        }
        $vars['contentTypeDescriptions'] = $contentTypeDescriptions;

        $builder = $this->createFormBuilder($section);
        $builder
            ->add('type', 'choice', array('label' => 'Тип раздела',
                'choices' => $contentTypes))
            /*->add('name', 'text', array('label'  => 'Имя'))
            ->add('title', 'text', array('label'  => 'Заголовок'))
            ->add('caption', 'text', array('label'  => 'Пункт меню'))
            ->add($builder->create('hint', 'text', array('label'  => 'Подсказка',
                'required' => false))->addModelTransformer($null2string))
            ->add($builder->create('description', 'text',
                array('label'  => 'Описание', 'required' => false))
                ->addModelTransformer($null2string))
            ->add($builder->create('keywords', 'text',
                array('label'  => 'Ключевые слова', 'required' => false))
                ->addModelTransformer($null2string))
            ->add('template', 'choice', array('label'  => 'Шаблон',
                'choices' => $templates->enum()))

            ->add('active', 'checkbox', array('label'  => 'Включить'))
            ->add('visible', 'checkbox', array('label'  => 'Показывать в меню'))*/;
            //$builder->add('position', 'integer', array('label'  => 'Порядковый номер'));
        /*$builder
            ->add($builder->create('options', 'textarea',
                array('label'  => 'Опции', 'required' => false))
                ->addModelTransformer(new OptionsTransformer()))
            ->add('created', 'datetime', array('label'  => 'Дата создания',
                'widget' => 'single_text', 'format' => \IntlDateFormatter::SHORT))
            ->add('updated', 'datetime', array('label'  => 'Дата изменения',
                'widget' => 'single_text', 'format' => \IntlDateFormatter::SHORT));*/

        $form = $builder->getForm();

        if ($request->getMethod() == 'POST')
        {
            $form->bind($request);
            if ($form->isValid())
            {
                /** @var \Doctrine\ORM\EntityManager $em */
                $em = $this->getDoctrine()->getManager();

                if (0 == $parent)
                {
                    $parent = null;
                }
                else
                {
                    $parent = $em->find('CmsBundle:Section', $parent);
                    if (null === $parent)
                    {
                        throw $this->createNotFoundException();
                    }
                }
                $section->parent = $parent;
                $em->persist($section);

                $q = $em->createQuery(
                    'SELECT MAX(s.position) FROM CmsBundle:Section s WHERE s.parent = :parent');
                $q->setParameter('parent', $parent);
                $max = $q->getSingleResult();
                $section->position = $max[1] + 1;

                $em->flush();
                return $this->redirect(Eresus_Kernel::app()->getPage()->url());
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
     * @return Response
     */
    public function editAction($id, Request $request)
    {
        $vars = $this->createTemplateVars();
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var \Eresus\CmsBundle\Entity\Section $section */
        $section = $em->find('CmsBundle:Section', $id);
        $vars['section'] = $section;

        return $this->render('CmsBundle:Content:Edit.html.twig', $vars);
    }

    /**
     * Изменение свойств раздела
     *
     * @param int $id
     * @param Request $request
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
        $vars['section'] = $section;
        $form = $this->getForm($section);

        if ($request->getMethod() == 'POST')
        {
            $form->bind($request);
            if ($form->isValid())
            {
                $em->flush();
                return $this->redirect($this->generateUrl('admin.content'));
            }
        }

        $vars['form'] = $form->createView();

        return $this->render('CmsBundle:Content:Properties.html.twig', $vars);
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
        if (null !== $section->parent)
        {
            $builder->add('name', 'text', array('label'  => 'Имя'));
        }
        $builder
            ->add('title', 'text', array('label'  => 'Заголовок'))
            ->add('caption', 'text', array('label'  => 'Пункт меню'))
            ->add($builder->create('hint', 'text', array('label'  => 'Подсказка',
                'required' => false))->addModelTransformer($null2string))
            ->add($builder->create('description', 'text',
                array('label'  => 'Описание', 'required' => false))
                ->addModelTransformer($null2string))
            ->add($builder->create('keywords', 'text',
                array('label'  => 'Ключевые слова', 'required' => false))
                ->addModelTransformer($null2string))
            /*->add('template', 'choice', array('label'  => 'Шаблон',
                'choices' => $templates->enum()))
            ->add('type', 'choice', array('label'  => 'Тип раздела',
                'choices' => $this->loadContentTypes()))*/
            ->add('active', 'checkbox', array('label'  => 'Включить'))
            ->add('visible', 'checkbox', array('label'  => 'Показывать в меню'));
        if ($section->id > 0)
        {
            $builder->add('position', 'integer', array('label'  => 'Порядковый номер'));
        }
        $builder
            ->add($builder->create('options', 'textarea',
                array('label'  => 'Опции', 'required' => false))
                ->addModelTransformer(new OptionsTransformer()))
            ->add('created', 'datetime', array('label'  => 'Дата создания',
                'widget' => 'single_text', 'format' => \IntlDateFormatter::SHORT))
            ->add('updated', 'datetime', array('label'  => 'Дата изменения',
                'widget' => 'single_text', 'format' => \IntlDateFormatter::SHORT));

        return $builder->getForm();
    }
}

