<?php
/**
 * Сессия
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

use DateTime;
use Eresus\Entity\Account;

/**
 * Сессия
 *
 * @api
 * @since 3.01
 */
class Session
{
    /**
     * Идентификатор пользователя
     * @var mixed
     * @since 3.01
     */
    protected $userId;

    /**
     * Время начала сессии
     * @var DateTime
     * @since 3.01
     */
    protected $started;

    /**
     * Время последней активности
     * @var DateTime
     * @since 3.01
     */
    protected $activity;

    /**
     * Создаёт новую сессию
     *
     * @param mixed $userId
     *
     * @since 3.01
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
        $this->started = new DateTime();
    }

    /**
     * Возвращает идентификатор пользователя
     *
     * @return mixed
     *
     * @since 3.01
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Возвращает время начала сессии
     *
     * @return DateTime
     *
     * @since 3.01
     */
    public function getTimeStarted()
    {
        return $this->started;
    }


    /**
     * Возвращает время последней активности
     *
     * @return DateTime
     *
     * @since 3.01
     */
    public function getLastActivity()
    {
        return $this->activity;
    }

    /**
     * Записывает время последней активности
     *
     * @return array
     *
     * @since 3.01
     */
    public function __sleep()
    {
        $this->activity = new DateTime();
        return array('userId', 'started', 'activity');
    }
}

