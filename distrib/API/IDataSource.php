<?php
/**
* Eresus™ 2, Интерфейс СУБД
*
* Этот класс описывает интерфейс к источнику данных (например к базе данных),
* используемый Eresus.
* Предполагается что данные в источнике разбиты по таблицам и работа с ними
* может производиться на SQL-подобном языке. 
*
* @author: Mikhail Krasilnikov <mk@procreat.ru>
* @modified: 2007-07-25
*/

class IDataSource {
  /**
  * Если TRUE (по умолчанию), в случае ошибки скрипт будет прерван и показано сообщение об ошибке
  *
  * @var  bool  $display_errors  
  */
  var $display_errors = true;

  /**
  * Открывает соединение с сервером данных и выбирает источник
  *
  * @param  string  $server    Сервер данных
  * @param  string  $username  Имя пользователя для доступа к серверу
  * @param  string  $password  Пароль пользователя
  * @param  string  $source    Имя источника данных
  * @param  string  $prefix    Префикс для имён таблиц. По умолчанию ''
  *
  * @return  bool  Результат соединения
  */
  function init($server, $username, $password, $source, $prefix='')

  /**
  * Выполняет запрос к источнику
  *
  * @param  string  $query    Запрос в формате источника
  *
  * @return  mixed  Результат запроса. Тип зависит от источника, запроса и результата
  */
  function query($query)
  /**
  * Выполняет запрос к источнику и возвращает ассоциативный массив значений
  *
  * @param  string  $query    Запрос в формате источника
  *
  * @return  array|bool  Ответ в виде массива или FALSE в случае ошибки
  */
  function query_array($query)

  /**
  * Производит выборку данных из источника
  *
  * @param  string   $tables    Список таблиц из которых проводится выборка
  * @param  string   $condition
  * @param  string   $order
  * @param  bool     $desc
  * @param  string   $fields
  * @param  integer  $rows
  * @param  integer  $offset
  * @param  string   $group
  * @param  bool     $distinct
  *
  * @return  array|bool  Выбранные элементы в виде массива или FALSE в случае ошибки
  */
  function select($tables, $condition = '', $order = '', $desc = false, $fields = '', $rows = 0, $offset = 0, $group = '', $distinct = false)

  /**
  * Вставка элементов в источник
  *
  * @param  string  $table  Таблица, в которую надо вставтиь элемент
  * @param  array   $item   Ассоциативный массив значений
  *
  * @return  mixed  Результат выполнения операции
  */
  function insert($table, $item)

  function update($table, $set, $condition)
  # Выполняет запрос UPDATE к базе данных используя метод query().
  #  $table - таблица, в которую надо внестит изменения
  #  $set - изменяемые значения
  #  $condition - условия для изменения

  function delete($table, $condition)
  # Выполняет запрос DELETE к базе данных используя метод query().
  #  $table - таблица, из которой требуется удалить записи
  #  $condition - признаки удаляемых записей

  function fields($table)
  # Возвращает список полей таблицы
  #  $table - таблица, для которой надо получить список полей

  function selectItem($table, $condition, $fields = '')

  function updateItem($table, $item, $condition)

  function count($table, $condition='', $group='', $rows=false)
  # Возвращает количество записей в таблице используя метод query().
  #  $table - таблица, для которой требуется посчитать кол-во записей

  function getInsertedID()

  function tableStatus($table, $param='')
}
?>