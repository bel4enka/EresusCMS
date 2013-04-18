Распаковка и установка прав
===========================

Загрузите архив с Eresus CMS в папку, являющуюся корнем Вашего сайта, затем распакуйте его. Пример::

.. code-block:: bash

   cd /home/user/htdocs
   wget http://eresus.ru/download/cms/eresus-cms-stable.tar.bz2
   tar -xf eresus-cms-stable.tar.bz2

После этого архив можно удалить::

.. code-block:: bash

   unlink eresus-cms-stable.tar.bz2

Установите необходимые права на файлы::

.. code-block:: bash

   cd /home/user/htdocs
   bin/setperms.sh

Теперь можно переходить к созданию :doc:`основного файла настроек<config-main>`.
