<?php
/**
 * ${product.title}
 *
 * Родительский класс для всех плагинов
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
 * Родительский класс для всех плагинов
 *
 * @package Eresus
 */
class Eresus_Extensions_Plugin
{
	/**
	 * Имя плагина
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Версия плагина
	 *
	 * Потомки должны перекрывать это своим значением
	 *
	 * @var string
	 */
	public $version = '0.00';

	/**
	 * Необходимая версия Eresus
	 *
	 * Потомки могут перекрывать это своим значением
	 *
	 * @var string
	 */
	public $kernel = '3.00';

	/**
	 * Название плагина
	 *
	 * Потомки должны перекрывать это своим значением
	 *
	 * @var string
	 */
	public $title = 'no title';

	/**
	 * Описание плагина
	 *
	 * Потомки должны перекрывать это своим значением
	 *
	 * @var string
	 */
	public $description = '';

	/**
	 * Настройки плагина
	 *
	 * Потомки могут перекрывать это своим значением
	 *
	 * @var array
	 */
	public $settings = array();

	/**
	 * Директория данных
	 *
	 * /data/имя_плагина
	 *
	 * @var string
	 */
	protected $dirData;

	/**
	 * URL данных
	 *
	 * @var string
	 */
	protected $urlData;

	/**
	 * Директория скриптов
	 *
	 * /ext/имя_плагина
	 *
	 * @var string
	 */
	protected $dirCode;

	/**
	 * URL скриптов
	 *
	 * @var string
	 */
	protected $urlCode;

	/**
	 * Директория оформления
	 *
	 * style/имя_плагина
	 *
	 * @var string
	 */
	protected $dirStyle;

	/**
	 * URL оформления
	 *
	 * @var string
	 */
	protected $urlStyle;

	/**
	 * Конструктор
	 *
	 * Производит чтение настроек плагина и подключение языковых файлов
	 *
	 * @uses $locale
	 * @uses FS::isFile
	 * @uses Core::safeInclude
	 * @uses Eresus_Extensions_Plugin::resetPlugin
	 */
	public function __construct()
	{
		global $locale;

		$this->name = strtolower(get_class($this));
		if (!empty($this->name) && isset(Eresus_CMS::getLegacyKernel()->plugins->list[$this->name]))
		{
			$this->settings =
				decodeOptions(Eresus_CMS::getLegacyKernel()->plugins->list[$this->name]['settings'],
					$this->settings);
			/*
			 * Если установлена версия плагина отличная от установленной ранее
			 * то необходимо произвести обновление информации о плагине в БД
 			 */
			if ($this->version != Eresus_CMS::getLegacyKernel()->plugins->list[$this->name]['version'])
			{
				$this->resetPlugin();
			}
		}
		$this->dirData = Eresus_CMS::getLegacyKernel()->fdata . $this->name . '/';
		$this->urlData = Eresus_CMS::getLegacyKernel()->data . $this->name . '/';
		$this->dirCode = Eresus_CMS::getLegacyKernel()->froot . 'ext/' . $this->name . '/';
		$this->urlCode = Eresus_CMS::getLegacyKernel()->root . 'ext/' . $this->name . '/';
		$this->dirStyle = Eresus_CMS::getLegacyKernel()->fstyle . $this->name . '/';
		$this->urlStyle = Eresus_CMS::getLegacyKernel()->style . $this->name . '/';
		$filename = Eresus_CMS::getLegacyKernel()->froot . 'lang/' . $this->name . '/' .
			$locale['lang'] . '.php';
		if (FS::isFile($filename))
		{
			Core::safeInclude($filename);
		}
	}

	/**
	 * Возвращает информацию о плагине
	 *
	 * @param array $item  Предыдущая версия информации (по умолчанию null)
	 *
	 * @return array Массив информации, пригодный для записи в БД
	 */
	public function __item($item = null)
	{
		$result['name'] = $this->name;
		$result['content'] = false;
		$result['active'] = is_null($item)? true : $item['active'];
		$result['settings'] = Eresus_CMS::getLegacyKernel()->db->
			escape(is_null($item) ? encodeOptions($this->settings) : $item['settings']);
		$result['title'] = $this->title;
		$result['version'] = $this->version;
		$result['description'] = $this->description;
		return $result;
	}

	/**
	 * Перехватчик обращений к несуществующим методам плагинов
	 *
	 * @param string $method  Имя вызванного метода
	 * @param array  $args    Переданные аргументы
	 *
	 * @throws EresusMethodNotExistsException
	 */
	public function __call($method, $args)
	{
		throw new EresusMethodNotExistsException($method, get_class($this));
	}

	/**
	 * Возвращает URL директории данных плагина
	 *
	 * @return string
	 *
	 * @since 2.15
	 */
	public function getDataURL()
	{
		return $this->urlData;
	}

	/**
	 * Возвращает URL директории файлов плагина
	 *
	 * @return string
	 *
	 * @since 2.15
	 */
	public function getCodeURL()
	{
		return $this->urlCode;
	}

