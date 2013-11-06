<?php
/**
 * Построитель адресов с аргументами в запросе
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

namespace Eresus\UI\Control\UrlBuilder;

use Eresus\UI\Control\AbstractControl;

/**
 * Построитель адресов с аргументами в запросе
 *
 * @api
 * @since 3.01
 */
class QueryUrlBuilder implements  UrlBuilderInterface
{
    /**
     * Базовый URL
     *
     * Всегда заканчивается символом & или ?
     *
     * @var string
     * @see __construct()
     * @since 3.01
     */
    private $baseURL;

    /**
     * Имя аргумента для передачи идентификатора
     *
     * @var string
     * @see setIdName()
     * @since 3.01
     */
    private $idName = 'id';

    /**
     * Конструктор
     *
     * @param string $baseURL  базовый URL, все аргументы будет присоединяться к нему
     *
     * @since 3.01
     */
    public function __construct($baseURL)
    {
        $this->baseURL = $baseURL;
        $lastChar = mb_substr($this->baseURL, -1);
        if ('&' != $lastChar && '?' != $lastChar)
        {
            $hasQuery = mb_strpos($this->baseURL, '?') !== false;
            $this->baseURL .= $hasQuery ? '&' : '?';
        }
    }

    /**
     * Задаёт имя аргумента для передачи идентификатора элемента списка
     *
     * По умолчанию имя аргумента «id».
     *
     * @param string $name
     *
     * @return void
     *
     * @since 3.01
     */
    public function setIdName($name)
    {
        $this->idName = strval($name);
    }

    /**
     * Возвращает адрес действия для переданного ЭУ
     *
     * @param array $params  ассоциативный массив, содержащий именованные параметры для URL
     *
     * @return string  URL
     *
     * @since 3.01
     */
    public function getUrl(array $params = null)
    {
        if (count($params) == 0)
        {
            return $this->baseURL;
        }

        $url = array();
        foreach ($params as $key => $value)
        {
            if ($value)
            {
                $url []= strval($key) . '=' . strval($value);
            }
        }
        $url = implode('&', $url);
        return $this->baseURL . $url;
    }

    /**
     * Возвращает адрес действия для переданного ЭУ
     *
     * @param string $action  действие
     * @param string $id      идентификатор объекта (опционально)
     *
     * @return string  URL
     *
     * @since 3.01
     */
    public function getActionUrl($action, $id = null)
    {
        return $this->getUrl(array('action' => $action, 'id' => $id));
    }
}

