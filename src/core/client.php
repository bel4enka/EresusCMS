<?php
/**
 * Страница клиентского интерфейса
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
 * Признак клиентского интерфейса
 *
 * @var bool
 */
define('CLIENTUI', true);

/**
 * Страница клиентского интерфейса
 *
 * @package Eresus
 */
class TClientUI extends Eresus_CMS_Page_Client
{
    public $name = ''; # Имя страницы
    public $owner = 0; # Идентификатор родительской страницы
    public $section = array(); # Массив заголовков страниц
    public $caption = ''; # Название страницы
    public $hint = ''; # Подсказка с описанием страницы
    public $description = ''; # Описание страницы
    public $keywords = ''; # Ключевые слова страницы
    public $access = GUEST; # Базовый уровень доступа к странице
    public $visible = true; # Видимость страницы

    /**
     * Шаблон страницы
     *
     * Ранее это свойство создавалось динамически и не было документировано. Если ваш модуль
     * использует эту недокументированную возможность, вы можете вместо чтения свойства $template
     * использовать метод {@link getTemplateName()}.
     *
     * @var Eresus_Template
     * @since 3.01
     */
    private $template;

    public $type = 'default'; # Тип страницы

    /**
     * Отрисованное содержимое области контента страницы
     *
     * @var string
     */
    public $content = '';
    public $options = array(); # Опции страницы
    public $Document; # DOM-интерфейс к странице
    public $plugin; # Плагин контента

    /**
     * Дополнительные скрипты
     * @var string
     * @deprecated с 3.01
     */
    public $scripts = '';

    /**
     * Дополнительные стили
     * @var string
     * @deprecated с 3.01
     */
    public $styles = '';
    public $subpage = 0; # Подстраница списка элементов

    /**
     * Идентификатор объекта контента
     *
     * Объект контента (или «топик») – это статья, новость, фотография или другой объект в разделе,
     * содержащим список таких однотипных объектов.
     *
     * В $topic помещается элемент массива {@link $Eresus::$request}}['params'] , следующий после
     * адреса текущего раздела и номера подстраницы списка (если он есть). Если такого элемента в
     * массиве нет, то $topic будет равен false.
     *
     * Примеры:
     *
     * - http://exmaple.org/articles/p2/123/ — $topic равен «123».
     * - http://exmaple.org/articles/123/ — $topic равен «123».
     * - http://exmaple.org/articles/ — $topic равен «false».
     * - http://exmaple.org/articles/123/file?key=value — $topic равен «123».
     * - http://exmaple.org/articles/file?key=value — $topic равен «false».
     *
     * @var string|bool
     * @since 2.10
     */
    public $topic = false;

    /**
     * Признак того, что сейчас обрабатывается ошибка
     *
     * @var bool
     * @since 3.01
     */
    private $processingError = false;

    /**
     * Подставляет значения макросов
     *
     * @param string $text
     * @return mixed
     */
    public function replaceMacros($text)
    {
        $section = $this->section;
        if (siteTitleReverse)
        {
            $section = array_reverse($section);
        }
        $section = strip_tags(implode($section, option('siteTitleDivider')));

        $result = str_replace(
            array(
                '$(httpHost)',
                '$(httpPath)',
                '$(httpRoot)',
                '$(styleRoot)',
                '$(dataRoot)',

                '$(siteName)',
                '$(siteTitle)',
                '$(siteKeywords)',
                '$(siteDescription)',

                '$(pageId)',
                '$(pageName)',
                '$(pageTitle)',
                '$(pageCaption)',
                '$(pageHint)',
                '$(pageDescription)',
                '$(pageKeywords)',
                '$(pageAccessLevel)',
                '$(pageAccessName)',

                '$(sectionTitle)',
            ),
            array(
                httpHost,
                httpPath,
                httpRoot,
                styleRoot,
                dataRoot,

                siteName,
                siteTitle,
                siteKeywords,
                siteDescription,

                $this->id,
                $this->name,
                $this->title,
                $this->caption,
                $this->hint,
                $this->description,
                $this->keywords,
                $this->access,
                constant('ACCESSLEVEL'.$this->access),
                $section,
            ),
            $text
        );
        $result = preg_replace_callback('/\$\(const:(.*?)\)/i', '__macroConst', $result);
        $result = preg_replace_callback('/\$\(var:(([\w]*)(\[.*?\]){0,1})\)/i', '__macroVar', $result);
        $result = preg_replace('/\$\(\w+(:.*?)*?\)/', '', $result);
        return $result;
    }

