<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2004, Михаил Красильников <m.krasilnikov@yandex.ru>
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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Eresus\CmsBundle\Entity\Account;

/**
 * Управление пользователями
 */
class Eresus_Admin_Controllers_Users extends Eresus_Admin_Controllers_Abstract
{
    public $access = ADMIN;

    public function check_for_root($item)
    {
        return ($item['access'] != ROOT);
    }

    public function check_for_edit($item)
    {
        return (($item['access'] != ROOT)||
            (Eresus_CMS::getLegacyKernel()->user['id'] == $item['id'])) && UserRights(ADMIN);
    }

    /**
     * @return Response
     */
    private function toggle()
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var Account $account */
        $account = $em->getRepository('EresusCmsBundle:Account')->find(arg('toggle', 'int'));
        $account->active = !$account->active;
        $em->flush();
        return new RedirectResponse(Eresus_Kernel::app()->getPage()->url());
    }

    /**
     * @return Response
     */
    private function update()
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var Account $account */
        $account = $em->getRepository('EresusCmsBundle:Account')->find(arg('update', 'int'));
        $account->login = arg('login', '/[^a-z0-9_]/');
        $account->password = arg('pswd1');
        $account->active = arg('active');
        $account->name = arg('name');
        $account->loginErrors = arg('loginErrors', 'int');
        $account->access = arg('access', 'int');
        $account->mail = arg('mail');

        return new RedirectResponse(arg('submitURL'));
    }

    /**
     * Создаёт учётную запись
     *
     * @return Response
     */
    private function insert()
    {
        $account = new Account();
        $account->name = arg('name');
        $account->login = arg('login', '/[^a-z0-9_]/');
        $account->access = arg('access', 'int');
        $account->password = arg('pswd1');
        $account->mail = arg('mail');

        /* Проверка входных данных */
        $error = false;
        if (empty($account->name))
        {
            ErrorMessage(admUsersNameInvalid);
            $error = true;
        }
        if (empty($account->login))
        {
            ErrorMessage(admUsersLoginInvalid);
            $error = true;
        }
        if ($$account->access <= ROOT)
        {
            ErrorMessage('Invalid access level!');
            $error = true;
        }
        if (arg('pswd1') != arg('pswd2'))
        {
            ErrorMessage(admUsersConfirmInvalid);
            $error = true;
        }

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $tmp = $em->getRepository('EresusCmsBundle:Account')->findBy(array('login' => $account->login));
        if (count($tmp) > 0)
        {
            ErrorMessage(admUsersLoginExists);
            $error = true;
        }
        if ($error)
        {
            return new RedirectResponse(Eresus_CMS::getLegacyKernel()->request['referer']);
        }
        $em->persist($account);
        $em->flush();
        return new RedirectResponse(arg('submitURL'));
    }

    /**
     * Удаляет пользователя
     *
     * @return Response
     */
    private function delete()
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $account = $em->getRepository('EresusCmsBundle:Account')->find(arg('delete', 'int'));
        $em->remove($account);
        $em->flush();
        return new RedirectResponse(Eresus_Kernel::app()->getPage()->url());
    }

    /**
     * Задаёт пароль
     *
     * @return Response
     */
    private function password()
    {
        if (arg('pswd1') == arg('pswd2'))
        {
            /** @var \Doctrine\ORM\EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            /** @var Account $account */
            $account = $em->getRepository('EresusCmsBundle:Account')->find(arg('password', 'int'));
            $account->password = arg('pswd1');
            $em->flush();
        }
        return new RedirectResponse(arg('submitURL'));
    }

    private function edit()
    {
        $item = Eresus_CMS::getLegacyKernel()->db->selectItem('users', "`id`='".arg('id')."'");
        $form = array(
            'name' => 'UserForm',
            'caption' => admUsersChangeUser.' №'.$item['id'],
            'width' => '400px',
            'fields' => array (
                array('type'=>'hidden','name'=>'update', 'value'=>$item['id']),
                array('type'=>'edit','name'=>'name','label'=>admUsersName,'maxlength'=>32,'width'=>'100%',
                    'value'=>$item['name'], 'pattern'=>'/.+/', 'errormsg'=>admUsersNameInvalid),
                array('type'=>'edit','name'=>'login','label'=>admUsersLogin,'maxlength'=>16,'width'=>'100%',
                    'value'=>$item['login'], 'pattern'=>'/^[a-z\d_]+$/', 'errormsg'=>admUsersLoginInvalid,
                    'access'=>ADMIN),
                array('type'=>'select','name'=>'access','label'=>admAccessLevel,
                    'values'=>array('2','3','4'),'items'=>array(ACCESSLEVEL2, ACCESSLEVEL3, ACCESSLEVEL4),
                    'value'=>$item['access'], 'disabled'=>$item['access'] == ROOT, 'access'=>ADMIN),
                array('type'=>'checkbox','name'=>'active','label'=>admUsersAccountState,
                    'value'=>$item['active'], 'access'=>ADMIN),
                array('type'=>'edit','name'=>'loginErrors','label'=>admUsersLoginErrors,'maxlength'=>2,
                    'width'=>'30px','value'=>$item['loginErrors'], 'access'=>ADMIN),
                array('type'=>'edit','name'=>'mail','label'=>admUsersMail,'maxlength'=>32,'width'=>'100%',
                    'value'=>$item['mail'], 'pattern'=>'/^[\w]+[\w\d_\.\-]+@[\w\d\-]{2,}\.[a-z]{2,5}$/i',
                    'errormsg'=>admUsersMailInvalid, 'access'=>ADMIN),
            ),
            'buttons' => array(UserRights($this->access)?'ok':'', 'apply', 'cancel'),
        );

        $pswd = array(
            'name' => 'PasswordForm',
            'caption' => admUsersChangePassword,
            'width' => '400px',
            'fields' => array (
                array('type'=>'hidden','name'=>'password', 'value'=>$item['id']),
                array('type'=>'password','name'=>'pswd1','label'=>admUsersPassword,'maxlength'=>32,
                    'width'=>'100%'),
                array('type'=>'password','name'=>'pswd2','label'=>admUsersConfirmation,'maxlength'=>32,
                    'width'=>'100%', 'equal'=>'pswd1', 'errormsg'=>admUsersConfirmInvalid),
            ),
            'buttons' => array(UserRights($this->access)?'ok':'apply', 'cancel'),
        );

        $result = Eresus_Kernel::app()->getPage()->renderForm($form)."<br />\n".
            Eresus_Kernel::app()->getPage()->renderForm($pswd);
        return $result;
    }

    function create()
    {
        $form = array(
            'name'=>'UserForm',
            'caption' => admUsersCreate,
            'width' => '400px',
            'fields' => array (
                array('type'=>'hidden','name'=>'action','value'=>'insert'),
                array('type'=>'edit','name'=>'name','label'=>admUsersName,'maxlength'=>32,'width'=>'100%',
                    'pattern'=>'/.+/', 'errormsg'=>admUsersNameInvalid),
                array('type'=>'edit','name'=>'login','label'=>admUsersLogin,'maxlength'=>16,'width'=>'100%',
                    'pattern'=>'/^[a-z0-9_]+$/i', 'errormsg'=>admUsersLoginInvalid),
                array('type'=>'select','name'=>'access','label'=>admAccessLevel, 'width'=>'100%',
                    'values'=>array('2','3','4'),'items'=>array(ACCESSLEVEL2,ACCESSLEVEL3,ACCESSLEVEL4),
                    'default' => USER),
                array('type'=>'checkbox','name'=>'active','label'=>admUsersAccountState, 'default' => true),
                array('type'=>'divider'),
                array('type'=>'password','name'=>'pswd1','label'=>admUsersPassword,'maxlength'=>32,
                    'width'=>'100%'),
                array('type'=>'password','name'=>'pswd2','label'=>admUsersConfirmation,'maxlength'=>32,
                    'width'=>'100%', 'equal'=>'pswd1', 'errormsg'=>admUsersConfirmInvalid),
                array('type'=>'divider'),
                array('type'=>'edit','name'=>'mail','label'=>admUsersMail,'maxlength'=>32,'width'=>'100%'),
            ),
            'buttons'=>array('ok', 'cancel')
        );
        $result = Eresus_Kernel::app()->getPage()->
            renderForm($form, Eresus_CMS::getLegacyKernel()->request['arg']);
        return $result;
    }


    /**
     * @param Request $request
     *
     * @return Response|string
     */
    public function adminRender(Request $request)
    {
        $result = '';
        $request = Eresus_CMS::getLegacyKernel()->request;
        $granted = false;
        if (UserRights($this->access))
        {
            $granted = true;
        }
        else
        {
            if (arg('id') == Eresus_CMS::getLegacyKernel()->user['id'])
            {
                if (!arg('password') || (arg('password') == Eresus_CMS::getLegacyKernel()->user['id']))
                {
                    $granted = true;
                }
                if (!arg('update') || (arg('update') == Eresus_CMS::getLegacyKernel()->user['id']))
                {
                    $granted = true;
                }
            }
        }
        if ($granted)
        {
            if (arg('update'))
            {
                return $this->update();
            }
            elseif (isset($request['arg']['password']) && (!isset($request['arg']['action']) ||
                ($request['arg']['action'] != 'login')))
            {
                return $this->password();
            }
            elseif (isset($request['arg']['toggle']))
            {
                return $this->toggle();
            }
            elseif (isset($request['arg']['delete']))
            {
                return $this->delete();
            }
            elseif (isset($request['arg']['id']))
            {
                $result = $this->edit();
            }
            elseif (isset($request['arg']['action']))
            {
                switch (arg('action'))
                {
                    case 'create':
                        $result = $this->create();
                        break;
                    case 'insert':
                        return $this->insert();
                        break;
                }
            }
            else
            {
                $table = array (
                    'name' => 'users',
                    'key'=>'id',
                    'itemsPerPage' => 20,
                    'columns' => array(
                        array('name' => 'id', 'caption' => 'ID', 'align' => 'right', 'width' => '40px'),
                        array('name' => 'name', 'caption' => admUsersName, 'align' => 'left'),
                        array('name' => 'access', 'caption' => admUsersAccessLevelShort, 'align' => 'center',
                            'width' => '70px', 'replace' => array (
                            '1' => '<span style="font-weight: bold; color: red;">ROOT</span>',
                            '2' => '<span style="font-weight: bold; color: red;">admin</span>',
                            '3' => '<span style="font-weight: bold; color: blue;">editor</span>',
                            '4' => 'user'
                        )),
                        array('name' => 'login', 'caption' => admUsersLogin, 'align' => 'left'),
                        array('name' => 'mail', 'caption' => admUsersMail, 'align' => 'center', 'macros'=>true,
                            'value'=>'<a href="mailto:$(mail)">$(mail)</a>'),
                        array('name' => 'lastVisit', 'caption' => admUsersLastVisitShort, 'align' => 'center',
                            'width' => '140px'),
                        array('name' => 'loginErrors', 'caption' => admUsersLoginErrorsShort,
                            'align' => 'center', 'replace' => array (
                            '0' => '',
                        )),
                    ),
                    'controls' => array (
                        'delete' => 'check_for_root',
                        'edit' => 'check_for_edit',
                        'toggle' => 'check_for_root',
                    ),
                    'tabs' => array(
                        'width'=>'180px',
                        'items'=>array(
                            array('caption'=>admUsersCreate, 'name'=>'action', 'value'=>'create')
                        )
                    )
                );
                $result = Eresus_Kernel::app()->getPage()->renderTable($table);
            }
            return $result;
        }
        else
        {
            return '';
        }
    }
}
