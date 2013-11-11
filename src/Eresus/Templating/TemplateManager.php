<?php
/**
 * Менеджер шаблонов
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

namespace Eresus\Templating;

use Eresus\Kernel;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Exception;
use RuntimeException;
use Dwoo;
use Dwoo_Template_File;

/**
 * Менеджер шаблонов
 *
 * @api
 * @since 3.01
 *
 * @todo Сделать кэширование объектов Template
 */
class TemplateManager
{
    /**
     * Контейнер
     * @var ContainerInterface
     * @since 3.01
     */
    private $container;

    /**
     * @var Dwoo
     *
     * @since 3.01
     */
    private $dwoo;

    /**
     * Глобальные переменные
     *
     * @var array
     *
     * @since 3.01
     */
    private $globals = array();

    /**
     * Конструктор
     *
     * @param ContainerInterface $container
     * @param Dwoo               $dwoo
     *
     * @since 3.01
     */
    public function __construct(ContainerInterface $container, Dwoo $dwoo)
    {
        $this->container = $container;
        $this->dwoo = $dwoo;
    }

    /**
     * Задаёт значение глобальной переменной для всех шаблонов
     *
     * @param string $name
     * @param mixed  $value
     *
     * @since 3.01
     */
    public function setGlobal($name, $value)
    {
        $this->globals[$name] = $value;
    }

    /**
     * Возвращает значение глобальной переменной
     *
     * @param string $name
     *
     * @return null|mixed  значение переменной или null, если такой переменной нет
     *
     * @since 3.01
     */
    public function getGlobal($name)
    {
        return array_key_exists($name, $this->globals) ? $this->globals[$name] : null;
    }

    /**
     * Удаляет глобальную переменную
     *
     * @param string $name
     *
     * @since 3.01
     */
    public function unsetGlobal($name)
    {
        if (array_key_exists($name, $this->globals))
        {
            unset($this->globals[$name]);
        }
    }

    /**
     * Возвращает все глобальные переменные в виде ассоциативного массива
     *
     * @return array
     *
     * @since 3.01
     */
    public function getGlobals()
    {
        return $this->globals;
    }

    /**
     * Устанавливает шаблоны в общую папку шаблонов
     *
     * @param string $filename   абсолютный путь к устанавливаемому шаблону
     * @param string $target     путь относительно общей директории шаблонов
     * @param bool   $overwrite  если целевой файл уже существует и этот аргумент равен true, файл
     *                           будет перезаписан, если false — будет вброшено исключение
     *
     * @throws RuntimeException
     *
     * @since 3.01
     */
    public function install($filename, $target, $overwrite = true)
    {
        if (!file_exists($filename))
        {
            throw new RuntimeException('Template file not found: ' . $filename);
        }

        $target = $this->getClientTmplDir() . '/' . $target;
        if (!file_exists($target))
        {
            try
            {
                $umask = umask(0000);
                mkdir($target, 0777, true);
                umask($umask);
            }
            catch (Exception $e)
            {
                throw new RuntimeException(
                    'Can not create target directory: ' . $target, null, $e);
            }
        }

        $target .= '/' . basename($filename);

        if (file_exists($target))
        {
            if ($overwrite)
            {
                if (!unlink($target))
                {
                    throw new RuntimeException(sprintf('Can not overwrite template "%s"', $target));
                }
            }
            else
            {
                throw new RuntimeException(sprintf('Template "%s" is already installed', $target));
            }
        }

        if (!copy($filename, $target))
        {
            throw new RuntimeException(sprintf('Failed to copy "%s" to "%s"', $filename, $target));
        }
    }

    /**
     * Удаляет шаблон или папку шаблонов
     *
     * @param string $path  имя файла или папки относительно общей директории шаблонов
     *
     * @throws RuntimeException
     *
     * @since 3.01
     */
    public function remove($path)
    {
        $path = $this->getClientTmplDir() . '/' . $path;
        if (!file_exists($path))
        {
            throw new RuntimeException(sprintf('Template file "%s" not found', $path));
        }

        if (is_file($path))
        {
            unlink($path);
        }
        else
        {
            $branch = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path),
                \RecursiveIteratorIterator::SELF_FIRST);

