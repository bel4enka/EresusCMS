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

use Symfony\Component\HttpFoundation\Request;

/**
 * Запрос HTTP
 *
 * @package Eresus
 * @since 3.01
 */
class Eresus_HTTP_Request extends Request
{
	/**
	 * Локальный корень (на случай когда сайт расположен не в корне)
	 *
	 * @var string
	 * @since 3.01
	 */
	protected $localRoot;

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
	 * Задаёт путь к корню сайта
	 *
	 * Этот путь будет вырезан из значений, возвращаемых {@link getLocalPath()}.
	 *
	 * <code>
	 * $req = new Eresus_HTTP_Request('http://example.org/some/path/script?query');
	 * echo $req->getLocalPath(); // '/some/path'
	 * $req->setLocalRoot('/some');
	 * echo $req->getLocalPath(); // '/path'
	 * </code>
	 *
	 * @param string $root
	 *
	 * @return void
	 *
	 * @since 3.01
	 */
	public function setLocalRoot($root)
	{
		if (substr($root, -1) == '/')
		{
			$root = substr($root, 0, -1);
		}
		$this->localRoot = $root;
	}

	/**
	 * Возвращает имя запрошенного в URL файла (без пути)
	 */
	public function getFilename()
	{
		return basename($this->getPathInfo());
	}
	//@codeCoverageIgnoreStart
}
//@codeCoverageIgnoreEnd