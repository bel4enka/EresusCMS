<?php
/**
 * Менеджер безопасности
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

namespace Eresus\Security;

use Eresus\Entity\Account;
use Eresus\Security\Exceptions\BadCredentialsException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Менеджер безопасности
 *
 * @api
 * @since 3.01
 */
class SecurityManager
{
    /**
     * Имя ключа сессии
     * @var string
     * @since 3.01
     */
    private static $sessionKey = 'ERESUS_SECURITY_SESSION';

    /**
     * @var ContainerInterface
     *
     * @since 3.01
     */
    private $container;

    /**
     * Текущий пользователь
     *
     * @var null|Account
     *
     * @since 3.01
     */
    private $currentUser = null;

    /**
     * @param ContainerInterface $container
     *
     * @since 3.01
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->checkSession();
    }

    /**
     * Производит попытку идентификации пользователя
     *
     * @param string $username  имя пользователя
     * @param string $hash      хэш пароля
     * @param bool   $remember  запомнить ли эти данные
     *
     * @throws BadCredentialsException
     *
     * @since 3.01
     */
    public function login($username, $hash, $remember = false)
    {
        /** @var \Eresus\ORM\Registry $doctrine */
        $doctrine = $this->container->get('doctrine');
        $om = $doctrine->getManager();
        $account = $om->getRepository('Eresus\Entity\Account')
            ->findOneBy(array('login' => $username, 'hash' => $hash, 'active' => true));
        if (is_null($account))
        {
            throw new BadCredentialsException;
        }

        $this->currentUser = $account;

        if ($remember)
        {
            $expires = time() + 2592000;
            setcookie('eresus_login', $account->getLogin(), $expires /*, TODO  */);
            setcookie('eresus_key', $account->getPasswordHash(), $expires /*, TODO  */);
        }
        else
        {
            setcookie('eresus_login', '', time()-3600 /*, TODO  */);
            setcookie('eresus_key', '', time()-3600 /*, TODO  */);
        }

        $_SESSION[self::$sessionKey] = new Session($this->currentUser->getId());
    }

    /**
     * Возвращает учётную запись текущего пользователя или null, если посетитель не идентифицирован
     *
     * @return null|Account
     *
     * @since 3.01
     */
    public function getCurrentUser()
    {
        return $this->currentUser;
    }

    /**
     * Проверяет сессию
     *
     * @since 3.01
     */
    private function checkSession()
    {
        if (array_key_exists(self::$sessionKey, $_SESSION))
        {
            $session = $_SESSION[self::$sessionKey];
            if ($session instanceof Session)
            {
                $ttl = $this->container->getParameter('security.session.ttl') * 3600;
                if (time() - $session->getLastActivity()->getTimestamp() <= $ttl)
                {
                    /** @var \Eresus\ORM\Registry $doctrine */
                    $doctrine = $this->container->get('doctrine');
                    $om = $doctrine->getManager();
                    $account = $om->find('Eresus\Entity\Account', $session->getUserId());
                    if (null !== $account)
                    {
                        $this->currentUser = $account;
                        return;
                    }
                }
            }
            unset($_SESSION[self::$sessionKey]);
        }
    }
}

