plugins.yml
===========

Файл, хранящий сведения об установленных модулях расширения и их настройках.

Пример:

.. code-block:: yaml

   Vendor1\Plugin1:
       enabled: true
       settings:
           param1: "value1"
           param2: "value2"

   Vendor2\Plugin1:
       enabled: false
       settings:
           param1: "value1"
           param2: "value2"
