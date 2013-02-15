<?php
/**
 * ${product.title}
 *
 * Страница клиентского интерфейса
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

namespace Eresus\CmsBundle;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

use Eresus\CmsBundle\HTTP\Request;
use Eresus\CmsBundle\Entity\Section;
use Eresus\CmsBundle\Templates;
use Eresus\CmsBundle\WebPage;
use Eresus_Kernel;
use Eresus_CMS;

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
class ClientUI extends WebPage
{
    /**
     * Текущий раздел
     *
     * @var Section
     */
    private $currentSection = null;

    public $name = ''; # Имя страницы
    public $owner = 0; # Идентификатор родительской страницы
    public $section = array(); # Массив заголовков страниц
    public $caption = ''; # Название страницы
    public $hint = ''; # Подсказка с описанием страницы
    public $description = ''; # Описание страницы
    public $keywords = ''; # Ключевые слова страницы
    public $access = GUEST; # Базовый уровень доступа к странице
    public $visible = true; # Видимость страницы
    public $type = 'default'; # Тип страницы
    public $content = ''; # Контент страницы
    public $options = array(); # Опции страницы
    public $Document; # DOM-интерфейс к странице
    public $plugin; # Плагин контента
    public $scripts = ''; # Скрипты
    public $styles = ''; # Стили
    public $subpage = 0; # Подстраница списка элементов

    /**
     * Имя шаблона страницы
     * @var string
     * @since 4.00
     */
    public $template;

    /**
     * Дата создания раздела
     * @var \DateTime
     * @since 4.00
     */
    public $created;

    /**
     * Дата обновления раздела
     * @var \DateTime
     * @since 4.00
     */
    public $updated;

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
     * - http://example.org/articles/p2/123/ — $topic равен «123».
     * - http://example.org/articles/123/ — $topic равен «123».
     * - http://example.org/articles/ — $topic равен «false».
     * - http://example.org/articles/123/file?key=value — $topic равен «123».
     * - http://example.org/articles/file?key=value — $topic равен «false».
     *
     * @var string|bool
     * @since 2.10
     */
    public $topic = false;

    /**
     * true если сейчас обрабатывается ошибкаs
     * @var bool
     * @see httpError()
     */
    private static $error = false;

    /**
     * Подставляет значения макросов
     *
     * @param string $text
     *
     * @return string
     */
    public function replaceMacros($text)
    {
        /** @var Request $request */
        $request = Eresus_Kernel::get('request');
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
                $request->getHost(),
                $request->getBasePath(),
                Eresus_CMS::getLegacyKernel()->root,
                Eresus_CMS::getLegacyKernel()->style,
                Eresus_CMS::getLegacyKernel()->data,

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
    //------------------------------------------------------------------------------

    /**
     * Отрисовка переключателя страниц
     *
     * @param int     $total      Общее количество страниц
     * @param int     $current    Номер текущей страницы
     * @param string  $url        Шаблон адреса для перехода к подстранице.
     * @param array   $templates  Шаблоны оформления
     * @return string
     */
    function pageSelector($total, $current, $url = null, $templates = null)
    {
        if (is_null($url))
        {
            $url = $this->url().'p%d/';
        }
        $Templates = new Templates();
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
    //------------------------------------------------------------------------------

    /**
     * Добавляет маршрут к разделу в список маршрутов
     *
     * @param RouteCollection $routes
     * @param Section         $section
     *
     * @since 4.00
     */
    private function addRoute(RouteCollection $routes, Section $section)
    {
        $url = $section->getClientUrl();
        $route = new Route($url, array(
            'section' => $section,
        ));
        $routes->add(str_replace('/', '_', $url), $route);

        foreach ($section->children as $child)
        {
            $this->addRoute($routes, $child);
        }
    }

    /**
     * Производит разбор URL и загрузку соответствующего раздела
     *
     * @return Section  запрошенный раздел сайта
     */
    private function loadPage()
    {
        /** @var Request $req */
        $req = $this->container->get('request');
        $routes = new RouteCollection();
        /** @var \Eresus\CmsBundle\Repository\SectionRepository $repo */
        $repo = $this->getDoctrine()->getManager()->getRepository('CmsBundle:Section');
        $this->addRoute($routes, $repo->getRoot());
        $context = new RequestContext($req->getLocalUrl());
        $matcher = new UrlMatcher($routes, $context);
        $params = $matcher->match($req->getPathInfo());
        return $params['section'];
    }

    /**
     * Проводит инициализацию страницы
     */
    public function init()
    {
        // TODO Eresus_CMS::getLegacyKernel()->plugins->clientOnStart();

        $section = $this->loadPage();
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
        $this->currentSection = $section;
        $this->id = $section->id;
        $this->name = $section->name;
        $this->owner = $section->parent;
        $this->title = $section->title;
        $this->description = $section->description;
        $this->keywords = $section->keywords;
        $this->caption = $section->caption;
        $this->hint = $section->hint;
        $this->access = $section->access;
        $this->visible = $section->visible;
        $this->type = $section->type;
        $this->template = $section->template;
        $this->created = $section->created;
        $this->updated = $section->updated;
        $this->content = $section->content;
        $this->scripts = '';
        $this->styles = '';
        $this->options = $section->options;
    }

    public function error404()
    {
        $this->httpError(404);
    }

    public function httpError($code)
    {
        if (self::$error)
        {
            return;
        }
        $ERROR = array(
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

        Header($_SERVER['SERVER_PROTOCOL'].' '.$code.' '.$ERROR[$code]['response']);

        if (defined('HTTP_CODE_'.$code))
        {
            $message = constant('HTTP_CODE_'.$code);
        }
        else
        {
            $message = $ERROR[$code]['response'];
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
        if (file_exists(filesRoot.'templates/std/'.$code.'.html'))
        {
            $this->template = 'std/'.$code;
            $this->content = '';
        }
        else
        {
            $this->template = 'default';
            $this->content = '<h1>HTTP ERROR '.$code.': '.$message.'</h1>';
        }
        self::$error = true;
        $this->render();
        exit;
    }
    //-----------------------------------------------------------------------------

    /**
     * Отправляет созданную страницу пользователю
     *
     * @return Response
     */
    public function render()
    {
        if (arg('HTTP_ERROR'))
        {
            $this->httpError(arg('HTTP_ERROR', 'int'));
        }
        /* Отрисовываем контент */
        $contentType = $this->currentSection->getContentType();
        if (null === $contentType)
        {
            throw new \DomainException('Unknown content type: ' . $this->currentSection->type);
        }
        $content = $contentType->getClientController()
            ->indexAction($this->container->get('request'));

        // TODO $content = Eresus_CMS::getLegacyKernel()->plugins->clientRenderContent();
        if ($content instanceof Response)
        {
            return $content;
        }
        $templates = new Templates;
        $this->template = $templates->get($this->template);
        // TODO $content = Eresus_CMS::getLegacyKernel()->plugins->clientOnContentRender($content);

        if (
            isset(Eresus_CMS::getLegacyKernel()->session['msg']['information']) &&
            count(Eresus_CMS::getLegacyKernel()->session['msg']['information'])
        )
        {
            $messages = '';
            foreach (Eresus_CMS::getLegacyKernel()->session['msg']['information'] as $message)
            {
                $messages .= InfoBox($message);
            }
            $content = $messages.$content;
            Eresus_CMS::getLegacyKernel()->session['msg']['information'] = array();
        }
        if (
            isset(Eresus_CMS::getLegacyKernel()->session['msg']['errors']) &&
            count(Eresus_CMS::getLegacyKernel()->session['msg']['errors'])
        )
        {
            $messages = '';
            foreach (Eresus_CMS::getLegacyKernel()->session['msg']['errors'] as $message)
            {
                $messages .= ErrorBox($message);
            }
            $content = $messages.$content;
            Eresus_CMS::getLegacyKernel()->session['msg']['errors'] = array();
        }
        $result = str_replace('$(Content)', $content, $this->template);

        # FIX: Обратная совместимость
        if (!empty($this->styles))
        {
            $this->addStyles($this->styles);
        }

        // TODO $result = Eresus_CMS::getLegacyKernel()->plugins->clientOnPageRender($result);

        // FIXME: Обратная совместимость
        if (!empty($this->scripts))
        {
            $this->addScripts($this->scripts);
        }

        $result = preg_replace('|(.*)</head>|i', '$1'.$this->renderHeadSection()."\n</head>", $result);

        # Замена макросов
        $result = $this->replaceMacros($result);

        if (count($this->headers))
        {
            foreach ($this->headers as $header)
            {
                header($header);
            }
        }

        // TODO $result = Eresus_CMS::getLegacyKernel()->plugins->clientBeforeSend($result);
        return new Response($result);
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
    function pages($pagesCount, $itemsPerPage, $reverse = false)
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
}

