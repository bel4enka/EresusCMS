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

/**
 * Управление пользователями
 *
 * @package Eresus
 */
class TUsers
{
	/**
	 * Класс для работы с учётными записями
	 *
	 * @var EresusAccounts
	 * @deprecated с 2.17
	 */
	private $accounts;

	/**
	 * Уровень доступа к модулю
	 * @var int
	 */
	private $access = ADMIN;

 /**
	* Конструктор
	*
	* @return TUsers
	*/
	public function __construct()
	{
		$this->accounts = new EresusAccounts();
	}
	//-----------------------------------------------------------------------------

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
			if (arg('id') == $Eresus->user->id) {
				if (!arg('password') || (arg('password') == $Eresus->user->id)) $granted = true;
				if (!arg('update') || (arg('update') == $Eresus->user->id)) $granted = true;
			}
		}

		if (!$granted)
		{
			eresus_log(__METHOD__, LOG_WARNING, 'Access denied for user "%s"', $Eresus->user->username);
			return '';
		}

		switch (arg('action'))
		{
			case 'add':
				$result = $this->addUserAction();
				break;

			case 'edit':
				$result = $this->editUserAction(arg('id', 'int'));
				break;

			case 'toggle':
				$this->toggle(arg('id', 'int'));
				break;

			case 'delete':
				$this->delete(arg('id', 'int'));
				break;

			default:
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
		$accounts = Doctrine_Core::getTable('Eresus_Entity_User')->findAll();
		$provider = new Eresus_UI_List_DataProvider_Array($accounts->getData(),
			array('enabled' => 'active'));
		$list = new Eresus_UI_List($provider);
		$tmpl = Eresus_Template::fromFile('core/templates/accounts/list.html');
		return $tmpl->compile(array('list' => $list));
		/*
			$table = array (
						'itemsPerPage' => 20,
						'controls' => array (
							'delete' => 'check_for_root',
							'edit' => 'check_for_edit',
							'toggle' => 'check_for_root',
		*/
	}
	//-----------------------------------------------------------------------------

	/**
	 * Добавляет нового пользователя
	 *
	 * @todo Сделать на конфликты username и email
	 *
	 * @return mixed
	 *
	 * @since 2.17
	 */
	private function addUserAction()
	{
		if ('POST' == $GLOBALS['Eresus']->request['method'])
		{
			$info = array(
				'username' => arg('username'),
				'password' => arg('password'),
				'active' => arg('enabled', 'int'),
				'access' => arg('access', 'int'),
				'fullname' => arg('fullname'),
				'mail' => arg('email'),
			);
			$this->accounts->add($info);
			HTTP::redirect($GLOBALS['page']->url(array('id' => null)));
		}

		$tmpl = Eresus_Template::fromFile('core/templates/accounts/add-dialog.html');
		$html = $tmpl->compile();
		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Настройки пользователя
	 *
	 * @todo Сделать на конфликты username и email
	 *
	 * @param int $id  ID пользователя
	 *
	 * @return mixed
	 *
	 * @since 2.17
	 */
	private function editUserAction($id)
	{
		$user = $this->accounts->get($id);
		if (false === $user)
		{
			throw new Eresus_CMS_Exception_NotFound;
		}

		if ('POST' == $GLOBALS['Eresus']->request['method'])
		{
			$user->username = arg('username');
			$user->active = arg('enabled', 'int');
			$user->access = arg('access', 'int');
			$user->fullname = arg('fullname');
			$user->mail = arg('email');
			if (arg('chpswd'))
			{
				$user->password = arg('password');
			}
			$user->save();
			HTTP::redirect($GLOBALS['page']->url(array('id' => null)));
		}

		$tmpl = Eresus_Template::fromFile('core/templates/accounts/edit-dialog.html');
		$html = $tmpl->compile(array('account' => $user));
		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Удаляет пользователя
	 *
	 * @param int $id  идентификатор пользователя
	 */
	private function delete($id)
	{
		if ($id != 1)
		{
			$this->accounts->delete($id);
		}
		else
		{
			ErrorMessage(i18n('Учётная запись root не может быть удалена', __CLASS__));
		}
		HTTP::redirect($GLOBALS['page']->url(array('id' => null)));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Переключает активность пользователя
	 *
	 * @param int $id  идентификатор пользователя
	 */
	private function toggle($id)
	{
		if ($id != 1)
		{
			$user = $this->accounts->get($id);
			if (false === $user)
			{
				throw new Eresus_CMS_Exception_NotFound;
			}
			$user->active = !$user->active;
			$user->save();
		}
		else
		{
			ErrorMessage(i18n('Учётная запись root не может быть отключена', __CLASS__));
		}
		HTTP::redirect($GLOBALS['page']->url(array('id' => null)));
	}
	//-----------------------------------------------------------------------------
}
