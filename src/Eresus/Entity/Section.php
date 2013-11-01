<?php
/**
 * Раздел сайта
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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * Раздел сайта
 *
 * @api
 * @since 3.01
 *
 * @ORM\Entity
 * @ORM\Table(name="sections", indexes={
 *     @ORM\Index(name="client_idx", columns={"parent", "active", "visible", "position"}),
 *     @ORM\Index(name="name_idx", columns={"name"})
 * })
 */
class Section
{
    /**
     * Идентификатор
     *
     * @var int
     *
     * @since 3.01
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * Родительский раздел
     *
     * @var Section|null
     *
     * @since 3.01
     *
     * @ORM\ManyToOne(targetEntity="Section", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent = null;

    /**
     * Подразделы
     *
     * @since 3.01
     *
     * @ORM\OneToMany(targetEntity="Section", mappedBy="parent")
     */
    private $children;

    /**
     * Имя раздела
     *
     * @var string
     *
     * @since 3.01
     *
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * Заголовок
     *
     * @var string
     *
     * @since 3.01
     *
     * @ORM\Column(type="string", length=4096)
     */
    private $title = '';

    /**
     * Пункт меню
     *
     * @var string
     *
     * @since 3.01
     *
     * @ORM\Column(type="string", length=64)
     */
    private $caption = '';

    /**
     * Описание
     *
     * @var string
     *
     * @since 3.01
     *
     * @ORM\Column(type="string", length=4096)
     */
    private $description = '';

    /**
     * Подсказка
     *
     * @var string
     *
     * @since 3.01
     *
     * @ORM\Column(type="string", length=4096)
     */
    private $hint = '';

    /**
     * Ключевые слова
     *
     * @var string
     *
     * @since 3.01
     *
     * @ORM\Column(type="string", length=4096)
     */
    private $keywords = '';

    /**
     * Порядковый номер
     *
     * @var int
     *
     * @since 3.01
     *
     * @ORM\Column(type="integer")
     */
    private $position;

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
     * Уровень доступа
     *
     * @var int
     *
     * @since 3.01
     *
     * @ORM\Column(type="integer")
     */
    private $access = GUEST;

    /**
     * Видимость
     *
     * @var bool
     *
     * @since 3.01
     *
     * @ORM\Column(type="boolean")
     */
    private $visible = true;

    /**
     * Имя шаблона
     *
     * @var string
     *
     * @since 3.01
     *
     * @ORM\Column(type="string", length=255)
     */
    private $template = 'default';

    /**
     * Тип
     *
     * @var string
     *
     * @since 3.01
     *
     * @ORM\Column(type="string", length=64)
     */
    private $type = 'default';

    /**
     * Пункт меню
     *
     * @var string
     *
     * @since 3.01
     *
     * @ORM\Column(type="string", length=4294967295)
     */
    private $content = '';

    /**
     * Опции
     *
     * @var array
     *
     * @since 3.01
     *
     * @ORM\Column(type="array")
     */
    private $options = array();

