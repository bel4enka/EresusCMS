<?php
/**
 * ${product.title}
 *
 * Пакет Eresus CMS
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

namespace Eresus\CmsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Пакет Eresus CMS
 *
 * @since 4.00
 * @package Eresus
 */
class CmsBundle extends Bundle
{
    /**
     * Доступные типы контента
     * @var ContentType[]
     * @since 4.00
     */
    private $contentTypes = array();

    /**
     * Действия при включении пакета
     * @since 4.00
     */
    public function boot()
    {
        $this->container->set('cms', $this);
    }

    /**
     * Регистрирует тип контента
     *
     * @param ContentType $type
     * @since 4.00
     */
    public function registerContentType(ContentType $type)
    {
        $this->contentTypes[$type->getId()] = $type;
    }

    /**
     * Возвращает список доступных типов контента
     *
     * @return ContentType[]
     * @since 4.00
     */
    public function getContentTypes()
    {
        return $this->contentTypes;
    }

    /**
     * Возвращает тип контента по его идентификатору
     *
     * @param string $id
     *
     * @return ContentType|null
     *
     * @since 4.00
     */
    public function getContentType($id)
    {
        return array_key_exists($id, $this->contentTypes)
            ? $this->contentTypes[$id]
            : null;
    }
}

