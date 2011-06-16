<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Режим CMS
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
 * @package CMS
 *
 * $Id$
 */

/**
 * Режим CMS
 *
 * @package CMS
 * @since 2.16
 */
abstract class Eresus_CMS_Mode
{
	/**
	 * Запрос к CMS
	 *
	 * @var Eresus_CMS_Request
	 */
	private $request;

	/**
	 * Пользовательский интерфейс
	 *
	 * @var Eresus_CMS_UI
	 */
	protected $ui;

	/**
	 * Возвращает обрабатываемый запрос к CMS
	 *
	 * @return Eresus_CMS_Request
	 *
	 * @since 2.16
	 */
	public function getRequest()
	{
		if (!$this->request)
		{
			$this->request = $this->createRequest();
		}
		return $this->request;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает текущий интерфейс CMS
	 *
	 * @return Eresus_CMS_UI
	 *
	 * @since 2.16
	 */
	public function getUI()
	{
		return $this->ui;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обрабатывает запрос и возвращает ответ
	 *
	 * @return Eresus_CMS_Response
	 *
	 * @since 2.16
	 */
	public function process()
	{
		return $this->ui->process();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Метод должен возвращать объект запроса к CMS
	 *
	 * @return Eresus_CMS_Request
	 *
	 * @since 2.16
	 */
	abstract protected function createRequest();
	//-----------------------------------------------------------------------------
}
