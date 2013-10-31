<?php
/**
 * Проверка зависимостей плагина
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

namespace Eresus\Plugins\Requirements;

use Eresus\Plugins\Plugin;
use Eresus\Plugins\PluginManager;

/**
 * Проверка зависимостей плагина
 *
 * @since 3.01
 */
class Checker
{
    /**
     * @var PluginManager
     *
     * @since 3.01
     */
    private $registry;

    /**
     * @param PluginManager $manager
     */
    public function __construct(PluginManager $manager)
    {
        $this->registry = $manager;
    }

    /**
     * Возвращает список неудовлетворённых зависимостей для указанного плагина
     *
     * @param Plugin $plugin
     *
     * @return array
     *
     * @since 3.01
     */
    public function getUnsatisfied(Plugin $plugin)
    {
        // TODO
        /*do
        {
            $success = true;
            foreach ($this->list as $plugin => $item)
            {
                if (!($item['info'] instanceof Eresus_PluginInfo))
                {
                    continue;
                }
                foreach ($item['info']->getRequiredPlugins() as $required)
                {
                    list ($name, $minVer, $maxVer) = $required;
                    if (
                        !isset($this->list[$name]) ||
                        ($minVer && version_compare($this->list[$name]['info']->version, $minVer, '<')) ||
                        ($maxVer && version_compare($this->list[$name]['info']->version, $maxVer, '>'))
                    )
                    {
                        $msg = 'Plugin "%s" requires plugin %s';
                        $requiredPlugin = $name . ' ' . $minVer . '-' . $maxVer;
                        Eresus_Kernel::log(__CLASS__, LOG_ERR, $msg, $plugin, $requiredPlugin);
                        /*$msg = I18n::getInstance()->getText($msg, $this);
                        ErrorMessage(sprintf($msg, $plugin, $requiredPlugin));* /
                        unset($this->list[$plugin]);
                        $success = false;
                    }
                }
            }
        }
        while (!$success);*/
        return array();
    }
}

