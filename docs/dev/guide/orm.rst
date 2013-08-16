ORM
===

Подсистема ORM предоставляет средства для работы с таблицами БД как с хранилищами объектов.

Создание классов моделей
------------------------

Для каждого вида моделей, который вы хотите использовать в своём приложении, вам надо создать два
класса:

#. класс самой модели (сущности) в файле ``ваш_модуль/classes/Entity/ИмяСущности.php``;
#. класс её таблицы БД в файле ``ваш_модуль/classes/Entity/Table/ИмяСущности.php``.

Например, для модуля Articles и сущности Article надо создать такие классы:

**articles/classes/Entity/Article.php**

.. code-block:: php

   <?php
   class Articles_Entity_Article extends Eresus_ORM_Entity
   {
   }

**articles/classes/Entity/Table/Article.php**

.. code-block:: php

   <?php
   class Articles_Entity_Table_Article extends Eresus_ORM_Table
   {
       public function setTableDefinition()
       {
           $this->setTableName('articles');
           $this->hasColumns(array(
               'id' => array(
                   'type' => 'integer',
                   'unsigned' => true,
                   'autoincrement' => true,
               ),
               'section' => array(
                   'type' => 'integer',
                   'unsigned' => true,
               ),
               'active' => array(
                   'type' => 'boolean',
               ),
               'position' => array(
                   'type' => 'integer',
                   'unsigned' => true,
               ),
               'posted' => array(
                   'type' => 'timestamp',
               ),
               'title' => array(
                   'type' => 'string',
                   'length' => 255,
               ),
               'text' => array(
                   'type' => 'string',
               ),
           ));
           $this->index('admin_idx', array('fields' => array('section', 'position')));
           $this->index('cl_position_idx', array('fields' => array('section', 'active', 'position')));
           $this->index('cl_date_idx', array('fields' => array('section', 'active', 'posted')));
       }
   }

Это минимальная необходимая конфигурация.

В потомке `Eresus_ORM_Table <../../api/classes/Eresus_ORM_Table.html>`_ надо перекрыть метод
``setTableDefinition`` и описать в нём таблицу: имя, поля и индексы.

.. hint::

   Описание структуры таблиц в модуле ORM сходно с
   `описанием моделей в Doctrine 1.x <http://docs.doctrine-project.org/projects/doctrine1/en/latest/en/manual/defining-models.html>`_.

Имя таблицы задаётся при помощи метода
`Eresus_ORM_Table::setTableName <../../api/classes/Eresus_ORM_Table.html#method_setTableName>`_.

При помощи метода
`Eresus_ORM_Table::hasColumns <../../api/classes/Eresus_ORM_Table.html#method_hasColumns>`_
задаётся список полей.

При помощи метода `Eresus_ORM_Table::index <../../api/classes/Eresus_ORM_Table.html#method_index>`_
задаются индексы.

Геттеры и сеттеры
-----------------

Все поля, объявленные в классе таблицы, будут доступны как свойства в экземплярах класса сущности.
Однако разработчик может добавлять сущности виртуальные свойства или же переопределять поведение
существующих при помощи геттеров и сеттеров.

Геттер — это защищённый или публичный метод, имя которого начинается с префикса «get», за которым
следует имя свойства. Метод должен возвращать значение свойства.

Сеттер — это защищённый или публичный метод, имя которого начинается с префикса «set», за которым
следует имя свойства. Метод должен принимать в качестве аргумента новое значение свойства.

**articles/classes/Entity/Article.php**

.. code-block:: php

   <?php
   class Articles_Entity_Article extends Eresus_ORM_Entity
   {
       /*
        * Изменяем тип свойства «posted» с int на string
        */
       protected function getPosted()
       {
           return $this->getProperty('posted')->format('d.m.y H:i:s');
       }
       protected function setPosted($value)
       {
           $this->setProperty('posted', new DateTime($value));
       }
   }

В геттерах и сеттерах для работы со свойствами следует использовать методы
`getProperty <../../api/classes/Eresus_ORM_Table.html#method_getProperty>`_ и
`setProperty <../../api/classes/Eresus_ORM_Table.html#method_setProperty>`_.

Создание и удаление таблиц
--------------------------

.. important::
   Все таблицы, описанные в ``classes/Entity/Table``, при установке модуля создаются автоматически.
   Также автоматически они будут удалены при удаления модуля.

