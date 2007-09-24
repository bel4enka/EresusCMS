<?php
/**
* Eresus™ 2
*
* Библиотека для работы с таблицами
*
* @author: Mikhail Krasilnikov <mk@procreat.ru>
* @version: 0.0.1
* @modified: 2007-07-23
*/

class AdminList {
  var $columns = array();
  var $head = array();
  var $body = array();
  /**
  * Устанавливает названия и параметры столбцов
  *
  * @access public
  *
  * @param  string  $text  Заголовок столбца
  * или
  * @param  array   $cell  Описание столбца
  */
  function setHead()
  {
    $this->head = array();
    $items = func_get_args();
    if (count($items)) foreach($items as $item) {
      if (is_string($item)) $this->head[] = array('text' => $item);
      elseif (is_array($item)) $this->head[] = $item;
    }
  }
  //------------------------------------------------------------------------------
  /**
  * Устанавливает параметры столбца
  *
  * Перезаписываются только параметры указанные в $params
  *
  * @access public
  *
  * @param  int    $index  Номер столбца
  * @param  array  $params Описание столбца
  */
  function setColumn($index, $params)
  {
    if (isset($this->columns[$index])) $this->columns[$index] = array_merge($this->columns[$index], $params);
    else $this->columns[$index] = $params;
  }
  //------------------------------------------------------------------------------
  /**
  * Добавляет строку в таблицу
  *
  * @access public
  *
  * @param  array  $cells  Ячейки строки
  */
  function addRow($cells)
  {
    for($i=0; $i < count($cells); $i++) {
      if (!is_array($cells[$i])) $cells[$i] = array('text' => $cells[$i]);
      if (!isset($cells[$i]['text']) && isset($cells[$i][0])) {
        $cells[$i]['text'] = $cells[$i][0];
        unset($cells[$i][0]);
      }
    }
    $this->body[] = $cells;
  }
  //------------------------------------------------------------------------------
  /**
  * Добавляет строки в таблицу
  *
  * @access public
  *
  * @param  array  $rows  Строки
  */
  function addRows($rows)
  {
    for($i=0; $i < count($rows); $i++) $this->addRow($rows[$i]);
  }
  //------------------------------------------------------------------------------
  /**
  * Отрисовывает ячейку таблицы
  *
  * @access  private
  */
  function renderCell($tag, $cell)
  {
    $style = '';
    $text= isset($cell['text']) ? $cell['text'] : '';
    if (isset($cell['href'])) $text = '<a href="'.$cell['href'].'">'.$text.'</a>';
    if (isset($cell['align'])) $style .= 'text-align: '.$cell['align'].';';
    if (isset($cell['style'])) $style .= $cell['style'];
    $result = '<'.$tag.(empty($style)?'':" style=\"$style\"").'>'.$text.'</'.$tag.'>';
    return $result;
  }
  //------------------------------------------------------------------------------
  /**
  * Отрисовывает таблицу
  *
  * @return  string  HTML-код таблицы
  */
  function render()
  {
    $thead = '';
    foreach($this->head as $cell) $thead .= $this->renderCell('th', $cell);
    $tbody = array();
    foreach($this->body as $row) {
      $cells = '<tr>';
      foreach($row as $cell) $cells .= $this->renderCell('td', $cell);
      $cells .= '</tr>';
      $tbody[] = $cells;
    }
    $table = '<table class="admList">';
    $table .= '<tr>'.$thead.'</tr>';
    $table .= implode("\n", $tbody);
    $table .= '</table>';
    return $table;
  }
  //------------------------------------------------------------------------------
}

?>