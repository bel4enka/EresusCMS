<?php
/**
 * Работа с разделами сайта
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
 *
 * @package Eresus
 * @subpackage DomainModel
 */

use Eresus\Entity\Section;
use Doctrine\ORM\EntityManager;

/**
 * Активные разделы
 * @var int
 */
define('SECTIONS_ACTIVE', 0x0001);

/**
 * Видимые разделы
 * @var int
 */
define('SECTIONS_VISIBLE', 0x0002);

/**
 * Работа с разделами сайта
 *
 * Необязательно создавать экземпляр этого класса самостоятельно, можно использовать экземпляр
 * из {@link Eresus::$sections}:
 *
 * <code>
 * $sections = Eresus_Kernel::app()->getLegacyKernel()->sections;
 * </code>
 *
 * @package Eresus
 * @subpackage DomainModel
 *
 * @since 2.10
 * @deprecated с 3.01
 */
class Sections
{
    /**
     * Индекс разделов
     *
     * @var array
     */
    private $index = array();

    /**
     * Кэш
     *
     * @var array
     */
    private $cache = array();

    /**
     * Создаёт индекс разделов
     *
     * @param bool $force  Игнорировать закэшированные данные
     * @return void
     */
    private function index($force = false)
    {
        if ($force || !$this->index)
        {
            $items = Eresus_CMS::getLegacyKernel()->db->
                select('sections', '', '`position`', '`id`,`parent_id`');
            if ($items)
            {
                $this->index = array();
                foreach ($items as $item)
                {
                    $this->index[intval($item['parent_id'])] []= $item['id'];
                }
            }
        }
    }

    /**
     * Создаёт список ID разделов определённой ветки
     *
     * @param int $owner  ID корневого раздела ветки
     * @return array  Список ID разделов
     */
    private function branchIds($owner)
    {
        $result = array();
        if (isset($this->index[$owner]))
        {
            $result = $this->index[$owner];
            foreach ($result as $section)
            {
                $result = array_merge($result, $this->branchIds($section));
            }
        }
        return $result;
    }

    /**
     * Выбирает разделы определённой ветки
     *
     * @param int $owner   Идентификатор корневого раздела ветки
     * @param int $access  Минимальный уровень доступа
     * @param int $flags   Флаги (см. SECTIONS_XXX)
     *
     * @return array  Описания разделов
     */
    public function branch($owner, $access = GUEST, $flags = 0)
    {
        $result = array();
        // Создаём индекс
        if (!$this->index)
        {
            $this->index();
        }
        // Находим ID разделов ветки.
        $set = $this->branchIds($owner);
        if (count($set))
        {
            $list = array();
            /* Читаем из кэша */
            for ($i=0; $i < count($set); $i++)
            {
                if (isset($this->cache[$set[$i]]))
                {
                    $list[] = $this->cache[$set[$i]];
                    array_splice($set, $i, 1);
                    $i--;
                }
            }
            if (count($set))
            {
                $fieldset = '';//implode(',', array_diff($this->fields(), array('content')));
                /* Читаем из БД */
                $set = implode(',', $set);
                $items = Eresus_CMS::getLegacyKernel()->db->select('sections',
                    "FIND_IN_SET(`id`, '$set') AND `access` >= $access", 'position', $fieldset);
                for ($i=0; $i<count($items); $i++)
                {
                    $this->cache[$items[$i]['id']] = $items[$i];
                    $list[] = $items[$i];
                }
            }

            if ($flags)
            {
                for ($i=0; $i<count($list); $i++)
                {
                    /* Фильтруем с учётом переданных флагов */
                    $filterByActivePassed = !($flags & SECTIONS_ACTIVE) || $list[$i]['active'];
                    $filterByVisiblePassed = !($flags & SECTIONS_VISIBLE) || $list[$i]['visible'];

                    if ($filterByActivePassed && $filterByVisiblePassed)
                    {
                        $result[] = $list[$i];
                    }
                }
            }
            else
            {
                $result = $list;
            }
        }
        return $result;
    }

    /**
     * Возвращает идентификаторы дочерних разделов указанного
     *
     * @param int $owner   Идентификатор корневого раздела ветки
     * @param int $access  Минимальный уровень доступа
     * @param int $flags   Флаги (см. SECTIONS_XXX)
     *
     * @return array  Идентификаторы разделов
     *
     * @since 2.10
     */
    public function children($owner, $access = GUEST, $flags = 0)
    {
        $items = $this->branch($owner, $access, $flags);
        $result = array();
        for ($i=0; $i<count($items); $i++)
        {
            if ($items[$i]['parent_id'] == $owner)
            {
                $result[] = $items[$i];
            }
        }
        return $result;
    }

    /**
     * Возвращает идентификаторы всех родительских разделов указанного
     *
     * @param int $id   Идентификатор раздела
     *
     * @return array  Идентификаторы разделов или NULL если раздела $id не существует
     */
    public function parents($id)
    {
        $this->index();
        $result = array();
        while ($id)
        {
            foreach ($this->index as $key => $value)
            {
                if (in_array($id, $value))
                {
                    $result[] = $id = $key;
                    break;
                }
            }
            if (!$result)
            {
                return null;
            }
        }
        $result = array_reverse($result);
        return $result;
    }

