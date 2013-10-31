<?php
/**
 * Модуль расширения
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

namespace Eresus\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Модуль расширения
 *
 * @api
 * @since 3.01
 *
 * @ORM\Entity
 * @ORM\Table(name="plugins", indexes={
 *     @ORM\Index(name="default_idx", columns={"name", "active"})
 * })
 */
class Plugin
{
    /**
     * Имя
     *
     * @var string
     *
     * @since 3.01
     *
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * Активность
     *
     * @var bool
     *
     * @since 3.01
     *
     * @ORM\Column(type="boolean")
     */
    private $active = true;

    /**
     * Настройки
     *
     * @var array
     *
     * @since 3.01
     *
     * @ORM\Column(type="array")
     */
    private $settings = array();

    /**
     * Возвращает имя
     *
     * @return string
     *
     * @since 3.01
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Задаёт имя
     *
     * @param string $name
     *
     * @since 3.01
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Возвращает активность
     *
     * @return bool
     *
     * @since 3.01
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Задаёт активность
     *
     * @param bool $active
     *
     * @since 3.01
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * Возвращает настройки
     *
     * @return array
     *
     * @since 3.01
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Задаёт настройки
     *
     * @param array $settings
     *
     * @since 3.01
     */
    public function setSettings(array $settings)
    {
        $this->settings = $settings;
    }
}

