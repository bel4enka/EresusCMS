<?php
/**
 * Таблица учётных записей
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
 * Таблица учётных записей
 *
 * @package Eresus
 */
class Eresus_Entity_Table_Account extends Eresus_ORM_Table
{
    protected function setTableDefinition()
    {
        $this->setTableName('users');
        $this->hasColumns(array(
            'id' => array(
                'type' => 'integer',
                'unsigned' => true,
                'autoincrement' => true,
            ),
            'login' => array(
                'type' => 'string',
                'length' => 16
            ),
            'hash' => array(
                'type' => 'string',
                'length' => 32
            ),
            'active' => array(
                'type' => 'boolean',
            ),
            'lastVisit' => array(
                'type' => 'timestamp',
            ),
            'lastLoginTime' => array(
                'type' => 'timestamp'
            ),
            'loginErrors' => array(
                'type' => 'integer',
                'unsigned' => true
            ),
            'access' => array(
                'type' => 'integer'
            ),
            'name' => array(
                'type' => 'string',
                'length' => 64
            ),
            'mail' => array(
                'type' => 'string',
                'length' => 64
            ),
            'profile' => array(
                'type' => 'string',
                'length' => 65535
            ),
        ));
    }

    /**
     * Возвращает учётную запись по имени пользователя (login)
     *
     * @param string $name
     *
     * @return Eresus_ORM_Entity|null
     */
    public function findByName($name)
    {
        $q = $this->createSelectQuery();
        $q->where($q->expr->eq('login', $q->bindValue($name, ':login')));
        return $this->loadOneFromQuery($q);
    }

    /**
     * Возвращает учётную запись по адресу e-mail
     *
     * @param string $email
     *
     * @return Eresus_ORM_Entity|null
     */
    public function findByMail($email)
    {
        $q = $this->createSelectQuery();
        $q->where($q->expr->eq('mail', $q->bindValue($email, ':mail')));
        return $this->loadOneFromQuery($q);
    }
}

