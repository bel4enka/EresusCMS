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
use Eresus\Security\Exceptions\BadCredentialsException;
use Eresus\Security\SecurityManager;
use Eresus\Templating\TemplateManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @return Response
     * @since 3.01
     */
    public function dispatch()
    {
        /** @var \Symfony\Component\EventDispatcher\EventDispatcher $dispatcher */
        $dispatcher = $this->container->get('events');
        $dispatcher->dispatch('cms.admin.start');

        /** @var SecurityManager $security */
        $security = $this->get('security');
        $user = $security->getCurrentUser();
        if (is_null($user) || !$user->hasAccess(EDITOR))
        {
            $response = $this->authAction();
        }
        else
        {
            /** @var \TAdminUI $page */
            $page = $this->getPage();
            $response = $page->render($this->getRequest());
        }
        return $response;
    }

    /**
     * Создаёт объект Eresus_CMS_Page
     *
     * @return \Eresus_CMS_Page
     * @since 3.01
     */
    protected function createPage()
    {
        $page = new \TAdminUI();
        $page->setContainer($this->container);
        return $page;
    }

    /**
     * Отрисовка страницы аутентификации
     *
     * @return Response
     *
     * @since 3.01
     */
    private function authAction()
    {
        $data = array('errors' => array(), 'user' => '', 'autologin' => '');

        $legacyKernel = \Eresus_CMS::getLegacyKernel();
        $req = $this->getRequest();
        if ($req->getMethod() == 'POST')
        {
            $user = trim($req->request->get('user'));
            $password = trim($req->request->get('password'));
            $auto = $req->request->get('autologin');

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
}