    /**
     * Отрисовка переключателя страниц
     *
     * @param int     $total      Общее количество страниц
     * @param int     $current    Номер текущей страницы
     * @param string  $url        Шаблон адреса для перехода к подстранице.
     * @param array   $templates  Шаблоны оформления
     * @return string
     */
    public function pageSelector($total, $current, $url = null, $templates = null)
    {
        if (is_null($url))
        {
            $url = $this->url().'p%d/';
        }
        $Templates = Templates::getInstance();
        $defaults = explode('---', $Templates->get('PageSelector', 'std'));
        if (!is_array($templates))
        {
            $templates = array();
        }
        for ($i=0; $i < 5; $i++)
        {
            if (!isset($templates[$i]))
            {
                $templates[$i] = $defaults[$i];
            }
        }
        $result = parent::pageSelector($total, $current, $url, $templates);
        return $result;
    }

    /**
     * Производит разбор URL и загрузку соответствующего раздела
     *
     * @return  array|bool  Описание загруженного раздела или false если он не найден
     */
    private function loadPage()
    {
        $result = false;
        $main_fake = false;
        if (!count(Eresus_CMS::getLegacyKernel()->request['params']) ||
            Eresus_CMS::getLegacyKernel()->request['params'][0] != 'main')
        {
            array_unshift(Eresus_CMS::getLegacyKernel()->request['params'], 'main');
            $main_fake = true;
        }
        reset(Eresus_CMS::getLegacyKernel()->request['params']);
        $item['id'] = 0;
        $url = '';
        do
        {
            $items = Eresus_CMS::getLegacyKernel()->sections->children($item['id'],
                Eresus_CMS::getLegacyKernel()->user['auth'] ?
                    Eresus_CMS::getLegacyKernel()->user['access'] : GUEST, SECTIONS_ACTIVE);
            $item = false;
            for ($i=0; $i<count($items); $i++)
            {
                if ($items[$i]['name'] == current(Eresus_CMS::getLegacyKernel()->request['params']))
                {
                    $result = $item = $items[$i];
                    if ($item['id'] != 1 || !$main_fake)
                    {
                        $url .= $item['name'].'/';
                    }
                    Eresus_CMS::getLegacyKernel()->plugins->clientOnURLSplit($item, $url);
                    $this->section[] = $item['title'];
                    next(Eresus_CMS::getLegacyKernel()->request['params']);
                    array_shift(Eresus_CMS::getLegacyKernel()->request['params']);
                    break;
                }
            }
            if ($item && $item['id'] == 1 && $main_fake)
            {
                $item['id'] = 0;
            }
        }
        while ($item && current(Eresus_CMS::getLegacyKernel()->request['params']));
        Eresus_CMS::getLegacyKernel()->request['path'] =
        Eresus_CMS::getLegacyKernel()->request['path'] = Eresus_CMS::getLegacyKernel()->root . $url;
        if ($result)
        {
            $result = Eresus_CMS::getLegacyKernel()->sections->get($result['id']);
        }
        return $result;
    }

    /**
     * Проводит инициализацию страницы
     */
    private function init()
    {
        Eresus_CMS::getLegacyKernel()->plugins->clientOnStart();

        $item = $this->loadPage();
        if ($item)
        {
            if (count(Eresus_CMS::getLegacyKernel()->request['params']))
            {
                if (preg_match('/p[\d]+/i', Eresus_CMS::getLegacyKernel()->request['params'][0]))
                {
                    $this->subpage = substr(array_shift(Eresus_CMS::getLegacyKernel()->request['params']), 1);
                }

                if (count(Eresus_CMS::getLegacyKernel()->request['params']))
                {
                    $this->topic = array_shift(Eresus_CMS::getLegacyKernel()->request['params']);
                }
            }
            $this->dbItem = $item;
            $this->id = $item['id'];
            $this->name = $item['name'];
            $this->owner = $item['owner'];
            $this->title = $item['title'];
            $this->description = $item['description'];
            $this->keywords = $item['keywords'];
            $this->caption = $item['caption'];
            $this->hint = $item['hint'];
            $this->access = $item['access'];
            $this->visible = $item['visible'];
            $this->type = $item['type'];
            $this->setTemplate($item['template']);
            $this->created = $item['created'];
            $this->updated = $item['updated'];
            $this->content = $item['content'];
            $this->scripts = '';
            $this->styles = '';
            $this->options = $item['options'];
        }
        else
        {
            $this->httpError(404);
        }
    }

    /**
     * Выводит сообщение об ошибке HTTP 404 и прекращает выполнение программы
     *
     * @deprecated используйте {@link httpError()}
     */
    public function Error404()
    {
        $this->httpError(404);
    }

