<?php
/**
 * Eresus 2.11
 *
 * Классы системы кэширования
 *
 * @copyright		2004-2007, ProCreat Systems, http://procreat.ru/
 * @copyright		2007-2008, Eresus Group, http://eresus.ru/
 * @license     http://www.gnu.org/licenses/gpl.txt  GPL License 3
 * @author      Mikhail Krasilnikov <mk@procreat.ru>
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
 */

/*************************************************************************************************
 *  Класс системы кэширования
 *************************************************************************************************/

class EresusCache {
 /**
	* Тип кэша по умолчанию
	*
	* @var string
	*/
	var $default = null;
 /**
	* Интерфейсы к различным тиам кэша
	*
	* @var array
	*/
	var $caches = array();
 /**
	* Конструктор
	*
	* @return EresusCache
	*/
	function EresusCache()
	{
	}
	//-----------------------------------------------------------------------------
 /**
	* Саздание новой подсистемы кэширования
	*
	* @param string $name     Имя подсистемы
	* @param string $class		Имя класса подсистемы
	* @param array  $options  Опции подсистемы
	*/
	function create($name, $options = null)
	{
		$className = $options['driver'];
		$this->caches[$name] = new $className($options);
		if (count($this->caches) == 1) $this->default = $name;
	}
	//-----------------------------------------------------------------------------
 /**
	* Получение количества свободной памяти (в байтах)
	*
	* @param string $target  Выбор кэша
	*
	* @return int
	*/
	function free($target = 'default')
	{
		if ($target == 'default') $target = $this->default;
		$result = isset($this->caches[$target]) ? $this->caches[$target]->free() : 0;
		return $result;
	}
	//-----------------------------------------------------------------------------
 /**
	* Поместить данные в кеш
	*
	* @param string $owner   Владелец данных
	* @param string $key     Идентификатор данных
	* @param mixed  $value   Данные
	* @param int    $lifetime  Срок жизни данных (секундны)
	* @param string $target  Кеш, где требуется сохранить данные (default)
	*/
	function put($owner, $key, $value, $lifetime = 0, $target = 'default')
	{
		if ($target == 'default') $target = $this->default;
		if (isset($this->caches[$target])) $this->caches[$target]->put("$owner.$key", $value, $lifetime);
	}
	//-----------------------------------------------------------------------------
 /**
	* Получить данные из кэша
	*
	* @param string $owner   Владелец данных
	* @param string $key     Идентификатор данных
	* @param string $target  Выбор кэша
	*
	* @return mixed
	*/
	function get($owner, $key, $target = 'default')
	{
		if ($target == 'default') $target = $this->default;
		$result = isset($this->caches[$target]) ? $this->caches[$target]->get("$owner.$key") : null;
		return $result;
	}
	//-----------------------------------------------------------------------------
 /**
	* Возвращает время окончания срока жизни данных
	*
	* @param string $owner   Владелец данных
	* @param string $key  Идентификатор данных
	* @param string $target  Выбор кэша
	*
	* @return int  Возраст в секундах
	*/
	function expires($owner, $key, $target = 'default')
	{
		if ($target == 'default') $target = $this->default;
		$result = isset($this->caches[$target]) ? $this->caches[$target]->expires("$owner.$key") : 0xffffffff;
		return $result;
	}
	//-----------------------------------------------------------------------------

}

/*************************************************************************************************
 *  Базовый класс подсисетмы кэширования
 *************************************************************************************************/

