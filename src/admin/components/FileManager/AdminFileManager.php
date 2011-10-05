<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Компонент "Файловый менеджер"
 *
 * @copyright 2010, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mk@procreat.ru>
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
 *
 * $Id: main.php 923 2010-06-10 13:12:43Z mk $
 */

/**
 * Компонент "Файловый менеджер"
 *
 * @package Eresus
 */
class AdminFileManager extends EresusAdminComponent
{
	/**
	 * Возвращает отрисованный интерфейс файлового менеджера
	 *
	 * @return string  HTML
	 */
	public function renderUI()
	{
		global $Eresus, $page;

		$theme = $page->getUITheme();
		$page->linkScripts($Eresus->root . 'admin/components/FileManager/fm.js');

		$data = array();
		$data['folders'] = $this->buildFolderList('/');
		$data['files'] = $this->buildFilesList('/');

		$tmpl = $theme->getTemplate('FileManager/main.html');
		$html = $tmpl->compile($data);

		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает список директорий по указанному пути
	 *
	 * @param string $path  Путь. "/" - соответвует директории data
	 * @return array
	 */
	private function buildFolderList($path)
	{
		global $Eresus;

		$folders = array();

		$list = glob($Eresus->froot . 'data/' . $path . '*');

		foreach ($list as $filename)
		{
			if (!FS::isDir($filename))
			{
				continue;
			}

			$folders []= basename($filename);
		}

		return $folders;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает список файлов по указанному пути
	 *
	 * @param string $path  Путь. "/" - соответвует директории data
	 * @return array
	 */
	private function buildFilesList($path)
	{
		global $Eresus;

		$files = array();

		$list = glob($Eresus->froot . 'data/' . $path . '*');

		foreach ($list as $filename)
		{
			if (FS::isDir($filename))
			{
				continue;
			}

			$files []= new AdminFileManagerObject($path . basename($filename));
		}

		return $files;
	}
	//-----------------------------------------------------------------------------
}




/**
 * Объект управления в файловом менеджере
 *
 * Файл, папка
 *
 * @property-read string $name
 * @property-read string $icon
 *
 * @package Eresus
 */
class AdminFileManagerObject
{
	/**
	 * Имя файла и путь
	 * @var string
	 */
	private $filepath;

	/**
	 * Карта соответствия расширения значку
	 * @var array
	 */
	private $ext2icon = array(
		'png' => 'image-x-generic.png',
	);

	/**
	 * Конструктор
	 * @param string $filename  Имя файла и путь относительно "data"
	 */
	public function __construct($filepath)
	{
		$this->filepath = $filepath;
	}
	//-----------------------------------------------------------------------------

	/**
	 * "Магический" геттер
	 *
	 * @param string $name  Имя свойства
	 * @return mixed
	 */
	public function __get($name)
	{
		$method = 'get' . $name;
		if (method_exists($this, $method))
		{
			return $this->$method();
		}

		return null;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Геттер свойства $name
	 *
	 * @return string
	 */
	private function getName()
	{
		return basename($this->filepath);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Геттер свойства $icon
	 *
	 * @return string
	 */
	private function getIcon()
	{
		global $page;

		$ext = strtolower(substr($this->filepath, strrpos($this->filepath, '.') + 1));
		if (isset($this->ext2icon[$ext]))
		{
			$icon = $this->ext2icon[$ext];
		}
		else
		{
			$icon = 'unknown.png';
		}

		return $page->getUITheme()->getIcon('mimetypes/' . $icon, 'large');
	}
	//-----------------------------------------------------------------------------
}