Переход на 3.01 с 3.00
======================

.. attention::
   Минимальная требуемая версия PHP теперь 5.3.3.

.. attention::
   Изменился формат файла настроек! Смотрите ``cfg/main.template.php``.

Удалено
-------

Классы
^^^^^^

- TPlugin, TContentPlugin, TListContentPlugin. Вместо них следует использовать классы
  Eresus\\CmsBundle\\Extensions\\Plugin и Eresus\\CmsBundle\\Extensions\\ContentPlugin.
- EresusSourceParseException. Вместо него вбрасывается DomainException.
- HttpResponse, HttpHeaders, HttpMessage, HTTP. См. :doc:`/dev/guide/responses`.

Свойства и методы
^^^^^^^^^^^^^^^^^

- Eresus::$sections удалено, используйте Eresus_Kernel::get('sections')
- TAdminUI::box()
- TAdminUI::window()

Функции
^^^^^^^

- form — краткая форма для обращения к Eresus\\CmsBundle\\UI\\Admin\\ArrayForm
- useLib — все классы теперь загружаются автоматически
- FatalError — используйте исключения
- dbReorderItems
- img
- gettime
- fileread
- filewrite
- filedelete
- HttpAnswer
- SendXML
- __macroConst
- __macroVar

Константы
^^^^^^^^^

- httpPath (используйте Eresus\\CmsBundle\\HTTP\\Request::getBasePath())
- httpHost (используйте Eresus\\CmsBundle\\HTTP\\Request::getHost())
- httpRoot (используйте Eresus_CMS::getLegacyKernel()->root)
- styleRoot (используйте Eresus_CMS::getLegacyKernel()->style)
- dataRoot (используйте Eresus_CMS::getLegacyKernel()->data)
- cookieHost
- cookiePath
- ERESUS_CMS_DEBUG
- KERNELNAME
- KERNELDATE

Глобальные переменные
^^^^^^^^^^^^^^^^^^^^^

- Eresus
- page

Изменено
--------

Перенаправления
^^^^^^^^^^^^^^^

Изменился механизм перенаправлений (редиректов). Подробнее см. раздел :doc:`/dev/guide/responses`.

Переименованы классы
^^^^^^^^^^^^^^^^^^^^

- Plugin ➙ Eresus\\CmsBundle\\Extensions\\Plugin
- ContentPlugin ➙ Eresus\\CmsBundle\\Extensions\\ContentPlugin
- EresusConnector ➙ Eresus\\CmsBundle\\Extensions\\Connector
- TAdminUI ➙ Eresus\\CmsBundle\\AdminUI
- TClientUI ➙ Eresus\\CmsBundle\\ClientUI
- Templates ➙ Eresus\\CmsBundle\\Templates
- EresusForm ➙ Eresus\\CmsBundle\\UI\\Form
- Sections ➙ Eresus\\CmsBundle\\Sections
- Form ➙ Eresus\\CmsBundle\\UI\\Admin\\ArrayForm
- PaginationHelper ➙ Eresus\\CmsBundle\\UI\\Pagination
- EresusAccounts ➙ Eresus\\CmsBundle\\Accounts
- EresusCollection ➙ Eresus\\CmsBundle\\Helpers\\Collection
- AdminList ➙ Eresus_UI_Admin_List
- AdminUITheme ➙ Eresus_Admin_Theme
- Plugins ➙ Eresus\\CmsBundle\\Extensions\\Registry
- EresusExtensions ➙ Eresus\\CmsBundle\\Extensions\\VendorRegistry
- WebServer ➙ Eresus_WebServer
- WebPage ➙ Eresus_WebPage
- i18n ➙ Eresus_i18n

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

- Eresus\\CmsBundle\\HTTP\\Request — обёртка для Symfony\Component\HttpFoundation\Request.
