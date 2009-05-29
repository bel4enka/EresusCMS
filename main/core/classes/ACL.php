<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Список контроля доступа
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
 * Логическая ошибка при работе с ACL
 *
 * @package EresusCMS
 */
class AclLogicException extends EresusLogicException {}



/**
 * Неизвестный ресурс
 *
 * Вбрасывается при обращении к незарегистрированному ресурсу
 *
 * @package EresusLogicException
 */
class AclUnknownResourceException extends AclLogicException {

	/**
	 * Конструктор
	 *
	 * @param mixed     $resourceId [optional]  Идентификатор ресурса
	 * @param Exception $previous [optional]    Предыдущее исключение
	 */
	public function __construct($roleId = null, $previous = null)
	{
		if ($roleId)
			$message = sprintf('Unknown resource with id "%s"', $roleId);
		else
			$message = sprintf('Unknown resource');

		parent::__construct($message, 'Unknown ACL resource', $previous);
	}
	//-----------------------------------------------------------------------------
}



/**
 * Неизвестная роль
 *
 * Вбрасывается при обращении к незарегистрированной роли
 *
 * @package EresusLogicException
 */
class AclUnknownRoleException extends AclLogicException {

	/**
	 * Конструктор
	 *
	 * @param mixed     $roleId [optional]    Идентификатор роли
	 * @param Exception $previous [optional]  Предыдущее исключение
	 */
	public function __construct($roleId = null, $previous = null)
	{
		if ($roleId)
			$message = sprintf('Unknown role with id "%s"', $roleId);
		else
			$message = sprintf('Unknown role');

		parent::__construct($message, 'Unknown ACL role', $previous);
	}
	//-----------------------------------------------------------------------------
}



/**
 * Интерфейс ресурса для ACL
 *
 * @package EresusCMS
 */
interface IAclResource {

	/**
	 * Метод должен возвращать идентификатор ресурса
	 *
	 * @return string
	 */
	public function getResourceId();

	/**
	 * Метод должен возвращать массив родительских ресурсов
	 *
	 * @return array
	 */
	public function getParentResources();
}



/**
 * Интерфейс роли для ACL
 *
 * @package EresusCMS
 */
interface IAclRole {

	/**
	 * Метод должен возвращать идентификатор роли
	 *
	 * @return string
	 */
	public function getRoleId();

	/**
	 * Метод должен возвращать массив родительских ролей
	 *
	 * @return array
	 */
	public function getParentRoles();
}



/**
 * Ресурс
 *
 * @package EresusCMS
 */
class AclResource implements IAclResource {

	/**
	 * Идентификатор ресурса
	 * @var string
	 */
	protected $resourceId;

	/**
	 * Родительские ресурсы
	 *
	 * В массиве хранятся идентификаторы ресурсов
	 *
	 * @var array
	 */
	protected $parentResources = array();

