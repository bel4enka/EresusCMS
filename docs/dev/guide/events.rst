События
=======

Плагины могут выполнять некоторые действия при возникновении определённых событий. Для этого надо:

#. Написать метод-обработчик
#. Зарегистрировать обработчик события

Метод-обработчик
----------------

Метод-обработчик — это открытый метод, имя которого совпадает с именем события
(см. :doc:`../appendix/events`).

Пример
^^^^^^

.. code-block:: php

   <?php
   class MyPlugin extends Eresus_Plugin
   {
     /**
      * Обработчик события "clientOnStart"
      */
     public function clientOnStart()
     {
       // Необходимые действия
     }

Регистрация обработчика
-----------------------

Регистрация выполняется при помощи метода
`Eresus_Plugin::listenEvents() <../../api/classes/Eresus_Plugin.html#listenEvents>`_. Обычно метод
вызывается в конструкторе, но он может также вызываться и в любом другом месте.

Пример 1
^^^^^^^^

.. code-block:: php

   <?php
   class MyPlugin extends Eresus_Plugin
   {
     /**
      * Конструктор
      */
     public function __construct()
     {
       parent::__construct();
       $this->listenEvents('clientOnStart');
     }

Пример 2
^^^^^^^^

В ``listenEvents`` можно указывать несколько событий, которые должен слушать плагин:

.. code-block:: php

   <?php
   class MyPlugin extends Eresus_Plugin
   {
     /**
      * Конструктор
      */
     public function __construct()
     {
       parent::__construct();
       $this->listenEvents('clientOnStart', 'clientOnPageRender');
     }
