<?php
/**
 * Расширение Twig
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
 */

namespace Eresus\CmsBundle\Twig;

use Twig_Extension;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Eresus\CmsBundle\CmsBundle;

/**
 * Расширение Twig
 *
 * @since 4.00
 */
class Extension extends Twig_Extension
{
    /**
     * @var ContainerInterface
     * @since 4.00
     */
    private $container;

    /**
     * @param ContainerInterface $container
     *
     * @since 4.00
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Возвращает имя расширения
     *
     * @return string
     *
     * @since 4.00
     */
    public function getName()
    {
        return 'eresus';
    }

    /**
     * Возвращает глобальные переменные
     *
     * @return array
     * @since 4.00
     */
    public function getGlobals()
    {
        /** @var CmsBundle $cms */
        $cms = $this->container->get('cms');
        $globals = array('globals' => $cms->getGlobals());
        return $globals;
    }

    /* *
     * Возвращает список функций
     *
     * @return array
     * @since 4.00
     * /
    public function getFunctions()
    {
        return array(
            'i18n' => new Twig_Function_Method($this, 'i18n'),
            'jslib' => new Twig_Function_Method($this, 'jslib'),
            'call' => new Twig_Function_Method($this, 'call'),
        );
    }

    /* *
     * Обёртка для {@link Eresus_i18n::getText()}
     * /
    public function i18n($key, $context = null)
    {
        return Eresus_I18n::getInstance()->getText($key, $context);
    }

    /* *
     * Подключает библиотеку JavaScript
     *
     * @see WebPage::linkJsLib()
     * @since 4.00
     * /
    public function jslib()
    {
        $args = func_get_args();
        call_user_func_array(array(Eresus_Kernel::app()->getPage(), 'linkJsLib'), $args);
        return '';
    }

    /* *
     * Вызывает метод объекта
     *
     * @param object $object
     * @param string $method
     *
     * @return string
     *
     * @since 4.00
     * /
    public function call($object, $method)
    {
        return call_user_func(array($object, $method));
    }*/
}
