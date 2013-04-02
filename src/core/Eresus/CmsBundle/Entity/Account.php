<?php
/**
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

use Eresus\ORMBundle\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Учётная запись пользователя
 *
 * @property       int       $id
 * @property       string    $username
 * @property       string    $salt
 * @property       string    $password
 * @property       bool      $isActive
 * @property       string    $email
 *
 * @package Eresus
 * @since 4.00
 *
 * @ORM\Entity
 * @ORM\Table(name="accounts")
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 */
class Account extends AbstractEntity implements UserInterface
{
    /**
     * Идентификатор
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Имя
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    protected $username;

    /**
     * Соль для хэширования пароля
     *
     * @var string
     *
     * @ORM\Column(type="string", length=32)
     */
    protected $salt;

    /**
     * Хэш пароля
     *
     * @var string
     *
     * @ORM\Column(type="string", length=40)
     */
    protected $password;

    /**
     * Активность
     *
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    protected $isActive;

    /**
     * E-mail
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    protected $email;

    /**
     *
     */
    public function __construct()
    {
        $this->isActive = true;
        $this->salt = '';//md5(uniqid(null, true));
    }

    /**
     * Возвращает имя пользователя
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        var_dump($this->salt); die;
        return $this->salt;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles()
    {
        return array('ROLE_USER', 'ROLE_ADMIN');
    }

    public function eraseCredentials()
    {
    }
}

