<?php
/**
 * Реестр модулей расширения
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
 * Реестр модулей расширения
 *
 * @package Eresus
 * @since 3.01
 */
class Eresus_Plugin_Registry
{
    /**
     * Список всех активированных плагинов
     *
     * @var array
     * @deprecated с 3.01
     * @todo сделать private
     */
    public $list = array();

    /**
     * Массив плагинов
     *
     * @var array
     * @deprecated с 3.01
     * @todo сделать private
     */
    public $items = array();

    /**
     * Таблица обработчиков событий
     *
     * @var array
     * @deprecated с 3.01
     * @todo сделать private
     */
    public $events = array();

    /**
     * Экземпляр-одиночка
     * @var Eresus_Plugin_Registry
     * @since 3.01
     */
    private static $instance = null;

    /**
     * Возвращает экземпляр-одиночку
     *
     * @return Eresus_Plugin_Registry
     */
    public static function getInstance()
    {
        if (null === self::$instance)
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @deprecated с 3.01 используйте {@link getInstance()}
     */
    public function __construct()
    {
        $items = Eresus_CMS::getLegacyKernel()->db->select('plugins', 'active = 1');
        if ($items)
        {
            foreach ($items as &$item)
            {
                $item['info'] = unserialize($item['info']);
                $this->list[$item['name']] = $item;
            }

            /* Проверяем зависимости */
            do
            {
                $success = true;
                foreach ($this->list as $plugin => $item)
                {
                    if (!($item['info'] instanceof Eresus_PluginInfo))
                    {
                        continue;
                    }
                    foreach ($item['info']->getRequiredPlugins() as $required)
                    {
                        list ($name, $minVer, $maxVer) = $required;
                        if (
                            !isset($this->list[$name]) ||
                            ($minVer && version_compare($this->list[$name]['info']->version, $minVer, '<')) ||
                            ($maxVer && version_compare($this->list[$name]['info']->version, $maxVer, '>'))
                        )
                        {
                            $msg = 'Plugin "%s" requires plugin %s';
                            $requiredPlugin = $name . ' ' . $minVer . '-' . $maxVer;
                            Eresus_Kernel::log(__CLASS__, LOG_ERR, $msg, $plugin, $requiredPlugin);
                            /*$msg = I18n::getInstance()->getText($msg, $this);
                            ErrorMessage(sprintf($msg, $plugin, $requiredPlugin));*/
                            unset($this->list[$plugin]);
                            $success = false;
                        }
                    }
                }
            }
            while (!$success);

            /* Загружаем плагины */
            foreach ($this->list as $item)
            {
                $this->load($item['name']);
            }
        }

        spl_autoload_register(array($this, 'autoload'));
    }

    /**
     * Загружает активные плагины
     *
     * @return void
     *
     * @since 2.16
     * @deprecated с 3.01 все действия этого метода выполняются в конструкторе
     */
    public function init()
    {
    }

    /**
     * Устанавливает плагин
     *
     * @param string $name  Имя плагина
     *
     * @return void
     */
    public function install($name)
    {
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, '("%s")', $name);

        $legacyKernel = Eresus_Kernel::app()->getLegacyKernel();
        $filename = $legacyKernel->froot . 'ext/'.$name.'.php';
        if (file_exists($filename))
        {
            $info = Eresus_PluginInfo::loadFromFile($filename);
            /*
             * Подключаем плагин через eval чтобы убедиться в отсутствии фатальных синтаксических
             * ошибок. Хотя и не факт, что это сработает.
             */
            $code = file_get_contents($filename);
            $code = preg_replace('/^\s*<\?php|\?>\s*$/m', '', $code);
            $code = str_replace('__FILE__', "'$filename'", $code);
            ini_set('track_errors', true);
            $valid = eval($code) !== false;
            ini_set('track_errors', false);
            if (!$valid)
            {
                Eresus_Kernel::app()->getPage()->addErrorMessage(
                    sprintf('Plugin "%s" is broken: %s', $name, $php_errormsg));
                return;
            }

            $className = $name;
            if (!class_exists($className, false) && class_exists('T' . $className, false))
            {
                $className = 'T' . $className; // FIXME: Обратная совместимость с версиями до 2.10b2
            }

            if (class_exists($className, false))
            {
                $this->items[$name] = new $className();
                $this->items[$name]->install();
                $item = $this->items[$name]->__item();
                $item['info'] = serialize($info);
                Eresus_CMS::getLegacyKernel()->db->insert('plugins', $item);
            }
            else
            {
                Eresus_Kernel::app()->getPage()->addErrorMessage(
                    sprintf(errClassNotFound, $className));
            }
        }
        else
        {
            Eresus_Kernel::log(__METHOD__, LOG_ERR, 'Can not find main file "%s" for plugin "%s"',
                $filename, $name);
            $msg = I18n::getInstance()->getText('Can not find main file "%s" for plugin "%s"', __CLASS__);
            $msg = sprintf($msg, $filename, $name);
            Eresus_Kernel::app()->getPage()->addErrorMessage($msg);
        }
    }