Для создания и удаления таблиц можно воспользоваться методами:
`Eresus_ORM_Driver_Abstract::createTable <../../api/classes/Eresus_ORM_Driver_Abstract.html#method_createTable>`_
и
`Eresus_ORM_Driver_Abstract::dropTable <../../api/classes/Eresus_ORM_Driver_Abstract.html#method_dropTable>`_:

.. code-block:: php

   <?php
   // $this — объект модуля расширения
   $table = Eresus_ORM::getTable($this, 'Article');
   $driver = Eresus_ORM::getDriver();
   $driver->createTable($table);

.. code-block:: php

   <?php
   // $this — объект модуля расширения
   $table = Eresus_ORM::getTable($this, 'Article');
   $driver = Eresus_ORM::getDriver();
   $driver->dropTable($table);

Получение объектов таблиц
-------------------------

Чтобы извлекать объекты из таблицы БД, помещать туда новые объекты, изменять или удалять имеющиеся,
нужно получить экземпляр класса этой таблицы. Подсистема ORM для этого предоставляет специальный
статический метод —
`Eresus_ORM::getTable <../../api/classes/Eresus_ORM.html#method_dropgetTable>`_.

.. code-block:: php

   <?php
   $table = Eresus_ORM::getTable($this, 'Article');

В примере $this — экземпляр основного класса плагина (articles), а «Article» — имя сущности. В
$table попадёт экземпляр класса Articles_Entity_Table_Article.

Объект для каждой таблицы всегда создаётся в единственном экземпляре и при многократных вызовах
всегда возвращается этот объект-одиночка.

Добавление объекта в таблицу
----------------------------

Добавление нового объекта в таблицу состоит из трёх шагов:

1. создание нового экземпляра класса сущности (т. е. унаследованного от Eresus_ORM_Entity);
2. установка свойств нового объекта;
3. помещение объекта в таблицу.

.. code-block:: php

   <?php
   // создание нового экземпляра класса сущности
   $article = new Articles_Entity_Article($this); // $this — объект основного класса модуля

   // установка свойств нового объекта
   $article->section = arg('section', 'int');
   $article->active = true;
   $article->posted = time();
   $article->block = arg('block', 'int');
   $article->title = arg('title');
   $article->text = arg('text');

   // помещение объекта в таблицу
   Eresus_ORM::getTable($this, 'Article')->persist($article);
   // или другой вариант
   $article->getTable()->persist($article);


Получение объектов из таблицы
-----------------------------

Получение одного объекта
^^^^^^^^^^^^^^^^^^^^^^^^

Для получения из таблицы одного объекта по его идентификатору можно использовать метод
`Eresus_ORM_Table::find() <../../api/classes/Eresus_ORM_Table.html#method_find>`_:

.. code-block:: php

   <?php
   $table = Eresus_ORM::getTable($plugin, 'Article');
   $article = $table->find(123);


Получение списка объектов
^^^^^^^^^^^^^^^^^^^^^^^^^

Безусловная выборка
"""""""""""""""""""

Для получения всех объектов из таблицы можно использовать метод
`Eresus_ORM_Table::findAll() <../../api/classes/Eresus_ORM_Table.html#method_findAll>`_:

.. code-block:: php

   <?php
   $table = Eresus_ORM::getTable($plugin, 'Article');
   $articles = $table->findAll();
   // или только часть этого списка
   $articles = $table->findAll(10, 20);


Выборка по условию
""""""""""""""""""

Также можно выбирать только объекты, удовлетворяющие определённым условиям при помощи методов
`Eresus_ORM_Table::createSelectQuery() <../../api/classes/Eresus_ORM_Table.html#method_createSelectQuery>`_
и
`Eresus_ORM_Table::loadFromQuery() <../../api/classes/Eresus_ORM_Table.html#method_loadFromQuery>`_:

.. code-block:: php

   <?php
   $table = Eresus_ORM::getTable($plugin, 'Article');

   $q = $table->createSelectQuery();
   // Только статьи, привязанные к текущему разделу сайта
   $q->where($q->expr->eq('section', $q->bindValue($GLOBALS['page']->id, null, PDO::PARAM_INT)));
   $articles = $table->loadFromQuery($q);