    /**
     * Выводит сообщение об ошибке и прекращает выполнение программы
     *
     * @param int $code  код ошибки HTTP
     */
    public function httpError($code)
    {
        if (true === $this->processingError)
        {
            return;
        }
        $httpErrors = array(
            '400' => array('response' => 'Bad Request'),
            '401' => array('response' => 'Unauthorized'),
            '402' => array('response' => 'Payment Required'),
            '403' => array('response' => 'Forbidden'),
            '404' => array('response' => 'Not Found'),
            '405' => array('response' => 'Method Not Allowed'),
            '406' => array('response' => 'Not Acceptable'),
            '407' => array('response' => 'Proxy Authentication Required'),
            '408' => array('response' => 'Request Timeout'),
            '409' => array('response' => 'Conflict'),
            '410' => array('response' => 'Gone'),
            '411' => array('response' => 'Length Required'),
            '412' => array('response' => 'Precondition Failed'),
            '413' => array('response' => 'Request Entity Too Large'),
            '414' => array('response' => 'Request-URI Too Long'),
            '415' => array('response' => 'Unsupported Media Type'),
            '416' => array('response' => 'Requested Range Not Satisfiable'),
            '417' => array('response' => 'Expectation Failed'),
        );

        header($_SERVER['SERVER_PROTOCOL'] . ' ' . $code . ' ' . $httpErrors[$code]['response']);

        if (defined('HTTP_CODE_'.$code))
        {
            $message = constant('HTTP_CODE_'.$code);
        }
        else
        {
            $message = $httpErrors[$code]['response'];
        }

        $this->section = array(siteTitle, $message);
        $this->title = $message;
        $this->description = '';
        $this->keywords = '';
        $this->caption = $message;
        $this->hint = '';
        $this->access = GUEST;
        $this->visible = true;
        $this->type = 'default';
        $this->content = '';
        $this->setTemplate(strval($code), 'std');
        if (null === $this->template)
        {
            $this->setTemplate('default');
            $this->content = "<h1>HTTP {$code}: {$message}</h1>";
        }
        $this->processingError = true;
        $this->render();
        exit;
    }

    /**
     * Отправляет созданную страницу пользователю.
     */
    public function render()
    {
        $this->init();
        if (arg('HTTP_ERROR'))
        {
            $this->httpError(arg('HTTP_ERROR', 'int'));
        }

        $legacyKernel = Eresus_Kernel::app()->getLegacyKernel();
        $plugins = $legacyKernel->plugins;

        $response = $plugins->clientRenderContent();
        if (!($response instanceof Eresus_HTTP_Response))
        {
            $content = new Eresus_CMS_Page_Content($this, $response);
            $content = $content->render();
            if (
                isset($legacyKernel->session['msg']['information']) &&
                count($legacyKernel->session['msg']['information'])
            )
            {
                $messages = '';
                foreach ($legacyKernel->session['msg']['information'] as $message)
                {
                    $messages .= InfoBox($message);
                }
                $content = $messages . $content;
                $legacyKernel->session['msg']['information'] = array();
            }
            if (
                isset($legacyKernel->session['msg']['errors']) &&
                count($legacyKernel->session['msg']['errors'])
            )
            {
                $messages = '';
                foreach ($legacyKernel->session['msg']['errors'] as $message)
                {
                    $messages .= ErrorBox($message);
                }
                $content = $messages . $content;
                $legacyKernel->session['msg']['errors'] = array();
            }

            $this->content = $content;
            $html = $this->template->compile();

            // TODO: Обратная совместимость (удалить)
            if (!empty($this->styles))
            {
                $this->addStyles($this->styles);
            }

            $html = $plugins->clientOnPageRender($html);

            // TODO: Обратная совместимость (удалить)
            if (!empty($this->scripts))
            {
                $this->addScripts($this->scripts);
            }

            $html = preg_replace('|(.*)</head>|i', '$1' . $this->renderHeadSection() . "\n</head>",
                $html);

            $response = new Eresus_HTTP_Response($html, 200, $this->headers);
        }

        // TODO: Обратная совместимость (убрать)
        $response->setContent($this->replaceMacros($response->getContent()));

        $response->sendHeaders();
        $response->setContent($plugins->clientBeforeSend($response->getContent()));
        if (!$legacyKernel->conf['debug']['enable'])
        {
            ob_start('ob_gzhandler');
        }
        $response->sendContent();
        if (!$legacyKernel->conf['debug']['enable'])
        {
            ob_end_flush();
        }
    }

