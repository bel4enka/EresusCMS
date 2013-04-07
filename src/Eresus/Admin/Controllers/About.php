<?php
/**
 * ${product.title}
 *
 * Страница "О программе"
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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Страница "О программе"
 */
class Eresus_Admin_Controllers_About extends Eresus_Admin_Controllers_Abstract
{
    /**
     * Возвращает страницу "О программе"
     *
     * @param Request $request
     *
     * @return Response|string  HTML
     */
    public function adminRender(Request $request)
    {
        /** @var Eresus_CMS $app */
        $app = $this->get('app');
        /** @var SimpleXMLElement $xml */
        $xml = simplexml_load_file($app->getFsRoot() . '/core/data/about.xml');

        $data = array();

        $product = $xml->product[0];
        $data['product'] = array();
        $data['product']['title'] = strval($product['title']);
        $data['product']['version'] = strval($product['version']);

        $data['product']['copyrights'] = array();
        $copyrights = $product->copyright;
        for ($i = 0; $i < count($copyrights); $i++)
        {
            $copyright = $copyrights[$i];
            $data['product']['copyrights'] []= array(
                'year' => strval($copyright['year']),
                'owner' => strval($copyright['owner']),
                'url' => strval($copyright['url']),
            );
        }

        $locale = Eresus_I18n::getInstance()->getLocale();

        $license = $xml->license[0];
        $data['license'] = array();
        $data['license']['text'] = strval($license->{$locale}[0]);

        $data['components'] = array();
        $components = $xml->xpath('//uses/item');
        foreach ($components as $component)
        {
            $data['components'] []= array(
                'title' => strval($component['title']),
                'url' => strval($component['url']),
                'logo' => strval($component['logo']),
                'description' => strval($component->{$locale}[0])
            );
        }

        $html = $this->renderView('core/templates/about.html.twig', $data);

        return $html;
    }
}
