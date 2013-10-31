<?php
/**
 * Менеджер учётных записей пользователей
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

use Doctrine\ORM\EntityManager;
use Eresus\Entity\Account;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Менеджер учётных записей пользователей
 *
 * @api
 * @since 3.01
 */
class AccountManager
{
    /**
     * @var ContainerInterface
     *
     * @since 3.01
     */
    private $container;

    /**
     * @param ContainerInterface $container
     *
     * @since 3.01
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Возвращает учётную запись по идентификатору
     *
     * @param int $id
     *
     * @return Account|null
     *
     * @since 3.01
     */
    public function get($id)
    {
        return $this->getRepository()->find($id);
    }

    /**
     * Добавляет учётную запись
     *
     * @param Account $account
     *
     * @since 3.01
     */
    public function add(Account $account)
    {
        $this->getObjectManager()->persist($account);
    }

    /**
     * Удаляет учётную запись
     *
     * @param Account $account
     *
     * @since 3.01
     */
    public function remove(Account $account)
    {
        $this->getObjectManager()->remove($account);
    }

    /**
     * Возвращает менеджер объектов
     *
     * @return \Doctrine\ORM\EntityManager
     *
     * @since 3.01
     */
    private function getObjectManager()
    {
        return $this->container->get('doctrine')->getManager();
    }

    /**
     * Возвращает хранилище записей
     *
     * @return \Doctrine\ORM\EntityRepository
     *
     * @since 3.01
     */
    private function getRepository()
    {
        return $this->getObjectManager()->getRepository('Eresus\Entity\Account');
    }
}

