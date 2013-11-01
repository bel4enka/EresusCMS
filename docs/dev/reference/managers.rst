Менеджеры
=========

Менеджеры — это специальные компоненты, предназначенные для управления объектами определённого
класса. В Eresus есть следующие менеджеры:

* `Eresus\Templating\TemplateManager <../../api/classes/Eresus.Templating.TemplateManager.html>`_ —
  менеджер шаблонов.
* `Eresus\Security\AccountManager <../../api/classes/Eresus.Security.AccountManager.html>`_ —
  менеджер учётных записей пользователей.
* `Eresus\Plugins\PluginManager <../../api/classes/Eresus.Plugins.PluginManager.html>`_ —
  менеджер модулей расширения.
* `Eresus\Sections\SectionManager <../../api/classes/Eresus.Sections.SectionManager.html>`_ —
  менеджер разделов сайта.

Все менеджеры доступны через :doc:`контейнер служб <../appendix/container>`, создавать их экземпляры
самостоятельно не надо.
