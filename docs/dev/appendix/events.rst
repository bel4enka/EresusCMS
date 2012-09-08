Справочник событий
==================

adminOnMenuRender
-----------------

Генерируется в методе `Eresus_AdminUI::render() <../../api/Eresus/Eresus_AdminUI.html#render>`_. после построения контента страницы.

clientBeforeSend
----------------

Генерируется в методе `Eresus_ClientUI::render() <../../api/Eresus/Eresus_ClientUI.html#render>`_ непосредственно перед отправкой страницы браузеру. Обработчику передаётся строка, содержащая код страницы.

Пример обработчика:

.. code-block:: php

   <?php
   /**
    * @param string $html  документ HTML, который отправляется браузеру
    * @return string  документ HTML, который надо отправить браузеру
    */
   public function clientBeforeSend($html)
   {
     // Необходимые действия
     return $html;
   }

clientOnContentRender
---------------------

Генерируется в методе `Eresus_ClientUI::render() <../../api/Eresus/Eresus_ClientUI.html#render>`_, после отрисовки контента и загрузки шаблона страницы. Обработчику передаётся строка, содержащая контент страницы.

clientOnPageRender
------------------

Генерируется в методе `Eresus_ClientUI::render() <../../api/Eresus/Eresus_ClientUI.html#render>`_, после подстановки контента в шаблон страницы. Обработчику передаётся строка, содержащая код страницы.

clientOnStart
-------------

Генерируется в методе `Eresus_ClientUI::init() <../../api/Eresus/Eresus_ClientUI.html#init>`_, сразу после загрузки плагинов.

clientOnURLSplit
----------------

Генерируется в методе `Eresus_ClientUI::init() <../../api/Eresus/Eresus_ClientUI.html#init>`_ при разборе URL запроса (после ``clientOnStart``)

Событие генерируется для каждой виртуальной директории в адресе, которая соответствует разделу сайта. Обработчику передаётся два аргумента:

* array **$section** --- раздел сайта, которому соответствует обрабатываемая виртуальная директория
* string **$url** --- путь к разделу $section относительно корня сайта

