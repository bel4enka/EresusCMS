<?php
/**
 * ${product.title}
 *
 * Модель плагина
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
 * $Id: Plugin.php 1609 2011-05-18 09:46:37Z mk $
 */

/**
 * Класс информации о плагине
 *
 * @property-read string         $uid
 * @property-read string         $name
 * @property-read string         $version
 * @property-read string         $title
 * @property-read string         $description
 * @property-read array          $requiredKernel
 * @property-read array          $requiredPlugins
 * @property-read array          $developers
 * @property-read array          $authors
 * @property-read array          $docs
 * @property      int            $active
 * @property      array          $settings
 *
 * @package Eresus
 * @since 2.17
 */
class Eresus_Entity_Plugin extends Eresus_DB_Record
{
	/**
	 * Главный объект модуля расширения
	 *
	 * @var Eresus_CMS_Plugin
	 */
	private $mainObject;

	/**
	 * @see Doctrine_Record_Abstract::setTableDefinition()
	 */
	public function setTableDefinition()
	{
		$this->setTableName('plugins');
		$this->hasColumns(array(
			'uid' => array(
				'type' => 'string',
				'length' => 255,
				'primary' => true,
				'notnull' => true,
				'autoincrement' => false,
			),
			'name' => array(
				'type' => 'string',
				'length' => 255,
				'notnull' => true,
			),
			'active' => array(
				'type' => 'boolean',
				'notnull' => true,
			),
			'settings' => array(
				'type' => 'array',
			),
			'xml' => array(
				'type' => 'object',
			),
		));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Создаёт объект из файла
	 *
	 * @param string $filename
	 *
	 * @throws RuntimeException  если файл содержит ошибки
	 *
	 * @return Eresus_Plugin
	 *
	 * @since 2.16
	 */
	public static function loadFromFile($filename)
	{
		$plugin = new self;
		$plugin->name = basename(dirname($filename));

		libxml_use_internal_errors(true);
		libxml_clear_errors();
		$plugin->xml = new Eresus_XML_Element($filename, 0, true);
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

		$plugin->uid = strval($plugin->xml['uid']);

		return $plugin;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Имитация свойств
	 *
	 * @param string $name
	 *
	 * @return mixed
	 *
	 * @since 2.17
	 */
	public function __get($name)
	{
		$getter = 'get' . $name;
		if (method_exists($this, $getter))
		{
			return $this->$getter();
		}
		return parent::__get($name);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает основной объект модуля расширения
	 *
	 * @throws DomainException  если основной класс модуля не найден
	 *
	 * @return Eresus_CMS_Plugin
	 *
	 * @since 2.17
	 */
	public function main()
	{
		if (!$this->mainObject)
		{
			$className = 'Plugin_' . $this->name;
			if (!class_exists($className))
			{
				Eresus_Logger::log(__METHOD__, LOG_ERR, 'Class not found: ' . $className);
				throw new DomainException(
					sprintf(i18n('Модуль расширения «%s» повреждён. Подробности в журнале.'), $this->title));
			}
			$this->mainObject = new $className(Eresus_Kernel::sc());
		}
		return $this->mainObject;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает название
	 *
	 * @return string
	 *
	 * @since 2.16
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function getTitle()
	{
		$tags = $this->xml->xpath('/declaration/title');
		return $tags[0]->getLocalized();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает версию
	 *
	 * @return string
	 *
	 * @since 2.16
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function getVersion()
	{
		$tags = $this->xml->xpath('/declaration/version');
		return strval($tags[0]);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает описание
	 *
	 * @return string
	 *
	 * @since 2.16
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function getDescription()
	{
		$tags = $this->xml->xpath('/declaration/description');
		return $tags[0]->getLocalized();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает требуемую версию CMS
	 *
	 * @return array
	 *
	 * @since 2.16
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function getRequiredKernel()
	{
		$tags = $this->xml->xpath('/declaration/requires/cms');
		return array(
			'min' => strval($tags[0]['min']),
			'max' => strval($tags[0]['max'])
		);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает список требуемых плагинов
	 *
	 * @return array
	 *
	 * @since 2.16
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function getRequiredPlugins()
	{
		$tags = $this->xml->xpath('/declaration/requires/plugin');
		$plugins = array();
		foreach ($tags as $tag)
		{
			$uid = strval($tag['uid']);
			$plugins[$uid] = array(
				'uid' => $uid,
				'min' => strval($tag['min']),
				'max' => strval($tag['max']),
				'name' => isset($tag['name']) ? strval($tag['name']) : null,
				'url' => isset($tag['url']) ? strval($tag['url']) : null,
			);
		}
		return $plugins;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает список разработчиков
	 *
	 * @return array
	 *
	 * @since 2.17
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function getDevelopers()
	{
		$tags = $this->xml->xpath('/declaration/developer');
		$developers = array();
		foreach ($tags as $tag)
		{
			$developers [] = array(
				'title' => strval($tag['title']),
				'url' => isset($tag['url']) ? strval($tag['url']) : null,
			);
		}
		return $developers;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает список авторов
	 *
	 * @return array
	 *
	 * @since 2.17
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function getAuthors()
	{
		$tags = $this->xml->xpath('/declaration/author');
		$authors = array();
		foreach ($tags as $tag)
		{
			$authors [] = array(
				'name' => strval($tag['name']),
				'email' => isset($tag['email']) ? strval($tag['email']) : null,
				'url' => isset($tag['url']) ? strval($tag['url']) : null,
			);
		}
		return $authors;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает список ссылок на документацию
	 *
	 * @return array
	 *
	 * @since 2.17
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function getDocs()
	{
		$tags = $this->xml->xpath('/declaration/docs');
		$docs = array();
		foreach ($tags as $tag)
		{
			$docs[strval($tag['lang'])] = strval($tag['url']);
		}
		return $docs;
	}
	//-----------------------------------------------------------------------------
}