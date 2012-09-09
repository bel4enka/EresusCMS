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
- img
- useLib — все классы теперь загружаются автоматически
- gettime
- fileread
- filewrite
- filedelete

Константы
---------

- httpPath (используйте Eresus_HTTP_Request::getBasePath())
- httpHost (используйте Eresus_HTTP_Request::getHost())
- httpRoot (используйте Eresus_CMS::getLegacyKernel()->root)
- styleRoot (используйте Eresus_CMS::getLegacyKernel()->style)
- dataRoot (используйте Eresus_CMS::getLegacyKernel()->data)
- cookieHost
- cookiePath
- ERESUS_CMS_DEBUG
- KERNELNAME
- KERNELDATE


Изменено
--------

Переименованы классы
^^^^^^^^^^^^^^^^^^^^

- Plugin в Eresus_Extensions_Plugin
- ContentPlugin в Eresus_Extensions_ContentPlugin
- EresusConnector в Eresus_Extensions_Connector
- TAdminUI в Eresus_AdminUI
- TClientUI в Eresus_ClientUI
- Templates в Eresus_Templates
- EresusForm в Eresus_UI_Form
- Sections в Eresus_Sections
- Form в Eresus_UI_Admin_ArrayForm
- PaginationHelper в Eresus_UI_Pagination
- EresusAccounts в Eresus_Accounts
- EresusCollection в Eresus_Helpers_Collection
- AdminList в Eresus_UI_Admin_List
- AdminUITheme в Eresus_Admin_Theme
- Plugins в Eresus_Extensions_Registry
- EresusExtensions в Eresus_Extensions_VendorRegistry
- WebServer в Eresus_WebServer
- WebPage в Eresus_WebPage
- i18n в Eresus_i18n

glib
^^^^

Функции библиотеки glib перенесены в старое ядро (kernel-legacy).

arg()
^^^^^

Функция arg теперь берёт данные из Eresus_Kernel::get('request')


Добавлено
---------

Классы
^^^^^^

- Eresus_HTTP_Request — обёртка для Symfony\Component\HttpFoundation\Request.