class EresusCacheSubsystem {
 /* * * * * * * * * * * * * * * * * * * * * * * * *
	* PRIVATE
	* * * * * * * * * * * * * * * * * * * * * * * * */
 /**
	* Размер кэша (всего)
	*
	* @var int
	*
	* @access protected
	*/
	var $size = 0xffffffff;
 /**
	* Размер закэшированных данных (в байтах)
	*
	* @var int
	*
	* @access protected
	*/
	var $used = 0;
 /**
	* Поместить данные в кэш
	*
	* @param string $key    Идентификатор данных
	* @param string $value  Данные
	*
	* @access protected
	* @abstract
	*/
	function data_put($key, $value)
	{
	}
	//-----------------------------------------------------------------------------
 /**
	* Получить данные из кэша
	*
	* @param string $key  Идентификатор данных
	*
	* @return string  Данные из кэша или NULL
	*
	* @access protected
	* @abstract
	*/
	function data_get($key)
	{
		return null;
	}
	//-----------------------------------------------------------------------------
 /**
	* Выкинуть данные из кэша
	*
	* @param string $key  Идентификатор записи
	*
	* @access protected
	* @abstract
	*/
	function data_drop($key)
	{
	}
	//-----------------------------------------------------------------------------
 /**
	* Проиндексировать запись
	*
	* @param string $key       Идентификатор данных
	* @param int    $lifetime  Срок жизни данных (секунды)
	*
	* @access protected
	* @abstract
	*/
	function index_put($key, $lifetime)
	{
	}
	//-----------------------------------------------------------------------------
 /**
	* Получить слудующую устаревшую запись и удалить её из индекса
	*
	* @param bool $force  Значение true заставляет вернуть наиболее старую, но ещё не устаревшую запись
	*
	* @return string  Идентификатор записи
	*
	* @access protected
	* @abstract
	*/
	function index_get($force = false)
	{
		return null;
	}
	//-----------------------------------------------------------------------------
 /**
	* Сериализация данных
	*
	* @param mixed $value
	* @return string
	*
	* @access protected
	*/
	function serialize($value)
	{
		$result = serialize($value);
		return $result;
	}
	//-----------------------------------------------------------------------------
 /**
	* Десериализация данных
	*
	* @param string $value
	* @return mixed
	*
	* @access protected
	*/
	function unserialize($value)
	{
		$result = unserialize($value);
		return $result;
	}
	//-----------------------------------------------------------------------------
 /**
	* Очистка мусора
	*
	* @param int $free  Требуемое количество свободной памяти
	*
	* @access protected
	*/
	function cleanup($free = 0)
	{
		while ($this->free() < $free && !is_null($key = $this->index_get($free > 0)))
			$this->data_drop($key);
	}
	//-----------------------------------------------------------------------------

 /* * * * * * * * * * * * * * * * * * * * * * * * *
	* PUBLIC
	* * * * * * * * * * * * * * * * * * * * * * * * */
 /**
	* Максимальный размер кэша (в килобайтах)
	*
	* Специальные значения:
	* -1 - отключить кэширование
	*  0 - нет ограничения
	*
	* @var int
	* @access public
	*/
	var $limit = 0;
 /**
	* Конструктор
	*
	* @return EresusAbstractCache
	*/
	function EresusCacheSubsystem()
	{
	}
	//-----------------------------------------------------------------------------
 /**
	* Получение количества свободной памяти (в байтах)
	*
	* @return int
	*/
	function free()
	{
		switch ($this->limit) {
			case -1: $result = 0; break;
			case  0: $result = $this->size - $this->used; break;
			default: $result = $this->limit - $this->used;
		}
		return $result;
	}
	//-----------------------------------------------------------------------------
 /**
	* Поместить данные в кэш
	*
	* @param string $key       Идентификатор данных
	* @param mixed  $value     Данные
	* @param int    $lifetime  Срок жизни данных (секундны)
	*/
	function put($key, $value, $lifetime = 0)
	{
		$value = $this->serialize($value);
		$size = strlen($value);
		if ($size > $this->free() && $size < $this->size) $this->cleanup($size);
		if ($size <= $this->free()) {
			$this->index_put($key, $lifetime);
			$this->data_put($key, $value);
		}
	}
	//-----------------------------------------------------------------------------
 /**
	* Получить данные из кэша
	*
	* @param string $key       Идентификатор данных
	*
	* @return mixed
	*/
	function get($key)
	{
		$result = $this->data_get($key);
		$result = $this->unserilize($result);
		return $result;
	}
	//-----------------------------------------------------------------------------
 /**
	* Возвращает время окончания срока жизни данных
	*
	* @param string $key  Идентификатор данных
	* @return int  Возраст в секундах
	*
	* @abstract
	*/
	function expires($key)
	{
		return 0xffffffff;
	}
	//-----------------------------------------------------------------------------
}

