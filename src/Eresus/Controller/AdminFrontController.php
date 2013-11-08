<?php
/**
 * Фронт-контроллер АИ
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

namespace Eresus\Controller;

use Eresus\Entity\Account;
use Eresus\Exceptions\NotFoundException;
use Eresus\Security\Exceptions\BadCredentialsException;
use Eresus\Security\SecurityManager;
use Eresus\Templating\TemplateManager;
use Eresus\UI\Menu\Menu;
use Eresus\UI\Menu\MenuItem;
use Eresus\UI\Page\AdminPage;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Eresus\UI\Page\Page;

/**
 * Фронт-контроллер АИ
 *
 * @since 3.01
 */
class AdminFrontController extends FrontController
{
    /**
     * Выполняет действия контроллера и возвращает ответ
     *
     * @param Request $request
     *
     * @return Response
     *
     * @since 3.01
     */
    public function process(Request $request)
    {
        $this->dispatchEvent('cms.admin.start');

        /** @var SecurityManager $security */
        $security = $this->get('security');
        $user = $security->getCurrentUser();
        if (is_null($user) || !$user->hasAccess(EDITOR))
        {
            return $this->authAction($request);
        }

        $response = $this->renderContent($request);

        if (!($response instanceof Response))
        {
            $page = $this->getPage();
            $page->set('content', $response);
            //$vars['errors'] = $this->getErrorMessages();
            $page->set('site', $this->getSite());
            //$vars['body'] = $this->renderBodySection();

            /** @var \Symfony\Component\DependencyInjection\ContainerInterface $container */
            //$container = $GLOBALS['_container'];
            /** @var \Eresus\Templating\TemplateManager $tm */
            //$tm = $container->get('templates');

            //$menu = new SectionMenu($tm);
            //$vars['sectionMenu'] = $menu->getHtml();
            //$vars['controlMenu'] = $this->renderControlMenu();
            $page->set('mainMenu', $this->createMainMenu());
            //$vars['user'] = Eresus_CMS::getLegacyKernel()->user;

            $response = new Response($page->getHtml(), 200/*, $this->headers*/);
        }

        return $response;
    }

    /**
     * Создаёт объект Page
     *
     * @return Page
     * @since 3.01
     */
    protected function createPage()
    {
        /** @var TemplateManager $tm */
        $tm = $this->get('templates');
        $page = new AdminPage($tm);
        $page->setTitle(_('Управление'));
        return $page;
    }

    /**
     * Отрисовка страницы аутентификации
     *
     * @param Request $request
     *
     * @return Response
     *
     * @since 3.01
     */
    private function authAction(Request $request)
    {
        $data = array('errors' => array(), 'user' => '', 'autologin' => '');

        $legacyKernel = \Eresus_CMS::getLegacyKernel();
        if ($request->getMethod() == 'POST')
        {
            $user = trim($request->request->get('user'));
            $password = trim($request->request->get('password'));
            $auto = $request->request->get('autologin');

            /** @var SecurityManager $security */
            $security = $this->container->get('security');
            try
            {
                $security->login($user, Account::hashPassword($password), $auto);
                return new RedirectResponse($legacyKernel->root . 'admin.php');
            }
            catch (BadCredentialsException $e)
            {
                $this->getPage()->addErrorMessage(_('Неправильный пароль или имя пользователя'));
            }
            $data['user'] = $user;
            $data['autologin'] = $auto;
        }

        $data['errors'] = $this->getPage()->getErrorMessages();
        $this->getPage()->clearErrorMessages();
        if (isset($legacyKernel->session['msg']['errors']) &&
            count($legacyKernel->session['msg']['errors']))
        {
            $data['errors'] = $legacyKernel->session['msg']['errors'];
            $legacyKernel->session['msg']['errors'] = array();
        }

        /** @var TemplateManager $templates */
        $templates = $this->container->get('templates');
        $tmpl = $templates->getAdminTemplate('Auth.html');
        $html = $tmpl->compile($data);
        $response = new Response($html);
        return $response;
    }

    /**
     * Отрисовывает область контента
     *
     * @param Request $request
     *
     * @return string|Response
     *
     * @throws NotFoundException
     * @throws \LogicException
     *
     * @since 3.01
     */
    private function renderContent(Request $request)
    {
        // TODO: Это временное решение до завершения выноса кода в контроллеры
        $routes = array(
            'users' => 'Eresus\Controller\Admin\AccountsController'
        );

        if (arg('mod'))
        {
            $module = arg('mod', '/[^\w-]/');
            if (array_key_exists($module, $routes))
            {
                $controller = new $routes[$module]($this->container);
            }
            /*elseif (substr($module, 0, 4) == 'ext-')
            {
                $name = substr($module, 4);
                $this->module = Eresus_CMS::getLegacyKernel()->plugins->load($name);
            }*/
            else
            {
                throw new NotFoundException();
            }

            if (!($controller instanceof Controller))
            {
                throw new \LogicException(
                    sprintf('Controller should be a descendant of Eresus\Controller\Controller'));
            }

            $response = $controller->process($request);
        }
        else
        {
            $response = '';
        }

        return $response;
    }

    /**
     * Создаёт главное меню
     *
     * @return Menu
     *
     * @since 3.01
     */
    private function createMainMenu()
    {
        /** @var \Eresus\Templating\TemplateManager $tm */
        $tm = $this->get('templates');
        /** @var \Eresus\Security\SecurityManager $security */
        $security = $this->get('security');

        $menu = new Menu($tm);
        $menu->add(new MenuItem(
            _('Разделы сайта'), 'admin.php?mod=pages'));
        $menu->add(new MenuItem(_('Файловый менеджер'), 'admin.php?mod=files'));
        if ($security->getCurrentUser()->hasAccess(ADMIN))
        {
            $menu->add(new MenuItem(_('Модули расширения'), 'admin.php?mod=plgmgr'));
            $menu->add(new MenuItem(_('Оформление'), 'admin.php?mod=themes'));
            $menu->add(new MenuItem(_('Пользователи'), 'admin.php?mod=users'));
            $menu->add(new MenuItem(_('Конфигурация'), 'admin.php?mod=settings'));
        }
        return $menu;
    }
}

