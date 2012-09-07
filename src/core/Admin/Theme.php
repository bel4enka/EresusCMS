<?php
/**
 * ${product.title}
 *
 * Тема оформления административного интерфейса
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

/**
 * Тема оформления административного интерфейса
 *
 * Экземпляр этого класса доступен через переменную {$theme} в шаблонах и может быть использован
 * для определения путей к файлам темы, вызова помощников (helpers) и других вспомогательных
 * функций.
 *
 * Автор темы может создать потомка этого класса, размещённого в файле theme.php в корне темы.
 * В этом случае его класс будет использован вместо стандартного.
 *
 * @package Eresus
 */
class Eresus_Admin_Theme
{
	/**
	 * Путь к директории тем относительно корня сайта
	 *
	 * @var string
	 */
	protected $prefix = 'admin/themes';

	/**
	 * Внутреннее имя темы
	 *
	 * Должно совпадать с именем директории темы.
	 *
	 * @var string
	 * @see getName
	 */
	protected $name = 'default';

	/**
	 * Конструктор
	 *
	 * @param string $name  Внутреннее имя темы (директория внутри themes)
	 *
	 * @return Eresus_Admin_Theme
	 */
	public function __construct($name = null)
	{
		if ($name)
		{
			$this->name = $name;
		}
	}

	/**
	 * Возвращает внутреннее имя темы
	 *
	 * @return string
	 * @see $name
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Возвращает адрес ресурса относительно корня сайта
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	public function getResource($path)
	{
		return $this->prefix . '/' . $this->getName() . '/' . $path;
	}

	/**
	 * Возвращает адрес картинки относительно корня сайта
	 *
	 * @param string $image
	 *
	 * @return string
	 */
	public function getImage($image)
	{
		return $this->getResource('img/' . $image);
	}

	/**
	 * Возвращает адрес иконки относительно корня сайта
	 *
	 * @param string $icon             Имя иконки
	 * @param string $size [optional]  Размер иконки. По умолчанию 'medium'
	 *
	 * @return string
	 */
	public function getIcon($icon, $size = 'medium')
	{
		return $this->getResource('img/' . $size . '/' . $icon);
	}

	/**
	 * Возвращает шаблон
	 *
	 * @param string $name
	 *
	 * @return Template
	 */
	public function getTemplate($name)
	{
		$filename = $this->getResource($name);
		$template = new Template($filename);
		return $template;
	}
}
