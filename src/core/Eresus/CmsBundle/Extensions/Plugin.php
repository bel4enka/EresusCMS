<?php
/**
 * ${product.title}
 *
 * Родительский класс для всех плагинов
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

namespace Eresus\CmsBundle\Extensions;

use Eresus_CMS;
use FS;
use Core;

/**
 * Родительский класс для всех плагинов
 *
 * @package Eresus
 */
class Plugin
{
    /**
     * Имя плагина
     *
     * @var string
     */
    public $name;

    /**
     * Версия плагина
     *
     * Потомки должны перекрывать это своим значением
     *
     * @var string
     */
    public $version = '0.00';

    /**
     * Необходимая версия Eresus
     *
     * Потомки могут перекрывать это своим значением
     *
     * @var string
     */
    public $kernel = '3.00';

    /**
     * Название плагина
     *
     * Потомки должны перекрывать это своим значением
     *
     * @var string
     */
    public $title = 'no title';

    /**
     * Описание плагина
     *
     * Потомки должны перекрывать это своим значением
     *
     * @var string
     */
    public $description = '';

    /**
     * Настройки плагина
     *
     * Потомки могут перекрывать это своим значением
     *
     * @var array
     */
    public $settings = array();

    /**
     * Директория данных
     *
     * /data/имя_плагина
     *
     * @var string
     */
    protected $dirData;

    /**
     * URL данных
     *
     * @var string
     */
    protected $urlData;

    /**
     * Директория скриптов
     *
     * /ext/имя_плагина
     *
     * @var string
     */
    protected $dirCode;

    /**
     * URL скриптов
     *
     * @var string
     */
    protected $urlCode;

    /**
     * Директория оформления
     *
     * style/имя_плагина
     *
     * @var string
     */
    protected $dirStyle;

    /**
     * URL оформления
     *
     * @var string
     */
    protected $urlStyle;

    /**
     * Конструктор
     *
     * Производит чтение настроек плагина и подключение языковых файлов
     *
     * @uses $locale
     * @uses FS::isFile
     * @uses Core::safeInclude
     * @uses Eresus_Extensions_Plugin::resetPlugin
     */
    public function __construct()
    {
        global $locale; // TODO Удалить

        $legacyKernel = Eresus_CMS::getLegacyKernel();

        $this->name = strtolower(get_class($this));
        /* Удаляем пространство имён */
        if (($pos = strrpos($this->name, '\\')) !== false)
        {
            $this->name = substr($this->name, $pos + 1);
        }
        if (!empty($this->name) && isset($legacyKernel->plugins->list[$this->name]))
        {
            $this->settings =
                decodeOptions($legacyKernel->plugins->list[$this->name]['settings'],
                    $this->settings);
            /*
             * Если установлена версия плагина отличная от установленной ранее
             * то необходимо произвести обновление информации о плагине в БД
             */
            if ($this->version != $legacyKernel->plugins->list[$this->name]['version'])
            {
                $this->resetPlugin();
            }
        }
        $this->dirData = $legacyKernel->fdata . $this->name . '/';
        $this->urlData = $legacyKernel->data . $this->name . '/';
        $this->dirCode = $legacyKernel->froot . 'ext/' . $this->name . '/';
        $this->urlCode = $legacyKernel->root . 'ext/' . $this->name . '/';
        $this->dirStyle = $legacyKernel->fstyle . $this->name . '/';
        $this->urlStyle = $legacyKernel->style . $this->name . '/';
        $filename = $legacyKernel->froot . 'lang/' . $this->name . '/' . $locale['lang'] . '.php';
        if (FS::isFile($filename))
        {
            Core::safeInclude($filename);
        }
    }

    /**
     * Возвращает информацию о плагине
     *
     * @param array $item  Предыдущая версия информации (по умолчанию null)
     *
     * @return array Массив информации, пригодный для записи в БД
     */
    public function __item($item = null)
    {
        $result['name'] = $this->name;
        $result['content'] = false;
        $result['active'] = is_null($item)? true : $item['active'];
        $result['settings'] = Eresus_CMS::getLegacyKernel()->db->
            escape(is_null($item) ? encodeOptions($this->settings) : $item['settings']);
        $result['title'] = $this->title;
        $result['version'] = $this->version;
        $result['description'] = $this->description;
        return $result;
    }

    /**
     * Перехватчик обращений к несуществующим методам плагинов
     *
     * @param string $method  Имя вызванного метода
     * @param array  $args    Переданные аргументы
     *
     * @throws \EresusMethodNotExistsException
     */
    public function __call($method, $args)
    {
        throw new \EresusMethodNotExistsException($method, get_class($this));
    }

    /**
     * Возвращает URL директории данных плагина
     *
     * @return string
     *
     * @since 2.15
     */
    public function getDataURL()
    {
        return $this->urlData;
    }

    /**
     * Возвращает URL директории файлов плагина
     *
     * @return string
     *
     * @since 2.15
     */
    public function getCodeURL()
    {
        return $this->urlCode;
    }

    /**
     * Возвращает URL директории стилей плагина
     *
     * @return string
     *
     * @since 2.15
     */
    public function getStyleURL()
    {
        return $this->urlStyle;
    }

    /**
     * Чтение настроек плагина из БД
     *
     * @return bool  Результат выполнения
     */
    protected function loadSettings()
    {
        $result = Eresus_CMS::getLegacyKernel()->db->
            selectItem('plugins', "`name`='".$this->name."'");
        if ($result)
        {
            $this->settings = decodeOptions($result['settings'], $this->settings);
        }
        return (bool) $result;
    }

