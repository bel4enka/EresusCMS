<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Контроллёр бэкэнда
 *
 * @copyright 2004-2007, ProCreat Systems, http://procreat.ru/
 * @copyright 2007-${build.year}, Eresus Project, http://eresus.ru/
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
 * @package EresusCMS
 *
 * $Id$
 */

/**
 * Контроллёр бэкэнда
 *
 * @package EresusCMS
 */
class AdminFrontController extends FrontController implements IAclResource {

	/**
	 * Запуск бэкэнда
	 */
	public function execute()
	{
		Registry::set('core.template.templateDir', Core::app()->getFsRoot() . 'core/admin/themes/classic');

		$acl = ACL::getInstance();

		$acl->addRole(new AclRole('guest'));
		$acl->addRole(new AclRole('user'));
		$acl->addRole(new AclRole('editor'));
		$acl->addRole(new AclRole('admin'));
		$acl->addRole(new AclRole('root'));

		$acl->addResource($this);

		$acl->allow('root');
		$acl->allow('admin', $this);
		$acl->allow('editor', $this, 'edit');

		if ($acl->isAllowed(UserModel::getCurrent(), $this)) {

			include_once 'kernel-legacy.php';
			$GLOBALS['Eresus'] = new Eresus;
			$GLOBALS['Eresus']->init();
			$GLOBALS['Eresus']->execute();

			include_once 'admin.php';

		} else {

			$ctrl = new AdminAuthController();

		}

		$ctrl->setRequest($this->request);
		$ctrl->setResponse($this->response);
		$ctrl->execute();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Идентификатор ресурса
	 *
	 * @return string
	 * @see main/core/classes/IAclResource#getResourceId()
	 */
	public function getResourceId()
	{
		return get_class($this);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @see main/core/classes/IAclResource#getParentResources()
	 */
	public function getParentResources()
	{
		return array();
	}
	//-----------------------------------------------------------------------------

}