	/**
	 * Конструктор ресурса
	 *
	 * @param string        $resourceId          Идентификатор ресурса
	 * @param string|array  $parents [optional]  Список родительсктх ресурсов
	 */
	public function __construct($resourceId, $parents = null)
	{
		if (empty($resourceId))
			elog(array(get_class($this), __METHOD__), LOG_WARNING, 'Empty $resourceId passed.');

		$this->resourceId = $resourceId;

		if ($parents) {

			if (!is_array($parents) && !is_string($parents))
				throw new EresusTypeException($parents, 'array or null');

			if (is_string($parents))
				$parents = array($parents);

			#TODO Сделать проверку каждого элемента массива
			$this->parentResources = $parents;

		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Получение идентификатора ресурса
	 *
	 * @return string
	 */
	public function getResourceId()
	{
		return $this->resourceId;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает массив идентификаторов родительских ресурсов
	 *
	 * @return array
	 * @see main/core/classes/IAclResource#getParentResources()
	 */
	public function getParentResources()
	{
		return $this->parentResources;
	}
	//-----------------------------------------------------------------------------

}



/**
 * Роль
 *
 * @package EresusCMS
 */
class AclRole implements IAclRole {

	/**
	 * Идентификатор роли
	 * @var string
	 */
	protected $roleId;

	/**
	 * Родительские роли
	 *
	 * В массиве хранятся идентификаторы ролей
	 *
	 * @var array
	 */
	protected $parentRoles = array();

	/**
	 * Конструктор роли
	 *
	 * @param string        $roleId              Идентификатор роли
	 * @param string|array  $parents [optional]  Список родительсктх ролей
	 */
	public function __construct($roleId, $parents = null)
	{
		if (empty($roleId))
			elog(array(get_class($this), __METHOD__), LOG_WARNING, 'Empty $roleId passed.');

		$this->roleId = $roleId;

		if ($parents) {

			if (!is_array($parents) && !is_string($parents))
				throw new EresusTypeException($parents, 'array or null');

			if (is_string($parents))
				$parents = array($parents);

			#TODO Сделать проверку каждого элемента массива
			$this->parentRoles = $parents;

		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Получение идентификатора роли
	 *
	 * @return string
	 */
	public function getRoleId()
	{
		return $this->roleId;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает массив идентификаторов родительских ролей
	 *
	 * @return array
	 * @see main/core/classes/IAclRole#getParentRoles()
	 */
	public function getParentRoles()
	{
		return $this->parentRoles;
	}
	//-----------------------------------------------------------------------------

}



/**
 * Список контроля доступа
 *
 * Этот класс позволяет создавать списки контроля доступа к ресурсам.
 *
 * Управление доступом основывается на двух понятиях:
 * - Ресурс - объект, доступ к которому контролируется.
 * - Роль - объект, который может запрашивать доступ к ресурсу.
 *
 * Все ресурсы и роли должны быть зарегистрированы в списке перед
 * добавления правил доступа с участием этих ресуров и ролей.
 *
 * Класс ACL имеет статический метод getInstance для реализации
 * паттерна "Одиночка".
 *
 * @package EresusCMS
 */
class ACL {

	/**
	 * Экземпляр-одиночка
	 *
	 * @var ACL
	 */
	private static $instance;

	/**
	 * Список зарегистрированных ресурсов
	 *
	 * В списке хранятся объекты ресурсов
	 *
	 * @var array
	 */
	protected $resources = array();

	/**
	 * Список зарегистрированных ролей
	 *
	 * В списке хранятся объекты ролей
	 *
	 * @var array
	 */
	protected $roles = array();

	/**
	 * Список правил
	 *
	 * Ассоциативный массив следующей структуры:
	 *
	 * resourceId1 => array ( // Идентификатор ресурса
	 *   roleId1 => array (   // Идентификатор роли
	 *     'allow' => array ( // Список разрешений
	 *       right1,          // Право
	 *       ...
	 *       rightN
	 *     ),
	 *     'deny' => array (  // Список запретов
	 *       ...
	 *     )
	 *   ),
	 * ),
	 * ...
	 * resourceIdN => ...
	 *
	 * @var array
	 */
	protected $acl = array();

	/**
	 * Получение экземпляра-одиночки
	 *
	 * @return ACL
	 */
	public static function getInstance()
	{
		if (!self::$instance) {

			self::$instance = new ACL();

		}

		return self::$instance;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Добавление ресурса к списку контролируемых
	 *
	 * @param IAclResource $resource  Ресурс
	 */
	public function addResource($resource)
	{
		/* Проверка аргументов */
		if (!($resource instanceof IAclResource))
			throw new EresusTypeException($resource, 'IAclResource');

		$this->resources[$resource->getResourceId()] = $resource;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Добавление роли к списку контролируемых
	 *
	 * @param IAclRole $role  Роль
	 */
	public function addRole($role)
	{
		/* Проверка аргументов */
		if (!($role instanceof IAclRole))
			throw new EresusTypeException($role, 'IAclRole');

		$this->roles[$role->getRoleId()] = $role;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Получение списка зарегистрированных ресурсов
	 *
	 * @return array
	 */
	public function getResources()
	{
		return $this->resources;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Получение списка зарегистрированных ролей
	 *
	 * @return array
	 */
	public function getRoles()
	{
		return $this->roles;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка наличия ресурса в списке
	 *
	 * @param IAclResource|string $resource  Ресурс или его идентификатор
	 * @return bool
	 */
	public function hasResource($resource)
	{
		/* Проверка аргументов */
	if (!is_string($resource) && !($resource instanceof IAclResource))
			throw new EresusTypeException($resource, 'IAclResource or string');

		/* Приведение типов */
		$resourceId = is_string($resource) ? $resource : $resource->getResourceId();

		return $resourceId == '*' || isset($this->resources[$resourceId]);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка наличия ресурса в списке
	 *
	 * @param IAclRole|string $role  Ресурс или его идентификатор
	 * @return bool
	 */
	public function hasRole($role)
	{
		/* Проверка аргументов */
	if (!is_string($role) && !($role instanceof IAclRole))
			throw new EresusTypeException($role, 'IAclRole or string');

		/* Приведение типов */
		$roleId = is_string($role) ? $role : $role->getRoleId();

		return isset($this->roles[$roleId]);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Добавление нового разрешающего правила
	 *
	 * @param IAclRole|string     $role                 Роль, для которой надо добавить правило.
	 * @param IAclResource|string $resource [optional]  Ресурс, доступ к которому контролируется.
	 *                                                  null - любой ресурс.
	 * @param string|array        $rights [optional]    Список прав, предоставляемых правилом.
	 *                                                  null - все права.
	 */
	public function allow($role, $resource = null, $rights = null)
	{
		/* Проверка аргументов */
		if (!is_string($role) && !($role instanceof IAclRole))
			throw new EresusTypeException($role, 'IAclRole or string');

		if (!is_null($resource) && !is_string($resource) && !($resource instanceof IAclResource))
			throw new EresusTypeException($resource, 'IAclResource, string or null');

		if (!is_null($rights) && !is_string($rights) && !is_array($rights))
			throw new EresusTypeException($rights, 'array, string or null');

		/* Приведение типов значений */
		$roleId = is_string($role) ? $role : $role->getRoleId();

		switch (true) {
			case is_null($resource):
				$resourceId = '*';
			break;
			case is_string($resource):
				$resourceId = $resource;
			break;
			default:
				$resourceId = $resource->getResourceId();
		}

		if (is_null($rights)) $rights = array('*');
		if (is_string($rights)) $rights = array($rights);

		/* Проверка наличия в списке роли и ресурса  */
		if (!$this->hasRole($roleId))
			throw new AclUnknownRoleException($roleId);

		if (!$this->hasResource($resourceId))
			throw new AclUnknownResourceException($resourceId);

		/* Создание набора правил для ресурса и роли */
		if (! isset($this->acl[$resourceId][$roleId]))
			$this->acl[$resourceId][$roleId] = array();

		/* Добавление права */
		$this->acl[$resourceId][$roleId] =
			array_merge($this->acl[$resourceId][$roleId], $rights);

	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка прав роли на ресурс
	 *
	 * @param IAclRole|string     $role                 Роль, для которой надо проверить доступ.
	 * @param IAclResource|string $resource [optional]  Ресурс, доступ к которому надо проверить.
	 *                                           null - проверить глобальное право.
	 * @param string|array        $rights [optional]    Список проверяемых прав.
	 *                                           null - все права?.
	 * @return bool  true - если у роли есть соответствующие права.
	 */
	public function isAllowed($role, $resource = null, $rights = null)
	{
		/* Проверка аргументов */
		if (!is_string($role) && !($role instanceof IAclRole))
			throw new EresusTypeException($role, 'IAclRole or string');

		if (!is_null($resource) && !is_string($resource) && !($resource instanceof IAclResource))
			throw new EresusTypeException($resource, 'IAclResource, string or null');

		if (!is_null($rights) && !is_string($rights) && !is_array($rights))
			throw new EresusTypeException($rights, 'array, string or null');

		/* Приведение типов значений */
		$roleId = is_string($role) ? $role : $role->getRoleId();

		switch (true) {
			case is_null($resource):
				$resourceId = '*';
			break;
			case is_string($resource):
				$resourceId = $resource;
			break;
			default:
				$resourceId = $resource->getResourceId();
		}

		if (is_null($rights)) $rights = array('*');
		if (is_string($rights)) $rights = array($rights);

		/*
		 * Проверка
		 */

		/* Проверка прав на конкретный ресурс */
		if (isset($this->acl[$resourceId][$roleId])) {
			$expectedMatches = count($rights);
			$realMatches = count(array_intersect($this->acl[$resourceId][$roleId], $rights));
			$hasSuperRight = in_array('*', $this->acl[$resourceId][$roleId]);
			if ($realMatches == $expectedMatches || $hasSuperRight)
				return true;
		}

		/* Проверка глобального права */
		if (! isset($this->acl['*'][$roleId])) return false;
		$expectedMatches = count($rights);
		$realMatches = count(array_intersect($this->acl['*'][$roleId], $rights));
		$hasSuperRight = in_array('*', $this->acl['*'][$roleId]);
		return $realMatches == $expectedMatches || $hasSuperRight;
	}
	//-----------------------------------------------------------------------------
}
