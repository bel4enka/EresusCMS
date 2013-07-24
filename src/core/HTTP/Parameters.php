<?php
/**
 * Параметры GET или POST
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
 * Параметры GET или POST
 *
 * @package Eresus
 * @subpackage HTTP
 *
 * @since 3.01
 */
class Eresus_HTTP_Parameters
{
    /**
     * Параметры
     * @var array
     * @since 3.01
     */
    private $data = array();

    /**
     * @param array $data
     * @since 3.01
     */
    public function __construct(array $data = array())
    {
        $this->data = $data;
    }

    /**
     * Возвращает все параметры в виде ассоциативного массива
     *
     * @return array
     * @since 3.01
     */
    public function all()
    {
        return $this->data;
    }

    /**
     * Заменяет текущий набор параметров новым
     *
     * @param array $parameters
     *
     * @since 3.01
     */
    public function replace(array $parameters)
    {
        $this->data = $parameters;
    }

    /**
     * Добавляет новый набор параметров
     *
     * @param array $parameters
     *
     * @since 3.01
     */
    public function add(array $parameters)
    {
        $this->data += $parameters;
    }

    /**
     * Возвращает значение параметра
     *
     * @param string $name     имя параметра
     * @param mixed  $default  значение по умолчанию, если параметр отсутствует
     *
     * @return mixed
     *
     * @since 3.01
     */
    public function get($name, $default = null)
    {
        return $this->has($name) ? $this->data[$name] : $default;
    }

    /**
     * Задаёт значение параметра
     *
     * @param string $name   имя параметра
     * @param mixed  $value  значение
     *
     * @since 3.01
     */
    public function set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * Проверяет наличие параметра
     *
     * @param string $name     имя параметра
     *
     * @return bool
     *
     * @since 3.01
     */
    public function has($name)
    {
        return array_key_exists($name, $this->data);
    }
}

