Инсталляция
===========

Чтобы выполнить какие-либо действия при инсталляции плагина, необходимо переопределить метод
`Eresus_Plugin::install() <../../api/classes/Eresus_Plugin.html#install>`_.

.. attention::
   Необходимо не забывать вызывать родительский метод. При этом обычно родительский ``install``
   вызывается до собственных действий:

.. code-block:: php

   <?php
   /**
    * Тестовый плагин
    * @package Demo
    */
   class MyPlugin extends Eresus_Plugin
   {
     ...
     /**
      * Действия при установке плагина
      * @return void
      */
     public function install()
     {
       parent::install();

       // Необходимые действия
     }
     ...
   }

.. hint::
   Для создания директорий данных плагина рекомендуется метод
   `Eresus_Plugin::mkdir() <../../api/classes/Eresus_Plugin.html#mkdir>`_.

