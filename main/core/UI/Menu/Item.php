<?php
/**
 * ${product.title}
 *
 * Пункт меню
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
 *
 * $Id: URI.php 1746 2011-07-27 06:53:41Z mk $
 */


/**
 * Пункт меню
 *
 * @package Eresus
 *
 * @since 2.20
 */
class Eresus_UI_Menu_Item
{
	/**
	 * Адрес
	 *
	 * @var string
	 */
	private $path;

	/**
	 * Название пункта
	 *
	 * @var string
	 */
	private $caption;

	/**
	 * Подсказка
	 *
	 * @var string
	 */
	private $hint;

	/**
	 * Доступ к виртуальным свойствам
	 *
	 * @param string $key
	 *
	 * @throws LogicException  если нет соответствующего геттера
	 *
	 * @return mixed
	 *
	 * @since 2.20
	 */
	public function __get($key)
	{
		$getter = 'get' . $key;
		if (method_exists($this, $getter))
		{
			return $this->$getter();
		}
		throw new LogicException('Undefined property: ' . $key);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает адрес
	 *
	 * @param string $path  полный путь от корня сайта, включая начальный слэш
	 *
	 * @return void
	 *
	 * @since 2.20
	 */
	public function setPath($path)
	{
		$this->path = $path;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает адрес
	 *
	 * @return string
	 *
	 * @since 2.20
	 */
	public function getPath()
	{
		return $this->path;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает название
	 *
	 * @param string $caption
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function setCaption($caption)
	{
		$this->caption = $caption;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает название
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	public function getCaption()
	{
		return $this->caption;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает подсказку
	 *
	 * @param string $hint
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function setHint($hint)
	{
		$this->hint = $hint;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает подсказку
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	public function getHint()
	{
		return $this->hint;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает true если пункт меню является текущим
	 *
	 * @return bool
	 *
	 * @since 2.20
	 */
	public function getCurrent()
	{
		$req = Eresus_CMS_Request::getInstance();
		$path = $req->getPathInfo();
		return
			$path == $this->path ||
			$path == $this->path . '/';
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает true если пункт меню открыт
	 *
	 * Открытым пункт считается, если выбран он или его подпункт.
	 *
	 * @return bool
	 *
	 * @since 2.20
	 */
	public function getOpened()
	{
		$req = Eresus_CMS_Request::getInstance();
		$path = $req->getPathInfo();
		// Извлекаем из пути строку, длинной $this->path + 1 символ, чтобы захватить слэш, если он есть
		$substr = substr($path, 0, strlen($this->path) + 1);
		return
			$substr == $this->path || // если после $this->path в $path больше ничего не было
			$substr == $this->path . '/';
	}
	//-----------------------------------------------------------------------------
}
