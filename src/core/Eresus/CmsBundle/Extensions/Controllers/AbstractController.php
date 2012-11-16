<?php
/**
 * ${product.title}
 *
 * Абстрактный контроллер плагина
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

namespace Eresus\CmsBundle\Extensions\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Eresus\CmsBundle\Extensions\Plugin;

/**
 * Абстрактный контроллер плагина
 *
 * @package Eresus
 * @since 4.00
 */
abstract class AbstractController extends Controller
{
    /**
     * Плагин
     *
     * @var Plugin
     * @since 4.00
     */
    protected $plugin;

    /**
     * Конструктор контроллера
     *
     * @param Plugin $plugin
     * @since 4.00
     */
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * Возвращает плагин
     *
     * @return Plugin
     * @since 4.00
     */
    protected function getPlugin()
    {
        return $this->plugin;
    }
}

