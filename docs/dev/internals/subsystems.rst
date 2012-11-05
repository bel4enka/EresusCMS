Подсистемы CMS
==============

Ядро
----

Фундамент CMS. Включает в себя такие классы как:

- Eresus_PHP
- Eresus_Kernel
- Eresus_CMS
- Eresus_Accounts
- Eresus_Sections
- Eresus\\CmsBundle\\Templates
- Eresus_WebPage
- Eresus_WebServer

А также некоторые устаревшие компоненты:

- Eresus
- MySQL
- Eresus Core

Административный интерфейс (АИ)
-------------------------------

- Eresus_Admin_Theme
- Eresus_Admin_Controllers_About
- Eresus\\CmsBundle\\AdminUI
- Eresus_Admin_Controllers_Content
- Eresus_Admin_Controllers_Files
- Eresus_Admin_Controllers_Pages
- Eresus_Admin_Controllers_Plgmgr
- Eresus_Admin_Controllers_Settings
- Eresus_Admin_Controllers_Themes
- Eresus_Admin_Controllers_Users

Клиентский интерфейс (КИ)
-------------------------

- Eresus\\CmsBundle\\ClientUI

Библиотека UI
-------------

- Eresus_HTML_Element
- Eresus_HTML_ScriptElement
- Eresus_UI_Pagination
- Eresus\\CmsBundle\\UI\\Form

Библиотека UI АИ
----------------

Классы для создания административного интерфейса.

- Eresus_UI_Admin_List
- Eresus_UI_Admin_ArrayForm (устаревший)

Подсистема расширения функционала
---------------------------------

- Eresus\\CmsBundle\\Extensions\\Registry
- Eresus\\CmsBundle\\Extensions\\Plugin
- Eresus\\CmsBundle\\Extensions\\ContentPlugin

Коннекторы сторонних компонентов
--------------------------------

- Eresus\\CmsBundle\\Extensions\\VendorRegistry
- Eresus\\CmsBundle\\Extensions\\Connector
- EditAreaConnector
- TinyMCEConnector

Интернационализация и локализация
---------------------------------

- Eresus_I18n

Вспомогательные средства
------------------------

- Eresus_Helpers_Collection
- Eresus_Feed_Writer
- Eresus_Feed_Writer_Item
- Eresus_FS_NameFilter
