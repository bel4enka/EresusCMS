<?php
/**
 * ${product.title}
 *
 * Тип контента
 *
 * @version ${product.version}
 * @copyright ${product.copyright}
 * @license ${license.uri} ${license.name}
 * @author Михаил Красильников <mihalych@vsepofigu.ru>
 *
 * Данная программа является свободным программным обеспечением. Вы
 * вправе распространять ее и/или модифицировать в соответствии с
 * условиями версии 3 либо (по вашему выбору) с условиями более поздней
 * версии Стандартной Общественной Лицензии GNU, опубликованной Free
 * Software Foundation.
 *
 * Мы распространяем эту программу в надежде на то, что она будет вам
 * полезной, однако НЕ ПРЕДОСТАВЛЯЕМ НА НЕЕ НИКАКИХ ГАРАНТИЙ, в том
 * числе ГАРАНТИИ ТОВАРНОГО СОСТОЯНИЯ ПРИ ПРОДАЖЕ и ПРИГОДНОСТИ ДЛЯ
 * ИСПОЛЬЗОВАНИЯ В КОНКРЕТНЫХ ЦЕЛЯХ. Для получения более подробной
 * информации ознакомьтесь со Стандартной Общественной Лицензией GNU.
 *
 * Вы должны были получить копию Стандартной Общественной Лицензии
 * GNU с этой программой. Если Вы ее не получили, смотрите документ на
 * <http://www.gnu.org/licenses/>
 *
 * @package Eresus
 */

namespace Eresus\CmsBundle;

/**
 * Тип контента
 *
 * @package Eresus
 * @since 4.00
 */
class ContentType
{
    /**
     * Пространство имён, где содержатся контроллеры этого типа
     *
     * @var string
     * @since 4.00
     */
    private $namespace;

    /**
     * Имя контроллеров этого типа
     *
     * @var string
     * @since 4.00
     */
    private $controller;

    /**
     * Название
     *
     * @var string|string[]
     * @since 4.00
     */
    private $title;

    /**
     * Описание
     *
     * @var string|string[]
     * @since 4.00
     */
    private $description = null;

    /**
     * Конструктор
     *
     * @param string          $namespace
     * @param string          $controller
     * @param string|string[] $title
     * @param string|string[] $description
     * @since 4.00
     */
    public function __construct($namespace, $controller, $title, $description = null)
    {
        $this->namespace = $namespace;
        $this->controller = $controller;
        $this->title = $title;
        $this->description = $description;
    }

    /**
     * Возвращает уникальный идентификатор типа контента
     *
     * @return string
     * @since 4.00
     */
    public function getId()
    {
        return $this->namespace . '.' . $this->controller;
    }

    /**
     * Возвращает название
     *
     * @return string|string[]
     * @since 4.00
     */
    public function getTitle()
    {
        return is_array($this->title)
            ? $this->title['ru'] // TODO исправить на локаль из настроек
            : $this->title;
    }

    /**
     * Возвращает описание
     *
     * @return string|string[]
     * @since 4.00
     */
    public function getDescription()
    {
        return is_array($this->description)
            ? $this->description['ru'] // TODO исправить на локаль из настроек
            : $this->description;
    }
}

