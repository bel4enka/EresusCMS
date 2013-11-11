<?php
/**
 * Управление разделами сайта
 *
 * @version ${product.version}
 * @copyright ${product.copyright}
 * @license ${license.uri} ${license.name}
 * @author Михаил Красильников <m.krasilnikov@yandex.ru>
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

namespace Eresus\Controller\Admin;

use Eresus\Sections\MenuItemProvider;
use Eresus\Sections\SectionManager;
use Eresus\Security\SecurityManager;
use Eresus\Templating\TemplateManager;
use Eresus\UI\Menu\Menu;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Управление разделами сайта
 *
 * @internal
 * @since 3.01
 */
class SectionsController extends AdminController
{
    /**
     * Обрабатывает полученный запрос и возвращает ответ
     *
     * @param Request $request
     *
     * @return string|Response
     */
    public function process(Request $request)
    {
        $args = $request->getMethod() == 'GET' ? $request->query : $request->request;

        switch ($args->get('action'))
        {
            case 'add':
                $response = $this->addAction($request);
                break;
            case 'edit':
                $response = $this->editAction($request);
                break;
            case 'toggle':
                $response = $this->toggleAction($request);
                break;
            case 'delete':
                $response = $this->deleteAction($request);
                break;
            default:
                $response = $this->indexAction($request);
        }
        return $response;
    }

    /**
     * Добавление учётной записи
     *
     * @param Request $request
     *
     * @return string|Response
     */
    private function addAction(Request $request)
    {
        if ($request->getMethod() == 'POST')
        {
            try
            {
                $account = new Account();
                $account->setName($request->request->get('name'));
                $account->setLogin($request->request->get('login'));
                $account->setAccess($request->request->getInt('access'));
                $account->setEnabled($request->request->getInt('active'));
                $account->setPassword($request->request->get('password'));
                $account->setMail($request->request->get('mail'));
                $this->getAccountManager()->add($account);
                return new RedirectResponse(arg('submitURL'));
            }
            catch (\Exception $e)
            {
                \Eresus_Kernel::app()->getPage()->addErrorMessage($e->getMessage());
            }
        }
        $form = array(
            'name' => 'AddAccountForm',
            'caption' => _('Создать пользователя'),
            'width' => '400px',
            'fields' => array(
                array('type' => 'hidden', 'name' => 'action', 'value' => 'add'),
                array('type' => 'edit', 'name' => 'name', 'label' => _('Имя'), 'maxlength' => 32,
                    'width'=>'100%', 'pattern'=>'/.+/',
                    'errormsg' => _('Псевдоним пользователя не может быть пустым.')),
                array('type' => 'edit', 'name' => 'login', 'label' => _('Логин'), 'maxlength' => 16,
                    'width' => '100%', 'pattern' => '/^[a-z0-9_]+$/i',
                    'errormsg' =>
                    _('Логин не может быть пустым и должен состоять только из букв a-z, цифр и '
                        . 'символа подчеркивания.')),
                array('type' => 'select', 'name' => 'access', 'label' => _('Уровень доступа'),
                    'width' => '100%', 'values' => array('2','3','4'),
                    'items' => array(_('Администратор'), _('Редактор'), _('Пользователь')),
                    'default' => USER),
                array('type' => 'checkbox', 'name' => 'active',
                    'label' => _('Учетная запись активна'), 'default' => true),
                array('type' => 'divider'),
                array('type' => 'edit', 'name' => 'password', 'label' => _('Пароль'),
                    'maxlength' => 255, 'width'=>'100%'),
                array('type' => 'divider'),
                array('type' => 'edit', 'name' => 'mail', 'label' => _('e-mail'),
                    'maxlength' => 255, 'width' => '100%'),
            ),
            'buttons' => array('ok', 'cancel')
        );
        /** @var \TAdminUI $page */
        $page = \Eresus_Kernel::app()->getPage();
        $result = $page->renderForm($form, $request->request->all());
        return $result;
    }

