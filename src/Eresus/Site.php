<?php
/**
 * Сайт
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

namespace Eresus;

use Eresus\Controller\AdminFrontController;
use Eresus\Exceptions\InvalidArgumentTypeException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Сайт
 *
 * <b>Внимание!</b> Не создавайте экземпляров этого класса самостоятельно!
 *
 *
 *
 * @since 3.01
 */
class Site
{
    /**
     * Контейнер служб
     *
     * @var ContainerInterface
     *
     * @since 3.01
     */
    private $container;

    /**
     * Имя сайта
     *
     * @var string
     *
     * @since 3.01
     */
    private $name = '';

    /**
     * URL корня сайта
     * @var string
     * @since 3.01
     */
    private $rootUrl = '';

    /**
     * URL папки стиля
     * @var string
     * @since 3.01
     */
    private $stylesUrl = '/styles';

    /**
     * Хост сайта
     * @var string
     * @since 3.01
     */
    private $domain = 'localhost';

    /**
     * Заголовок сайта
     * @var string
     * @since 3.01
     */
    private $title = '';

    /**
     * Описание сайта
     * @var string
     * @since 3.01
     */
    private $description = '';

    /**
     * Ключевые слова сайта
     * @var string
     * @since 3.01
     */
    private $keywords = '';

    /**
     * Создаёт описание сайта
     *
     * @param Kernel  $kernel
     * @param Request $request
     */
    public function __construct(Kernel $kernel, Request $request)
    {
        $this->container = $kernel->getContainer();
        $this->rootUrl = $request->getSchemeAndHttpHost() . $request->getBasePath();
        $this->stylesUrl = $this->rootUrl . '/style';
        $this->domain = $request->getHttpHost();

        include $kernel->getAppDir() . '/cfg/settings.php';
        $this->name = siteName;
        $this->title = siteTitle;
        $this->description = siteDescription;
        $this->keywords = siteKeywords;
    }

    /**
     * Возвращает URL корня сайта
     *
     * @return string
     *
     * @since 3.01
     */
    public function getRootUrl()
    {
        return $this->rootUrl;
    }

    /**
     * Возвращает URL папки стилей
     *
     * @return string
     *
     * @since 3.01
     */
    public function getStylesUrl()
    {
        return $this->stylesUrl;
    }

    /**
     * Возвращает доменное имя
     *
     * @return string
     *
     * @since 3.01
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Возвращает название
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
     * Задаёт название
     *
     * @param string $name
     *
     * @throws InvalidArgumentTypeException
     * @since 3.01
     */
    public function setName($name)
    {
        if (!is_string($name))
        {
            throw InvalidArgumentTypeException::factory(__METHOD__, 1, 'string', $name);
        }
        $this->name = $name;
    }

    /**
     * Возвращает название
     *
     * @return string
     *
     * @since 3.01
     * @todo Вынести в отдельный класс
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Задаёт заголовок сайта
     *
     * @param string $title  новый заголовок
     *
     * @throws InvalidArgumentTypeException
     * @since 3.01
     * @todo Вынести в отдельный класс
     */
    public function setTitle($title)
    {
        if (!is_string($title))
        {
            throw InvalidArgumentTypeException::factory(__METHOD__, 1, 'string', $title);
        }
        $this->title = $title;
    }

    /**
     * Возвращает описание
     *
     * @return string
     *
     * @since 3.01
     * @todo Вынести в отдельный класс
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Задаёт описание сайта
     *
     * @param string $description  новое описание
     *
     * @throws InvalidArgumentTypeException
     *
     * @since 3.01
     * @todo Вынести в отдельный класс
     */
    public function setDescription($description)
    {
        if (!is_string($description))
        {
            throw InvalidArgumentTypeException::factory(__METHOD__, 1, 'string', $description);
        }
        $this->description = $description;
    }

    /**
     * Возвращает ключевые слова
     *
     * @return string
     *
     * @since 3.01
     * @todo Вынести в отдельный класс
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Задаёт список ключевых слов
     *
     * @param string $keywords  новый список ключевых слов
     *
     * @throws InvalidArgumentTypeException
     *
     * @since 3.01
     * @todo Вынести в отдельный класс
     */
    public function setKeywords($keywords)
    {
        if (!is_string($keywords))
        {
            throw InvalidArgumentTypeException::factory(__METHOD__, 1, 'string', $keywords);
        }
        $this->keywords = $keywords;
    }

    /**
     * Обрабатывает запрос и возвращает ответ
     *
     * @param Request $request
     *
     * @return Response
     *
     * @since 3.01
     */
    public function handleRequest(Request $request)
    {
        if (substr($request->getPathInfo(), 0, 8) == '/ext-3rd')
        {
            // TODO
            $this->call3rdPartyExtension($request);
        }
        else
        {
            if ($request->getPathInfo() == '/admin/' || $request->getPathInfo() == '/admin.php')
            {
                $controller = new AdminFrontController($this->container, $this);
            }
            else
            {
                // TODO
                $controller = new Eresus_Client_FrontController($this->container, $request);
            }
            /** @var Response $response */
            $response = $controller->process($request);
        }
        return $response;
    }
}