            $files = array();
            $dirs = array();
            foreach ($branch as $file)
            {
                /** @var \SplFileInfo $file */
                if (preg_match('/^\.{1,2}$/', $file->getFilename()))
                {
                    continue;
                }
                if ($file->isDir())
                {
                    $dirs []= $file->getPathname();
                }
                else
                {
                    $files []= $file->getPathname();
                }
            }

            /* Вначале удаляем все файлы */
            foreach ($files as $file)
            {
                unlink($file);
            }
            /* Теперь удаляем директории, начиная с самых глубоких */
            for ($i = count($dirs) - 1; $i >= 0; $i--)
            {
                rmdir($dirs[$i]);
            }
            rmdir($path);
        }
    }

    /**
     * Возвращает содержимое шаблона
     *
     * @param string $name    имя файла шаблона
     * @param string $prefix  опциональный префикс (путь относительно корня шаблонов)
     *
     * @throws RuntimeException
     *
     * @return string
     *
     * @since 3.01
     */
    public function getContents($name, $prefix = '')
    {
        $path = $this->getClientTmplDir() . '/' . $this->getFilename($name, $prefix);

        if (!is_file($path))
        {
            throw new RuntimeException('Template not exists: ' . $path);
        }

        $contents = file_get_contents($path);

        return $contents;
    }

    /**
     * Записывает содержимое шаблона
     *
     * @param string $contents  содержимое шаблона
     * @param string $name      имя файла шаблона
     * @param string $prefix    опциональный префикс (путь относительно корня шаблонов)
     *
     * @throws RuntimeException
     *
     * @return void
     *
     * @since 3.01
     */
    public function setContents($contents, $name, $prefix = '')
    {
        $path = $this->getClientTmplDir() . '/' . $this->getFilename($name, $prefix);

        if (!is_file($path))
        {
            throw new RuntimeException('Template not exists: ' . $path);
        }

        @file_put_contents($path, $contents);
    }

    /**
     * Возвращает путь к файлу шаблона
     *
     * @param string $name    имя файла шаблона
     * @param string $prefix  опциональный префикс (путь относительно корня шаблонов)
     *
     * @return string
     *
     * @since 1.00
     */
    public function getFilename($name, $prefix = '')
    {
        $path = $name;
        if ($prefix != '')
        {
            $path = $prefix . '/' . $path;
        }

        return $path;
    }

    /**
     * Возвращает объект шаблона
     *
     * @param string $filename    имя файла шаблона
     * @param string $prefix  опциональный префикс (путь относительно корня шаблонов)
     *
     * @throws RuntimeException
     *
     * @return Template
     *
     * @since 3.01
     */
    public function getTemplate($filename, $prefix = '')
    {
        $path = $this->getFilename($filename, $prefix);

        if (!file_exists($this->getClientTmplDir() . '/' . $path))
        {
            throw new RuntimeException('Template not exists: ' . $path);
        }

        $tmpl = new Template('templates/' . $path);

        return $tmpl;
    }

    /**
     * Возвращает объект шаблона АИ
     *
     * @param string $name    имя файла шаблона
     *
     * @throws RuntimeException
     *
     * @return Template
     *
     * @since 3.01
     */
    public function getAdminTemplate($name)
    {
        /** @var Kernel $kernel */
        $kernel = $this->container->get('kernel');
        $path = $kernel->getAppDir() . '/Eresus/Resources/views/' . $name;

        if (!file_exists($path))
        {
            throw new RuntimeException('Template not exists: ' . $path);
        }

        $file = new Dwoo_Template_File($path);
        $tmpl = new Template($file, $this->container);

        return $tmpl;
    }

    /**
     * Возвращает папку шаблонов КИ
     *
     * @return string
     *
     * @since 3.01
     */
    private function getClientTmplDir()
    {
        /** @var Kernel $kernel */
        $kernel = $this->container->get('kernel');
        return $kernel->getAppDir() . '/templates';

    }
}

