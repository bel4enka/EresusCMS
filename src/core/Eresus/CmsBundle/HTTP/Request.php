<?php
/**
 * ${product.title}
 *
 * Запрос HTTP
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

namespace Eresus\CmsBundle\HTTP;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * Запрос HTTP
 *
 * @package Eresus
 * @since 3.01
 */
class Request extends SymfonyRequest
{
    /**
     * Возвращает URL относительно корня сайта
     *
     * @return string
     *
     * @see setLocalRoot()
     * @since 3.01
     */
    public function getLocalUrl()
    {
        return substr($this->getRequestUri(), strlen($this->getBasePath()));
    }

    /**
     * Возвращает имя запрошенного в URL файла (без пути)
     */
    public function getFilename()
    {
        return basename($this->getPathInfo());
    }

    /**
     * Возвращает из URL путь без имени файла
     */
    public function getPath()
    {
        $path = $this->getPathInfo();
        if (substr($path, -1) == '/')
        {
            $path = substr($path, 0, -1);
        }
        else
        {
            $path = dirname($path);
        }
        return $path;
    }
    //@codeCoverageIgnoreStart
}
//@codeCoverageIgnoreEnd

