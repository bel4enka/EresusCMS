Веб-формы HTML5 с библиотекой Webshim
=====================================

В состав Eresus CMS входит библиотека `Websim <http://afarkas.github.com/webshim/demos/index.html>`_, обеспечивающая поддержку функций HTML5 в старых браузерах. Среди прочего она позволяет значительно упростить разработку веб-форм.

Подключить библиотеку Webshim можно из PHP-кода следующим образом:

.. code-block:: php

   <?php
   Eresus_Kernel::app()->getPage()->linkJsLib('webshim');

После этого, можно использовать в формах возможности HTML5:

.. code-block:: html

   <form action="...">
     <input type="number" min="1" required>
   </form>
