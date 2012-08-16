Подключение собственных JS и CSS
================================

Если плагину требуется подключить к странице собственные скрипты JavaScript или стили, то можно воспользоваться методами `WebPage::linkScripts() <../../api/Eresus/WebPage.html#linkScripts>`_, `WebPage::addScripts() <../../api/Eresus/WebPage.html#addScripts>`_, `WebPage::linkStyles() <../../api/Eresus/WebPage.html#linkStyles>`_, `WebPage::addStyles() <../../api/Eresus/WebPage.html#addStyles>`_.

Примеры
-------

Подключение файлов ``/ext/myplugin/client.js`` и ``/ext/myplugin/client.css``.

.. code-block:: php

   <?php
   $page = Eresus_Kernel::app()->getPage();

   $page->linkScripts($this->urlCode . 'client.js');
   $page->linkStyles($this->urlCode . 'client.css');

