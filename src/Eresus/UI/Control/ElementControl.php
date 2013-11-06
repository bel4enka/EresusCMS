<?php
/**
 * Элемент управления элементом наполнения сайта
 *
 * @version ${product.version}
 * @copyright ${product.copyright}
 * @license ${license.uri} ${license.name}
 * @author Михаил Красильников <m.krasilnikov@yandex.ru>
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
 */

namespace Eresus\UI\Control;

use Eresus\Content\ElementInterface;
use Eresus\Templating\TemplateManager;

/**
 * Элемент управления элементом наполнения сайта
 *
 * @api
 * @since 3.01
 */
abstract class ElementControl extends Control
{
    /**
     * Элемент наполнения, которым управляет этот ЭУ
     *
     * @var ElementInterface|null
     *
     * @since 3.01
     */
    private $element;

    /**
     * Функция, вызываемая при отрисовке ЭУ
     *
     * @var null|callable
     *
     * @since 3.01
     */
    private $filter = null;

    /**
     * Конструктор виджета
     *
     * @param TemplateManager $templateManager
     * @param ElementInterface         $element
     *
     * @since 3.01
     */
    public function __construct(TemplateManager $templateManager, ElementInterface $element = null)
    {
        parent::__construct($templateManager);
        $this->element = $element;
    }

    /**
     * Возвращает URL действия
     *
     * @return string
     *
     * @since 3.01
     */
    public function getActionUrl()
    {
        $e = $this->getElement();
        if (is_null($this->urlBuilder))
        {
            return $e->getId() . '/' . $this->getActionName();
        }

        return $this->urlBuilder->getActionUrl($this->getActionName(), $e->getId());
    }

    /**
     * Задаёт элемент наполнения, которым управляет этот ЭУ
     *
     * @param ElementInterface $element
     *
     * @since 3.01
     */
    public function setElement(ElementInterface $element)
    {
        $this->element = $element;
    }

    /**
     * Возвращает элемент наполнения, которым управляет этот ЭУ
     *
     * @throws \LogicException
     *
     * @return ElementInterface
     *
     * @since 3.01
     */
    public function getElement()
    {
        if (is_null($this->element))
        {
            throw new \LogicException(
                sprintf('No element specified for %s', get_class($this))
            );
        }
        return $this->element;
    }

    /**
     * Задаёт функцию, вызываемую при отрисовке ЭУ
     *
     * Функции на входе передаётся элемент, для которого сейчас отрисовывается ЭУ. Функция может
     * возвращать следующие значения:
     *
     * - {@link Eresus\Content\ElementInterface} — ЭУ будет отрисован для этого объекта
     * - false — не отрисовывать ЭУ для текущего объекта
     * - null — продолжить отрисовку без изменений
     *
     * Любые другие возвращаемые значения расцениваются как null.
     *
     * Обратите внимание! Фильтрующей функции передаётся клон исходного объекта, так что его
     * изменение больше нигде не отразится.
     *
     * @param callable $filter
     *
     * @since 3.01
     */
    public function setFilter($filter)
    {
        assert('is_callable($filter)');
        $this->filter = $filter;
    }

    /**
     * Возвращает разметку
     *
     * @return string  HTML
     *
     * @since 3.01
     */
    public function getHtml()
    {
        $original = $this->element;
        if (!is_null($this->filter))
        {
            $result = call_user_func($this->filter, clone $this->element);
            if (false === $result)
            {
                return '';
            }
            if ($result instanceof ElementInterface)
            {
                $this->element = $result;
            }
        }
        $html = parent::getHtml();
        $this->element = $original;
        return $html;
    }
}

