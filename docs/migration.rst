Переход на 3.01 с 3.00
======================

Для верстальщиков
-----------------

* В :doc:`шаблонах страниц </markup/page-templates>` теперь можно использовать Dwoo.
* Библиотека `jQuery <http://jquery.com/>`_ обновлена до 1.10.2
* Плагин `jquery-cookie <https://github.com/carhartl/jquery-cookie>`_ обновлён до 1.3.1
* Библиотека `jQuery UI <http://jqueryui.com/>`_ обновлена до 1.10.3
* Библиотека `Webshim <https://github.com/aFarkas/webshim/>`_ обновлена до 1.10.10
* Библиотека Webshim в АИ теперь доступна всегда (т. е. её не надо больше подключать самостоятельно).
* Библиотека `Botobor <https://github.com/mekras/botobor>`_ до обновлена 0.4.0

Для разработчиков расширений
----------------------------

Удалено
^^^^^^^

* Удалён псевдоним ``Accounts`` класса `EresusAccounts <api/classes/EresusAccounts.html>`_.
* Удалены классы PHP и System, используйте одноимённые методы класса
  `Eresus_Kernel <api/classes/Eresus_Kernel.html>`_.
* Удалён класс FS, используйте методы класса `Eresus_FS_Tool <api/classes/Eresus_FS_Tool.html>`_.
* Удалены классы EresusFsFileNotExistsException, EresusFsPathNotExistsException,
  EresusFsRuntimeException, EresusPropertyNotExistsException

Изменено
^^^^^^^^

* Класс Plugin переименован в `Eresus_Plugin <api/classes/Eresus_Plugin.html>`_. Для обратной
  совместимости имя Plugin оставлено как псевдоним к Eresus_Plugin.
* Функция ``eresus_log`` объявлена устаревшей, вместо неё следует использовать
  `Eresus_Kernel::log <api/classes/Eresus_Kernel.html#method_log>`_.
* Библиотека Webshim в АИ теперь доступна всегда (т. е. её не надо больше подключать самостоятельно).
* Свойство ``Eresus_Plugin::$name`` объявлено устаревшим, вместо него следует использовать метод
  `Eresus_Plugin::getName <api/classes/Eresus_Plugin.html#method_getName>`_.
* Свойство ``TClientUI::$template`` сделано приватным. Для чтения его значения используйте
  `TClientUI::getTemplateName <api/classes/TClientUI.html#method_getTemplateName>`_
* Класс `WebPage <api/classes/WebPage.html>`_ унаследован от
  `Eresus_CMS_Page <api/classes/Eresus_CMS_Page.html>`_ и сделан абстрактным.

Обновлено
^^^^^^^^^

* `jQuery <http://jquery.com/>`_ до 1.10.2
* `jquery-cookie <http://jquery.com/>`_ до 1.3.1
* `jQuery UI <http://jqueryui.com/>`_ до 1.10.3
* `Webshim <https://github.com/aFarkas/webshim/>`_ до 1.10.10
* `Botobor <https://github.com/mekras/botobor>`_ до 0.4.0

Добавлено
^^^^^^^^^

* :doc:`Новый механизм работы с шаблонами </dev/guide/templates>`
* `Eresus_Kernel::log <api/classes/Eresus_Kernel.html#method_log>`_
* `Eresus_CMS_Exception_NotFound <api/classes/Eresus_CMS_Exception_NotFound.html>`_
* `Eresus_Plugin_Component <api/classes/Eresus_Plugin_Component.html>`_
* `Eresus_Plugin_Controller_Abstract <api/classes/Eresus_Plugin_Controller_Abstract.html>`_
* `Eresus_Plugin_Controller_Admin <api/classes/Eresus_Plugin_Controller_Admin.html>`_
* `Eresus_Plugin_Controller_Admin_Content <api/classes/Eresus_Plugin_Controller_Admin_Content.html>`_
* `Eresus_Plugin_Controller_Client <api/classes/Eresus_Plugin_Controller_Client.html>`_
* `Eresus_Plugin_Controller_Client_Content <api/classes/Eresus_Plugin_Controller_Client_Content.html>`_
* `Eresus_Template_Service <api/classes/Eresus_Template_Service.html>`_
* Метод `Eresus_Plugin::getCodeDir <api/classes/Eresus_Plugin.html#method_getCodeDir>`_
* Метод `Eresus_Plugin::getDataDir <api/classes/Eresus_Plugin.html#method_getDataDir>`_
* Метод `Eresus_Plugin::getStyleDir <api/classes/Eresus_Plugin.html#method_getStyleDir>`_
* Метод `TClientUI::getTemplateName <api/classes/TClientUI.html#method_getTemplateName>`_
* Метод `TClientUI::setTemplate <api/classes/TClientUI.html#method_setTemplate>`_
* Метод `Template::loadFromFile <api/classes/Template.html#method_loadFromFile>`_
* Метод `Template::getSource <api/classes/Template.html#method_getSource>`_
* Метод `Template::setSource <api/classes/Template.html#method_setSource>`_
* Метод `Templates::load <api/classes/Templates.html#method_load>`_