    /**
     * Выводит список подстраниц для навигации по ним
     *
     * @param int  $pagesCount
     * @param int  $itemsPerPage
     * @param bool $reverse
     *
     * @return string
     */
    public function pages($pagesCount, $itemsPerPage, $reverse = false)
    {
        $eresus = Eresus_CMS::getLegacyKernel();

        if ($pagesCount>1)
        {
            $at_once = option('clientPagesAtOnce');
            if (!$at_once)
            {
                $at_once = 10;
            }

            $side_left = '';
            $side_right = '';

            $for_from = $reverse ? $pagesCount : 1;
            $default = $for_from;
            $for_to = $reverse ? 0 : $pagesCount+1;
            $for_delta = $reverse ? -1 : 1;

            # Если количество страниц превышает AT_ONCE
            if ($pagesCount > $at_once)
            {
                # Если установлен обратный порядок страниц
                if ($reverse)
                {
                    if ($this->subpage < ($pagesCount - (integer) ($at_once / 2)))
                    {
                        $for_from = ($this->subpage + (integer) ($at_once / 2));
                    }
                    if ($this->subpage < (integer) ($at_once / 2))
                    {
                        $for_from = $at_once;
                    }
                    $for_to = $for_from - $at_once;
                    if ($for_to < 0)
                    {
                        $for_from += abs($for_to);
                        $for_to = 0;
                    }
                    if ($for_from != $pagesCount)
                    {
                        $side_left = "<a href=\"".$eresus->request['path']."\" title=\"".strLastPage.
                            "\">&nbsp;&laquo;&nbsp;</a>";
                    }
                    if ($for_to != 0)
                    {
                        $side_right = "<a href=\"".$eresus->request['path']."p1/\" title=\"".strFirstPage.
                            "\">&nbsp;&raquo;&nbsp;</a>";
                    }
                }
                # Если установлен прямой порядок страниц
                else
                {
                    if ($this->subpage > (integer) ($at_once / 2))
                    {
                        $for_from = $this->subpage - (integer) ($at_once / 2);
                    }
                    if ($pagesCount - $this->subpage < (integer) ($at_once / 2) + (($at_once % 2)>0))
                    {
                        $for_from = $pagesCount - $at_once+1;
                    }
                    $for_to = $for_from + $at_once;
                    if ($for_from != 1)
                    {
                        $side_left = "<a href=\"".$eresus->request['path']."\" title=\"".strFirstPage.
                            "\">&nbsp;&laquo;&nbsp;</a>";
                    }
                    if ($for_to < $pagesCount)
                    {
                        $side_right = "<a href=\"".$eresus->request['path']."p".$pagesCount."/\" title=\"".
                            strLastPage."\">&nbsp;&raquo;&nbsp;</a>";
                    }
                }
            }
            $result = '<div class="pages">'.strPages;
            $result .= $side_left;
            for ($i = $for_from; $i != $for_to; $i += $for_delta)
            {
                if ($i == $this->subpage)
                {
                    $result .= '<span class="selected">&nbsp;'.$i.'&nbsp;</span>';
                }
                else
                {
                    $result .= '<a href="'.$eresus->request['path'].($i==$default?'':'p'.$i.'/').
                        '">&nbsp;'.$i.'&nbsp;</a>';
                }
            }
            $result .= $side_right;
            $result .= "</div>\n";
            return $result;
        }
        else
        {
            return '';
        }
    }

    /**
     * Возвращает имя шаблона страницы, заданного по умолчанию
     *
     * <b>Обратите внимание!</b> Этот метод всегда возвращает имя шаблона, назначенного разделу
     * в АИ. Метод {@link setTemplate()} не влияет на результат, возвращаемый getTemplateName.
     *
     * @return string
     *
     * @since 3.01
     */
    public function getTemplateName()
    {
        return $this->dbItem['template'];
    }

    /**
     * Задаёт шаблон страницы
     *
     * <b>Обратите внимание!</b> Этот метод не влияет на результат, возвращаемый
     * {@link getTemplateName()}.
     *
     * @param string|Eresus_Template $template  имя файла шаблона или уже созданный объект шаблона
     * @param string                 $type      тип шаблона, только если $template — строка
     *
     * @throws Eresus_Exception_InvalidArgumentType
     *
     * @since 3.01
     */
    public function setTemplate($template, $type = '')
    {
        if (!is_string($template)
            && (!is_object($template) || !($template instanceof Eresus_Template)))
        {
            throw Eresus_Exception_InvalidArgumentType::factory(__METHOD__, 1,
                'string or an instance of Eresus_Template', $template);
        }
        if (is_string($template))
        {
            $template = Templates::getInstance()->load($template, $type);
        }
        $this->template = $template;
    }
}