    /**
     * Деинсталлирует плагин
     *
     * @param string $name  Имя плагина
     */
    public function uninstall($name)
    {
        if (!isset($this->items[$name]))
        {
            $this->load($name);
        }
        if (isset($this->items[$name]))
        {
            $this->items[$name]->uninstall();
        }
        $item = Eresus_CMS::getLegacyKernel()->db->selectItem('plugins', "`name`='".$name."'");
        if (!is_null($item))
        {
            Eresus_CMS::getLegacyKernel()->db->delete('plugins', "`name`='".$name."'");
        }
        //$filename = filesRoot.'ext/'.$name.'.php';
        //if (file_exists($filename)) unlink($filename);
    }

    /**
     * Загружает плагин и возвращает его экземпляр
     *
     * Метод пытается загрузить плагин с именем $name (если он не был загружен ранее). В случае успеха
     * создаётся и возвращается экземпляр основного класса плагина (либо экземпляр, созданный ранее).
     *
     * @param string $name  Имя плагина
     *
     * @return Plugin|TContentPlugin|bool  Экземпляр плагина или false если не удалось загрузить плагин
     *
     * @since 2.10
     */
    public function load($name)
    {
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, '("%s")', $name);
        /* Если плагин уже был загружен возвращаем экземпляр из реестра */
        if (isset($this->items[$name]))
        {
            Eresus_Kernel::log(__METHOD__, LOG_DEBUG, 'Plugin "%s" already loaded', $name);
            return $this->items[$name];
        }

        /* Если такой плагин не зарегистрирован, возвращаем FASLE */
        if (!isset($this->list[$name]))
        {
            Eresus_Kernel::log(__METHOD__, LOG_DEBUG, 'Plugin "%s" not registered', $name);
            return false;
        }

        $legacyKernel = Eresus_Kernel::app()->getLegacyKernel();
        // Путь к файлу плагина
        $filename = $legacyKernel->froot . 'ext/' . $name . '.php';

        /* Если такого файла нет, возвращаем false */
        if (!file_exists($filename))
        {
            Eresus_Kernel::log(__METHOD__, LOG_ERR, 'Can not find main file "%s" for plugin "%s"',
                $filename, $name);
            return false;
        }

        include_once $filename;
        $className = $name;

        /* TODO: Обратная совместимость с версиями до 2.10b2. Отказаться в новых версиях */
        if (!class_exists($className, false) && class_exists('T' . $className))
        {
            $className = 'T' . $className;
        }

        if (!class_exists($className, false))
        {
            Eresus_Kernel::log(__METHOD__, LOG_ERR, 'Main class %s for plugin "%s" not found in "%s"',
                $className, $name, $filename);
            Eresus_Kernel::app()->getPage()->addErrorMessage(sprintf(errClassNotFound, $name));
            return false;
        }

