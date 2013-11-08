<?php
/**
 * Страница, создаваемая CMS
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

namespace Eresus\UI\Page;

use Eresus\UI\Widget;
use Eresus\UI\HTML\Link;
use Eresus\UI\HTML\Meta;
use Eresus\UI\HTML\Script;
use Eresus\UI\HTML\Style;
use Eresus_Kernel;

/**
 * Страница, создаваемая CMS
 *
 * Как правило, результатом запроса к сайту является страница (документ) HTML, отправляемый в
 * браузеру. Этот класс описывает такую страницу.
 *
 * @since 3.01
 */
abstract class Page extends Widget
{
    /**
     * Дополнительные элементы страницы
     *
     * @var Widget[]
     */
    protected $extra = array();

    /**
     * Сообщения об ошибках
     * @var array
     * @since 3.01
     */
    private $errorMessages = array();

    /**
     * Переменные для шаблона
     *
     * @var array
     *
     * @since 3.01
     */
    private $vars = array();

    /**
     * Добавляет на страницу сообщение об ошибке
     *
     * @param string $html  сообщение
     *
     * @see getErrorMessages()
     * @see clearErrorMessages()
     * @since 3.01
     */
    public function addErrorMessage($html)
    {
        $this->errorMessages []= $html;
    }

    /**
     * Возвращает имеющиеся сообщения об ошибках
     *
     * @return string[]
     *
     * @since 3.01
     * @see addErrorMessage()
     * @see clearErrorMessages()
     */
    public function getErrorMessages()
    {
        return $this->errorMessages;
    }

    /**
     * Очищает имеющиеся сообщения об ошибках
     *
     * @since 3.01
     * @see addErrorMessage()
     * @see getErrorMessages()
     */
    public function clearErrorMessages()
    {
        $this->errorMessages = array();
    }

    /**
     * Возвращает полный заголовок страницы
     *
     * Этот метод возвращает полный заголовок страницы, куда, в зависимости от настроек сайта, могут
     * входить: имя сайта, заголовок сайта, заголовок раздела и т. д.
     *
     * @return string
     * @since 3.01
     */
    abstract public function getTitle();

    /**
     * Задаёт заголовок страницы
     *
     * @param string $title
     *
     * @since 3.01
     */
    abstract public function setTitle($title);

    /**
     * Возвращает описание страницы
     *
     * Этот метод возвращает полное описание страницы для мета-тега description. В зависимости от
     * настроек сайта, в него могут входить: описание сайта и описание раздела.
     *
     * @return string
     * @since 3.01
     */
    abstract public function getDescription();

    /**
     * Возвращает ключевые слова страницы
     *
     * Этот метод возвращает полный набор ключевых слов страницы для мета-тега keywords. В
     * зависимости от настроек сайта, в него могут входить: ключевые слова сайта и ключевые слова
     * раздела.
     *
     * @return string
     * @since 3.01
     */
    abstract public function getKeywords();

    /**
     * Добавляет или изменяет мета-тег http-equiv="…"
     *
     * @param string $httpHeader  имя заголовка HTTP
     * @param string $content  	 значение заголовка
     *
     * @since 2.10
     */
    public function setMetaHeader($httpHeader, $content)
    {
        foreach ($this->extra as $item)
        {
            if ($item instanceof Meta && $item->getHeader() == $httpHeader)
            {
                $item->setContent($content);
                return;
            }
        }

        $meta = new Meta($this->getTemplateManager());
        $meta
            ->setHeader($httpHeader)
            ->setContent($content);
        $this->extra []= $meta;
    }

    /**
     * Устанавливает мета-тег name="…"
     *
     * @param string $name     имя
     * @param string $content  значение
     *
     * @since 2.10
     */
    public function setMetaTag($name, $content)
    {
        foreach ($this->extra as $item)
        {
            if ($item instanceof Meta && $item->getName() == $name)
            {
                $item->setContent($content);
                return;
            }
        }

        $meta = new Meta($this->getTemplateManager());
        $meta
            ->setName($name)
            ->setContent($content);
        $this->extra []= $meta;
    }

    /**
     * Подключает файл CSS
     *
     * @param string $url    URL файла
     * @param string $media  тип носителя
     *
     * @since 2.10
     */
    public function linkStyles($url, $media = null)
    {
        /* Проверяем, не добавлен ли уже этот URL  */
        foreach ($this->extra as $item)
        {
            if ($item instanceof Link && $item->getHref() == $url)
            {
                return;
            }
        }

        $link = new Link($this->getTemplateManager());
        $link
            ->setHref($url)
            ->setRel('stylesheet');

        if (!is_null($media))
        {
            $link->setMedia($media);
        }

        $this->extra []= $link;
    }

    /**
     * Встраивает CSS
     *
     * @param string $contents  стили CSS
     * @param string $media     тип носителя
     *
     * @since 2.10
     */
    public function addStyles($contents, $media = '')
    {
        $contents = preg_replace(array('/^(\s)+/m', '/^(\S)/m'), array('		', '	\1'), $contents);
        $contents = rtrim($contents);
        $style = new Style($this->getTemplateManager());
        $style
            ->setContents($contents)
            ->setMedia($media);
        $this->extra []= $style;
    }