/*************************************************************************************************
 *  Кэширование в оперативной памяти
 *************************************************************************************************/

class EresusMemoryCache extends EresusCacheSubsystem {
 /**
	* Хранилище данных
	*
	* @var array
	*/
	var $storage = array();
 /**
	* Индекс данных
	*
	* @var array
	*/
	var $index = array(
		'expires' => array(),
		'keys' => array(),
	);
 /**
	* Поместить данные в кэш
	*
	* @param string $key    Идентификатор данных
	* @param string $value  Данные
	*
	* @access protected
	*/
	function data_put($key, $value)
	{
		$this->storage[$key] = $value;
		$this->used += strlen($value);
	}
	//-----------------------------------------------------------------------------
 /**
	* Получить данные из кэша
	*
	* @param string $key  Идентификатор данных
	*
	* @return string  Данные из кэша или NULL
	*
	* @access protected
	*/
	function data_get($key)
	{
		$result = isset($this->storage[$key]) ? $this->storage[$key] : null;
		return $result;
	}
	//-----------------------------------------------------------------------------
 /**
	* Выкинуть данные из кэша
	*
	* @param string $key  Идентификатор записи
	*
	* @access protected
	*/
	function data_drop($key)
	{
		if (isset($this->storage[$key])) {
			$this->used -= strlen($this->storage[$key]);
			unsert($this->storage[$key]);
		}
	}
	//-----------------------------------------------------------------------------
 /**
	* Проиндексировать запись
	*
	* @param string $key       Идентификатор данных
	* @param int    $lifetime  Срок жизни данных (секунды)
	*
	* @access protected
	*/
	function index_put($key, $lifetime)
	{
		$expires = time() + $lifetime;

		$lb = $pos = 0;
		$ub = count($this->index['expires']) - 1;

		while ($lb <= $ub) {
			$pos = floor(($lb + $ub) / 2);
			if ($expires < $this->index['expires'][$pos]) {
				$lb = $pos + 1;
			} elseif ($expires > $this->index['expires'][$pos]) {
				$ub = $pos - 1;
			} else break;
		}

		if (count($this->index['expires']) && $expires < $this->index['expires'][$pos]) $pos++;

		array_splice($this->index['keys'], $pos, 0, array($key));
		array_splice($this->index['expires'], $pos, 0, array($expires));

		return null;
	}
	//-----------------------------------------------------------------------------
 /**
	* Получить слудующую устаревшую запись и удалить её из индекса
	*
	* @param bool $force  Значение true заставляет вернуть наиболее старую, но ещё не устаревшую запись
	*
	* @return string  Идентификатор записи
	*
	* @access protected
	*/
	function index_get($force = false)
	{
		if ($force) {
			$result = count($this->index['keys']) ? array_pop($this->index['keys']) : null;
			array_pop($this->index['expires']);
		} else {
			$expires = end($this->index['expires']);
			if ($expires !== false && $expires < time()) {
				$result = array_pop($this->index['keys']);
				array_pop($this->index['expires']);
			} else $result = null;
		}
		return $result;
	}
	//-----------------------------------------------------------------------------
 /**
	* Возвращает время окончания срока жизни данных
	*
	* @param string $key  Идентификатор данных
	*
	* @return int  UNIX timestamp
	*/
	function expires($key)
	{
		$index = array_search($key, $this->index['keys']);
		$result = $index !== false ? $this->index['expires'][$index] : false;
		return $result;
	}
	//-----------------------------------------------------------------------------
}

?>