    /**
     * Время создания
     *
     * @var DateTime
     *
     * @since 3.01
     *
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * Время изменения
     *
     * @var DateTime
     *
     * @since 3.01
     *
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->created = new DateTime();
        $this->updated = clone $this->created;
    }

    /**
     * Возвращает идентификатор
     *
     * @return int
     *
     * @since 3.01
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Возвращает родительский раздел или null
     *
     * @return Section|null
     *
     * @since 3.01
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Задаёт родительский раздел
     *
     * @param Section $parent
     *
     * @since 3.01
     */
    public function setParent(Section $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * Возвращает подразделы
     *
     * @return Section[]|ArrayCollection
     *
     * @since 3.01
     */
    public function getChildren()
    {
        return $this->children;
    }

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
     * @throws \Exception
     *
     * @since 3.01
     */
    public function setName($name)
    {
        $name = trim(strval($name));
        $name = preg_replace('/[^a-z0-9_-]/i', '', $name);
        if (mb_strlen($name) == 0)
        {
            throw new \Exception(_('Имя раздела не может быть пустым.'));
        }
        $this->name = $name;
    }

    /**
     * Возвращает заголовок
     *
     * @return string
     *
     * @since 3.01
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Задаёт заголовок
     *
     * @param string $title
     *
     * @since 3.01
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Возвращает пункт меню
     *
     * @return string
     *
     * @since 3.01
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * Задаёт пункт меню
     *
     * @param string $caption
     *
     * @since 3.01
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;
    }

    /**
     * Возвращает описание
     *
     * @return string
     *
     * @since 3.01
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Задаёт описание
     *
     * @param string $description
     *
     * @since 3.01
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Возвращает подсказку
     *
     * @return string
     *
     * @since 3.01
     */
    public function getHint()
    {
        return $this->hint;
    }

    /**
     * Задаёт подсказку
     *
     * @param string $hint
     *
     * @since 3.01
     */
    public function setHint($hint)
    {
        $this->hint = $hint;
    }

    /**
     * Возвращает ключевые слова
     *
     * @return string
     *
     * @since 3.01
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Задаёт ключевые слова
     *
     * @param string $keywords
     *
     * @since 3.01
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * Возвращает порядковый номер
     *
     * @return bool
     *
     * @since 3.01
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Задаёт порядковый номер
     *
     * @param int $position
     *
     * @since 3.01
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * Возвращает true, если раздел активен
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
     * Задаёт активность раздела
     *
     * @param bool $active
     *
     * @since 3.01
     */
    public function setActive($active)
    {
        $this->active = (bool) $active;
    }

    /**
     * Возвращает уровень доступа
     *
     * @return int
     *
     * @since 3.01
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * Задаёт уровень доступа
     *
     * @param int $access
     *
     * @since 3.01
     */
    public function setAccess($access)
    {
        $this->access = $access;
    }

    /**
     * Возвращает true, если раздел видимый
     *
     * @return bool
     *
     * @since 3.01
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * Задаёт видимость раздела
     *
     * @param bool $visible
     *
     * @since 3.01
     */
    public function setVisible($visible)
    {
        $this->visible = (bool) $visible;
    }

    /**
     * Возвращает шаблон
     *
     * @return string
     *
     * @since 3.01
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Задаёт шаблон
     *
     * @param string $template
     *
     * @since 3.01
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * Возвращает тип
     *
     * @return string
     *
     * @since 3.01
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Задаёт тип
     *
     * @param string $type
     *
     * @since 3.01
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Возвращает контент
     *
     * @return string
     *
     * @since 3.01
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Задаёт контент
     *
     * @param string $content
     *
     * @since 3.01
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Возвращает опции
     *
     * @return array
     *
     * @since 3.01
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Задаёт опции
     *
     * @param array $options
     *
     * @since 3.01
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * Возвращает время создания
     *
     * @return DateTime
     *
     * @since 3.01
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Задаёт время создания
     *
     * @param DateTime $time
     *
     * @since 3.01
     */
    public function setCreated(DateTime $time)
    {
        $this->created = $time;
    }

    /**
     * Возвращает время изменения
     *
     * @return DateTime
     *
     * @since 3.01
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Задаёт время изменения
     *
     * @param DateTime $time
     *
     * @since 3.01
     */
    public function setUpdated(DateTime $time)
    {
        $this->updated = $time;
    }

    /**
     * Возвращает данные раздела в виде массива
     *
     * @return array
     *
     * @since 3.01
     * @deprecated используется только для обеспечения обратной совместимости
     */
    public function toLegacyArray()
    {
        return array(
            'id' => $this->getId(),
            'owner' => $this->getParent()->getId(),
            'name' => $this->getName(),
            'caption' => $this->getCaption(),
            'title' => $this->getTitle(),
            // TODO Остальные свойства
        );
    }
}

