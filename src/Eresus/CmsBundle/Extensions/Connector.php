<?php
/**
 * ${product.title}
 *
 * Базовый класс коннектора сторонних расширений
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

namespace Eresus\CmsBundle\Extensions;

use Eresus_CMS;
use FS;

/**
 * Базовый класс коннектора сторонних расширений
 */
class Connector
{
    /**
     * Корневой URL расширения
     *
     * @var string
     */
    protected $root;

    /**
     * Корневой путь расширения
     *
     * @var string
     */
    protected $froot;

    /**
     * Конструктор
     */
    public function __construct()
    {
        $name = strtolower(substr(get_class($this), 0, -9));
        $this->root = Eresus_CMS::getLegacyKernel()->root.'ext-3rd/'.$name.'/';
        $this->froot = Eresus_CMS::getLegacyKernel()->froot.'ext-3rd/'.$name.'/';
    }

    /**
     * Заменяет глобальные макросы
     *
     * @param string $text
     * @return string
     */
    protected function replaceMacros($text)
    {
        $text = str_replace(
            array(
                '$(httpHost)',
                '$(httpPath)',
                '$(httpRoot)',
                '$(styleRoot)',
                '$(dataRoot)',
            ),
            array(
                Eresus_CMS::getLegacyKernel()->host,
                Eresus_CMS::getLegacyKernel()->path,
                Eresus_CMS::getLegacyKernel()->root,
                Eresus_CMS::getLegacyKernel()->style,
                Eresus_CMS::getLegacyKernel()->data
            ),
            $text
        );

        return $text;
    }

    /**
     * Метод вызывается при проксировании прямых запросов к расширению
     *
     */
    public function proxy()
    {
        if (!UserRights(EDITOR))
        {
            die;
        }

        $filename = Eresus_CMS::getLegacyKernel()->request['path'] .
            Eresus_CMS::getLegacyKernel()->request['file'];
        $filename = Eresus_CMS::getLegacyKernel()->froot . substr($filename,
            strlen(Eresus_CMS::getLegacyKernel()->root));

        if (FS::isDir($filename))
        {
            $filename = FS::normalize($filename . '/index.php');
        }

        if (!FS::isFile($filename))
        {
            header('Not found', true, 404);
            die('<h1>Not found.</h1>');
        }

        $ext = strtolower(substr($filename, strrpos($filename, '.') + 1));

        switch (true)
        {
            case in_array($ext, array('png', 'jpg', 'jpeg', 'gif')):
                $info = getimagesize($filename);
                header('Content-type: '.$info['mime']);
                echo file_get_contents($filename);
                break;
            case $ext == 'js':
                header('Content-type: text/javascript');
                $s = file_get_contents($filename);
                $s = $this->replaceMacros($s);
                echo $s;
                break;
            case $ext == 'css':
                header('Content-type: text/css');
                $s = file_get_contents($filename);
                $s = $this->replaceMacros($s);
                echo $s;
                break;
            case $ext == 'html':
            case $ext == 'htm':
                header('Content-type: text/html');
                $s = file_get_contents($filename);
                $s = $this->replaceMacros($s);
                echo $s;
                break;
            case $ext == 'php':
                Eresus_CMS::getLegacyKernel()->conf['debug']['enable'] = false;
                restore_error_handler();
                chdir(dirname($filename));
                require $filename;
                break;
        }
    }
}

