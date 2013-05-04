<?php
/**
 * Реестр возможностей
 *
 * @version ${product.version}
 * @copyright 2013, Михаил Красильников <m.krasilnikov@yandex.ru>
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

namespace Eresus\CmsBundle\Features;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Реестр возможностей
 *
 * @since 4.00
 */
class Registry
{
    /**
     * Контейнер служб
     * @var ContainerInterface
     * @since 4.00
     */
    private $container;

    /**
     * Список классов поставщиков возможностей
     * @var array
     * @since 4.00
     */
    private $classes = array();

    /**
     * Список поставщиков возможностей
     * @var array
     * @since 4.00
     */
    private $providers = array();

    /**
     * Конструктор
     *
     * @param ContainerInterface $container
     *
     * @since 4.00
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Регистрирует поставщик возможности
     *
     * @param string $feature
     * @param string $class
     *
     * @since 4.00
     */
    public function register($feature, $class)
    {
        $feature = ltrim($feature, '\\');
        if (!array_key_exists($feature, $this->classes))
        {
            $this->classes[$feature] = array();
        }
        $this->classes[$feature] []= $class;
    }

    /**
     * Возвращает поставщика указанной возможности или null, если поставщиков нет
     *
     * @param string $feature
     *
     * @return object|null
     */
    public function getProvider($feature)
    {
        assert('is_string($feature)');
        // Убеждаемся, что модули зарегистрировали своих поставщиков
        $this->container->get('extensions');

        $feature = ltrim($feature, '\\');
        // Проверяем, есть ли поставщики этой возможности
        if (!array_key_exists($feature, $this->classes) || count($this->classes[$feature]) == 0)
        {
            return null;
        }

        // Берём первого поставщика в списке
        $class = $this->classes[$feature][0];

        if (!array_key_exists($class, $this->providers))
        {
            $this->providers[$class] = $provider = new $class();
            if ($provider instanceof ContainerAwareInterface)
            {
                $provider->setContainer($this->container);
            }
        }
        return $this->providers[$class];
    }
}