    /**
     * Подключает JavaScript
     *
     * В качестве дополнительных параметров метод может принимать:
     *
     * <b>Параметры загрузки скриптов</b>
     * - async
     * - defer
     * - top
     *
     * Если скрипту передан параметр defer, то скрипт будет подключён в конце документа, перед
     * </body>, в противном случае он будет подключён в <head>.
     *
     * Если передан аргумент «top», то скрипт будет подключен в самом начале блока скриптов.
     *
     * @param string $url  URL скрипта
     * @param string ...   дополнительные параметры
     *
     * @since 2.10
     */
    public function linkScripts($url)
    {
        foreach ($this->extra as $item)
        {
            if ($item instanceof Script && $item->getUrl() == $url)
            {
                return;
            }
        }

        $script = new Script($this->getTemplateManager());
        $script->setUrl($url);

        $args = func_get_args();
        // Отбрасываем $url
        array_shift($args);

        $top = false;

        foreach ($args as $arg)
        {
            switch (strtolower($arg))
            {
                case 'async':
                    $script->setAsync(true);
                    break;
                case 'defer':
                    $script->setDefer(true);
                    break;
                case 'top':
                    $top = true;
                    break;
            }
        }

        if ($top)
        {
            array_unshift($this->extra, $script);
        }
        else
        {
            $this->extra []= $script;
        }
    }

    /**
     * Встраивает в страницу клиентские скрипты
     *
     * <b>Параметры загрузки скриптов</b>
     * - head - вставить в секцию <head> (по умолчанию)
     * - body - вставить в секцию <body>
     *
     * @param string $code  код скрипта
     * @param string ...    дополнительные параметры
     *
     * @since 2.10
     */
    public function addScripts($code)
    {
        $script = new Script($this->getTemplateManager());
        $script->setContents($code);

        $args = func_get_args();
        // Отбрасываем $code
        array_shift($args);

        foreach ($args as $arg)
        {
            switch (strtolower($arg))
            {
                case 'body':
                    $script->setDefer(true);
                    break;
            }
        }

        $this->extra []= $script;
    }

    /**
     * Подключает библиотеку JavaScript
     *
     * При множественном вызове метода, библиотека будет подключена только один раз.
     *
     * Доступные библиотеки:
     *
     * - jquery — {@link http://jquery.com/ jQuery}
     * - modernizr — {@link http://modernizr.com/ Modernizr}
     * - webshim — {@link http://afarkas.github.com/webshim/demos/ Webshim}
     * - webshims — устаревший синоним для webshim
     *
     * Аргументы для библиотеки jquery:
     *
     * - ui — jQuery UI
     * - cookie — jQuery.Cookie
     *
     * @param string $library  имя библиотеки
     * @param ...              дополнительные аргументы
     *
     * @return void
     *
     * @since 2.16
     */
    public function linkJsLib($library)
    {
        $args = func_get_args();
        array_shift($args);
        $root = Eresus_Kernel::app()->getLegacyKernel()->root;
        switch ($library)
        {
            case 'jquery':
                if (in_array('ui', $args))
                {
                    $this->linkScripts($root . 'core/jquery/jquery-ui.min.js', 'top');
                }
                if (in_array('cookie', $args))
                {
                    $this->linkScripts($root . 'core/jquery/jquery.cookie.js', 'top');
                }
                $this->linkScripts($root . 'core/jquery/jquery.min.js', 'top');
                break;
            case 'modernizr':
                $this->linkScripts($root . 'core/js/modernizr/modernizr.min.js', 'top');
                break;
            case 'webshim':
            case 'webshims': // TODO @deprecated удалить
                $this->linkScripts($root . 'core/js/webshim/polyfiller.js', 'top');
                $this->linkJsLib('modernizr');
                $this->linkJsLib('jquery');
                $this->addScripts(
                    "jQuery.webshims.polyfill();\n" .
                    "jQuery.webshims.setOptions('forms-ext'," .
                    " {datepicker: {dateFormat: \"yy-mm-dd\"}});");
                break;
        }
    }

    /**
     * Добавляет переменную в шаблон страницы
     *
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value)
    {
        $this->vars[$name] = $value;
    }

    /**
     * Возвращает разметку
     *
     * @return string  HTML
     *
     * @since 3.01
     */
    public function getHtml()
    {
        $vars = array('page' => $this);
        $vars = array_merge($vars, $this->vars);
        $tmpl = $this->getTemplate();
        $html = $tmpl->compile($vars);

        if (($pos = stripos($html, '</head>')) !== false)
        {
            $html = substr_replace($html, $this->renderHeadSection(), $pos, 0);
        }

        return $html;
    }

    /**
     * Отрисовывает секцию <head>
     *
     * @return string  HTML
     */
    protected function renderHeadSection()
    {
        $result = array(
            'meta' => '',
            'link' => '',
            'style' => '',
            'script' => '',
        );

        foreach ($this->extra as $item)
        {
            switch (true)
            {
                case $item instanceof Meta:
                    $result['meta'] .= $item->getHtml();
                    break;
                case $item instanceof Link:
                    $result['link'] .= $item->getHtml();
                    break;
                case $item instanceof Style:
                    $result['style'] .= $item->getHtml();
                    break;
                case $item instanceof Script:
                    /** @var Script $item */
                    if (!$item->isDefer())
                    {
                        $result['meta'] .= $item->getHtml();
                    }
                    break;
            }
        }

        $html = implode($result) . "\n";
        return $html;
    }

    /**
     * Отрисовка секции <body>
     *
     * @return string  HTML
     */
    protected function renderBodySection()
    {
        $html = '';
        foreach ($this->extra as $item)
        {
            if ($item instanceof Script && $item->isDefer())
            {
                $html .= $item->getHtml() . "\n";
            }
        }
        return $html;
    }
}

