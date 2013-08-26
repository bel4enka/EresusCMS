<?php
/**
 * Учётная запись
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
 * Учётная запись
 *
 * @property       string $name
 * @property       string $login
 * @property       int    $access
 * @property       bool   $active
 * @property-write string $password
 * @property       string $hash      хэш текущего пароля
 * @property       string $mail
 *
 * @package Eresus
 */
class Eresus_Entity_Account extends Eresus_ORM_Entity
{
    /**
     * Хеширует пароль
     *
     * @param string $password  Пароль
     *
     * @return string  Хеш
     */
    public static function hashPassword($password)
    {
        return md5(md5($password));
    }

    /**
     * Задаёт имя пользователя
     *
     * @param string $name
     * @throws Exception
     */
    protected function setName($name)
    {
        $name = trim(strval($name));
        if (mb_strlen($name) == 0)
        {
            throw new Exception(_('Имя пользователя не может быть пустым.'));
        }
        $this->setProperty('name', $name);
    }

    /**
     * Задаёт пароль
     * @param string $password
     */
    protected function setPassword($password)
    {
        $this->setProperty('hash', self::hashPassword($password));
    }
}

