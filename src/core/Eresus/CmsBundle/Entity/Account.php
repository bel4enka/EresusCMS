<?php
/**
 * ${product.title}
 *
 * Учётная запись пользователя
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

namespace Eresus\CmsBundle\Entity;

use LogicException;
use InvalidArgumentException;
use Eresus\ORMBundle\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Учётная запись пользователя
 *
 * @package Eresus
 * @since 3.01
 *
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 */
class Account extends AbstractEntity
{
    /**
     * Идентификатор
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * Имя входа
     *
     * @var string
     *
     * @ORM\Column(length=16)
     */
    protected $login;

    /**
     * Хэш пароля
     *
     * @var string
     *
     * @ORM\Column(length=32)
     */
    protected $hash;

    /**
     * Активность
     *
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $active;

    /**
     * Дата последнего визита
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $lastVisit;

    /**
     * Дата последней авторизации
     *
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $lastLoginTime;

    /**
     * Количество неудачных авторизаций
     *
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $loginErrors;

    /**
     * Уровень доступа
     *
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $access;

    /**
     * Имя
     *
     * @var string
     *
     * @ORM\Column(length=64)
     */
    protected $name;

    /**
     * E-mail
     *
     * @var string
     *
     * @ORM\Column(length=64)
     */
    protected $mail;

    /**
     * Профиль
     *
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected $profile;
}