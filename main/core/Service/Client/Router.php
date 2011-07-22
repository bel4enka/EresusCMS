<?php
/**
 * ${product.title} ${product.version}
 *
 * Служба роутинга КИ
 *
 * @copyright 2010, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mihalych@vsepofigu.ru>
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
 * @package Service
 *
 * $Id$
 */

/**
 * Служба роутинга КИ
 *
 * @package Service
 *
 * @since 2.16
 */
class Eresus_Service_Client_Router
{
	/**
	 * Экземпляр-одиночка
	 *
	 * @var Eresus_Service_Client_Router
	 */
	private static $instance = null;

	/**
	 * Возвращает экземпляр класса
	 *
	 * @return Eresus_Service_Client_Router
	 *
	 * @since 2.16
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new self();
		}
		return self::$instance;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает запрошенный раздел
	 *
	 * @param Eresus_CMS_Request $request
	 *
	 * @throws Eresus_CMS_Exception_NotFound  если запрошенный ресурс не найден
	 *
	 * @return Eresus_Model_Section
	 *
	 * @since 2.16
	 */
	public function findSection(Eresus_CMS_Request $request)
	{
		$srvSections = Eresus_Service_Sections::getInstance();
		$section = $srvSections->getRoot();

		$names = explode('/', $request->getPath());

		$user = Eresus_Auth::getInstance()->getUser();
		$userAccessLevel = $user ? $user->access : 5; // FIXME: Заменить на константу

		$url = '';

		foreach ($names as $name)
		{
			$tmp = $section->getChildByName($name);
			if (!$tmp)
			{
				break;
			}

			if (!$tmp->active)
			{
				throw new Eresus_CMS_Exception_NotFound();
			}

			if ($tmp->access < $userAccessLevel)
			{
				throw new Eresus_CMS_Exception_Forbidden();
			}

			$section = $tmp;

			if ($section->name)
			{
				$url .= $section->name . '/';
			}
			$event = new Eresus_CMS_Event('clientOnURLSplit');
			$event->section = $section;
			$event->url = $url;
			$event->dispath();
			//$this->section []= $section->title;
		}

		//$GLOBALS['Eresus']->request['path'] = $GLOBALS['Eresus']->root . $url;

		return $section;

		/*
		global $Eresus;

		$result = false;
		$main_fake = false;
		if (!count($Eresus->request['params']) || $Eresus->request['params'][0] != 'main') {
			array_unshift($Eresus->request['params'], 'main');
			$main_fake = true;
		}
		reset($Eresus->request['params']);
		$item['id'] = 0;
		$url = '';
		do {
			$items = $Eresus->sections->children($item['id'],
				$_SESSION['user_auth'] ? $Eresus->user['access'] : GUEST, SECTIONS_ACTIVE);
			$item = false;
			for($i=0; $i<count($items); $i++) if ($items[$i]['name'] == current($Eresus->request['params'])) {
				$result = $item = $items[$i];
				if ($item['id'] != 1 || !$main_fake) $url .= $item['name'].'/';
				$Eresus->plugins->clientOnURLSplit($item, $url);
				$this->section[] = $item['title'];
				next($Eresus->request['params']);
				array_shift($Eresus->request['params']);
				break;
			}
			if ($item && $item['id'] == 1 && $main_fake) $item['id'] = 0;
		} while ($item && current($Eresus->request['params']));
		$Eresus->request['path'] = $Eresus->request['path'] = $Eresus->root.$url;
		if ($result) $result = $Eresus->sections->get($result['id']);
		return $result; */


		throw new Eresus_CMS_Exception_NotFound;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Скрываем конструктор
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	// @codeCoverageIgnoreStart
	private function __construct()
	{
	}
	// @codeCoverageIgnoreEnd
	//-----------------------------------------------------------------------------

	/**
	 * Блокируем клонирование
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	// @codeCoverageIgnoreStart
	private function __clone()
	{
	}
	// @codeCoverageIgnoreEnd
	//-----------------------------------------------------------------------------
}
