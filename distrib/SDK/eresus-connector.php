<?php
/**
 * Eresus 2.11
 *
 * Пример файла-коннектора для подключения к Eresus стороннего расширения
 *
 * Система управления контентом Eresus™ 2
 * © 2004-2007, ProCreat Systems, http://procreat.ru/
 * © 2007-2008, Eresus Group, http://eresus.ru/
 *
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Все строки 'ExtensionName' необходимо изменить на имя расширения. Имя расширения
 * должно совпадать с директорией, в которой оно будет расположено.
 *
 * Для каждой расширяемой функции должен быть определён метод с именем вида:
 *   класс_функция()
 * например:
 *   forms_html()
 *
 */

/**
 * Класс-коннектор
 *
 * Класс-коннектор должен имть имя вида 'ИмяРасширенияConnector' и наследоваться от
 * базового класса EresusExtensionConnector.
 */
class ExntensionNameConntector extends EresusExtensionConnector {

}

?>