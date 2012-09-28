<?php
/**
 * ${product.title}
 *
 * Раздел сайта
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
use Symfony\Component\Validator\Constraints as Assert;
use Eresus\ORMBundle\AbstractEntity;
use Eresus\CmsBundle\Repository\SectionRepository;

/**
 * Раздел сайта
 *
 * @property int       $id
 * @property string    $name
 * @property Section   $parent
 * @property string    $title
 * @property string    $caption
 * @property string    $description
 * @property string    $hint
 * @property string    $keywords
 * @property int       $position
 * @property bool      $active
 * @property int       $access
 * @property bool      $visible
 * @property string    $template
 * @property string    $type
 * @property string    $content
 * @property array     $options
 * @property \DateTime $created
 * @property \DateTime $updated
 * @property Section[] $children
 *
 * @package Eresus
 * @since 3.01
 *
 * @ORM\Entity(repositoryClass="Eresus\CmsBundle\Repository\SectionRepository")
 * @ORM\Table(name="pages")
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 */
class Section extends AbstractEntity
{
    /**
     * Идентификатор
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * Имя
     *
     * @var string
     *
     * @ORM\Column(length=32)
     */
    protected $name;

    /**
     * Родитель
     *
     * @var Section
     *
     * @ORM\ManyToOne(targetEntity="Section", inversedBy="children")
     * @ORM\JoinColumn(name="owner", referencedColumnName="id")
     */
    protected $parent;

    /**
     * Заголовок
     *
     * @var string
     *
     * @ORM\Column(type="text")
     */
    protected $title;

    /**
     * Текст для пункта меню
     *
     * @var string
     *
     * @ORM\Column(length=64)
     */
    protected $caption;

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
     * Подсказка
     *
     * @var int
     *
     * @ORM\Column(type="text")
     */
    protected $hint;

    /**
     * Ключевые слова
     *
     * @var int
     *
     * @ORM\Column(type="text")
     */
    protected $keywords;

    /**
     * Порядковый номер
     *
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $position;

    /**
     * Активность
     *
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $active;

    /**
     * Уровень доступа
     *
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $access;

    /**
     * Видимость
     *
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $visible;

    /**
     * Шаблон
     *
     * @var string
     *
     * @ORM\Column(length=64)
     */
    protected $template;

    /**
     * Тип раздела
     *
     * @var string
     *
     * @ORM\Column(length=32)
     */
    protected $type;

    /**
     * Содержимое
     *
     * @var string
     *
     * @ORM\Column(type="text")
     */
    protected $content;

    /**
     * Опции
     *
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected $options;

    /**
     * Время создания
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * Время обновления
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Section", mappedBy="parent")
     */
    protected $children;

    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
    }
}

