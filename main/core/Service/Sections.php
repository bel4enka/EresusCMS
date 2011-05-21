<?php
/**
 * ${product.title} ${product.version}
 *
 * Служба по работе с разделами сайта
 *
 * @copyright 2011, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mihalych@vsepofigu.ru>
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
 * @package Domain
 *
 * $Id$
 */

/**
 * Служба по работе с разделами сайта
 *
 * @package Domain
 * @since 2.16
 */
class Eresus_Service_Sections implements Eresus_CMS_Service
{
	/**
	 * Экземпляр-одиночка
	 *
	 * @var Eresus_Service_Sections
	 */
	private static $instance = null;

	/**
	 * Реестр разделов
	 *
	 * Хранит объекты всех разделов сайта для ускорения работы с ними
	 *
	 * @var array
	 */
	private $registry = array();

	/**
	 * Индекс разделов
	 *
	 * Содержит ссылки на разделы по разным признакам
	 *
	 * @var array
	 */
	private $index = array();

	/**
	 * Возвращает экземпляр класса
	 *
	 * @return Eresus_Service_Sections
	 *
	 * @since 2.16
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new self();
		}
		return self::$instance;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает корневой раздел сайта
	 *
	 * @return Eresus_Model_Section
	 *
	 * @since 2.16
	 */
	public function getRoot()
	{
		$this->buildIndex();
		$index = $this->index['id'][1];
		return $this->registry[$index];
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает подраздел указанного раздела по его имени
	 *
	 * @param int    $owner
	 * @param string $name
	 *
	 * @return Eresus_Model_Section|null
	 *
	 * @since 2.16
	 */
	public function getChildByName($owner, $name)
	{
		$this->buildIndex();
		$coll = $this->index['owner'][$owner];
		if ($coll)
		{
			foreach ($coll as $index)
			{
				$model = $this->registry[$index];
				if ($model->name = $name)
				{
					return $model;
				}
			}
		}
		return null;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Скрываем конструктор
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	// @codeCoverageIgnoreStart
	private function __construct()
	{
	}
	// @codeCoverageIgnoreEnd
	//-----------------------------------------------------------------------------

	/**
	 * Блокируем клонирование
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	// @codeCoverageIgnoreStart
	private function __clone()
	{
	}
	// @codeCoverageIgnoreEnd
	//-----------------------------------------------------------------------------

	/**
	 * Создаёт индекс разделов
	 *
	 * @param bool $force  игнорировать закэшированные данные
	 * @return void
	 */
	private function buildIndex($force = false)
	{
		if ($force || !$this->index)
		{
			/*
			 * Для уменьшения количества запросов при работе с разделами загружаем все разделы разом.
			 * Для экономии памяти поле content не загружаем — оно будет загружено "ленивым" способом.
			 */
			$q = Eresus_DB_Query::create()->select('s.id, s.name, s.owner, s.title, s.caption, ' .
				's.description, s.hint, s.keywords, s.position, s.active, s.access, s.visible, ' .
				's.template, s.type, s.options, s.created, s.updated')->
				from('Eresus_Model_Section s')->orderBy('s.position');
			$this->registry = $q->execute();

			$this->index = new Eresus_Helper_Collection(array(
				'id' => array(),
				'owner' => new Eresus_Helper_Collection(),
			));
			$this->index->setDefaultValue(array());
			$this->index['owner']->setDefaultValue(array());

			if ($this->registry)
			{
				foreach ($this->registry as $index => $model)
				{
					$this->index['id'][$model->id] = $index;
					$this->index['owner'][$model->owner] []= $index;
				}
			}
			else
			{
				$this->registry = array();
			}
		}
	}
	//------------------------------------------------------------------------------

	/**
	 * Ищет в наборе объект с указанным именем
	 *
	 * @param Eresus_Helper_Collection $set
	 * @param string $name
	 *
	 * @return mixed
	 *
	 * @since 2.16
	 */
	private function helperFindByName(Eresus_Helper_Collection $set, $name)
	{
		for ($i = 0; $i < count($set); $i++)
		{
			if ($set[$i]['name'] == $name)
			{
				return $set[$i];
			}
		}
		return null;
	}
	//-----------------------------------------------------------------------------
}