        // Заносим экземпляр в реестр
        $this->items[$name] = new $className();
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, 'Plugin "%s" loaded', $name);

        return $this->items[$name];
    }
    //-----------------------------------------------------------------------------

    /**
     * Отрисовка контента раздела
     *
     * @return string  Контент
     */
    public function clientRenderContent()
    {
        /* @var TClientUI $page */
        $page = Eresus_Kernel::app()->getPage();
        $result = '';
        switch ($page->type)
        {
            case 'default':
                $plugin = new ContentPlugin;
                $result = $plugin->clientRenderContent();
                break;
            case 'list':
                /* Если в URL указано что-либо кроме адреса раздела, отправляет ответ 404 */
                if (Eresus_CMS::getLegacyKernel()->request['file'] ||
                    Eresus_CMS::getLegacyKernel()->request['query'] ||
                    $page->subpage ||
                    $page->topic)
                {
                    $page->httpError(404);
                }

                $subitems = Eresus_CMS::getLegacyKernel()->db->select('pages', "(`owner`='" .
                    $page->id .
                    "') AND (`active`='1') AND (`access` >= '" .
                    (Eresus_CMS::getLegacyKernel()->user['auth'] ?
                        Eresus_CMS::getLegacyKernel()->user['access'] : GUEST)."')", "`position`");
                if (empty($page->content))
                {
                    $page->content = '$(items)';
                }
                $templates = Templates::getInstance();
                $template = $templates->get('SectionListItem', 'std');
                if (false === $template)
                {
                    $template = '<h1><a href="$(link)" title="$(hint)">$(caption)</a></h1>$(description)';
                }
                $items = '';
                foreach ($subitems as $item)
                {
                    $items .= str_replace(
                        array(
                            '$(id)',
                            '$(name)',
                            '$(title)',
                            '$(caption)',
                            '$(description)',
                            '$(hint)',
                            '$(link)',
                        ),
                        array(
                            $item['id'],
                            $item['name'],
                            $item['title'],
                            $item['caption'],
                            $item['description'],
                            $item['hint'],
                            Eresus_CMS::getLegacyKernel()->request['url'] .
                            ($page->name == 'main' &&
                            !$page->owner ? 'main/' : '').$item['name'].'/',
                        ),
                        $template
                    );
                }
                $result = str_replace('$(items)', $items, $page->content);
                break;
            case 'url':
                $controller = new Eresus_Client_Controller_Content_Url();
                break;
            default:
                if ($this->load($page->type))
                {
                    if (method_exists($this->items[$page->type], 'clientRenderContent'))
                    {
                        $result = $this->items[$page->type]->clientRenderContent();
                    }
                    else
                    {
                        Eresus_Kernel::app()->getPage()->addErrorMessage(
                            sprintf(errMethodNotFound, 'clientRenderContent',
                                get_class($this->items[$page->type])));
                    }
                }
                else
                {
                    Eresus_Kernel::app()->getPage()->addErrorMessage(
                        sprintf(errContentPluginNotFound, $page->type));
                }
        }
        if (isset($controller)
            && $controller instanceof Eresus_Client_Controller_Content_Interface)
        {
            $controller->setPage($page);
            $result = $controller->getHtml();
        }
        return $result;
    }

    /**
     *
     */
    public function clientOnStart()
    {
        if (isset($this->events['clientOnStart'])) foreach($this->events['clientOnStart'] as $plugin) $this->items[$plugin]->clientOnStart();
    }

    function clientOnURLSplit($item, $url)
    {
        if (isset($this->events['clientOnURLSplit'])) foreach($this->events['clientOnURLSplit'] as $plugin) $this->items[$plugin]->clientOnURLSplit($item, $url);
    }

    function clientOnTopicRender($text, $topic = null)
    {
        if (isset($this->events['clientOnTopicRender'])) foreach($this->events['clientOnTopicRender'] as $plugin) $text = $this->items[$plugin]->clientOnTopicRender($text, $topic);
        return $text;
    }

    function clientOnContentRender($text)
    {
        if (isset($this->events['clientOnContentRender']))
            foreach($this->events['clientOnContentRender'] as $plugin) $text = $this->items[$plugin]->clientOnContentRender($text);
        return $text;
    }

    function clientOnPageRender($text)
    {
        if (isset($this->events['clientOnPageRender']))
            foreach($this->events['clientOnPageRender'] as $plugin) $text = $this->items[$plugin]->clientOnPageRender($text);
        return $text;
    }

    function clientBeforeSend($text)
    {
        if (isset($this->events['clientBeforeSend']))
            foreach($this->events['clientBeforeSend'] as $plugin) $text = $this->items[$plugin]->clientBeforeSend($text);
        return $text;
    }

    /* function clientOnFormControlRender($formName, $control, $text)
    {
        if (isset($this->events['clientOnFormControlRender'])) foreach($this->events['clientOnFormControlRender'] as $plugin) $text = $this->items[$plugin]->clientOnFormControlRender($formName, $control, $text);
        return $text;
    }*/

    function adminOnMenuRender()
    {
        if (isset($this->events['adminOnMenuRender'])) foreach($this->events['adminOnMenuRender'] as $plugin)
            if (method_exists($this->items[$plugin], 'adminOnMenuRender')) $this->items[$plugin]->adminOnMenuRender();
            else
            {
                Eresus_Kernel::app()->getPage()->addErrorMessage(
                    sprintf(errMethodNotFound, 'adminOnMenuRender', $plugin));
            }
    }

    /**
     * Событие ajaxOnRequest
     */
    function ajaxOnRequest()
    {
        if (isset($this->events['ajaxOnRequest']))
            foreach($this->events['ajaxOnRequest'] as $plugin)
                $this->items[$plugin]->ajaxOnRequest();
    }
    //-----------------------------------------------------------------------------

    /**
     * Автозагрузка классов плагинов
     *
     * @param string $className
     *
     * @return boolean
     *
     * @since 3.00
     */
    public function autoload($className)
    {
        $pluginName = strtolower(substr($className, 0, strpos($className, '_')));

        if ($this->load($pluginName))
        {
            $filename = Eresus_Kernel::app()->getFsRoot() . '/ext/' . $pluginName . '/classes/' .
                str_replace('_', '/', substr($className, strlen($pluginName) + 1)) . '.php';
            if (file_exists($filename))
            {
                include $filename;
                return Eresus_Kernel::classExists($className);
            }
        }

        return false;
    }
    //-----------------------------------------------------------------------------
}

