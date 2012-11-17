plugin.yml
==========

Для параметров ``title``, ``description`` можно задавать значения на нескольких языках одновременно,
указывая язык в качестве ключа.

title
-----

Название модуля. Обязательный параметр.

Примеры:

.. code-block:: yaml

   title: "Мой модуль"

.. code-block:: yaml

   title:
       ru: "Мой модуль"
       en: "My module"

description
-----------

Описание модуля.

Примеры:

.. code-block:: yaml

   description: "Подробное описание модуля"

.. code-block:: yaml

   title:
       ru: "Подробное описание модуля"
       en: "Detailed module description"

version
-------

Версия модуля. Обязательный параметр.

Примеры:

.. code-block:: yaml

   version: "1.06b"

requires
--------

Блок описания зависимостей. Обязательный параметр. Блок должен содержать сведения о совместимых
версиях CMS, а также список других модулей, необходимых для работы данного модуля. Версия CMS
задаётся ключом ``cms``, модули указываются по своим пространствам имён. Минимальная версия задаётся
ключом ``min``, максимальная --- ``max``. Если максимальная версия не указана, она считается равной
минимальной.

.. code-block:: yaml

   requires:
       cms: {min: "4.00", max: "4.05"}
       Acme\Foo:
           min: "1.00"
           max: "1.05"
       Acme\Bar: {min: "3.00"}

settings
--------

Настройки модуля и их значения по умолчанию.

.. code-block:: yaml

   settings:
       param1: true
       param2: "foo bar"

Пример файла
------------

.. code-block:: yaml

   title: "Название"
   version: "1.00"
   description:
       ru: "Описание"
       en: "Description"
   requires:
       cms: {min: "4.00", max: "4.05"}
       Acme\Foo:
           min: "1.00"
           max: "1.05"
   settings:
     param1: true
     param2: "foo bar"
