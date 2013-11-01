<?php
/**
 * Шаблон
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

namespace Eresus\Templating;

use Dwoo;
use Dwoo_ITemplate;
use Dwoo_Template_String;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Шаблон
 *
 * @api
 */
class Template
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Внутреннее представление шаблона
     * @var Dwoo_ITemplate
     * @since 3.01
     */
    protected $template = null;

    /**
     * Конструктор
     *
     * @param Dwoo_ITemplate     $template
     * @param ContainerInterface $container
     */
    public function __construct(Dwoo_ITemplate $template, ContainerInterface $container)
    {
        $this->container = $container;
        $this->template = $template;
    }

    /**
     * Возвращает исходный код шаблона
     *
     * @return string
     * @since 3.01
     */
    public function getSource()
    {
        return $this->template->getSource();
    }

    /**
     * Задаёт исходный код шаблона в виде строки
     *
     * @param string $source
     *
     * @return void
     *
     * @since 3.01
     */
    public function setSource($source)
    {
        $this->template = new Dwoo_Template_String($source);
    }

    /**
     * Компилирует шаблон
     *
     * @param array $data  данные для подстановки в шаблон
     *
     * @return string
     */
    public function compile($data = null)
    {
        /** @var TemplateManager $manager */
        $manager = $this->container->get('templates');
        /** @var Dwoo $dwoo */
        $dwoo = $this->container->get('templates.dwoo');
        if ($data)
        {
            $data = array_merge($data, $manager->getGlobals());
        }
        else
        {
            $data = $manager->getGlobals();
        }

        return $dwoo->get($this->template, $data);
    }
}

