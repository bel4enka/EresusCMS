Bundle.php
==========

Т.к. модуль расширения Eresus CMS --- это пакет Symfony, то у него должен быть класс пакета. Однако
для работы в Eresus CMS, этот класс должен несколько отличаться, от обычного для Symfony формата.

Наследование от PluginBundle
----------------------------

В Symfony класс пакета должен наследоваться от ``Symfony\Component\HttpKernel\Bundle\Bundle``, в
Eresus он должен наследоваться от `Eresus\\CmsBundle\\Extensions\\PluginBundle <../../api/classes/Eresus.CmsBundle.Extensions.PluginBundle.html>`_:

Имя класса пакета должно быть Bundle
------------------------------------

В Symfony класс пакета советуют называть «ПроизводительПакетBundle», например ``AcmeDemoBundle``. В
Eresus этот класс всегда должен называться просто ``Bundle``. При этом метод ``getBundle`` будет
возвращать имя являющееся объединением всех частей пространства имён модуля с добавлением слова
«Bundle». Т. е. для класса ``Foo\\Bar\\Bundle`` будет возвращено «FooBarBundle».