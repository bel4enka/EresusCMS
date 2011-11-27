<?php
/**
 * ${product.title}
 *
 * Консольный интерфейс
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
 * $Id$
 */


/**
 * Консольный интерфейс
 *
 * @package Eresus
 * @since 2.17
 */
class Eresus_Console extends Eresus_Application
{
	/**
	 * Доступные команды
	 *
	 * @var array
	 */
	private $commands;

	/**
	 * Основной метод приложения
	 *
	 * @return int  Код завершения для консольных вызовов
	 */
	public function main()
	{
		try
		{
			$this->initConf();
			$this->initDebugTools();
			$this->initTimezone();
			$this->initLocale();
			$this->initDB();
			$this->initPlugins();
			$this->initTemplateEngine();

			$this->loadCommands();

			// FIXME не учитываются опции перед командой
			$command = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : null;

			$exitCode = 0;

			if ($command)
			{
				if (isset($this->commands[$command]))
				{
					$exitCode = $this->commands[$command]->execute();
				}
				else
				{
					echo 'ERROR: Unknown command!' . PHP_EOL;
					$exitCode = -1;
				}
			}
			else
			{
				$this->showHelp();
			}
		}
		catch (Exception $e)
		{
			Eresus_Logger::exception($e);
			echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
			$exitCode = -1;
		}

		return $exitCode;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Загружает список команд
	 *
	 * @return void
	 *
	 * @since 2.17
	 */
	private function loadCommands()
	{
		$files = glob(dirname(__FILE__) . '/Console/Command/*.php');
		foreach ($files as $file)
		{
			include $file;
			$className = 'Eresus_Console_Command_' . basename($file, '.php');
			$command = new $className(Eresus_Kernel::sc());
			$this->commands[$command->getName()] = $command;
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Выводит помощь по использованию консольного интерфейса
	 *
	 * @return void
	 *
	 * @since 2.17
	 */
	private function showHelp()
	{
		/*
		 * Вычисляем наибольшую длину команды для выравнивания описаний
		 */
		$maxNameLen = 0;
		foreach ($this->commands as $command)
		{
			$len = mb_strlen($command->getName());
			if ($len > $maxNameLen)
			{
				$maxNameLen = $len;
			}
		}

		$commands = '';

		foreach ($this->commands as $command)
		{
			$commands .= '  ' . $command->getName();
			$commands .= str_repeat(' ', $maxNameLen - mb_strlen($command->getName()) + 2);
			$commands .= $command->getDescription();
			$commands .= PHP_EOL;
		}

		echo
			'Eresus ${product.version} console interface' . PHP_EOL .
			PHP_EOL .
			'Usage:'  . PHP_EOL .
			'  console [options] command [arguments]' . PHP_EOL .
			PHP_EOL .
			'Commands:' . PHP_EOL .
			$commands .
			PHP_EOL;
	}
	//-----------------------------------------------------------------------------

	/**
	 * @see Eresus_Application::initPlugins()
	 */
	protected function initPlugins()
	{
		try
		{
			parent::initPlugins();
		}
		catch (Doctrine_Connection_Exception $e)
		{
			// Ничего не делаем. Обычно это исключение означает лишь, что в БД нет таблицы расширений
		}
	}
	//-----------------------------------------------------------------------------
}
