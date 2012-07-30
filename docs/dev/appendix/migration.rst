Переход на 3.00 с 2.16
======================

Переход на UTF-8
----------------

Основное отличие 3.00 от 2.16 — полный переход на UTF-8. Это означает, что все файлы, содержащие текст, предназначенный для вывода пользователю, должны быть в кодировке UTF-8.

Удалено
-------

* функция ``dbShiftItems``
* свойство ``Eresus::$https``
* методы ``Eresus::isWin32``, ``Eresus::isUnix``, ``Eresus::isMac``, ``Eresus::isModule``, ``Eresus::isCgi``, ``Eresus::isCli``. Вместо них используйте соответственно `System::isWindows() <../../api/Core/System.html#isWindows>`_, `System::isUnixLike() <../../api/Core/System.html#isUnixLike>`_, `System::isMac() <../../api/Core/System.html#isMac>`_, `PHP::isModule() <../../api/Core/PHP.html#isModule>`_, `PHP::isCGI() <../../api/Core/PHP.html#isCGI>`_, `PHP::isCLI() <../../api/Core/PHP.html#isCLI>`_.
* функция ``FormatDate``. Вместо неё используйте возможности шаблонизатора.
* функция ``useClass``. Вместо неё теперь используется автозагрузка классов.

Изменено
--------

Обновлены
^^^^^^^^^

* jQuery до 1.7.2
* jQuery.Cookie до 1.1
* jQuery UI до 1.8.21
* Modernizr до 2.6.1
* Webshim до 1.8.12
* TinyMCE до 3.5.6

Добавлено
---------

Eresus_CMS::getLegacyKernel()
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Метод `Eresus_CMS::getLegacyKernel() <../../api/Eresus/Eresus_CMS.html#getLegacyKernel>`_ возвращает экземпляр класса `Eresus <../../api/Eresus/Eresus.html>`_. Его следует использовать вместо глобальной переменной ``$Eresus``. Пример:

.. code-block:: php

   <?php
   $sections = Eresus_CMS::getLegacyKernel()->sections;


Eresus_CMS::getPage()
^^^^^^^^^^^^^^^^^^^^^

Метод `Eresus_CMS::Page() <../../api/Eresus/Eresus_CMS.html#getPage>`_ возвращает экземпляр класса `TClientUI <../../api/Eresus/TClientUI.html>`_ или `TAdminUI <../../api/Eresus/TAdminUI.html>`_. Его следует использовать вместо глобальной переменной ``$page``. Пример:

.. code-block:: php

   <?php
   $pageId = Eresus_Kernel::app()->getPage()->id;

