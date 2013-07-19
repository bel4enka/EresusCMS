Переменные, доступные в шаблонах страниц
========================================

$page
-----

Экземпляр класса `TClientUI <../../api/classes/TClientUI.html>`_ описывающий отрисовываемую в данный
момент страницу.

Пример использования:

.. code-block:: html

   <head>
       ...
       <!-- Вывод заголовка раздела -->
	   <title>{$page->title}</title>
	   <!-- Вывод ключевых слов раздела -->
	   <meta name="keywords" content="{$page->keywords}">
	   <!-- Вывод описания сайта -->
	   <meta name="description" content="{$page->description}">
   </head>
   <body>
       ...
       <!-- Вывод контента страницы -->
       {$page->content}
       ...
   </body>
   </html>
