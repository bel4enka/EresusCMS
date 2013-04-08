Поставщики возможностей
=======================

Возможности (Features) — один из способов расширения функциональности CMS.

Для предоставления возможности надо сделать следующее:

#. Добавить в :doc:`../reference/plugin.yml` раздел ``features``.
#. Написать поставщик возможности.

plugin.yml
----------

Добавим в конец файла ``plugin.yml`` следующие строки:

.. code-block:: yaml

   features:
       Eresus\CmsBundle\Features\WysiwygFeature
           class: Acme\MyPlugin\Features\WysiwygFeature

Они означают что:

#. Наш модуль предоставляет поставщика возможности «Eresus\CmsBundle\Features\WysiwygFeature»
#. Поставщик этой возможности расположен в классе Acme\MyPlugin\Features\WysiwygFeature

Класс-поставщик
---------------

Идентификатор возможности — это имя интерфейса, который должны реализовывать поставщики этой
возможности.

Если вашему классу нужен доступ к контейнеру служб, он должен поддерживать интерфейс
Symfony\Component\DependencyInjection\ContainerAwareInterface.