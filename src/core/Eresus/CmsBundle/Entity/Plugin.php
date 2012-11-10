<?php
/**
 * Плагин
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

namespace Eresus\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Eresus\ORMBundle\AbstractEntity;
use Eresus_PluginInfo;

/**
 * Плагин
 *
 * @property string            $name
 * @property bool              $active
 * @property bool              $content
 * @property array             $settings
 * @property string            $version
 * @property string            $title
 * @property string            $description  описание
 * @property Eresus_PluginInfo $info
 *
 * @package Eresus
 * @since 4.00
 *
 * @ORM\Entity
 * @ORM\Table(name="plugins")
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 */
class Plugin extends AbstractEntity
{
    /**
     * Имя
     *
     * @var string
     *
     * @ORM\Column(length=32)
     */
    protected $name;

    /**
     * Активность
     *
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $active;

    /**
     * Модуль контента
     *
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $content;

    /**
     * Настройки
     *
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected $settings;

    /**
     * Версия
     *
     * @var string
     *
     * @ORM\Column(length=16)
     */
    protected $caption;

    /**
     * Название
     *
     * @var string
     *
     * @ORM\Column(length=64)
     */
    protected $title;

    /**
     * Описание
     *
     * @var string
     *
     * @ORM\Column(type="text")
     * @Assert\NotNull()
     */
    protected $description;

    /**
     * Время обновления
     *
     * @var Eresus_PluginInfo
     *
     * @ORM\Column(type="object")
     */
    protected $info;
}

