<?php
/**
 * ${product.title}
 *
 * Информация о плагине
 *
 * @version ${product.version}
 *
 * @copyright 2011, Eresus Project, http://eresus.ru/
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
 * $Id$
 */



/**
 * Информация о плагине
 *
 * @package Eresus
 * @since 2.16
 */
class Eresus_PluginInfo
{
	/**
	 * Данные в XML
	 * @var SimpleXMLElement
	 * @since 2.17
	 */
	private $xmlData;

	/**
	 * UID плагина
	 *
	 * @var string
	 */
	private $uid;

	/**
	 * Имя
	 *
	 * @var string
	 */
	private $name;

	/**
	 * Версия
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Название
	 *
	 * @var string
	 */
	private $title;

	/**
	 * Описание
	 *
	 * @var string
	 */
	private $description;

	/**
	 * Требуемая версия ядар
	 *
	 * @var array [min, max]
	 */
	private $requiredKernel = array();

	/**
	 * Требуемые плагины
	 *
	 * @var array [[uid, min, max, name, url]]
	 * @since 2.17
	 */
	private $requiredPlugins = array();

	/**
	 * Разработчики (компании)
	 *
	 * @var array [[title, url]]
	 * @since 2.17
	 */
	private $developers = array();

	/**
	 * Авторы (люди)
	 *
	 * @var array [[name, email, url]]
	 * @since 2.17
	 */
	private $authors = array();

	/**
	 * Документация
	 *
	 * @var array [xx_XX => url]
	 * @since 2.17
	 */
	private $docs = array();

	/**
	 * Создаёт объект из файла
	 *
	 * @param string $filename
	 *
	 * @throws RuntimeException  если файл содержит ошибки
	 *
	 * @return Eresus_PluginInfo
	 *
	 * @since 2.16
	 */
	public static function loadFromFile($filename)
	{
		$info = new self;
		$info->name = basename(dirname($filename));

		libxml_use_internal_errors(true);
		libxml_clear_errors();
		$info->xmlData = new Eresus_XML_Element($filename, 0, true);
		$errors = libxml_get_errors();
		if (count($errors))
		{
			libxml_clear_errors();
			$msg = array();
			foreach ($errors as $e)
			{
				$msg []= $e->message . '(' . $e->file . ':' . $e->line . ':' . $e->column . ')';
			}
			$msg = implode('; ', $msg);
			throw new RuntimeException($msg);
		}

		$info->uid = strval($info->xmlData['uid']);

		return $info;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Имитация свойств
	 *
	 * @param string $name
	 *
	 * @return mixed
	 *
	 * @since 2.16
	 */
	public function __get($name)
	{
		$getter = 'get' . $name;
		if (!method_exists($this, $getter))
		{
			throw new RuntimeException('Access to unexistent property ' . $name . ' in class ' .
				get_class($this));
		}
		return $this->$getter();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает UID плагина
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	public function getUID()
	{
		return $this->uid ? $this->uid : $this->getName() . '@unknown.eresus.ru';
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает имя
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	public function getName()
	{
		return $this->name;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает название
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	public function getTitle()
	{
		if (!$this->title)
		{
			$tags = $this->xmlData->xpath('/declaration/title');
			$this->title = $tags[0]->getLocalized();
		}
		return $this->title;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает версию
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	public function getVersion()
	{
		if (!$this->version)
		{
			$tags = $this->xmlData->xpath('/declaration/version');
			$this->version = strval($tags[0]);
		}
		return $this->version;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает описание
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	public function getDescription()
	{
		if (!$this->description)
		{
			$tags = $this->xmlData->xpath('/declaration/description');
			$this->description = $tags[0]->getLocalized();
		}
		return $this->description;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает требуемую версию CMS
	 *
	 * @return array
	 *
	 * @since 2.16
	 */
	public function getRequiredKernel()
	{
		if (!$this->requiredKernel)
		{
			$tags = $this->xmlData->xpath('/declaration/requires/cms');
			$this->requiredKernel = array(
				'min' => strval($tags[0]['min']),
				'max' => strval($tags[0]['max'])
			);
		}
		return $this->requiredKernel;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает список требуемых плагинов
	 *
	 * @return array
	 *
	 * @since 2.16
	 */
	public function getRequiredPlugins()
	{
		if (!$this->requiredPlugins)
		{
			$tags = $this->xmlData->xpath('/declaration/requires/plugin');
			$this->requiredPlugins = array();
			foreach ($tags as $tag)
			{
				$uid = strval($tag['uid']);
				$this->requiredPlugins[$uid] = array(
					'uid' => $uid,
					'min' => strval($tag['min']),
					'max' => strval($tag['max']),
					'name' => isset($tag['name']) ? strval($tag['name']) : null,
					'url' => isset($tag['url']) ? strval($tag['url']) : null,
				);
			}
		}
		return $this->requiredPlugins;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает список разработчиков
	 *
	 * @return array
	 *
	 * @since 2.17
	 */
	public function getDevelopers()
	{
		if (!$this->developers)
		{
			$tags = $this->xmlData->xpath('/declaration/developer');
			$this->developers = array();
			foreach ($tags as $tag)
			{
				$this->developers [] = array(
					'title' => strval($tag['title']),
					'url' => isset($tag['url']) ? strval($tag['url']) : null,
				);
			}
		}
		return $this->developers;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает список авторов
	 *
	 * @return array
	 *
	 * @since 2.17
	 */
	public function getAuthors()
	{
		if (!$this->authors)
		{
			$tags = $this->xmlData->xpath('/declaration/author');
			$this->authors = array();
			foreach ($tags as $tag)
			{
				$this->authors [] = array(
					'name' => strval($tag['name']),
					'email' => isset($tag['email']) ? strval($tag['email']) : null,
					'url' => isset($tag['url']) ? strval($tag['url']) : null,
				);
			}
		}
		return $this->authors;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает список ссылок на документацию
	 *
	 * @return array
	 *
	 * @since 2.17
	 */
	public function getDocs()
	{
		if (!$this->docs)
		{
			$tags = $this->xmlData->xpath('/declaration/docs');
			$this->docs = array();
			foreach ($tags as $tag)
			{
				$this->docs[strval($tag['lang'])] = strval($tag['url']);
			}
		}
		return $this->docs;
	}
	//-----------------------------------------------------------------------------
}
