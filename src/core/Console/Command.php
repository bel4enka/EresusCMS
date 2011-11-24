<?php
/**
 * ${product.title}
 *
 * Абстрактная консольная команда
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
 * $Id: Kernel.php 1978 2011-11-22 14:49:17Z mk $
 */


/**
 * Абстрактная консольная команда
 *
 * @package Eresus
 * @since 2.17
 */
abstract class Eresus_Console_Command
{
	/**
	 * Имя команды
	 *
	 * @var string
	 */
	private $name;

	/**
	 * Описание команды
	 *
	 * @var string
	 */
	private $description;

	/**
	 * Хранилище служб
	 *
	 * @var sfServiceContainer
	 * @since 2.17
	 */
	protected $container;

	/**
	 * Конструктор
	 *
	 * @param sfServiceContainer $container  хранилище служб
	 *
	 * @throws LogicException  если не установлено имя команды
	 *
	 * @return Eresus_Console_Command
	 *
	 * @since 2.17
	 */
	public function __construct(sfServiceContainer $container)
	{
		$this->container = $container;
		$this->configure();

		if (!$this->name)
		{
			throw new LogicException('Command name not set');
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает имя команды
	 *
	 * @return string
	 *
	 * @since 2.17
	 */
	public function getName()
	{
		return $this->name;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает описание команды
	 *
	 * @return string
	 *
	 * @since 2.17
	 */
	public function getDescription()
	{
		return $this->description;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Выполнение команды
	 *
	 * @return int  код завершения
	 *
	 * @since 2.17
	 */
	abstract public function execute();
	//-----------------------------------------------------------------------------

	/**
	 * Настройка команды
	 *
	 * Потомки должны как минимум устанавливать имя команды.
	 *
	 * @return void
	 *
	 * @since 2.17
	 */
	abstract protected function configure();
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает имя команды
	 *
	 * @param string $name
	 *
	 * @return Eresus_Console_Command
	 *
	 * @since 2.17
	 */
	protected function setName($name)
	{
		$this->name = $name;
		return $this;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает описание команды
	 *
	 * @param string $description
	 *
	 * @return Eresus_Console_Command
	 *
	 * @since 2.17
	 */
	protected function setDescription($description)
	{
		$this->description = $description;
		return $this;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Выводит переданные строки на консоль, добавляя к каждой перевод строки
	 *
	 * @return void
	 *
	 * @since 2.17
	 */
	protected function out()
	{
		$output = implode(PHP_EOL, func_get_args()) . PHP_EOL;
		echo $output;
	}
	//-----------------------------------------------------------------------------

}
