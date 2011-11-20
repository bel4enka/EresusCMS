<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2004, ProCreat Systems, http://procreat.ru/
 * @copyright 2007, Eresus Project, http://eresus.ru/
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
 * $Id$
 */


/**
 * Родительский класс для всех плагинов
 *
 * @package Eresus
 * @deprecated Используйте Plugin
 */
class TPlugin
{
	/**
	 * Имя плагина
	 * @var string
	 */
	public $name;

	/**
	 * Версия плагина
	 * @var string
	 */
	public $version;

	/**
	 * Название плагина
	 * @var string
	 */
	public $title;

	/**
	 * Описание плагина
	 * @var string
	 */
	public $description;

	/**
	 * Не используется начиная с 2.13
	 * @var void
	 */
	public $type;

	/**
	 * Настройки плагина
	 * @var array
	 */
	public $settings = array();

	/**
	 * Конструктор
	 *
	 * Производит чтение настроек плагина и подключение языковых файлов
	 */
	public function __construct()
	{
		global $Eresus;

		if (!empty($this->name) && isset($Eresus->plugins->list[$this->name]))
		{
			$this->settings = decodeOptions($Eresus->plugins->list[$this->name]['settings'], $this->settings);
			# Если установлена версия плагина отличная от установленной ранее
			# то необходимо произвести обновление информации о плагине в БД
			if ($this->version != $Eresus->plugins->list[$this->name]['version'])
				$this->resetPlugin();
		}
	}
	//------------------------------------------------------------------------------

	/**
	 * Возвращает информацию о плагине
	 *
	 * @param  array  $item  Предыдущая версия информации (по умолчанию null)
	 *
	 * @return  array  Массив информации, пригодный для записи в БД
	 */
	function __item($item = null)
	{
		global $Eresus;

		$result['name'] = $this->name;
		$result['content'] = false;
		$result['active'] = is_null($item) ? true : $item['active'];
		$result['settings'] = $Eresus->db->escape(is_null($item) ? encodeOptions($this->settings) : $item['settings']);
		$result['title'] = $this->title;
		$result['version'] = $this->version;
		$result['description'] = $this->description;
		return $result;
	}
	//------------------------------------------------------------------------------

/**
* Чтение настроек плагина из БД
*
* @return  bool  Результат выполнения
*/
function loadSettings()
{
	global $Eresus;
	$result = $Eresus->db->selectItem('plugins', "`name`='".$this->name."'");
	if ($result) $this->settings = decodeOptions($result['settings'], $this->settings);
	return (bool)$result;
}
//------------------------------------------------------------------------------
/**
* Сохранение настроек плагина в БД
*
* @return  bool  Результат выполнения
*/
function saveSettings()
{
	global $Eresus;
	$item = $Eresus->db->selectItem('plugins', "`name`='{$this->name}'");
	$item = $this->__item($item);
	$item['settings'] = $Eresus->db->escape(encodeOptions($this->settings));
	$result = $Eresus->db->updateItem('plugins', $item, "`name`='".$this->name."'");
	return $result;
}
//------------------------------------------------------------------------------
/**
* Обновление данных о плагине в БД
*/
function resetPlugin()
{
	$this->loadSettings();
	$this->saveSettings();
}
//------------------------------------------------------------------------------
/**
* Действия, выполняемые при инсталляции плагина
*/
function install() {}
//------------------------------------------------------------------------------
/**
* Действия, выполняемые при деинсталляции плагина
*/
function uninstall() {}
//------------------------------------------------------------------------------
/**
* Действия при изменении настроек
*/
function onSettingsUpdate() {}
//------------------------------------------------------------------------------
/**
* Сохраняет в БД изменения настроек плагина
*/
function updateSettings()
{
	global $Eresus;

	foreach ($this->settings as $key => $value) if (!is_null(arg($key))) $this->settings[$key] = arg($key);
	$this->onSettingsUpdate();
	$this->saveSettings();
}
//------------------------------------------------------------------------------
/**
* Замена макросов
*
* @param  string  $template  Строка в которой требуется провести замену макросов
* @param  arrya   $item      Ассоциативный массив со значениями для подстановки вместо макросов
*
* @return  string  Метод возвращает строку, в которой заменены все макросы, совпадающие с полями массива item
*/
function replaceMacros($template, $item)
{
	preg_match_all('/\$\(([^(]+)\)/U', $template, $matches);
	if (count($matches[1])) foreach($matches[1] as $macros)
		if (isset($item[$macros])) $template = str_replace('$('.$macros.')', $item[$macros], $template);
	return $template;
}
//------------------------------------------------------------------------------
}
