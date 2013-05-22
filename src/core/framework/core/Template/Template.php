<?php
/**
 * Eresus Core
 *
 * @version 0.1.3
 *
 * Dwoo template engine adapter
 *
 * @copyright 2007, Eresus Project, http://eresus.ru/
 * @license http://www.gnu.org/licenses/gpl.txt GPL License 3
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Template
 *
 * @uses Dwoo kernel.php#FS
 *
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 *
 * $Id: Template.php 524 2010-06-05 12:53:45Z mk $
 */

/*
 * Including Dwoo
 */
include_once __DIR__ . '/../3rdparty/dwoo/dwooAutoload.php';

/**
 * Template package settings
 *
 * This class can be used to configure behavor of the Template package.
 *
 * @package Template
 *
 */
class TemplateSettings {

	/**
	 * Global substitution value to be used in all templates
	 * @var array
	 */
	private static $gloablValues = array();

	/**
	 * Set global substitution value to be used in all templates
	 *
	 * @param string $name
	 * @param mixed  $value
	 */
	public static function setGlobalValue($name, $value)
	{
		self::$gloablValues[$name] = $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get global substitution value
	 *
	 * @param string $name
	 * @return null|mixed  Null will be returned if value not set
	 */
	public static function getGlobalValue($name)
	{
		return ecArrayValue(self::$gloablValues, $name);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Remove global substitution value
	 *
	 * @param string $name
	 */
	public static function removeGlobalValue($name)
	{
		if (isset(self::$gloablValues[$name])) unset(self::$gloablValues[$name]);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get all global substitution values
	 *
	 * @return array
	 */
	public static function getGlobalValues()
	{
		return self::$gloablValues;
	}
	//-----------------------------------------------------------------------------
}


/**
 * Template file
 *
 * @package Template
 *
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 */
class TemplateFile extends Dwoo_Template_File {

}


/**
 * Template
 *
 * <b>CONFIGURATION</b>
 * Templte uses Core::getValue to read its configuration:
 *
 * <b>core.template.templateDir</b>
 * Directory where templates located.
 *
 * <b>core.template.compileDir</b>
 * Directory to store compiled templates.
 *
 * <b>core.template.charset</b>
 * Charset of template files.
 *
 * <b>core.template.fileExtension</b>
 * Default extensions of template files.
 *
 * @package Template
 */
class Template
{
	/**
	 * Dwoo object
	 * @var Dwoo
	 */
	protected $dwoo;

	/**
	 * Template file object
	 * @var TemplateFile
	 */
	protected $file;

	/**
	 * Constructor
	 * @var string $filename [optional]  Template file name
	 */
	public function __construct($filename = null)
	{
		$compileDir = $this->detectCompileDir();
		$compileDir = FS::nativeForm($compileDir);
		$this->dwoo = new Dwoo($compileDir);

		if (Core::getValue('core.template.charset'))
			$this->dwoo->setCharset(Core::getValue('core.template.charset'));

		if ($filename) $this->loadFile($filename);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Load template file
	 * @param string $filename  Template file name
	 */
	public function loadFile($filename)
	{
		$templateDir = $this->detectTemplateDir();
		$fileExtension = $this->detectFileExtension();
		$templateDir = FS::normalize($templateDir);
		$template = $templateDir . '/' . $filename . $fileExtension;
		$template = FS::nativeForm($template);
		$this->file = new TemplateFile($template, null, $filename, $filename);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Compile template
	 *
	 * @param array $data [optional]  Data for template
	 *
	 * @return string
	 */
	function compile($data = null)
	{
		if ($data)
			$data = array_merge($data, TemplateSettings::getGlobalValues());
		else
			$data = TemplateSettings::getGlobalValues();

		return $this->dwoo->get($this->file, $data);
	}
	//-----------------------------------------------------------------------------

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
	//-----------------------------------------------------------------------------

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
	//-----------------------------------------------------------------------------

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
	//-----------------------------------------------------------------------------
}
