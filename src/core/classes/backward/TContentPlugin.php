<?php
/**
 * Устаревший базовый класс для плагинов, предоставляющих тип контента
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
 */

/**
 * Устаревший базовый класс для плагинов, предоставляющих тип контента
 *
 * @package Eresus
 * @deprecated с 3.01 используйте ContentPlugin
 */
class TContentPlugin
{
    /**
     * Имя плагина
     * @var string
     */
    public $name;

    /**
     * Версия плагина
     * @var string
     */
    public $version;

    /**
     * Название плагина
     * @var string
     */
    public $title;

    /**
     * Описание плагина
     * @var string
     */
    public $description;

    /**
     * Настройки плагина
     * @var array
     */
    public $settings = array();

    /**
     * Конструктор
     */
    public function __construct()
    {
        global $locale;

        $legacyKernel = Eresus_Kernel::app()->getLegacyKernel();
        $plugins = Eresus_Plugin_Registry::getInstance();

        if (!empty($this->name) && isset($plugins->list[$this->name]))
        {
            $this->settings = decodeOptions($plugins->list[$this->name]['settings'],
                $this->settings);
            # Если установлена версия плагина отличная от установленной ранее
            # то необходимо произвести обновление информации о плагине в БД
            if ($this->version != $plugins->list[$this->name]['version'])
            {
                $this->resetPlugin();
            }
        }
        $filename = $legacyKernel->froot . 'lang/' . $this->name . '/' . $locale['lang'] . '.php';
        if (is_file($filename))
        {
            /** @noinspection PhpIncludeInspection */
            include $filename;
        }

        /** @var TClientUI|TAdminUI $page */
        $page = Eresus_Kernel::app()->getPage();
        if ($page)
        {
            $page->plugin = $this->name;
            if (count($page->options))
            {
                foreach ($page->options as $key => $value)
                {
                    $this->settings[$key] = $value;
                }
            }
        }
    }

    /**
     * Возвращает информацию о плагине
     *
     * @param  array  $item  Предыдущая версия информации (по умолчанию null)
     *
     * @return  array  Массив информации, пригодный для записи в БД
     */
    public function __item($item = null)
    {
        $result['name'] = $this->name;
        $result['content'] = false;
        $result['active'] = is_null($item) ? true : $item['active'];
        $result['settings'] = Eresus_CMS::getLegacyKernel()->db
            ->escape(is_null($item) ? encodeOptions($this->settings) : $item['settings']);
        $result['title'] = $this->title;
        $result['version'] = $this->version;
        $result['description'] = $this->description;
        return $result;
    }

    /**
     * Чтение настроек плагина из БД
     *
     * @return  bool  Результат выполнения
     */
    protected function loadSettings()
    {
        $result = Eresus_CMS::getLegacyKernel()->db
            ->selectItem('plugins', "`name`='".$this->name."'");
        if ($result)
        {
            $this->settings = decodeOptions($result['settings'], $this->settings);
        }
        return (bool) $result;
    }

    /**
     * Сохранение настроек плагина в БД
     *
     * @return  bool  Результат выполнения
     */
    protected function saveSettings()
    {
        $db = Eresus_CMS::getLegacyKernel()->db;
        $item = $db->selectItem('plugins', "`name`='{$this->name}'");
        $item = $this->__item($item);
        $item['settings'] = $db->escape(encodeOptions($this->settings));
        $result = $db->updateItem('plugins', $item, "`name`='".$this->name."'");
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
     * Дополнительные действия при изменении настроек
     */
    public function onSettingsUpdate()
    {
    }

    /**
     * Сохраняет в БД изменения настроек плагина
     */
    public function updateSettings()
    {
        foreach (array_keys($this->settings) as $key)
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
     * @param  string  $template  Строка в которой требуется провести замену макросов
     * @param  array   $item      Ассоциативный массив со значениями для подстановки вместо макросов
     *
     * @return  string  Метод возвращает строку, в которой заменены все макросы, совпадающие с полями массива item
     */
    public function replaceMacros($template, $item)
    {
        preg_match_all('/\$\(([^(]+)\)/U', $template, $matches);
        if (count($matches[1]))
        {
            foreach ($matches[1] as $macros)
            {
                if (isset($item[$macros]))
                {
                    $template = str_replace('$('.$macros.')', $item[$macros], $template);
                }
            }
        }
        return $template;
    }

    /**
     * Обновляет контент страницы в БД
     *
     * @param  string  $content  Контент
     */
    protected function updateContent($content)
    {
        $item = Eresus_CMS::getLegacyKernel()->db->
            selectItem('pages', "`id`='".Eresus_Kernel::app()->getPage()->id."'");
        $item['content'] = $content;
        Eresus_CMS::getLegacyKernel()->db->
            updateItem('pages', $item, "`id`='".Eresus_Kernel::app()->getPage()->id."'");
    }

    /**
     * Обновляет контент страницы
     */
    public function update()
    {
        $this->updateContent(arg('content', 'dbsafe'));
        HTTP::redirect(arg('submitURL'));
    }

    /**
     * Отрисовка клиентской части
     *
     * @return  string  контент
     */
    public function clientRenderContent()
    {
        return Eresus_Kernel::app()->getPage()->content;
    }

    /**
     * Отрисовка административной части
     *
     * @return  string  Контент
     */
    public function adminRenderContent()
    {
        $item = Eresus_CMS::getLegacyKernel()->db->selectItem('pages', "`id`='".
            Eresus_Kernel::app()->getPage()->id."'");
        $form = array(
            'name' => 'content',
            'caption' => Eresus_Kernel::app()->getPage()->title,
            'width' => '100%',
            'fields' => array (
                array ('type'=>'hidden','name'=>'update'),
                array ('type' => 'memo', 'name' => 'content', 'label' => strEdit, 'height' => '30'),
            ),
            'buttons' => array('apply', 'reset'),
        );

        $result = Eresus_Kernel::app()->getPage()->renderForm($form, $item);
        return $result;
    }
}

