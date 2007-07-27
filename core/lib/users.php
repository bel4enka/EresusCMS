<?php
/**
* Eresus™ 2
*
* Библиотека для работы с учётными записями пользователей
*
* @author: Mikhail Krasilnikov <mk@procreat.ru>
* @version: 0.0.1
* @modified: 2007-07-22
*/

class TUsers {
  var $table = 'users';
  var $cache;
  /**
  * Возвращает учётную запись
  *
  * @access  public
  *
  * @param  int  $id  Идентификатор пользователя, если не указан, используется текущий
  *
  * @return  array  Учётная запись
  */
  function get($id)
  {
    global $db;
    
    if (!$this->fieldset) $this->fieldset = $db->fields($this->table);
    if ($force || !$this->index) {
      $items = $db->select($this->table, '', '`id`', false, '`id`,`owner`');
      if ($items) {
        $this->index = array();
        foreach($items as $item) $this->index[$item['owner']][] = $item['id'];
      }
    }
  }
  //------------------------------------------------------------------------------
  /**
  * Создаёт список ID разделов определённой ветки
  *
  * @access  private
  *
  * @param  int  $owner  ID корневого раздела ветки
  *
  * @return  array  Список ID разделов
  */
  function brunch_ids($owner)
  {
    $result = array();
    if (isset($this->index[$owner])) {
      $result = $this->index[$owner];
      foreach($result as $section) $result = array_merge($result, $this->brunch_ids($section));
    }
    return $result;
  }
  //------------------------------------------------------------------------------
  /**
  * Выбирает разделы определённой ветки
  *
  * @access  public
  *
  * @param  int  $owner   Идентификатор корневого раздела ветки
  * @param  int  $access  Минимальный уровень доступа
  *
  * @return  array  Описания разделов
  */
  function brunch($owner, $access = GUEST)
  {
    global $db;
    
    $result = array();
    # Создаём индекс
    if (!$this->index) $this->index();
    # Находим ID разделов ветки.
    $set = $this->brunch_ids($owner);
    if (count($set)) {
      # Читаем из кэша
      for($i=0; $i < count($set); $i++) if (isset($this->cache[$set[$i]])) {
        $result[] = $this->cache[$set[$i]];
        array_splice($set, $i, 1);
        $i--;
      }
      if (count($set)) {
        $fieldset = implode(',', array_diff($this->fieldset, array('content')));
        # Читаем из БД
        $set = implode(',', $set);
        $items = $db->select($this->table, "FIND_IN_SET(`id`, '$set') AND `access` >= $access", 'position', false, $fieldset);
        for($i=0; $i<count($items); $i++) {
          $this->cache[$items[$i]['id']] = $items[$i];
          $result[] = $items[$i];
        }
      }
    }
    return $result;
  }
  //------------------------------------------------------------------------------
  /**
  * Возвращает дочерние разделы указанного
  *
  * @access public
  *
  * @param  int  $owner   Идентификатор корневого раздела ветки
  * @param  int  $access  Минимальный уровень доступа
  *
  * @return  array  Описания разделов
  */
  function children($owner, $access = GUEST)
  {
    $items = $this->brunch($owner, $access);
    $result = array();
    for($i=0; $i<count($items); $i++) if ($items[$i]['owner'] == $owner) $result[] = $items[$i];
    return $result;
  }
  //------------------------------------------------------------------------------
}

?>