	/**
	 * Возвращает URL директории стилей плагина
	 *
	 * @return string
	 *
	 * @since 2.15
	 */
	public function getStyleURL()
	{
		return $this->urlStyle;
	}

	/**
	 * Чтение настроек плагина из БД
	 *
	 * @return bool  Результат выполнения
	 */
	protected function loadSettings()
	{
		$result = Eresus_CMS::getLegacyKernel()->db->
			selectItem('plugins', "`name`='".$this->name."'");
		if ($result)
		{
			$this->settings = decodeOptions($result['settings'], $this->settings);
		}
		return (bool) $result;
	}

	/**
	 * Сохранение настроек плагина в БД
	 *
	 * @return bool  Результат выполнения
	 */
	protected function saveSettings()
	{
		$result = Eresus_CMS::getLegacyKernel()->db->selectItem('plugins', "`name`='{$this->name}'");
		$result = $this->__item($result);
		$result['settings'] = Eresus_CMS::getLegacyKernel()->db->escape(encodeOptions($this->settings));
		$result = Eresus_CMS::getLegacyKernel()->db->
			updateItem('plugins', $result, "`name`='".$this->name."'");

		return $result;
	}

	/**
	 * Обновление данных о плагине в БД
	 */
	protected function resetPlugin()
	{
		$this->loadSettings();
		$this->saveSettings();
	}

	/**
	 * Действия, выполняемые при инсталляции плагина
	 */
	public function install()
	{

	}

	/**
	 * Действия, выполняемые при деинсталляции плагина
	 */
	public function uninstall()
	{
		$eresus = Eresus_CMS::getLegacyKernel();
		$tables = $eresus->db->query_array("SHOW TABLES LIKE '{$eresus->db->prefix}{$this->name}_%'");
		$tables = array_merge($tables, $eresus->db->
			query_array("SHOW TABLES LIKE '{$eresus->db->prefix}{$this->name}'"));
		for ($i=0; $i < count($tables); $i++)
		{
			$this->dbDropTable(substr(current($tables[$i]), strlen($this->name)+1));
		}
	}

	/**
	 * Действия при изменении настроек
	 */
	public function onSettingsUpdate()
	{
	}

	/**
	 * Сохраняет в БД изменения настроек плагина
	 */
	public function updateSettings()
	{
		foreach ($this->settings as $key => $value)
		{
			if (!is_null(arg($key)))
			{
				$this->settings[$key] = arg($key);
			}
		}
		$this->onSettingsUpdate();
		$this->saveSettings();
	}

	/**
	 * Замена макросов
	 *
	 * @param string $template  Строка в которой требуется провести замену макросов
	 * @param mixed  $item      Ассоциативный массив со значениями для подстановки вместо макросов
	 *
	 * @return  string  Обработанная строка
	 */
	protected function replaceMacros($template, $item)
	{
		$result = replaceMacros($template, $item);
		return $result;
	}

	/**
	 * Создание новой директории
	 *
	 * @param string $name Имя директории
	 * @return bool Результат
	 */
	protected function mkdir($name = '')
	{
		$result = true;
		$umask = umask(0000);
		// Проверка и создание корневой директории данных
		if (!is_dir($this->dirData))
		{
			$result = mkdir($this->dirData);
		}
		if ($result)
		{
			// Удаляем директории вида "." и "..", а также финальный и лидирующий слэши
			$name = preg_replace(array('!\.{1,2}/!', '!^/!', '!/$!'), '', $name);
			if ($name)
			{
				$name = explode('/', $name);
				$root = substr($this->dirData, 0, -1);
				for ($i=0; $i<count($name); $i++)
				{
					if ($name[$i])
					{
						$root .= '/'.$name[$i];
						if (!is_dir($root))
						{
							$result = mkdir($root);
						}
						if (!$result)
						{
							break;
						}
					}
				}
			}
		}
		umask($umask);
		return $result;
	}

	/**
	 * Удаление директории и файлов
	 *
	 * @param string $name Имя директории
	 * @return bool Результат
	 */
	protected function rmdir($name = '')
	{
		$result = true;
		$name = preg_replace(array('!\.{1,2}/!', '!^/!', '!/$!'), '', $name);
		$name = $this->dirData.$name;
		if (is_dir($name))
		{
			$files = glob($name.'/{.*,*}', GLOB_BRACE);
			for ($i = 0; $i < count($files); $i++)
			{
				if (substr($files[$i], -2) == '/.' || substr($files[$i], -3) == '/..')
				{
					continue;
				}
				if (is_dir($files[$i]))
				{
					$result = $this->rmdir(substr($files[$i], strlen($this->dirData)));
				}
				elseif (is_file($files[$i]))
				{
					$result = unlink($files[$i]);
				}
				if (!$result)
				{
					break;
				}
			}
			if ($result)
			{
				$result = rmdir($name);
			}
		}
		return $result;
	}

	/**
	 * Возвращает реальное имя таблицы
	 *
	 * @param string $table  Локальное имя таблицы
	 * @return string Реальное имя таблицы
	 */
	protected function __table($table)
	{
		return $this->name.(empty($table)?'':'_'.$table);
	}