    /**
     * Сохранение настроек плагина в БД
     *
     * @return bool  Результат выполнения
     */
    protected function saveSettings()
    {
        $result = Eresus_CMS::getLegacyKernel()->db->selectItem('plugins', "`name`='{$this->name}'");
        $result = $this->__item($result);
        $result['settings'] = Eresus_CMS::getLegacyKernel()->db->escape(encodeOptions($this->settings));
        $result = Eresus_CMS::getLegacyKernel()->db->
            updateItem('plugins', $result, "`name`='".$this->name."'");

        return $result;
    }

    /**
     * Обновление данных о плагине в БД
     */
    protected function resetPlugin()
    {
        $this->loadSettings();
        $this->saveSettings();
    }

    /**
     * Действия, выполняемые при инсталляции плагина
     */
    public function install()
    {

    }

    /**
     * Действия, выполняемые при деинсталляции плагина
     */
    public function uninstall()
    {
        $eresus = Eresus_CMS::getLegacyKernel();
        $tables = $eresus->db->query_array("SHOW TABLES LIKE '{$eresus->db->prefix}{$this->name}_%'");
        $tables = array_merge($tables, $eresus->db->
            query_array("SHOW TABLES LIKE '{$eresus->db->prefix}{$this->name}'"));
        for ($i=0; $i < count($tables); $i++)
        {
            $this->dbDropTable(substr(current($tables[$i]), strlen($this->name)+1));
        }
    }

    /**
     * Действия при изменении настроек
     */
    public function onSettingsUpdate()
    {
    }

    /**
     * Сохраняет в БД изменения настроек плагина
     */
    public function updateSettings()
    {
        foreach ($this->settings as $key => $value)
        {
            if (!is_null(arg($key)))
            {
                $this->settings[$key] = arg($key);
            }
        }
        $this->onSettingsUpdate();
        $this->saveSettings();
    }

    /**
     * Замена макросов
     *
     * @param string $template  Строка в которой требуется провести замену макросов
     * @param mixed  $item      Ассоциативный массив со значениями для подстановки вместо макросов
     *
     * @return  string  Обработанная строка
     */
    protected function replaceMacros($template, $item)
    {
        $result = replaceMacros($template, $item);
        return $result;
    }

    /**
     * Создание новой директории
     *
     * @param string $name Имя директории
     * @return bool Результат
     */
    protected function mkdir($name = '')
    {
        $result = true;
        $umask = umask(0000);
        // Проверка и создание корневой директории данных
        if (!is_dir($this->dirData))
        {
            $result = mkdir($this->dirData);
        }
        if ($result)
        {
            // Удаляем директории вида "." и "..", а также финальный и лидирующий слэши
            $name = preg_replace(array('!\.{1,2}/!', '!^/!', '!/$!'), '', $name);
            if ($name)
            {
                $name = explode('/', $name);
                $root = substr($this->dirData, 0, -1);
                for ($i=0; $i<count($name); $i++)
                {
                    if ($name[$i])
                    {
                        $root .= '/'.$name[$i];
                        if (!is_dir($root))
                        {
                            $result = mkdir($root);
                        }
                        if (!$result)
                        {
                            break;
                        }
                    }
                }
            }
        }
        umask($umask);
        return $result;
    }

    /**
     * Удаление директории и файлов
     *
     * @param string $name Имя директории
     * @return bool Результат
     */
    protected function rmdir($name = '')
    {
        $result = true;
        $name = preg_replace(array('!\.{1,2}/!', '!^/!', '!/$!'), '', $name);
        $name = $this->dirData.$name;
        if (is_dir($name))
        {
            $files = glob($name.'/{.*,*}', GLOB_BRACE);
            for ($i = 0; $i < count($files); $i++)
            {
                if (substr($files[$i], -2) == '/.' || substr($files[$i], -3) == '/..')
                {
                    continue;
                }
                if (is_dir($files[$i]))
                {
                    $result = $this->rmdir(substr($files[$i], strlen($this->dirData)));
                }
                elseif (is_file($files[$i]))
                {
                    $result = unlink($files[$i]);
                }
                if (!$result)
                {
                    break;
                }
            }
            if ($result)
            {
                $result = rmdir($name);
            }
        }
        return $result;
    }

    /**
     * Возвращает реальное имя таблицы
     *
     * @param string $table  Локальное имя таблицы
     * @return string Реальное имя таблицы
     */
    protected function __table($table)
    {
        return $this->name.(empty($table)?'':'_'.$table);
    }

    /**
     * Создание таблицы в БД
     *
     * @param string $SQL Описание таблицы
     * @param string $name Имя таблицы
     *
     * @return bool Результат выполнения
     */
    protected function dbCreateTable($SQL, $name = '')
    {
        $result = Eresus_CMS::getLegacyKernel()->db->create($this->__table($name), $SQL);
        return $result;
    }

    /**
     * Удаление таблицы БД
     *
     * @param string $name Имя таблицы
     *
     * @return bool Результат выполнения
     */
    protected function dbDropTable($name = '')
    {
        $result = Eresus_CMS::getLegacyKernel()->db->drop($this->__table($name));
        return $result;
    }

    /**
     * Регистрация обработчиков событий
     *
     * @param string $event...  Имя события
     */
    protected function listenEvents()
    {
        for ($i=0; $i < func_num_args(); $i++)
        {
            Eresus_CMS::getLegacyKernel()->plugins->events[func_get_arg($i)][] = $this->name;
        }
    }
}

