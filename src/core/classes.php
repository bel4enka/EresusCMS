<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2004, Михаил Красильников <mihalych@vsepofigu.ru>
 * @copyright 2007, Eresus Project, http://eresus.ru/
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


/**
 * Плагин повреждён
 *
 * Обычно это означает синтаксическую ошибку в файле плагина.
 *
 * @package Eresus
 */
class EresusSourceParseException extends EresusRuntimeException {};


/**
 * Работа с плагинами
 *
 * @package Eresus
 */
class Plugins
{
    /**
     * Список всех активированных плагинов
     *
     * @var array
     * @todo сделать private
     */
    public $list = array();

    /**
     * Массив плагинов
     *
     * @var array
     * @todo сделать private
     */
    public $items = array();

    /**
     * Таблица обработчиков событий
     *
     * @var array
     * @todo сделать private
     */
    public $events = array();

    /**
     * Загружает активные плагины
     *
     * @return void
     *
     * @since 2.16
     */
    public function init()
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
    //-----------------------------------------------------------------------------

    /**
     * Устанавливает плагин
     *
     * @param string $name  Имя плагина
     *
     * @return void
     *
     * @throws EresusSourceParseException
     */
    public function install($name)
    {
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, '("%s")', $name);

        $filename = filesRoot.'ext/'.$name.'.php';
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
                throw new DomainException(
                    sprintf('Plugin "%s" is broken: %s', $name, $php_errormsg)
                );
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
                FatalError(sprintf(errClassNotFound, $className));
            }
        }
        else
        {
            Eresus_Kernel::log(__METHOD__, LOG_ERR, 'Can not find main file "%s" for plugin "%s"',
                $filename, $name);
            $msg = I18n::getInstance()->getText('Can not find main file "%s" for plugin "%s"', __CLASS__);
            $msg = sprintf($msg, $filename, $name);
            ErrorMessage($msg);
        }
    }
    //-----------------------------------------------------------------------------

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
     * @return Plugin|TPlugin|bool  Экземпляр плагина или false если не удалось загрузить плагин
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

        // Путь к файлу плагина
        $filename = filesRoot . 'ext/' . $name . '.php';

        /* Если такого файла нет, возвращаем FASLE */
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
            FatalError(sprintf(errClassNotFound, $name));
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
                $templates = new Templates();
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
                $controller = new Eresus_CMS_Controller_Client_UrlContent();
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
                        ErrorMessage(sprintf(errMethodNotFound, 'clientRenderContent',
                            get_class($this->items[$page->type])));
                    }
                }
                else
                {
                    ErrorMessage(sprintf(errContentPluginNotFound, $page->type));
                }
        }
        if (isset($controller)
            && $controller instanceof Eresus_CMS_Controller_Client_ContentInterface)
        {
            $controller->setPage($page);
            $result = $controller->getHtml();
        }
        return $result;
    }

    function clientOnStart()
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
            else ErrorMessage(sprintf(errMethodNotFound, 'adminOnMenuRender', $plugin));
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

/* * * * * * * * * * * * * * * * * * * * * * * *
*
*     Классы-предки для создания плагинов
*
* * * * * * * * * * * * * * * * * * * * * * * */

/**
 * Класс для обратной совместимости
 *
 * @package Eresus
 * @subpackage BC
 * @deprecated с 3.01 используйте {@link Eresus_Plugin}
 */
class Plugin extends Eresus_Plugin
{
}



/**
 * Базовый класс для плагинов, предоставляющих тип контента
 *
 * @package Eresus
 */
class ContentPlugin extends Plugin
{
    /**
     * Конструктор
     *
     * Устанавливает плагин в качестве плагина контента и читает локальные настройки
     */
    public function __construct()
    {
        parent::__construct();

        /* @var TClientUI $page */
        $page = Eresus_Kernel::app()->getPage();
        if ($page instanceof TClientUI)
        {
            $page->plugin = $this->getName();
            if (isset($page->options) && count($page->options))
            {
                foreach ($page->options as $key=>$value)
                {
                    $this->settings[$key] = $value;
                }
            }
        }
    }
    //------------------------------------------------------------------------------

