Добавление пункта в меню "Расширения"
=====================================

Для добавления собственного пункта меню "Расширения" в АИ, надо выполнить три действия:

**1. Установить обработчик события adminOnMenuRender**

.. code-block:: php

   <?php
   /**
    * Конструктор
    */
   public function __construct()
   {
     parent::__construct();
     $this->listenEvents('adminOnMenuRender');
   }


**2. Определить метод adminOnMenuRender**

.. code-block:: php

   <?php
   /**
    * Добавляет пункт "Мой плагин" меню "Расширения"
    *
    * @return void
    */
   public function adminOnMenuRender()
   {
     Eresus_Kernel::app()->getPage()->addMenuItem(admExtensions, array(
       'access'  => EDITOR,
       'link'  => $this->getName(),
       'caption'  => $this->title,
       'hint'  => $this->description
     ));
   }


**3. Определить метод adminRender**

.. code-block:: php

   <?php
   /**
    * Вывод интерфейса
    *
    * @return string  HTML
    */
   public function adminRender()
   {
     return '...';
   }
