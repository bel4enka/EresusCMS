Переход на 3.01 с 3.00
======================

Удалено
-------

* Псевдоним ``Accounts`` класса ``EresusAccounts``.

Изменено
--------

* Класс Plugin переименован в Eresus_Plugin. Для обратной совместимости имя Plugin оставлено как
псевдоним к Eresus_Plugin.

Eresus_Plugin
^^^^^^^^^^^^^

* Свойство ``name`` объявлено устаревшим, вместо него следует использовать метод
`Eresus_Plugin::getName <../../api/classes/Eresus_Plugin.html#method_getName>`_.

TClientUI
^^^^^^^^^

* Свойство ``template`` сделано приватным. Для чтения его значения используйте
  `TClientUI::getTemplateName <../../api/classes/TClientUI.html#method_getTemplateName>`_

Обновлены
^^^^^^^^^

* `Botobor <https://github.com/mekras/botobor>`_ до 0.4.0

Добавлено
---------

* :doc:`Новый механизм работы с шаблонами<../guide/templates>`
* `Eresus_CMS_Excpetion_NotFound <../../api/classes/Eresus_CMS_Excpetion_NotFound.html`_
* `Eresus_Plugin_Component <../../api/classes/Eresus_Plugin_Component.html`_
* `Eresus_Plugin_Controller_Abstract <../../api/classes/Eresus_Plugin_Controller_Abstract.html`_
* `Eresus_Plugin_Controller_Admin <../../api/classes/Eresus_Plugin_Controller_Admin.html`_
* `Eresus_Plugin_Controller_Admin_Content <../../api/classes/Eresus_Plugin_Controller_Admin_Content.html`_
* `Eresus_Plugin_Controller_Client <../../api/classes/Eresus_Plugin_Controller_Client.html`_
* `Eresus_Plugin_Controller_Client_Content <../../api/classes/Eresus_Plugin_Controller_Client_Content.html`_
* `Eresus_Template_Service <../../api/classes/Eresus_Template_Service.html`_

TClientUI
^^^^^^^^^

* Метод `getTemplateName <../../api/classes/TClientUI.html#method_getTemplateName>`_
* Метод `setTemplate <../../api/classes/TClientUI.html#method_setTemplate>`_

Template
^^^^^^^^

* Метод `loadFromFile <../../api/classes/Template.html#method_loadFromFile>`_
* Метод `getSource <../../api/classes/Template.html#method_getSource>`_
* Метод `setSource <../../api/classes/Template.html#method_setSource>`_

Templates
^^^^^^^^

* Метод `load <../../api/classes/Templates.html#method_load>`_
