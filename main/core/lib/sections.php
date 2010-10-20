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
 * @package EresusCMS
 *
 * $Id$
 *
 * TODO: Перенести сохранение сквозной нумерации позицию сюда из pages
 *
 */

/**
 * Активные разделы
 * @var int
 */
define('SECTIONS_ACTIVE',  0x0001);

/**
 * Видимые разделы
 * @var int
 */
define('SECTIONS_VISIBLE', 0x0002);

/**
 * Работа с разделами сайта
 *
 * @package EresusCMS
 */
class Sections
{
	/**
	 * Имя таблицы разделов
	 *
	 * @var string
	 */
	private $table = 'pages';

	/**
	 * Индекс разделов
	 *
	 * @var array
	 */
	private $index = array();

	/**
	 * Кэш
	 *
	 * @var array
	 */
	private $cache = array();

	/**
	 * Создаёт индекс разделов
	 *
	 * @param bool $force  Игнорировать закэшированные данные
	 * @return void
	 */
	private function index($force = false)
	{
		global $Eresus;

		if ($force || !$this->index)
		{
			$items = $Eresus->db->select($this->table, '', '`position`', '`id`,`owner`');
			if ($items)
			{
				$this->index = array();
				foreach($items as $item)
				{
					$this->index[$item['owner']] []= $item['id'];
				}
			}
		}
	}
	//------------------------------------------------------------------------------

