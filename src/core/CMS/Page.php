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
 *
 * @package Eresus
 */

/**
 * Страница, создаваемая CMS
 *
 * Как правило, результатом запроса к сайту является страница (документ) HTML, отправляемый в
 * браузеру. Этот класс описывает такую страницу.
 *
 * @property-read string $title        полный заголовок страницы
 * @property-read string $description  полное описание страницы
 * @property-read string $keywords     ключевые слова страницы
 *
 * @package Eresus
 * @since 3.01
 */
abstract class Eresus_CMS_Page
{
    /**
     * Сообщения об ошибках
     * @var array
     * @since 3.01
     */
    private $errorMessages = array();

    /**
     * Магический метод для доступа к свойствам страницы
     *
     * @param string $property  имя свойства
     * @return mixed
     * @since 3.01
     */
    public function __get($property)
    {
        $method = 'get' . $property;
        if (method_exists($this, $method))
        {
            return $this->{$method}();
        }
        return null;
    }

    /**
     * Магический метод записи свойств страницы
     *
     * @param string $property  имя свойства
     * @param mixed  $value
     *
     * @throws LogicException
     *
     * @since 3.01
     */
    public function __set($property, $value)
    {
        $method = 'set' . $property;
        if (method_exists($this, $method))
        {
            $this->{$method}($value);
        }
        else
        {
            throw new LogicException(sprintf(
                'Property "%s" not exists in class "%s"', $property, get_class($this)
            ));
        }
    }

    /**
     * Добавляет на страницу сообщение об ошибке
     *
     * @param string $html  сообщение
     *
     * @see getErrorMessages
     * @see clearErrorMessages
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
     * @see addErrorMessage
     * @see clearErrorMessages
     */
    public function getErrorMessages()
    {
        return $this->errorMessages;
    }

    /**
     * Очищает имеющиеся сообщения об ошибках
     *
     * @since 3.01
     * @see addErrorMessage
     * @see getErrorMessages
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
}

