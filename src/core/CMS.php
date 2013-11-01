<?php
/**
 * Класс приложения Eresus CMS
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

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Класс приложения Eresus CMS
 *
 * @property-read string $version  версия CMS
 *
 * @package Eresus
 */
class Eresus_CMS
{
    /**
     * Название CMS
     * @var string
     * @since 3.01
     */
    private $name = 'Eresus';

    /**
     * Версия CMS
     * @var string
     * @since 3.01
     */
    private $version = '${product.version}';

    /**
     * Объект создаваемой страницы
     * @var WebPage
     * @since 3.00
     */
    protected $page;

    /**
     * Основной метод приложения
     *
     * @return int  Код завершения для консольных вызовов
     *
     * @see EresusApplication::main()
     */
    public function main()
    {
        TemplateSettings::setGlobalValue('cms', $this);
        $i18n = I18n::getInstance();
        TemplateSettings::setGlobalValue('i18n', $i18n);
        TemplateSettings::setGlobalValue('Eresus', Eresus_CMS::getLegacyKernel());
    }

    /**
     * Магический метод для обеспечения доступа к свойствам только на чтение
     *
     * @param string $property
     * @return mixed
     * @throws LogicException  если свойства $property нет
     */
    public function __get($property)
    {
        if (property_exists($this, $property))
        {
            return $this->{$property};
        }
        throw new LogicException(sprintf('Trying to access unknown property %s of %s',
            $property, __CLASS__));
    }

    /**
     * Выводит сообщение о фатальной ошибке и прекращает работу приложения
     *
     * @param Exception|string $error  исключение или описание ошибки
     * @param bool             $exit   завершить или нет выполнение приложения
     *
     * @return void
     *
     * @since 2.16
     * @deprecated с 3.01, вбрасывайте исключения
     */
    public function fatalError(/** @noinspection PhpUnusedParameterInspection */
        $error = null, $exit = true)
    {
        include dirname(__FILE__) . '/fatal.html.php';
        die;
    }

    /**
     * Возвращает экземпляр класса Eresus
     *
     * Метод нужен до отказа от класса Eresus
     *
     * @return Eresus
     *
     * @since 3.00
     */
    public static function getLegacyKernel()
    {
        return $GLOBALS['Eresus'];
    }

    /**
     * Возвращает экземпляр класса Eresus_Site
     *
     * @return Eresus_Site
     *
     * @since 3.01
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Возвращает экземпляр класса TClientUI или TAdminUI
     *
     * Метод нужен до отказа от переменной $page
     *
     * @return WebPage
     *
     * @since 3.00
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Обрабатывает запрос к стороннему расширению
     *
     * Вызов производится через коннектор этого расширения
     *
     * @param Request $request
     *
     * @return void
     */
    protected function call3rdPartyExtension(Request $request)
    {
        $extension = substr($request->getDirectory(), 9);
        if (($p = strpos($extension, '/')) !== false)
        {
            $extension = substr($extension, 0, $p);
        }

        $filename = $this->getFsRoot() . '/ext-3rd/' . $extension . '/eresus-connector.php';
        if ($extension && is_file($filename))
        {
            /** @noinspection PhpIncludeInspection */
            include_once $filename;
            $className = $extension . 'Connector';
            /** @var EresusExtensionConnector $connector */
            $connector = new $className;
            $connector->proxy();
        }
        else
        {
            header('404 Not Found', true, 404);
            echo '404 Not Found';
        }
    }

    /**
     * Get application root directory
     *
     * @return string
     * @see fsRoot
     */
    public function getFsRoot()
    {
        return $this->fsRoot;
    }
}

