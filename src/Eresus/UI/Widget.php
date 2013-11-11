<?php
/**
 * Абстрактный виджет
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

namespace Eresus\UI;

use Eresus\Templating\Template;
use Eresus\Templating\TemplateManager;

/**
 * Абстрактный виджет
 *
 * Виджет — это элемент пользовательского интерфейса, который может быть создан программно серверной
 * частью приложения.
 *
 * @api
 * @since 3.01
 */
abstract class Widget
{
    /**
     * @var TemplateManager
     * @since 3.01
     */
    private $templateManager = null;

    /**
     * Имя файла шаблона
     *
     * @var null|string
     *
     * @since 3.01
     */
    private $templateName = null;

    /**
     * Кэш шаблона
     *
     * @var null|Template
     *
     * @since 3.01
     */
    private $template = null;

    /**
     * Конструктор виджета
     *
     * @param TemplateManager $templateManager
     *
     * @since 3.01
     */
    public function __construct(TemplateManager $templateManager)
    {
        $this->templateManager = $templateManager;
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
        $tmpl = $this->getTemplate();
        return $tmpl->compile(array('widget' => $this));
    }

    /**
     * Задаёт имя файла шаблона
     *
     * @param string $filename
     *
     * @since 3.01
     */
    public function setTemplateName($filename)
    {
        $this->templateName = $filename;
    }

    /**
     * Возвращает имя файла шаблона
     *
     * @return string
     *
     * @since 3.01
     */
    protected function getTemplateName()
    {
        if (is_null($this->templateName))
        {
            $class = get_class($this);
            $this->templateName = str_replace('\\', '/', substr($class, strlen('Eresus\\')))
                . '.html';
        }
        return $this->templateName;
    }

    /**
     * Задаёт менеджер шаблонов
     *
     * @param TemplateManager $templateManager
     *
     * @return Widget
     *
     * @since 3.01
     */
    public function setTemplateManager(TemplateManager $templateManager)
    {
        $this->templateManager = $templateManager;
        return $this;
    }

    /**
     * Возвращает менеджер шаблонов
     *
     * @throws \LogicException
     *
     * @return TemplateManager
     *
     * @since 3.01
     */
    protected function getTemplateManager()
    {
        if (is_null($this->templateManager))
        {
            throw new \LogicException(
                sprintf('Template manager not set for %s', get_class($this)));
        }
        return $this->templateManager;
    }

    /**
     * Возвращает шаблон
     *
     * @return Template
     *
     * @since 3.01
     */
    protected function getTemplate()
    {
        if (is_null($this->template))
        {
            $this->template = $this->getTemplateManager()
                ->getAdminTemplate($this->getTemplateName());
        }
        return $this->template;
    }
}