    /**
     * Возвращает информацию о плагине
     *
     * @param  array  $item  Предыдущая версия информации (по умолчанию null)
     *
     * @return  array  Массив информации, пригодный для записи в БД
     */
    public function __item($item = null)
    {
        $result = parent::__item($item);
        $result['content'] = true;
        return $result;
    }
    //------------------------------------------------------------------------------

    /**
     * Действия при удалении раздела данного типа
     * @param int     $id     Идентификатор удаляемого раздела
     * @param string  $table  Имя таблицы
     */
    public function onSectionDelete($id, $table = '')
    {
        if (count($this->dbTable($table)))
            $this->dbDelete($table, $id, 'section');
    }
    //-----------------------------------------------------------------------------

    /**
     * Обновляет контент страницы в БД
     *
     * @param  string  $content  Контент
     */
    public function updateContent($content)
    {
        $item = Eresus_CMS::getLegacyKernel()->db->selectItem('pages', "`id`='".Eresus_Kernel::app()->getPage()->id."'");
        $item['content'] = $content;
        Eresus_CMS::getLegacyKernel()->db->updateItem('pages', $item, "`id`='".Eresus_Kernel::app()->getPage()->id."'");
    }

    /**
     * Обновляет контент страницы
     */
    protected function adminUpdate()
    {
        $this->updateContent(arg('content', 'dbsafe'));
        HTTP::redirect(arg('submitURL'));
    }

    /**
     * Отрисовка клиентской части
     *
     * @return  string  Контент
     */
    public function clientRenderContent()
    {
        /* Если в URL указано что-либо кроме адреса раздела, отправляет ответ 404 */
        if (Eresus_CMS::getLegacyKernel()->request['file'] ||
            Eresus_CMS::getLegacyKernel()->request['query'] ||
            Eresus_Kernel::app()->getPage()->subpage || Eresus_Kernel::app()->getPage()->topic)
        {
            Eresus_Kernel::app()->getPage()->httpError(404);
        }

        return Eresus_Kernel::app()->getPage()->content;
    }

    /**
     * Отрисовка административной части
     *
     * @return  string  Контент
     */
    public function adminRenderContent()
    {
        if (arg('action') == 'update') $this->adminUpdate();
        $item = Eresus_CMS::getLegacyKernel()->db->selectItem('pages', "`id`='".
            Eresus_Kernel::app()->getPage()->id."'");
        $form = array(
            'name' => 'editForm',
            'caption' => Eresus_Kernel::app()->getPage()->title,
            'width' => '100%',
            'fields' => array (
                array ('type'=>'hidden','name'=>'action', 'value' => 'update'),
                array ('type' => 'memo', 'name' => 'content', 'label' => strEdit, 'height' => '30'),
            ),
            'buttons' => array('apply', 'reset'),
        );

        $result = Eresus_Kernel::app()->getPage()->renderForm($form, $item);
        return $result;
    }
    //------------------------------------------------------------------------------
}

/**
 * Базовый класс коннектора сторонних расширений
 *
 * @package Eresus
 */
class EresusExtensionConnector
{
    /**
     * Корневой URL расширения
     *
     * @var string
     */
    protected $root;

    /**
     * Корневой путь расширения
     *
     * @var string
     */
    protected $froot;

    /**
     * Конструктор
     *
     * @return EresusExtensionConnector
     */
    function __construct()
    {
        $name = strtolower(substr(get_class($this), 0, -9));
        $this->root = Eresus_CMS::getLegacyKernel()->root.'ext-3rd/'.$name.'/';
        $this->froot = Eresus_CMS::getLegacyKernel()->froot.'ext-3rd/'.$name.'/';
    }
    //-----------------------------------------------------------------------------

    /**
     * Заменяет глобальные макросы
     *
     * @param string $text
     * @return string
     */
    protected function replaceMacros($text)
    {
        $text = str_replace(
            array(
                '$(httpHost)',
                '$(httpPath)',
                '$(httpRoot)',
                '$(styleRoot)',
                '$(dataRoot)',
            ),
            array(
                Eresus_CMS::getLegacyKernel()->host,
                Eresus_CMS::getLegacyKernel()->path,
                Eresus_CMS::getLegacyKernel()->root,
                Eresus_CMS::getLegacyKernel()->style,
                Eresus_CMS::getLegacyKernel()->data
            ),
            $text
        );

        return $text;
    }
    //-----------------------------------------------------------------------------

