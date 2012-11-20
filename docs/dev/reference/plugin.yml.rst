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

content_types
-------------

Список типов контента, предоставляемых модулем. Каждый тип описывается набором полей.

title
^^^^^

Название типа. Обязательный параметр.

controller
^^^^^^^^^^

Имя контроллера, обрабатывающего этот тип контента. Обязательный параметр. Если имя модуля
"Acme\\Foo", а в поле "controller" указано "Bar", то для обработки разделов этого типа будет
использоваться в КИ класс ``Acme\\Foo\\Controller\\BarContentClientController``, а в АИ ---
``Acme\\Foo\\Controller\\BarContentAdminController``.

description
^^^^^^^^^^^

Описание типа.

.. code-block:: yaml

   content_types:
       -
           title: "Овощи"
           controller: Vegetables
           description: "Раздел для размещения овощей"
       -
           title:
               ru: "Фрукты"
               en: "Fruits"
           controller: Fruits
           description:
               ru: "Раздел для размещения фруктов"

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
   content_types:
       -
           title: "Название типа контента"
           controller: Default
