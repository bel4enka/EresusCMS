<?php
/**
 * Управление пользователями
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
 *
 * @package Eresus
 */

/**
 * Управление пользователями
 *
 * @package Eresus
 * @internal
 */
class Eresus_Admin_Controller_Accounts implements Eresus_Admin_Controller_Interface
{
    private $accounts;

    public $access = ADMIN;

    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->accounts = new EresusAccounts();
    }

    /**
     * Возвращает разметку
     *
     * @param Eresus_CMS_Request $request
     *
     * @return string|Eresus_HTTP_Response
     */
    public function getHtml(Eresus_CMS_Request $request)
    {
        $result = '';
        $granted = false;

        $args = $request->getMethod() == 'GET' ? $request->query : $request->request;

        if (UserRights($this->access))
        {
            $granted = true;
        }
        else
        {
            if ($args->get('id') == Eresus_CMS::getLegacyKernel()->user['id'])
            {
                if (is_null($args->get('password'))
                    || ($args->get('password') == Eresus_CMS::getLegacyKernel()->user['id']))
                {
                    $granted = true;
                }
                if (is_null($args->get('update'))
                    || ($args->get('update') == Eresus_CMS::getLegacyKernel()->user['id']))
                {
                    $granted = true;
                }
            }
        }
        if ($granted)
        {
            if (null !== $args->get('update'))
            {
                $this->update(null);
            }
            elseif ($args->get('password')
                && (!$args->get('action') || $args->get('action') != 'login'))
            {
                $this->password();
            }
            elseif ($args->get('toggle'))
            {
                $result = $this->toggleAction($request);
            }
            elseif ($args->get('delete'))
            {
                $this->delete(null);
            }
            elseif ($args->get('id'))
            {
                $result = $this->edit();
            }
            elseif ($args->get('action'))
            {
                switch ($args->get('action'))
                {
                    case 'add':
                        $result = $this->addAction($request);
                        break;
                }
            }
            else
            {
                $result = $this->indexAction();
            }
            return $result;
        }
        else
        {
            return '';
        }
    }

    /**
     * @param $mail
     * @return bool
     */
    public function checkMail($mail)
    {
        $host = substr($mail, strpos($mail, '@')+1);
        $ip = gethostbyname($host);
        if ($ip == $host)
        {
            Eresus_Kernel::app()->getPage()->addErrorMessage(sprintf(errNonexistedDomain, $host));
            return false;
        }
        return true;
    }

    /**
     * @param $item
     * @return bool
     */
    public function check_for_root($item)
    {
        return ($item['access'] != ROOT);
    }

    /**
     * @param $item
     * @return bool
     */
    public function check_for_edit($item)
    {
        return (($item['access'] != ROOT)||
            (Eresus_CMS::getLegacyKernel()->user['id'] == $item['id'])) && UserRights(ADMIN);
    }

    /**
     * Переключает активность учётной записи
     *
     * @param Eresus_CMS_Request $request
     *
     * @throws Eresus_CMS_Exception_NotFound
     *
     * @return string|Eresus_HTTP_Response
     */
    private function toggleAction(Eresus_CMS_Request $request)
    {
        $table = Eresus_ORM::getTable(Eresus_Kernel::app(), 'Account');
        /** @var Eresus_Entity_Account $account */
        $account = $table->find($request->query->getInt('toggle'));
        if (null === $account)
        {
            throw new Eresus_CMS_Exception_NotFound;
        }
        $account->active = !$account->active;
        $table->update($account);
        return new Eresus_HTTP_Redirect(Eresus_Kernel::app()->getPage()->url());
    }

    /**
     * @param mixed $dummy  Используется для совместимости с родительским методом
     *
     * @return mixed  void
     *
     * @see EresusAccounts::update()
     */
    public function update($dummy)
    {
        $item = $this->accounts->get(arg('update', 'int'));
        foreach ($item as $key => $value)
        {
            if (isset(Eresus_CMS::getLegacyKernel()->request['arg'][$key]))
            {
                $item[$key] = arg($key, 'dbsafe');
            }
        }
        $item['active'] = arg('active') || (Eresus_CMS::getLegacyKernel()->user['id'] == $item['id']);
        if ($this->checkMail($item['mail']))
        {
            $this->accounts->update($item);
        };
        HTTP::redirect(arg('submitURL'));
    }

    /**
     * @param mixed $dummy  Используется для совместимости с родительским методом
     *
     * @return mixed void
     *
     * @see EresusAccounts::delete()
     */
    public function delete($dummy)
    {
        $this->accounts->get(arg('delete', 'int'));
        $this->accounts->delete(arg('delete', 'int'));
        HTTP::redirect(Eresus_Kernel::app()->getPage()->url());
    }

    /**
     *
     */
    public function password()
    {
        $item = $this->accounts->get(arg('password', 'int'));
        if (arg('pswd1') == arg('pswd2'))
        {
            $item['hash'] = Eresus_CMS::getLegacyKernel()->password_hash(arg('pswd1'));
            $this->accounts->update($item);
        }
        HTTP::redirect(arg('submitURL'));
    }

    /**
     * @return string
     */
    public function edit()
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

    /**
     * Добавление учётной записи
     *
     * @param Eresus_CMS_Request $request
     *
     * @return string|Eresus_HTTP_Response
     */
    private function addAction(Eresus_CMS_Request $request)
    {
        if ($request->getMethod() == 'POST')
        {
            try
            {
                $account = new Eresus_Entity_Account(Eresus_Kernel::app());
                $account->name = $request->request->get('name');
                $account->login = $request->request->get('login');
                $account->access = $request->request->getInt('access');
                $account->active = $request->request->getInt('active');
                $account->password = $request->request->get('pswd1');
                $account->mail = $request->request->get('mail');
                $account->getTable()->persist($account);
                return new Eresus_HTTP_Redirect(arg('submitURL'));
            }
            catch (Exception $e)
            {
                Eresus_Kernel::app()->getPage()->addErrorMessage($e->getMessage());
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
                    _('Логин не может быть пустым и должен состоять только из букв a-z, цифр и символа подчеркивания.')),
                array('type' => 'select', 'name' => 'access', 'label' => _('Уровень доступа'),
                    'width' => '100%', 'values' => array('2','3','4'),
                    'items' => array(ACCESSLEVEL2, ACCESSLEVEL3, ACCESSLEVEL4), 'default' => USER),
                array('type' => 'checkbox', 'name' => 'active',
                    'label' => _('Учетная запись активна'), 'default' => true),
                array('type' => 'divider'),
                array('type' => 'password', 'name' => 'pswd1', 'label' => _('Пароль'),
                    'maxlength' => 32, 'width'=>'100%'),
                array('type' => 'password', 'name' => 'pswd2', 'label' => _('Подтверждение'),
                    'maxlength' => 32, 'width' => '100%', 'equal' => 'pswd1',
                    'errormsg' => _('Пароль и подтверждение не совпадают.')),
                array('type' => 'divider'),
                array('type' => 'edit', 'name' => 'mail', 'label' => _('e-mail'),
                    'maxlength' => 32, 'width' => '100%'),
            ),
            'buttons' => array('ok', 'cancel')
        );
        /** @var TAdminUI $page */
        $page = Eresus_Kernel::app()->getPage();
        $result = $page->renderForm($form, $request->request->all());
        return $result;
    }

    /**
     * Список пользователей
     *
     * @return string
     */
    private function indexAction()
    {
        $table = array(
            'name' => 'users',
            'key' => 'id',
            'itemsPerPage' => 20,
            'columns' => array(
                array('name' => 'id', 'caption' => 'ID', 'align' => 'right', 'width' => '40px'),
                array('name' => 'name', 'caption' => admUsersName, 'align' => 'left'),
                array('name' => 'access', 'caption' => admUsersAccessLevelShort,
                    'align' => 'center',
                    'width' => '70px', 'replace' => array(
                    '1' => '<span style="font-weight: bold; color: red;">ROOT</span>',
                    '2' => '<span style="font-weight: bold; color: red;">admin</span>',
                    '3' => '<span style="font-weight: bold; color: blue;">editor</span>',
                    '4' => 'user'
                )),
                array('name' => 'login', 'caption' => admUsersLogin, 'align' => 'left'),
                array('name' => 'mail', 'caption' => admUsersMail, 'align' => 'center',
                    'macros' => true,
                    'value' => '<a href="mailto:$(mail)">$(mail)</a>'),
                array('name' => 'lastVisit', 'caption' => admUsersLastVisitShort,
                    'align' => 'center',
                    'width' => '140px'),
                array('name' => 'loginErrors', 'caption' => admUsersLoginErrorsShort,
                    'align' => 'center', 'replace' => array(
                    '0' => '',
                )),
            ),
            'controls' => array(
                'delete' => 'check_for_root',
                'edit' => 'check_for_edit',
                'toggle' => 'check_for_root',
            ),
            'tabs' => array(
                'width' => '180px',
                'items' => array(
                    array('caption' => admUsersCreate, 'name' => 'action', 'value' => 'add')
                )
            )
        );
        /** @var TAdminUI $page */
        $page = Eresus_Kernel::app()->getPage();
        $html = $page->renderTable($table);
        return $html;
    }
}

