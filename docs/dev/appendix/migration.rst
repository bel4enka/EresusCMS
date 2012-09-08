Переход на 3.01 с 3.00
======================

Удалено
-------

Классы
^^^^^^

- TPlugin, TContentPlugin, TListContentPlugin. Вместо них следует использовать классы
  Eresus_Extensions_Plugin и Eresus_Extensions_ContentPlugin.
- EresusSourceParseException. Вместо него вбрасывается DomainException.

Функции
^^^^^^^

- form — краткая форма для обращения к Eresus_UI_Admin_ArrayForm
- useLib — все классы теперь загружаются автоматически

Изменено
--------

Переименованы классы
^^^^^^^^^^^^^^^^^^^^

- AdminList в Eresus_UI_Admin_List (использовать useLib для его подключения больше не надо)
- AdminUITheme в Eresus_Admin_Theme
- Plugin в Eresus_Extensions_Plugin
- ContentPlugin в Eresus_Extensions_ContentPlugin
- EresusAccounts в Eresus_Accounts
- EresusCollection в Eresus_Helpers_Collection
- Plugins в Eresus_Extensions_Registry
- EresusConnector в Eresus_Extensions_Connector
- EresusExtensions в Eresus_Extensions_VendorRegistry
- WebServer в Eresus_WebServer
- PaginationHelper в Eresus_UI_Pagination
- WebPage в Eresus_WebPage
- i18n в Eresus_i18n
- EresusForm в Eresus_UI_Form
- Templates в Eresus_Templates
- Sections в Eresus_Sections
- Form в Eresus_UI_Admin_ArrayForm

glib
^^^^

Функции библиотеки glib перенесены в старое ядро (kernel-legacy).
