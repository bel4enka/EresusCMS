Настройки модуля
================

Чтобы добавить модулю настройки, надо сделать две вещи:

#. описать настройки в файле ``plugin.yml``;
#. описать диалог настроек.

Описание настроек
-----------------

В файле ``plugin.yml`` в разделе ``settings`` надо перечислить все необходимые настройки и их значения
по умолчанию. Подробное описание см. в :doc:`../reference/plugin.yml`.

Пример:

.. code-block:: yaml

   title: Foo
   version: "1.00"
   description: "Foo plugin"
   require:
       CMS: {min: "4.00"}
   settings:
       param1: "foo bar"
       param2: false

После установки модуля, его настройки будут скопированы в файл ``config/plugins.yml``.

Диалог настройки
----------------

Для описания диалога надо создать файл ``Resources/views/AdminConfigDialog.html.twig``, содержащий
разметку полей формы.

.. code-block:: html

   <div class="form__row">
       <label for="param1-input">Настройка param1</label>
       <input name="settings[param1]" type="text" id="param1-input"
           value="{{ plugin.settings.value1 }}>
   </div>
   <div class="form__row">
       <label>
           <input name="settings[param2]" type="checkbox" value="1"
               {% if plugin.settings.param2 %} checked{% endif %}>
           <input name="settings[param2]" type="hidden" value="0">
           Настройка param2
       </label>
   </div>


Собственный контроллер диалога настройки
----------------------------------------

Если описанных выше возможностей окажется недостаточно, можно создать собственный контроллер диалога
настройки. Для этого нужно в файле ``Controllers/Admin/ConfigDialog.php`` описать класс
``ConfigDialog``, унаследованный от `Eresus\\CmsBundle\\Extensions\\Controllers\\ConfigDialog <../../api/classes/Eresus.CmsBundle.Extensions.Controllers.ConfigDialog.html>`_.
Это изменить любое поведение диалога настройки.

.. code-block:: php

   <?php
   namespace Acme\Foo\Controllers\Admin;

   use Symfony\Component\HttpFoundation\Request;
   use Eresus\CmsBundle\Extensions\Controllers\ConfigDialog as NativeConfigDialog;

   class ConfigDialog extends NativeConfigDialog
   {
       // Здесь переопределите нужные вам методы.
   }