    /**
     * Метод вызывается при проксировании прямых запросов к расширению
     *
     */
    function proxy()
    {
        if (!UserRights(EDITOR))
            die;

        $filename = Eresus_CMS::getLegacyKernel()->request['path'] .
            Eresus_CMS::getLegacyKernel()->request['file'];
        $filename = Eresus_CMS::getLegacyKernel()->froot . substr($filename,
                strlen(Eresus_CMS::getLegacyKernel()->root));

        if (is_dir($filename))
        {
            $filename = Eresus_FS_Tool::normalize($filename . '/index.php');
        }

        if (!is_file($filename))
        {
            header('Not found', true, 404);
            die('<h1>Not found.</h1>');
        }

        $ext = strtolower(substr($filename, strrpos($filename, '.') + 1));

        switch (true)
        {
            case in_array($ext, array('png', 'jpg', 'jpeg', 'gif')):
                $info = getimagesize($filename);
                header('Content-type: '.$info['mime']);
                echo file_get_contents($filename);
                break;

            case $ext == 'js':
                header('Content-type: text/javascript');
                $s = file_get_contents($filename);
                $s = $this->replaceMacros($s);
                echo $s;
                break;

            case $ext == 'css':
                header('Content-type: text/css');
                $s = file_get_contents($filename);
                $s = $this->replaceMacros($s);
                echo $s;
                break;

            case $ext == 'html':
            case $ext == 'htm':
                header('Content-type: text/html');
                $s = file_get_contents($filename);
                $s = $this->replaceMacros($s);
                echo $s;
                break;

            case $ext == 'php':
                Eresus_CMS::getLegacyKernel()->conf['debug']['enable'] = false;
                restore_error_handler();
                chdir(dirname($filename));
                require $filename;
                break;
        }
    }
    //-----------------------------------------------------------------------------
}



/**
 * Класс для работы с расширениями системы
 *
 * @package Eresus
 */
class EresusExtensions
{
    /**
     * Загруженные расширения
     *
     * @var array
     */
    var $items = array();
    /**
     * Определение имени расширения
     *
     * @param string $class     Класс расширения
     * @param string $function  Расширяемая функция
     * @param string $name      Имя расширения
     *
     * @return mixed  Имя расширения или false если подходящего расширения не найдено
     */
    function get_name($class, $function, $name = null)
    {
        $result = false;
        if (isset(Eresus_CMS::getLegacyKernel()->conf['extensions']))
        {
            if (isset(Eresus_CMS::getLegacyKernel()->conf['extensions'][$class]))
            {
                if (isset(Eresus_CMS::getLegacyKernel()->conf['extensions'][$class][$function]))
                {
                    $items = Eresus_CMS::getLegacyKernel()->conf['extensions'][$class][$function];
                    reset($items);
                    $result = isset($items[$name]) ? $name : key($items);
                }
            }
        }

        return $result;
    }
    //-----------------------------------------------------------------------------
    /**
     * Загрузка расширения
     *
     * @param string $class     Класс расширения
     * @param string $function  Расширяемая функция
     * @param string $name      Имя расширения
     *
     * @return mixed  Экземпляр класса EresusExtensionConnector или false если не удалось загрузить расширение
     */
    function load($class, $function, $name = null)
    {
        $result = false;
        $name = $this->get_name($class, $function, $name);

        if (isset($this->items[$name]))
        {
            $result = $this->items[$name];
        }
        else
        {
            $filename = Eresus_CMS::getLegacyKernel()->froot.'ext-3rd/' . $name .
                '/eresus-connector.php';
            if (is_file($filename))
            {
                include_once $filename;
                $class = $name.'Connector';
                if (class_exists($class))
                {
                    $this->items[$name] = new $class();
                    $result = $this->items[$name];
                }
            }
        }
        return $result;
    }
    //-----------------------------------------------------------------------------
}

