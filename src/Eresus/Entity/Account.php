<?php
/**
 * Учётная запись пользователя
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

namespace Eresus\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * Учётная запись пользователя
 *
 * @api
 * @since 3.01
 *
 * @ORM\Entity
 * @ORM\Table(name="accounts", indexes={
 *     @ORM\Index(name="default_idx", columns={"login", "active"})
 * })
 */
class Account
{
    /**
     * Идентификатор
     *
     * @var int
     *
     * @since 3.01
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * Имя входа
     *
     * @var string
     *
     * @since 3.01
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $login;

    /**
     * Хэш пароля
     *
     * @var string
     *
     * @since 3.01
     *
     * @ORM\Column(type="string", length=32)
     */
    private $hash;

    /**
     * Активность
     *
     * @var bool
     *
     * @since 3.01
     *
     * @ORM\Column(type="boolean")
     */
    private $active = false;

    /**
     * Время последней активности на сайте
     *
     * @var DateTime
     *
     * @since 3.01
     *
     * @ORM\Column(type="datetime")
     */
    private $lastVisit;

    /**
     * Время последнего входа на сайт
     *
     * @var DateTime
     *
     * @since 3.01
     *
     * @ORM\Column(type="datetime")
     */
    private $lastLoginTime;

    /**
     * Кол-во неудачных попыток входа
     *
     * @var int
     *
     * @since 3.01
     *
     * @ORM\Column(type="integer")
     */
    private $loginErrors = 0;

    /**
     * Уровень доступа
     *
     * @var int
     *
     * @since 3.01
     *
     * @ORM\Column(type="integer")
     */
    private $access = USER;

    /**
     * Имя пользователя
     *
     * @var string
     *
     * @since 3.01
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * Адрес e-mail
     *
     * @var string
     *
     * @since 3.01
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $mail;

    /**
     * Профиль
     *
     * @var array
     *
     * @since 3.01
     *
     * @ORM\Column(type="array")
     */
    private $profile = array();

    /**
     * Возвращает идентификатор
     *
     * @return int
     *
     * @since 3.01
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Возвращает имя входа
     *
     * @return string
     *
     * @since 3.01
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Задаёт имя входа
     *
     * @param string $login
     *
     * @since 3.01
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * Возвращает хеш пароля
     *
     * @return string
     *
     * @since 3.01
     */
    public function getPasswordHash()
    {
        return $this->hash;
    }

    /**
     * Задаёт пароль
     *
     * @param string $password
     *
     * @since 3.01
     */
    public function setPassword($password)
    {
        $this->hash = self::hashPassword($password);
    }

    /**
     * Возвращает true, если запись активна
     *
     * @return bool
     *
     * @since 3.01
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Задаёт активность записи
     *
     * @param bool $active
     *
     * @since 3.01
     */
    public function setActive($active)
    {
        $this->active = (bool) $active;
    }

    /**
     * Возвращает время последней активности на сайте
     *
     * @return DateTime
     *
     * @since 3.01
     */
    public function getLastVisit()
    {
        return $this->lastVisit;
    }

    /**
     * Задаёт время последней активности на сайте
     * @param DateTime $time
     *
     * @since 3.01
     */
    public function setLastVisit(DateTime $time)
    {
        $this->lastVisit = $time;
    }

    /**
     * Возвращает время последнего входа на сайт
     *
     * @return DateTime
     *
     * @since 3.01
     */
    public function getLastLoginTime()
    {
        return $this->lastLoginTime;
    }

    /**
     * Задаёт время последнего входа на сайт
     *
     * @param DateTime $time
     *
     * @since 3.01
     */
    public function setLastLoginTime(DateTime $time)
    {
        $this->lastLoginTime = $time;
    }

    /**
     * Возвращает количество ошибочных попыток входа
     *
     * @return int
     *
     * @since 3.01
     */
    public function getLoginErrors()
    {
        return $this->loginErrors;
    }

    /**
     * Задаёт количество ошибочных попыток входа
     *
     * @param int $count
     *
     * @since 3.01
     */
    public function setLoginErrors($count)
    {
        $this->loginErrors = $count;
    }

    /**
     * Возвращает уровень доступа
     *
     * @return int
     *
     * @since 3.01
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * Задаёт уровень доступа
     *
     * @param int $access
     *
     * @since 3.01
     */
    public function setAccess($access)
    {
        $this->access = $access;
    }

    /**
     * Возвращает имя пользователя
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
     * Задаёт имя пользователя
     *
     * @param string $name
     * @throws \Exception
     *
     * @since 3.01
     */
    public function setName($name)
    {
        $name = trim(strval($name));
        if (mb_strlen($name) == 0)
        {
            throw new \Exception(_('Имя пользователя не может быть пустым.'));
        }
        $this->name = $name;
    }

    /**
     * Возвращает адрес e-mail
     *
     * @return string
     *
     * @since 3.01
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Задаёт адрес e-mail
     *
     * @param string $email
     *
     * @since 3.01
     */
    public function setMail($email)
    {
        $this->mail = $email;
    }

    /**
     * Возвращает профиль пользователя
     *
     * @return array
     *
     * @since 3.01
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Задаёт профиль пользователя
     *
     * @param array $profile
     *
     * @since 3.01
     */
    public function setProfile(array $profile)
    {
        $this->profile = $profile;
    }

    /**
     * Хеширует пароль
     *
     * @param string $password  Пароль
     *
     * @return string  хеш пароля
     */
    public static function hashPassword($password)
    {
        return md5(md5($password));
    }
}

