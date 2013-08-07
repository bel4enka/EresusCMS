<?php
/**
 * Конфигурация расширений
 *
 * @version ${product.version}
 * @copyright ${product.copyright}
 * @license ${license.uri} ${license.name}
 * @author Михаил Красильников <m.krasilnikov@yandex.ru>
 *
 * Этот файл содержит список установленных расширений, а так же их настройки.
 * Расширения размещаются в директории ext-3rd, каждое в отдельной поддиректории.
 * В директории расширения должен находиться файл eresus-connector.php, обеспечивающий
 * взаимодействие этого расширения с Eresus.
 *
 * Все расширения разбиты по группам, определяющим область применения расширения. Внутри
 * группы они так же делятся по функциям, которые они расширяют.
 *
 * Внутри расширяемой функции в виде ассоциативного массива перечисляются все установленные
 * расширения. В качестве ключа должно использоваться имя директории, в которой расположено
 * расширение. Формат данных пока не определён, используйте значение null.
 *
 * @package Eresus
 */

$GLOBALS['Eresus']->conf['extensions'] = array(
    // Расширение возможностей форм ввода
    'forms' => array(
        // Расширение полей типа memo
        'memo_syntax' => array(
            'editarea' => null,
        ),
        // Расширение полей типа html
        'html' => array(
            'tinymce' => null,
        ),
    ),
);

