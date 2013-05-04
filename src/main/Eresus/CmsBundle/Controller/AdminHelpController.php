<?php
/**
 * Контроллер помощи
 *
 * @version ${product.version}
 * @copyright 2013, Михаил Красильников <m.krasilnikov@yandex.ru>
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

namespace Eresus\CmsBundle\Controller;

use Eresus\CmsBundle\Kernel;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;

/**
 * Контроллер помощи
 *
 * @since 4.0.0
 */
class AdminHelpController extends AdminAbstractController
{
    /**
     * @return Response
     *
     * @since 4.0.0
     */
    public function aboutAction()
    {
        $vars = $this->createTemplateVars();

        /** @var Kernel $kernel */
        $kernel = $this->get('kernel');

        $filename = $kernel->locateResource('@EresusCmsBundle/Resources/about/about.yml');
        $vars['about'] = Yaml::parse($filename);

        $filename = $kernel->locateResource('@EresusCmsBundle/Resources/meta/LICENSE');
        $vars['about']['license']['text'] = file_get_contents($filename);

        return $this->render('EresusCmsBundle:Help:About.html.twig', $vars);
    }

    /**
     * Возвращает массив переменных для подстановки в шаблон
     *
     * @return array
     *
     * @since 4.0.0
     */
    private function createTemplateVars()
    {
        return array(
            'mainMenuRoute' => 'admin.help',
        );
    }
}