    /**
     * Возвращает список полей
     *
     * @return  array  Список полей
     */
    public function fields()
    {
        if (isset($this->cache['fields']))
        {
            $result = $this->cache['fields'];
        }
        else
        {
            $result = Eresus_CMS::getLegacyKernel()->db->fields('sections');
            $this->cache['fields'] = $result;
        }
        return $result;
    }

    /**
     * Возвращает раздел или список разделов
     *
     * * Если $what — целое число, то будет возвращён раздел с указанным идентификатором.
     * * Если $what — массив, то будут возвращены разделы с идентификаторами из этого массива.
     * * Если $what — строка, то она будет использована как условие WHERE в запросе SQL.
     *
     * Описание раздела возвращается в виде ассоциативного массива, где ключами выступают имена
     * полей таблицы pages БД.
     *
     * @param int|array|string $what  ID раздела / список идентификаторов / условие SQL
     *
     * @return array|bool  описание раздела (разделов) или false, если ничего не найдено
     */
    public function get($what)
    {
        if (is_array($what))
        {
            $where = "FIND_IN_SET(`id`, '".implode(',', $what)."')";
        }
        elseif (is_numeric($what))
        {
            $where = "`id`=$what";
        }
        else
        {
            $where = $what;
        }
        $result = Eresus_CMS::getLegacyKernel()->db->select('sections', $where);
        if ($result)
        {
            for ($i=0; $i<count($result); $i++)
            {
                $result[$i]['options'] = decodeOptions($result[$i]['options']);
            }
        }
        if (is_numeric($what) && $result && count($result))
        {
            $result = $result[0];
        }

        return $result;
    }

    /**
     * Добавляет раздел
     *
     * @param array $item  Массив свойств раздела
     * @return mixed  Описание нового раздела или FALSE в случае неудачи
     */
    public function add($item)
    {
        if (!$this->index)
        {
            $this->index();
        }

        $om = $this->getObjectManager();
        $section = new Section();
        if ($item['parent_id'])
        {
            $parent = $om->find('Eresus\Entity\Section', $item['parent_id']);
            $section->setParent($parent);
        }
        $section->setName($item['name']);
        $section->setTitle($item['title']);
        $section->setCaption($item['caption']);
        $section->setDescription($item['description']);
        $section->setHint($item['hint']);
        $section->setKeywords($item['keywords']);
        $section->setActive($item['active']);
        $section->setAccess($item['access']);
        $section->setVisible($item['visible']);
        $section->setTemplate($item['template']);
        $section->setType($item['type']);
        $section->setContent($item['content']);
        $section->setoptions($item['options']);
        $section->setCreated(new DateTime());
        $section->setUpdated(new DateTime());

        if (!isset($item['position']) || !$item['position'])
        {
            $section->setPosition(isset($this->index[$item['parent_id']]) ?
                    count($this->index[$item['parent_id']]) : 0);
        }

        $om->persist($section);
        $om->flush(); // TODO Убрать отсюда!
        return $item;
    }

    /**
     * Изменяет раздел
     *
     * @param  array  $item  Раздел
     *
     * @return  mixed  Описание нового раздела или false в случае неудачи
     */
    public function update($item)
    {
        $om = $this->getObjectManager();
        $section = $om->find('Eresus\Entity\Section', $item['id']);
        if ($section->getParent()->getId() != $item['parent_id'])
        {
            $parent = $om->find('Eresus\Entity\Section', $item['parent_id']);
            $section->setParent($parent);
        }
        $section->setName($item['name']);
        $section->setTitle($item['title']);
        $section->setCaption($item['caption']);
        $section->setDescription($item['description']);
        $section->setHint($item['hint']);
        $section->setKeywords($item['keywords']);
        $section->setActive($item['active']);
        $section->setAccess($item['access']);
        $section->setVisible($item['visible']);
        $section->setTemplate($item['template']);
        $section->setType($item['type']);
        $section->setContent($item['content']);
        $section->setoptions($item['options']);
        $section->setUpdated(new DateTime());

        $om->flush(); // TODO Убрать отсюда

        return $item;
    }

    /**
     * Удаляет раздел и подразделы
     *
     * @param  int  $id  Идентификатор раздела
     *
     * @return  bool  Результат операции
     */
    public function delete($id)
    {
        $result = true;
        $children = $this->children($id);
        /* Удаляем подразделы */
        for ($i = 0; $i < count($children); $i++)
        {
            try
            {
                $this->delete($children[$i]['id']);
            }
            catch (Eresus_DB_Exception_QueryFailed $e)
            {
                $result = false;
                break;
            }
        }

        /* Если подразделы успешно удалены, удаляем контент раздела */
        if ($result)
        {
            $section = $this->get($id);
            if ($plugin = Eresus_CMS::getLegacyKernel()->plugins->load($section['type']))
            {
                if (method_exists($plugin, 'onSectionDelete'))
                {
                    $plugin->onSectionDelete($id);
                }
            }
            Eresus_CMS::getLegacyKernel()->db->delete('sections', "`id`=$id");
        }
        return $result;
    }

    /**
     * Возвращает контейнер
     *
     * @return EntityManager
     *
     * @since 3.01
     */
    private function getObjectManager()
    {
        /** @var \Symfony\Component\DependencyInjection\ContainerInterface $container */
        $container = $GLOBALS['_container'];
        /** @var \Eresus\ORM\Registry $doctrine */
        $doctrine = $container->get('doctrine');
        return $doctrine->getManager();
    }
}