	/**
	 * Создание таблицы в БД
	 *
	 * @param string $SQL Описание таблицы
	 * @param string $name Имя таблицы
	 *
	 * @return bool Результат выполнения
	 */
	protected function dbCreateTable($SQL, $name = '')
	{
		$result = Eresus_CMS::getLegacyKernel()->db->create($this->__table($name), $SQL);
		return $result;
	}

	/**
	 * Удаление таблицы БД
	 *
	 * @param string $name Имя таблицы
	 *
	 * @return bool Результат выполнения
	 */
	protected function dbDropTable($name = '')
	{
		$result = Eresus_CMS::getLegacyKernel()->db->drop($this->__table($name));
		return $result;
	}

	/**
	 * Производит выборку из таблицы БД
	 *
	 * @param string	$table				Имя таблицы (пустое значение - таблица по умолчанию)
	 * @param string	$condition		Условие выборки
	 * @param string	$order				Порядок выборки
	 * @param string	$fields				Список полей
	 * @param int			$limit				Вернуть не больше полей чем limit
	 * @param int			$offset				Смещение выборки
	 * @param string  $group        поле для группировки
	 * @param bool		$distinct			Только уникальные результаты
	 *
	 * @return array|bool  Выбранные элементы в виде массива или FALSE в случае ошибки
	 */
	public function dbSelect($table = '', $condition = '', $order = '', $fields = '', $limit = 0,
		$offset = 0, $group = '', $distinct = false)
	{
		$result = Eresus_CMS::getLegacyKernel()->db->select($this->__table($table), $condition, $order,
			$fields, $limit, $offset, $group, $distinct);

		return $result;
	}

	/**
	 * Получение записи из БД
	 *
	 * @param string $table  Имя таблицы
	 * @param mixed  $id   	 Идентификатор элемента
	 * @param string $key    Имя ключевого поля
	 *
	 * @return array Элемент
	 */
	public function dbItem($table, $id, $key = 'id')
	{
		$result = Eresus_CMS::getLegacyKernel()->db->selectItem($this->__table($table),
			"`$key` = '$id'");

		return $result;
	}

	/**
	 * Вставка в таблицу БД
	 *
	 * @param string $table  Имя таблицы
	 * @param array  $item   Вставляемый элемент
	 * @param string $key    Имя ключевого поля. По умолчанию "id"
	 *
	 * @return array
	 */
	public function dbInsert($table, $item, $key = 'id')
	{
		Eresus_CMS::getLegacyKernel()->db->insert($this->__table($table), $item);
		$result = $this->dbItem($table, Eresus_CMS::getLegacyKernel()->db->getInsertedId(), $key);

		return $result;
	}

	/**
	 * Изменение данных в БД
	 *
	 * @param string $table      Имя таблицы
	 * @param mixed  $data       Изменяемый эелемент / Изменения
	 * @param string $condition  Ключевое поле / Условие для замены
	 *
	 * @return bool Результат
	 */
	public function dbUpdate($table, $data, $condition = '')
	{
		if (is_array($data))
		{
			if (empty($condition))
			{
				$condition = 'id';
			}
			$result = Eresus_CMS::getLegacyKernel()->db->
				updateItem($this->__table($table), $data, "`$condition` = '{$data[$condition]}'");
		}
		elseif (is_string($data))
		{
			$result = Eresus_CMS::getLegacyKernel()->db->
				update($this->__table($table), $data, $condition);
		}
		else
		{
			$result = false;
		}

		return $result;
	}

	/**
	 * Удаление элемента из БД
	 *
	 * @param string $table  Имя таблицы
	 * @param mixed  $item   Удаляемый элемент / Идентификатор
	 * @param string $key    Ключевое поле
	 *
	 * @return bool Результат
	 */
	public function dbDelete($table, $item, $key = 'id')
	{
		$result = Eresus_CMS::getLegacyKernel()->db->
			delete($this->__table($table), "`$key` = '".(is_array($item)? $item[$key] : $item)."'");

		return $result;
	}

	/**
	 * Подсчёт количества записей в БД
	 *
	 * @param string $table      Имя таблицы
	 * @param string $condition  Условие для включения в подсчёт
	 *
	 * @return int Количество записей, удовлетворяющих условию
	 */
	public function dbCount($table, $condition = '')
	{
		$result = Eresus_CMS::getLegacyKernel()->db->count($this->__table($table), $condition);

		return $result;
	}

	/**
	 * Получение информации о таблицах
	 *
	 * @param string $table  Маска имени таблицы
	 * @param string $param  Вернуть только указанный параметр
	 *
	 * @return mixed
	 */
	public function dbTable($table, $param = '')
	{
		$result = Eresus_CMS::getLegacyKernel()->db->tableStatus($this->__table($table), $param);

		return $result;
	}

	/**
	 * Регистрация обработчиков событий
	 *
	 * @param string $event...  Имя события
	 */
	protected function listenEvents()
	{
		for ($i=0; $i < func_num_args(); $i++)
		{
			Eresus_CMS::getLegacyKernel()->plugins->events[func_get_arg($i)][] = $this->name;
		}
	}
}