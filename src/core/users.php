<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2004, ProCreat Systems, http://procreat.ru/
 * @copyright 2007, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mk@procreat.ru>
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
 *
 * $Id$
 */

useLib('accounts');

/**
 * Управление пользователями
 *
 * @package Eresus
 */
class TUsers extends Accounts
{
	private $accounts;

	/**
	 * Уровень доступа к модулю
	 * @var int
	 */
	private $access = ADMIN;

	private $itemsPerPage = 30;

	private $pagesDesc = false;

 /**
	* Конструктор
	*
	* @return TUsers
	*/
	function __construct()
	{
		$this->accounts = new Accounts();
	}
	//-----------------------------------------------------------------------------
	function checkMail($mail)
	{
		$host = substr($mail, strpos($mail, '@')+1);
		$ip = gethostbyname($host);
		if ($ip == $host) {
			ErrorMessage(sprintf(i18n('Несуществующий домен: "%s"', __CLASS__), $host));
			return false;
		}
		return true;
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
	function check_for_root($item)
	{
		return ($item['access'] != ROOT);
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
	function check_for_edit($item)
	{
		global $Eresus;

		return (($item['access'] != ROOT)||($Eresus->user['id'] == $item['id'])) && UserRights(ADMIN);
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
	function toggle()
	{
		global $Eresus, $page;

		$item = $this->accounts->get(arg('toggle', 'int'));
		$item['active'] = !$item['active'];
		$this->accounts->update($item);
		HTTP::redirect($page->url());
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#

	/**
	 * @param void $dummy  Используется для совместимости с родительтским методом
	 * @see main/core/lib/EresusAccounts#update($item)
	 */
	function update($dummy)
	{
		global $Eresus, $page;

		$item = $this->accounts->get(arg('update', 'int'));
		$old = $item;
		foreach ($item as $key => $value) if (isset($Eresus->request['arg'][$key])) $item[$key] = arg($key, 'dbsafe');
		$item['active'] = $Eresus->request['arg']['active'] || ($Eresus->user['id'] == $item['id']);
		if ($this->checkMail($item['mail'])) {
			$this->accounts->update($item);
		};
		HTTP::redirect(arg('submitURL'));
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
 /**
	* Создание учётной записи
	*/
	function insert()
	{
		global $Eresus, $page;

		# Получение данных
		$item = array(
			'name' => arg('name', 'dbsafe'),
			'login' => arg('login', '/[^a-z0-9_]/'),
			'access' => arg('access', 'int'),
			'hash' => $Eresus->password_hash(arg('pswd1')),
			'mail' => arg('mail', 'dbsafe'),
		);
		# Проверка входных данных
		$error = false;
		if (empty($item['name']))
		{
			ErrorMessage(i18n('Псевдоним пользователя не может быть пустым.', __CLASS__));
			$error = true;
		}
		if (empty($item['login']))
		{
			ErrorMessage(i18n('Логин не может быть пустым и должен состоять только из букв a-z, цифр и ' .
				'символа подчеркивания.', __CLASS__));
			$error = true;
		}
		if ($item['access'] <= ROOT) { ErrorMessage('Invalid access level!'); $error = true;}
		if ($item['hash'] != $Eresus->password_hash(arg('pswd2')))
		{
			ErrorMessage(i18n('Пароль и подтверждение не совпадают.', __CLASS__));
			$error = true;
		}
		# Проверка данных на уникальность
		$check = $this->accounts->get("`login` = '{$item['login']}'");
		if ($check)
		{
			ErrorMessage(i18n('Пользователь с таким логином уже существует.', __CLASS__));
			$error = true;
		}
		if ($error)
		{
			saveRequest();
			HTTP::redirect($Eresus->request['referer']);
		}
		if (!$this->accounts->add($item))
		{
			ErrorMessage('Error creating user account');
		}
		HTTP::redirect(arg('submitURL'));
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#

	/**
	 * @param void $dummy  Используется для совместимости с родительтским методом
	 * @see main/core/lib/EresusAccounts#delete($id)
	 */
	function delete($dummy)
	{
		global $Eresus, $page;

		$item = $this->accounts->get(arg('delete', 'int'));
		$this->accounts->delete(arg('delete', 'int'));
		HTTP::redirect($page->url());
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
	function password()
	{
		global $Eresus, $page;

		$item = $this->accounts->get(arg('password', 'int'));
		if (arg('pswd1') == arg('pswd2')) {
			$item['hash'] = $Eresus->password_hash(arg('pswd1'));
			$this->accounts->update($item);
		}
		HTTP::redirect(arg('submitURL'));
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
	function edit()
	{
		global $Eresus, $page;

		$item = $Eresus->db->selectItem('users', "`id`='".arg('id')."'");
		$form = array(
			'name' => 'UserForm',
			'caption' => i18n('Изменить учетную запись', __CLASS__) . ' №' . $item['id'],
			'width' => '400px',
			'fields' => array (
				array('type'=>'hidden','name'=>'update', 'value'=>$item['id']),
				array('type' => 'edit', 'name' => 'name', 'label' => i18n('Имя', __CLASS__),
					'maxlength' => 32, 'width' => '100%', 'value' => $item['name'], 'pattern'=>'/.+/',
					'errormsg' => i18n('Псевдоним пользователя не может быть пустым.', __CLASS__)),
				array('type' => 'edit', 'name' => 'login', 'label' => i18n('Логин', __CLASS__),
					'maxlength' => 16, 'width' => '100%', 'value' => $item['login'],
					'pattern' => '/^[a-z\d_]+$/',
					'errormsg' => 'Логин не может быть пустым и должен состоять только из букв a-z, цифр и ' .
						'символа подчеркивания.', 'access'=>ADMIN),
				array('type' => 'select', 'name' => 'access', 'label' => i18n('Уровень доступа', __CLASS__),
					'values' => array('2','3','4'),
					'items' => array(
						i18n('Администратор'),
						i18n('Редактор'),
						i18n('Пользователь')
					), 'value'=>$item['access'], 'disabled'=>$item['access'] == ROOT, 'access'=>ADMIN),
				array('type' => 'checkbox', 'name' => 'active',
					'label' => i18n('Учетная запись активна', __CLASS__), 'value' => $item['active'],
					'access' => ADMIN),
				array('type' => 'edit', 'name' => 'loginErrors', 'label' => i18n('Ошибок входа', __CLASS__),
					'maxlength' => 2, 'width' => '30px', 'value' => $item['loginErrors'], 'access' => ADMIN),
				array('type' => 'edit', 'name' => 'mail', 'label' => i18n('e-mail', __CLASS__),
					'maxlength' => 32, 'width' => '100%', 'value' => $item['mail'],
					'pattern' => '/^[\w]+[\w\d_\.\-]+@[\w\d\-]{2,}\.[a-z]{2,5}$/i',
					'errormsg' => i18n('Неверно указан почтовый адрес.', __CLASS__), 'access' => ADMIN),
			),
			'buttons' => array(UserRights($this->access)?'ok':'', 'apply', 'cancel'),
		);

		$pswd = array(
			'name' => 'PasswordForm',
			'caption' => i18n('Изменить пароль', __CLASS__),
			'width' => '400px',
			'fields' => array (
				array('type'=>'hidden','name'=>'password', 'value'=>$item['id']),
				array('type' => 'password', 'name' => 'pswd1', 'label' => i18n('Пароль', __CLASS__),
					'maxlength' => 32, 'width' => '100%'),
				array('type' => 'password', 'name' => 'pswd2', 'label' => i18n('Подтверждение', __CLASS__),
					'maxlength' => 32, 'width' => '100%', 'equal' => 'pswd1',
					'errormsg' => i18n('Пароль и подтверждение не совпадают.', __CLASS__)),
			),
			'buttons' => array(UserRights($this->access)?'ok':'apply', 'cancel'),
		);

		$result = $page->renderForm($form)."<br />\n".$page->renderForm($pswd);
		return $result;
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
	function create()
	{
		global $Eresus, $page;

		restoreRequest();
		$form = array(
			'name'=>'UserForm',
			'caption' => i18n('Создать пользователя', __CLASS__),
			'width' => '400px',
			'fields' => array (
				array('type'=>'hidden','name'=>'action','value'=>'insert'),
				array('type' => 'edit', 'name' => 'name', 'label' => i18n('Имя', __CLASS__),
					'maxlength' => 32, 'width' => '100%', 'pattern' => '/.+/',
					'errormsg' => i18n('Псевдоним пользователя не может быть пустым.', __CLASS__)),
				array('type' => 'edit', 'name' => 'login', 'label' => i18n('Логин', __CLASS__),
					'maxlength' => 16, 'width' => '100%', 'pattern' => '/^[a-z0-9_]+$/i',
					'errormsg' => 'Логин не может быть пустым и должен состоять только из букв a-z, цифр и ' .
						'символа подчеркивания.', __CLASS__),
				array('type' => 'select', 'name' => 'access', 'label' => i18n('Уровень доступа', __CLASS__),
					'width'=>'100%', 'values' => array('2','3','4'),
					'items' => array(
						i18n('Администратор'),
						i18n('Редактор'),
						i18n('Пользователь')
					), 'default' => USER),
				array('type' => 'checkbox', 'name' => 'active',
					'label' => i18n('Учетная запись активна', __CLASS__), 'default' => true),
				array('type'=>'divider'),
				array('type' => 'password', 'name' => 'pswd1', 'label' => i18n('Пароль', __CLASS__),
					'maxlength' => 32, 'width' => '100%'),
				array('type' => 'password', 'name' => 'pswd2', 'label' => i18n('Подтверждение', __CLASS__),
					'maxlength' => 32, 'width' => '100%', 'equal' => 'pswd1',
					'errormsg' => i18n('Пароль и подтверждение не совпадают.', __CLASS__)),
				array('type'=>'divider'),
				array('type' => 'edit', 'name' => 'mail', 'label' => i18n('e-mail', __CLASS__),
					'maxlength' => 32, 'width' => '100%'),
			),
			'buttons'=>array('ok', 'cancel')
		);
		$result = $page->renderForm($form, $Eresus->request['arg']);
		return $result;
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#

	/**
	 *
	 * @return string
	 */
	public function adminRender()
	{
		global $Eresus, $page;

		$result = '';
		$granted = false;
		if (UserRights($this->access))
		{
			$granted = true;
		}
		else
		{
			if (arg('id') == $Eresus->user['id']) {
				if (!arg('password') || (arg('password') == $Eresus->user['id'])) $granted = true;
				if (!arg('update') || (arg('update') == $Eresus->user['id'])) $granted = true;
			}
		}

		if (!$granted)
		{
			eresus_log(__METHOD__, LOG_WARNING, 'Access denied for user "%s"', $Eresus->user['name']);
			return '';
		}

		if (arg('update')) $this->update(null);
		elseif (isset($Eresus->request['arg']['password'])  && (!isset($Eresus->request['arg']['action']) || ($Eresus->request['arg']['action'] != 'login'))) $this->password();
		elseif (isset($Eresus->request['arg']['toggle'])) $this->toggle();
		elseif (isset($Eresus->request['arg']['delete'])) $this->delete(null);
		elseif (isset($Eresus->request['arg']['id'])) $result = $this->edit();
		elseif (isset($Eresus->request['arg']['action'])) switch(arg('action')) {
			case 'create': $result = $this->create(); break;
			case 'insert': $this->insert(); break;
		}
		else
		{
			$result = $this->listAction();
		}
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Отрисовывает список пользователей
	 *
	 * @return string  HTML
	 *
	 * @since 2.17
	 */
	private function listAction()
	{
		$table = array (
						'name' => 'users',
						'key'=>'id',
						'itemsPerPage' => 20,
						'columns' => array(
		array('name' => 'id', 'caption' => 'ID', 'align' => 'right', 'width' => '40px'),
		array('name' => 'name', 'caption' => i18n('Имя', __CLASS__), 'align' => 'left'),
		array('name' => 'access', 'caption' => i18n('Дост.', __CLASS__), 'align' => 'center',
								'width' => '70px', 'replace' => array (
									'1' => '<span style="font-weight: bold; color: red;">ROOT</span>',
									'2' => '<span style="font-weight: bold; color: red;">admin</span>',
									'3' => '<span style="font-weight: bold; color: blue;">editor</span>',
									'4' => 'user'
		)),
		array('name' => 'login', 'caption' => i18n('Логин', __CLASS__), 'align' => 'left'),
		array('name' => 'mail', 'caption' => i18n('e-mail', __CLASS__), 'align' => 'center',
								'macros'=>true, 'value'=>'<a href="mailto:$(mail)">$(mail)</a>'),
		array('name' => 'lastVisit', 'caption' => i18n('Последний визит', __CLASS__),
								'align' => 'center', 'width' => '140px'),
		array('name' => 'loginErrors', 'caption' => i18n('Ошиб.', __CLASS__),
								'align' => 'center', 'replace' => array ('0' => '')),
		),
						'controls' => array (
							'delete' => 'check_for_root',
							'edit' => 'check_for_edit',
							'toggle' => 'check_for_root',
		),
						'tabs' => array(
							'width'=>'180px',
							'items'=>array(
		array('caption' => i18n('Создать пользователя', __CLASS__), 'name' => 'action',
							 	'value' => 'create')
		)
		)
		);
		$result = $page->renderTable($table);
	}
	//-----------------------------------------------------------------------------
}