    /**
     * Изменение учётной записи
     *
     * @param Request $request
     *
     * @return string|Response
     */
    private function editAction(Request $request)
    {
        $account = $this->getAccountFromRequest($request);
        if ($request->getMethod() == 'POST')
        {
            $args = $request->request;
            $account->setLogin($args->get('login'));
            $account->setEnabled($args->get('active'));
            $account->setName($args->get('name'));
            $account->setAccess($args->get('access'));
            $account->setMail($args->get('mail'));
            if ($args->get('password'))
            {
                $account->setPassword($args->get('password'));
            }
            // TODO Проверка почты, переключения активности (отключение самого себя)
            return new RedirectResponse(arg('submitURL'));
        }
        $form = array(
            'name' => 'UserForm',
            'caption' => sprintf(_('Изменить учетную запись № %d'), $account->getId()),
            'width' => '400px',
            'fields' => array (
                array('type' => 'hidden', 'name' => 'action', 'value' => 'edit'),
                array('type' => 'hidden', 'name' => 'id', 'value' => $account->getId()),
                array('type' => 'edit', 'name' => 'name', 'label' => _('Имя'), 'maxlength' => 255,
                    'width' => '100%', 'value' => $account->getName(), 'pattern' => '/.+/',
                    'errormsg' => _('Псевдоним пользователя не может быть пустым.')),
                array('type' => 'edit', 'name' => 'login', 'label' => _('Логин'),
                    'maxlength' => 255, 'width'=>'100%', 'value' => $account->getLogin(),
                    'pattern' => '/^[a-z\d_]+$/',
                    'errormsg' => _('Логин не может быть пустым и должен состоять только из букв '
                        . 'a-z, цифр и символа подчеркивания.')),
                array('type' => 'select', 'name' => 'access', 'label' => _('Уровень доступа'),
                    'values' => array('2', '3', '4'), 'items' => array(_('Администратор'),
                        _('Редактор'), _('Пользователь')), 'value' => $account->getAccess(),
                        'disabled' => $account->getAccess() == ROOT),
                array('type' => 'checkbox', 'name' => 'active',
                    'label' => _('Учетная запись активна'), 'value' => $account->isEnabled()),
                array('type' => 'edit', 'name' => 'mail', 'label' => _('E-mail'),
                    'maxlength' => 255, 'width' => '100%', 'value' => $account->getMail(),
                    'pattern' => '/^[\w]+[\w\d_\.\-]+@[\w\d\-]{2,}\.[a-z]{2,5}$/i',
                    'errormsg' => _('Неверно указан почтовый адрес.')),
                array('type' => 'edit', 'name' => 'password', 'label' => _('Новый пароль'),
                    'width' => '100%',
                    'comment' => _('Заполняйте это поле только если хотите сменить пароль!')),
            ),
            'buttons' => array('ok', 'apply', 'cancel'),
        );

        /** @var \TAdminUI $page */
        $page = \Eresus_Kernel::app()->getPage();
        $html = $page->renderForm($form);
        return $html;
    }

    /**
     * Список разделов
     *
     * @param Request $request
     *
     * @return string
     */
    private function indexAction(Request $request)
    {
        /* * @var Registry $doctrine * /
        $doctrine = $this->get('doctrine');*/
        /** @var TemplateManager $tm */
        $tm = $this->get('templates');
        /** @var SectionManager $sm */
        $sm = $this->get('sections');
        /*$urlBuilder = new QueryUrlBuilder('admin.php?mod=' . $request->query->get('mod'));

        $provider
            = new EntityProvider($doctrine->getManager()->getRepository('Eresus\Entity\Account'));
        */
        $list = new Menu($tm);
        $list->setItemProvider(new MenuItemProvider($sm));
        /*
        $list = new ListTable($tm, $provider);
        $list
            ->setPageSize(2)
            ->setCurrentPage($request->query->getInt('page', 1))
            ->setControlUrlBuilder($urlBuilder);

        $checkForRoot =
            function (Account $a)
            {
                return $a->getLogin() != 'root' ? $a : false;
            };
        $edit = new EditControl($tm);
        $toggle = new ToggleControl($tm);
        $toggle->setFilter($checkForRoot);
        $delete = new DeleteControl($tm);
        $delete->setFilter($checkForRoot);
        $list->addColumn(ControlsColumn::create($edit, $toggle, $delete));

        $list->addColumn(Column::create('Полное имя')->setGetter('getName'));
        $list->addColumn(Column::create('Имя входа')->setGetter('getLogin'));
        $list->addColumn(Column::create('E-mail')->setGetter('getMail')
            ->setCallback(
                function ($email)
                {
                    return sprintf('<a href="mailto:%s" class="link link_type_email">%1$s</a>',
                        $email);
                }
            ));
        $list->addColumn(Column::create('Доступ')->setGetter('getAccess')
            ->setAlign(Column::ALIGN_CENTER)
            ->setValueMap(array(
                1 => _('Главный администратор'),
                2 => _('Администратор'),
                3 => _('Редактор'),
                4 => _('Обычный пользователь')
            )));

        $addButton = new Control($tm);
        $addButton
            ->setStyle(Control::STYLE_BUTTON)
            ->setLabel(_('Добавить пользователя'))
            ->setActionUrl($urlBuilder->getActionUrl('add'));
        */
        return $this->renderView('SectionsManager/Index.html', array(
            //'addButton' => $addButton,
            'list' => $list
        ));
    }

    /**
     * Переключает активность учётной записи
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    private function toggleAction(Request $request)
    {
        $account = $this->getAccountFromRequest($request);
        $account->toggle();
        return new RedirectResponse($request->headers->get('REFERER'));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    private function deleteAction(Request $request)
    {
        $account = $this->getAccountFromRequest($request);
        $this->getAccountManager()->remove($account);
        return new RedirectResponse(\Eresus_Kernel::app()->getPage()->url());
    }

    /**
     * Возвращает менеджер учётных записей
     *
     * @return \Eresus\Security\AccountManager
     *
     * @since 3.01
     */
    private function getAccountManager()
    {
        return $this->container->get('accounts');
    }

    /**
     * Возвращает учётную запись, указанную в запросе
     *
     * @param Request $request
     *
     * @return Account
     *
     * @throws NotFoundException
     *
     * @since 3.01
     */
    private function getAccountFromRequest(Request $request)
    {
        $args = $request->getMethod() == 'POST' ? $request->request : $request->query;
        $account = $this->getAccountManager()->get($args->getInt('id'));
        if (null === $account)
        {
            throw new NotFoundException;
        }
        return $account;
    }
}

