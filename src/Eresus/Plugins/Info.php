<?php
/**
 * Описание модуля расширения
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

namespace Eresus\Plugins;

use Eresus\Plugins\Requirements\CmsRequirement;
use Eresus\Plugins\Requirements\PluginRequirement;
use Eresus\Plugins\Requirements\Requirement;
use SimpleXMLElement;
use RuntimeException;

/**
 * Описание модуля расширения
 *
 * @since 3.01
 */
class Info
{
    /**
     * Обнаруженные ошибки
     *
     * @var string[]
     *
     * @since 3.01
     */
    private $errors = array();

    /**
     * UID плагина
     *
     * @var string
     */
    private $uid;

    /**
     * @var SimpleXMLElement
     *
     * @since 3.01
     */
    private $xml;

    /**
     * Версия
     *
     * @var string
     */
    private $version = null;

    /**
     * Название
     *
     * @var string
     */
    private $title = null;

    /**
     * Описание
     *
     * @var string
     */
    private $description = null;

    /**
     * Зависимости модуля
     *
     * @var Requirement[]
     */
    private $requirements = null;

    /**
     * Создаёт объект из файла XML
     *
     * @param string $filename
     *
     * @since 3.01
     */
    public function __construct($filename)
    {
        libxml_use_internal_errors(true);
        libxml_clear_errors();
        $this->xml = new SimpleXMLElement($filename, 0, true);
        $errors = libxml_get_errors();
        if (count($errors))
        {
            libxml_clear_errors();
            $msg = array();
            foreach ($errors as $e)
            {
                $msg [] = $e->message . '(' . $e->file . ':' . $e->line . ':' . $e->column . ')';
            }
            $this->errors['xml'] = implode('; ', $msg);
        }
        else
        {
            if (is_null($this->uid) && !array_key_exists('xml', $this->errors))
            {
                $this->uid = strval($this->xml['uid']);
            }
        }
        if (!$this->uid)
        {
            $this->errors['uid'] = _('Не задан или пуст атрибут «uid»');
            $this->uid = basename(dirname($filename)) . '@???';
        }
    }

    /**
     * Возвращает true, если описание не содержит ошибок
     *
     * @return bool
     *
     * @since 3.01
     */
    public function isValid()
    {
        return count($this->errors) == 0;
    }

    /**
     * Возвращает список ошибок
     *
     * @return string[]
     *
     * @since 3.01
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Возвращает UID плагина
     *
     * @return string
     *
     * @since 3.01
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Возвращает название
     *
     * @return string
     *
     * @since 3.01
     */
    public function getTitle()
    {
        if (is_null($this->title) && !array_key_exists('xml', $this->errors))
        {
            $this->title = strval($this->xml->title[0]);
            if ('' === $this->title)
            {
                $this->errors['title'] = _('Не задан или пуст тег «title»');
                $this->title = $this->getUid();
            }
        }
        return $this->title;
    }

    /**
     * Возвращает версию
     *
     * @return string
     *
     * @since 3.01
     */
    public function getVersion()
    {
        if (is_null($this->version) && !array_key_exists('xml', $this->errors))
        {
            $this->version = strval($this->xml->version[0]);
            if ('' === $this->version)
            {
                $this->errors['version'] = _('Не задан или пуст тег «version»');
                $this->version = _('н/д');
            }
        }
        return $this->version;
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
        if (is_null($this->description) && !array_key_exists('xml', $this->errors))
        {
            $this->description = strval($this->xml->description[0]);
            if ('' === $this->description)
            {
                $this->errors['description'] = _('Не задан или пуст тег «description»');
                $this->description = _('н/д');
            }
        }
        return $this->description;
    }

    /**
     * Возвращает список зависимостей
     *
     * @return array
     *
     * @since 3.01
     */
    public function getRequirements()
    {
        if (is_null($this->requirements) && !array_key_exists('xml', $this->errors))
        {
            $reqs = array();
            foreach ($this->xml->requires as $item)
            {
                /** @var SimpleXMLElement $item */
                switch (strtolower($item->getName()))
                {
                    case 'cms':
                        $reqs []= new CmsRequirement(strval($item['min']), strval($item['max']));
                        break;
                    case 'plugin':
                        $reqs []= new PluginRequirement(strval($item['uid']), strval($item['name']),
                            strval($item['min']), strval($item['max']));
                        break;
                }
            }
            $this->requirements = $reqs;
        }
        return $this->requirements;
    }
}

