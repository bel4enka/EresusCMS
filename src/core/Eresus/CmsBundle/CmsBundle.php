<?php
/**
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
 */

namespace Eresus\CmsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Yaml\Yaml;
use Eresus\CmsBundle\Exceptions\RuntimeException;

/**
 * Пакет Eresus CMS
 *
 * @since 4.00
 */
class CmsBundle extends Bundle
{
    /**
     * Глобальные настройки сайта
     * @var array
     * @since 4.00
     */
    private $globals;

    /**
     * Действия при включении пакета
     *
     * @throws RuntimeException
     *
     * @since 4.00
     */
    public function boot()
    {
        $this->container->set('cms', $this);
        /** @var FileLocator $locator */
        $locator = $this->container->get('config_locator');
        $filename = $locator->locate('global.yml');
        if (!$filename)
        {
            throw new RuntimeException('"config/global.yml" not found');
        }
        $this->globals = Yaml::parse($filename);
    }

    /**
     * Возвращает глобальные настройки сайта.
     * @return array
     */
    public function getGlobals()
    {
        return $this->globals;
    }
}

