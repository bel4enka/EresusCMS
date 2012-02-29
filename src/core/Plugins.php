<?php
/**
 * ${product.title}
 *
 * Интерфейс к модулям расширения
 *
 * @version ${product.version}
 * @copyright ${product.copyright}
 * @license ${license.uri} ${license.name}
 * @author Михаил Красильников <mihalych@vsepofigu.ru>
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
* Работа с плагинами
*
* @package Eresus
*/
class Eresus_Plugins
{
	/**
	 * Список активных модулей
	 *
	 * @var array
	 * @since 2.17
	 */
	private $plugins = array();

	/**
	 * Таблица обработчиков событий
	 *
	 * @var array
	 * @todo сделать private
	 */
	public $events = array();

	/**
	 * Загружает активные плагины
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function init()
	{
		$plugins = Doctrine_Core::getTable('Eresus_Entity_Plugin')->findAll();
		foreach ($plugins as $plugin)
		{
			if ($plugin->active)
			{
				$this->plugins[$plugin->uid] = $plugin;
			}
		}
		/*$items = $GLOBALS['Eresus']->db->select('plugins', 'active = 1');
		if ($items)
		{
			foreach ($items as &$item)
			{
				$item['info'] = unserialize($item['info']);
				$this->list[$item['name']] = $item;
			}

			/* Проверяем зависимости * /
			do
			{
				$success = true;
				foreach ($this->list as $plugin => $item)
				{
					foreach ($item['info']->getRequiredPlugins() as $required)
					{
						list ($name, $minVer, $maxVer) = $required;
						if (
						!isset($this->list[$name]) ||
						($minVer && version_compare($this->list[$name]['info']->version, $minVer, '<')) ||
						($maxVer && version_compare($this->list[$name]['info']->version, $maxVer, '>'))
						)
						{
							$msg = 'Plugin "%s" requires plugin %s';
							$requiredPlugin = $name . ' ' . $minVer . '-' . $maxVer;
							eresus_log(__CLASS__, LOG_ERR, $msg, $plugin, $requiredPlugin);
							/*$msg = I18n::getInstance()->getText($msg, $this);
							 ErrorMessage(sprintf($msg, $plugin, $requiredPlugin));* /
							unset($this->list[$plugin]);
							$success = false;
						}
					}
				}
			}
			while (!$success);

			/* Загружаем плагины  * /
			foreach ($this->list as $item)
			{
				$this->load($item['name']);
			}
		}*/
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяет доступность модуля с указанными параметрами
	 *
	 * @param string $uid     UID
	 * @param string $minVer  минимальная версия
	 * @param string $maxVer  максимальная версия
	 *
	 * @return bool
	 *
	 * @since 2.17
	 */
	public function isAvailable($uid, $minVer, $maxVer)
	{
		if (!isset($this->plugins[$uid]))
		{
			return false;
		}

		$version = $this->plugins[$uid]->version;
		if (version_compare($version, $minVer, '<') || version_compare($version, $maxVer, '>'))
		{
			return false;
		}

		return true;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возврашает объект расширения
	 *
	 * @param string $uid
	 *
	 * @return Eresus_Entity_Plugin|null
	 *
	 * @since 2.17
	 */
	public function get($uid)
	{
		if (isset($this->plugins[$uid]))
		{
			return $this->plugins[$uid];
		}
		return null;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает установленные расширения, зависящие от указанного
	 *
	 * @param string $uid
	 *
	 * @return array
	 *
	 * @since 2.17
	 */
	public function getDependent($uid)
	{
		$dependent = array();
		foreach ($this->plugins as $plugin)
		{
			$deps = $plugin->requiredPlugins;
			if (isset($deps[$uid]))
			{
				$dependent []= $plugin;
				$dependent = array_merge($dependent, $this->getDependent($plugin->uid));
			}
		}
		return $dependent;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает список доступных типов разделов
	 *
	 * @return array
	 *
	 * @since 2.17
	 */
	public function getContentTypes()
	{
		/* Встроенные типы */
		$types = array(
			'ru.eresus.cms.default' => array('title' => i18n('По умолчанию', __CLASS__)),
			'ru.eresus.cms.list' => array('title' => i18n('Список подразделов', __CLASS__)),
			'ru.eresus.cms.url' => array('title' => i18n('URL', __CLASS__))
		);

		foreach ($this->plugins as $plugin)
		{
			$types = array_merge($types, $plugin->getContentTypes());
		}

		return $types;
	}
	//-----------------------------------------------------------------------------
}
