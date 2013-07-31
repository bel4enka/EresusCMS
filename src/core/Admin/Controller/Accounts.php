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

    private $itemsPerPage = 30;

    private $pagesDesc = false;

    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->accounts = new EresusAccounts();
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
     * @param $new
     * @param null $old
     * @return string
     */
    public function notifyMessage($new, $old=null)
    {
        $result = '';
        if (is_null($old))
        {
            $result .= admUsersName.": ".$new['name']."\n";
            $result .= admUsersLogin.": ".$new['login']."\n";
            $result .= admAccessLevel.": ".constant('ACCESSLEVEL'.$new['access'])."\n";
            $result .= admUsersMail.": ".$new['mail']."\n";
        }
        else
        {
            $result = "ID ".$new['id']." - <strong>".$old['name']."</strong>\n".admChanges.":\n";
            if ($new['name'] != $old['name'])
            {
                $result .= admUsersName.": ".$old['name']." &rarr; ".$new['name']."\n";
            }
            if ($new['login'] != $old['login'])
            {
                $result .= admUsersLogin.": ".$old['login']." &rarr; ".$new['login']."\n";
            }
            if ($new['active'] != $old['active'])
            {
                $result .= admUsersAccountState.": ".($old['active']?strYes:strNo)." &rarr; ".
                    ($new['active']?strYes:strNo)."\n";
            }
            if ($new['loginErrors'] != $old['loginErrors'])
            {
                $result .= admUsersLoginErrors.": ".$old['loginErrors']." &rarr; ".$new['loginErrors']."\n";
            }
            if ($new['access'] != $old['access'])
            {
                $result .= admAccessLevel.": ".constant('ACCESSLEVEL'.$old['access'])." &rarr; ".
                    constant('ACCESSLEVEL'.$new['access'])."\n";
            }
            if ($new['mail'] != $old['mail'])
            {
                $result .= admUsersMail.": ".$old['mail']." &rarr; ".$new['mail']."\n";
            }
        }
        return $result;
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
     *
     */
    public function toggle()
    {
        $item = $this->accounts->get(arg('toggle', 'int'));
        $item['active'] = !$item['active'];
        $this->accounts->update($item);
        HTTP::redirect(Eresus_Kernel::app()->getPage()->url());
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
     * Создание учётной записи
     */
    public function insert()
    {
        # Получение данных
        $item = array(
            'name' => arg('name', 'dbsafe'),
            'login' => arg('login', '/[^a-z0-9_]/'),
            'access' => arg('access', 'int'),
            'hash' => Eresus_CMS::getLegacyKernel()->password_hash(arg('pswd1')),
            'mail' => arg('mail', 'dbsafe'),
        );
        # Проверка входных данных
        $error = false;
        if (empty($item['name']))
        {
            Eresus_Kernel::app()->getPage()->addErrorMessage(admUsersNameInvalid);
            $error = true;
        }
        if (empty($item['login']))
        {
            Eresus_Kernel::app()->getPage()->addErrorMessage(admUsersLoginInvalid);
            $error = true;
        }
        if ($item['access'] <= ROOT)
        {
            Eresus_Kernel::app()->getPage()->addErrorMessage('Invalid access level!');
            $error = true;
        }
        if ($item['hash'] != Eresus_CMS::getLegacyKernel()->password_hash(arg('pswd2')))
        {
            Eresus_Kernel::app()->getPage()->addErrorMessage(admUsersConfirmInvalid);
            $error = true;
        }
        # Проверка данных на уникальность
        $check = $this->accounts->get("`login` = '{$item['login']}'");
        if ($check)
        {
            Eresus_Kernel::app()->getPage()->addErrorMessage(admUsersLoginExists);
            $error = true;
        }
        if ($error)
        {
            saveRequest();
            HTTP::redirect(Eresus_CMS::getLegacyKernel()->request['referer']);
        }
        if (!$this->accounts->add($item))
        {
            Eresus_Kernel::app()->getPage()->addErrorMessage('Error creating user account');
        }
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
     * @return mixed
     */
    public function create()
    {
        restoreRequest();
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
     * Возвращает разметку
     *
     * @param Eresus_CMS_Request $request
     *
     * @return string
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
                $this->toggle();
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
                    case 'create':
                        $result = $this->create();
                        break;
                    case 'insert':
                        $this->insert();
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
                        array('name' => 'access', 'caption' => admUsersAccessLevelShort,
                            'align' => 'center',
                            'width' => '70px', 'replace' => array (
                            '1' => '<span style="font-weight: bold; color: red;">ROOT</span>',
                            '2' => '<span style="font-weight: bold; color: red;">admin</span>',
                            '3' => '<span style="font-weight: bold; color: blue;">editor</span>',
                            '4' => 'user'
                        )),
                        array('name' => 'login', 'caption' => admUsersLogin, 'align' => 'left'),
                        array('name' => 'mail', 'caption' => admUsersMail, 'align' => 'center',
                            'macros'=>true,
                            'value'=>'<a href="mailto:$(mail)">$(mail)</a>'),
                        array('name' => 'lastVisit', 'caption' => admUsersLastVisitShort,
                            'align' => 'center',
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