	/**
	 * Создаёт список ID разделов определённой ветки
	 *
	 * @param int $owner  ID корневого раздела ветки
	 * @return array  Список ID разделов
	 */
	private function branch_ids($owner)
	{
		$result = array();
		if (isset($this->index[$owner]))
		{
			$result = $this->index[$owner];
			foreach($result as $section)
			{
				if (!$section) continue;
				$result = array_merge($result, $this->branch_ids($section));
			}
		}
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * Выбирает разделы определённой ветки
	 *
	 * @param int $owner   Идентификатор корневого раздела ветки
	 * @param int $access  Минимальный уровень доступа
	 * @param int $flags   Флаги (см. SECTIONS_XXX)
	 *
	 * @return array  Описания разделов
	 */
	public function branch($owner, $access = GUEST, $flags = 0)
	{
		global $Eresus;

		$result = array();
		// Создаём индекс
		if (!$this->index) $this->index();
		// Находим ID разделов ветки.
		$set = $this->branch_ids($owner);
		if (count($set))
		{
			$list = array();
			/* Читаем из кэша */
			for ($i=0; $i < count($set); $i++)
			{
				if (isset($this->cache[$set[$i]]))
				{
					$list[] = $this->cache[$set[$i]];
					array_splice($set, $i, 1);
					$i--;
				}
			}
			if (count($set))
			{
				$fieldset = '';//implode(',', array_diff($this->fields(), array('content')));
				# Читаем из БД
				$set = implode(',', $set);
				$items = $Eresus->db->select($this->table, "FIND_IN_SET(`id`, '$set') AND `access` >= $access", 'position', $fieldset);
				for($i=0; $i<count($items); $i++)
				{
					$this->cache[$items[$i]['id']] = $items[$i];
					$list[] = $items[$i];
				}
			}

			if ($flags)
			{
				for($i=0; $i<count($list); $i++) if
					(
						(!($flags & SECTIONS_ACTIVE) || $list[$i]['active']) &&
						(!($flags & SECTIONS_VISIBLE) || $list[$i]['visible'])
					) $result[] = $list[$i];
			} else $result = $list;
		}
		return $result;
	}

	/**
	 * @deprecated since 2.14
	 */
	function brunch($owner, $access = GUEST, $flags = 0)
	{
		return $this->branch($owner, $access, $flags);
	}
	//------------------------------------------------------------------------------

	/**
	 * Возвращает идентификаторы дочерних разделов указанного
	 *
	 * @param int $owner   Идентификатор корневого раздела ветки
	 * @param int $access  Минимальный уровень доступа
	 * @param int $flags   Флаги (см. SECTIONS_XXX)
	 *
	 * @return array  Идентификаторы разделов
	 */
	public function children($owner, $access = GUEST, $flags = 0)
	{
		$items = $this->branch($owner, $access, $flags);
		$result = array();
		for ($i=0; $i<count($items); $i++)
		{
			if ($items[$i]['owner'] == $owner)
			{
				$result[] = $items[$i];
			}
		}
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * Возвращает идентификаторы всех родительских разделов указанного
	 *
	 * @param int $id   Идентификатор раздела
	 *
	 * @return array  Идентификаторы разделов или NULL если раздела $id не существует
	 */
	public function parents($id)
	{
		$this->index();
		$result = array();
		while ($id)
		{
			foreach ($this->index as $key => $value)
			{
				if (in_array($id, $value))
				{
					$result[] = $id = $key;
					break;
				}
			}
			if (!$result) return null;
		}
		$result = array_reverse($result);
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * Возвращает список полей
	 *
	 * @return  array  Список полей
	 */
	public function fields()
	{
		global $Eresus;

		if (isset($this->cache['fields']))
		{
			$result = $this->cache['fields'];
		}
		else
		{
			$result = $Eresus->db->fields($this->table);
			$this->cache['fields'] = $result;
		}
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * Возвращает раздел
	 *
	 * @param int|array|string $id  ID раздела / Список идентификаторов / SQL-условие
	 *
	 * @return  array  Описание раздела
	 */
	public function get($id)
	{
		global $Eresus;

		if (is_array($id))
		{
			$what = "FIND_IN_SET(`id`, '".implode(',', $id)."')";
		}
		elseif (is_numeric($id))
		{
			$what = "`id`=$id";
		}
		else
		{
			$what = $id;
		}
		$result = $Eresus->db->select($this->table, $what);
		if ($result)
		{
			for ($i=0; $i<count($result); $i++)
			{
				$result[$i]['options'] = decodeOptions($result[$i]['options']);
			}
		}
		if (is_numeric($id) && $result && count($result)) $result = $result[0];
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * Добавляет раздел
	 *
	 * @param  array  $item  Раздел
	 * @return  mixed  Описание нового раздела или false в случае неудачи
	 */
	public function add($item)
	{
		global $Eresus;

		if (!$this->index)
		{
			$this->index();
		}

		$result = false;
		if (isset($item['id'])) unset($item['id']);
		if (!isset($item['owner'])) $item['owner'] = 0;
		$item['created'] = gettime('Y-m-d H:i:s');
		$item['updated'] = $item['created'];
		$item['options'] = isset($item['options']) ? trim($item['options']) : '';
		$item['options'] = (empty($item['options']))?'':encodeOptions(text2array($item['options'], true));
		if (!isset($item['position']) || $item['position'] === '') $item['position'] = isset($this->index[$item['owner']]) ? count($this->index[$item['owner']]) : 0;
		if ($Eresus->db->insert($this->table, $item))
			$result = $this->get($Eresus->db->getInsertedId());
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * Изменяет раздел
	 *
	 * @param  array  $item  Раздел
	 *
	 * @return  mixed  Описание нового раздела или false в случае неудачи
	 */
	public function update($item)
	{
		global $Eresus;

		$result = false;
		$item['updated'] = gettime('Y-m-d H:i:s');
		$item['options'] = encodeOptions($item['options']);
		$item['title'] = $Eresus->db->escape($item['title']);
		$item['caption'] = $Eresus->db->escape($item['caption']);
		$item['description'] = $Eresus->db->escape($item['description']);
		$item['hint'] = $Eresus->db->escape($item['hint']);
		$item['keywords'] = $Eresus->db->escape($item['keywords']);
		$item['content'] = $Eresus->db->escape($item['content']);
		$item['options'] = $Eresus->db->escape($item['options']);
		$result = $Eresus->db->updateItem($this->table, $item, "`id`={$item['id']}");
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * Удаляет раздел и подразделы
	 *
	 * @param  int  $id  Идентификатор раздела
	 *
	 * @return  bool  Результат операции
	 */
	public function delete($id)
	{
		global $Eresus;

		$result = true;
		$children = $this->children($id);
		for($i=0; $i<count($children); $i++) if  (!$result = $this->delete($children[$i]['id'])) break;
		if ($result) {
			# Удаляем контент раздела
			$section = $this->get($id);
			if ($plugin = $Eresus->plugins->load($section['type'])) {
				if (method_exists($plugin, 'onSectionDelete')) $plugin->onSectionDelete($id);
			}
			$result = $Eresus->db->delete($this->table, "`id`=$id");
		}
		return $result;
	}
	//------------------------------------------------------------------------------
}
