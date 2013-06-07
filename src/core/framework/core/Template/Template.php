<?php
/**
 * Работа с шаблонами Dwoo
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
 * @subpackage Templates
 */

/**
 * Подключаем Dwoo
 */
include_once __DIR__ . '/../3rdparty/dwoo/dwooAutoload.php';

/**
 * Настройки шаблонов
 *
 * @package Eresus
 * @subpackage Templates
 */
class TemplateSettings
{
    /**
     * Global substitution value to be used in all templates
     * @var array
     */
    private static $globalValues = array();

    /**
     * Set global substitution value to be used in all templates
     *
     * @param string $name
     * @param mixed  $value
     */
    public static function setGlobalValue($name, $value)
    {
        self::$globalValues[$name] = $value;
    }

    /**
     * Get global substitution value
     *
     * @param string $name
     * @return null|mixed  Null will be returned if value not set
     */
    public static function getGlobalValue($name)
    {
        return array_key_exists($name, self::$globalValues) ? self::$globalValues[$name] : null;
    }

    /**
     * Remove global substitution value
     *
     * @param string $name
     */
    public static function removeGlobalValue($name)
    {
        if (isset(self::$globalValues[$name]))
        {
            unset(self::$globalValues[$name]);
        }
    }

    /**
     * Get all global substitution values
     *
     * @return array
     */
    public static function getGlobalValues()
    {
        return self::$globalValues;
    }
}


/**
 * Файл шаблона
 *
 * @package Eresus
 * @subpackage Templates
 */
class TemplateFile extends Dwoo_Template_File
{
}


/**
 * Шаблон
 *
 * <b>Настройка</b>
 *
 * Template использует {@link Core::getValue()} чтобы получать свои настройки:
 *
 * - <b>core.template.templateDir</b> — Directory where templates located.
 * - <b>core.template.compileDir</b> — Directory to store compiled templates.
 * - <b>core.template.charset</b> — Charset of template files.
 * - <b>core.template.fileExtension</b> — Default extensions of template files.
 *
 * @package Eresus
 * @subpackage Templates
 */
class Template
{
    /**
     * Объект Dwoo
     * @var null|Dwoo
     */
    protected static $dwoo = null;

    /**
     * Внутреннее представление шаблона
     * @var null|Dwoo_Template_String
     * @since 3.01
     */
    protected $template = null;

    /**
     * Загружает шаблон из файла
     *
     * Если $filename — относительный путь, то он будет расценен как путь относительно
     * core.template.templateDir.
     *
     * @param string $filename  имя файла шаблона
     *
     * @return Template
     *
     * @since 3.01
     */
    public static function loadFromFile($filename)
    {
        $fileExtension = Core::getValue('core.template.fileExtension', '');
        $path = $filename . $fileExtension;
        /* Если это относительный путь, добавляем папку шаблонов */
        if (!preg_match('#^(/|\w{1,10}://|[A-Z]:\\\)#', $filename))
        {
            $templateDir = Core::getValue('core.template.templateDir', '');
            $path = $templateDir . '/' . $path;
        }
        $template = new self();
        $template->template = new TemplateFile($path, null, $filename, $filename);
        return $template;
    }

    /**
     * Constructor
     * @var string $filename  Template file name
     * @todo заменить имя файла на исходник шаблона и сделать обязательным, а файл загружать через
     *       статический метод loadFromFile
     */
    public function __construct($filename = null)
    {
        if (null == self::$dwoo)
        {
            $compileDir = $this->detectCompileDir();
            self::$dwoo = new Dwoo($compileDir);
            if (Core::getValue('core.template.charset'))
            {
                self::$dwoo->setCharset(Core::getValue('core.template.charset'));
            }
        }

        if ($filename)
        {
            $this->loadFile($filename);
        }
    }

    /**
     * Возвращает исходный код шаблона или null, если шаблон не загружен
     *
     * @return null|string
     * @since 3.01
     */
    public function getSource()
    {
        if (null === $this->template)
        {
            return null;
        }
        return $this->template->getSource();
    }

    /**
     * Задаёт исходный код шаблона в виде строки
     *
     * @param string $source
     *
     * @return void
     *
     * @since 3.01
     */
    public function setSource($source)
    {
        $this->template = new Dwoo_Template_String($source);
    }

    /**
     * Load template file
     * @param string $filename  Template file name
     */
    public function loadFile($filename)
    {
        $templateDir = $this->detectTemplateDir();
        $fileExtension = $this->detectFileExtension();
        $templateDir = Eresus_FS_Tool::normalize($templateDir);
        $template = $templateDir . '/' . $filename . $fileExtension;
        $this->template = new TemplateFile($template, null, $filename, $filename);
    }

    /**
     * Компилирует шаблон
     *
     * @param array $data  данные для подстановки в шаблон
     *
     * @return string
     */
    public function compile($data = null)
    {
        if ($data)
        {
            $data = array_merge($data, TemplateSettings::getGlobalValues());
        }
        else
        {
            $data = TemplateSettings::getGlobalValues();
        }

        return self::$dwoo->get($this->template, $data);
    }

    /**
     * Detect directory where templates located
     *
     * @return string
     */
    protected function detectTemplateDir()
    {
        $compileDir = Core::getValue('core.template.templateDir', '');

        return $compileDir;
    }

    /**
     * Detect template files extension
     *
     * @return string
     */
    protected function detectFileExtension()
    {
        $fileExtension = Core::getValue('core.template.fileExtension', '');

        return $fileExtension;
    }

    /**
     * Detect directory where compiled templates will be stored
     *
     * @return string
     */
    protected function detectCompileDir()
    {
        $compileDir = Core::getValue('core.template.compileDir', '');

        return $compileDir;
    }
}

