<?php
/**
 * Eresus Core
 *
 * @version 0.1.0
 *
 * Dwoo template engine adapter
 *
 * @copyright 2007-2009, Eresus Project, http://eresus.ru/
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
 * @package Core
 * @subpackage Template
 *
 * @uses Dwoo kernel.php#FS Registry
 *
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 *
 * $Id: Template.php 276 2009-05-18 11:36:58Z mekras $
 */

/**
 * Including Dwoo
 */
include_once '3rdparty/dwoo/dwooAutoload.php';


/**
 * Template file
 *
 * @package Core
 * @subpackage Template
 *
 * @author mekras
 *
 */
class TemplateFile extends Dwoo_Template_File {

}


/**
 * Template
 *
 * <b>CONFIGURATION</b>
 * Templte uses Registry to read its configuration:
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
 *
 * @package Core
 * @subpackage Template
 *
 * @author mekras
 *
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

		if (Registry::exists('core.template.charset'))
			$this->dwoo->setCharset(Registry::get('core.template.charset'));

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
		$templateDir = FS::normalize($templateDir, 'dir');
		$template = $templateDir . $filename . $fileExtension;
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
		if (!is_null($data))
			return $this->dwoo->get($this->file, $data);
		else
			return $this->dwoo->get($this->file);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Detect directory where templates located
	 *
	 * @return string
	 */
	protected function detectTemplateDir()
	{
		if (Registry::exists('core.template.templateDir')) {

			$compileDir = Registry::get('core.template.templateDir');

		}	elseif ($app = Core::app()) { #TODO Deprecated. Remove.

			$compileDir = $app->getOpt('templates', 'templateDir');
			if ($compileDir)
				$compileDir = $app->getFsRoot() . $compileDir;

		} else $compileDir = '';

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
		if (Registry::exists('core.template.fileExtension'))
			$fileExtension = Registry::get('core.template.fileExtension');

		elseif ($app = Core::app()) #TODO Deprecated. Remove.
			$fileExtension = $app->getOpt('templates', 'fileExt');

		else $fileExtension = '';

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
		if (Registry::exists('core.template.compileDir')) {

			$compileDir = Registry::get('core.template.compileDir');

		}	elseif ($app = Core::app()) { #TODO Deprecated. Remove.

			$compileDir = $app->getOpt('templates', 'compileDir');
			if ($compileDir)
				$compileDir = $app->getFsRoot() . $compileDir;

		} else $compileDir = '';

		return $compileDir;
	}
	//-----------------------------------------------------------------------------
}
