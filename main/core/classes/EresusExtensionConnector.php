<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Базовый класс коннектора сторонних расширений
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
 * @package EresusCMS
 *
 * $Id$
 */



/**
 * Базовый класс коннектора сторонних расширений
 *
 * @package EresusCMS
 */
class EresusExtensionConnector
{
	/**
	 * Корневой URL расширения
	 *
	 * @var string
	 */
	private $root;

	/**
	 * Корневой путь расширения
	 *
	 * @var string
	 */
	private $froot;

	/**
	 * Конструктор
	 *
	 * @return EresusExtensionConnector
	 */
	public function __construct()
	{
		$name = strtolower(substr(get_class($this), 0, -9));
		$this->root = $GLOBALS['Eresus']->root . 'ext-3rd/' . $name . '/';
		$this->froot = $GLOBALS['Eresus']->froot . 'ext-3rd/' . $name . '/';
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает адрес директории расширения
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	public function getRoot()
	{
		return $this->root;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает путь к директории расширения
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	public function getRootDir()
	{
		return $this->froot;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Заменяет глобальные макросы
	 *
	 * @param string $text
	 * @return string
	 */
	protected function replaceMacros($text)
	{
		global $Eresus;

		$text = str_replace(
			array(
				'$(httpHost)',
				'$(httpPath)',
				'$(httpRoot)',
				'$(styleRoot)',
				'$(dataRoot)',
			),
			array(
				$Eresus->host,
				$Eresus->path,
				$Eresus->root,
				$Eresus->style,
				$Eresus->data
			),
			$text
		);

		return $text;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Метод вызывается при проксировании прямых запросов к расширению
	 *
	 * return void
	 */
	public function proxy()
	{
		global $Eresus;

		if (!UserRights(EDITOR))
		{
			header('Forbidden', true, 403);
			die('<h1>Forbidden.</h1>');
		}

		$filename = $Eresus->request['path'] . $Eresus->request['file'];
		$filename = $Eresus->froot . substr($filename, strlen($Eresus->root));

		if (is_dir($filename))
		{
			$filename = FS::driver()->normalize($filename . '/index.php');
		}

		if (!is_file($filename))
		{
			$this->proxyUnexistent($filename);
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
				$Eresus->conf['debug']['enable'] = false;
				restore_error_handler();
				chdir(dirname($filename));
				require $filename;
			break;
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обрабатывает запрос к несуществующему файлу
	 *
	 * @param string $path
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	protected function proxyUnexistent($path)
	{
		header('Not found', true, 404);
		echo '<h1>Not found.</h1>';
		throw new ExitException;
	}
	//-----------------------------------------------------------------------------
}