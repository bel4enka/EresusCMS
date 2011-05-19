<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Средство журналирования
 *
 * @copyright 2004, Eresus Project, http://eresus.ru/
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
 * @package Core
 *
 * $Id$
 */

/**
 * Средство журналирования
 *
 * @package Core
 *
 * @since 2.16
 */
class Eresus_Logger
{
	/**
	 * Названия уровней важности
	 *
	 * @var array
	 */
	static private $priorityNames = array(
		LOG_DEBUG   => 'debug',
		LOG_INFO    => 'info',
		LOG_NOTICE  => 'notice',
		LOG_WARNING => 'warning',
		LOG_ERR     => 'error',
		LOG_CRIT    => 'critical',
		LOG_ALERT   => 'ALERT',
		LOG_EMERG   => 'PANIC'
	);

	/**
	 * Записывет сообщение в журнал
	 *
	 * По умолчанию записываются только сообщения с уровнем LOG_ERR или более важные.
	 * Это можно изменить при помощи настройки
	 * {@link Eresus_Config}::set('eresus.cms.log.level', LOG_XXX)
	 *
	 * Имя файла журнала задаётся параметрами PHP error_log и log_errors.
	 *
	 * @param string|array $sender    Отправитель сообщения. Используйте __METHOD__,
	 *                                array(get_class($this), __METHOD__) или __FUNCTION__
	 * @param int          $priority  Важность сообщения. См. константы LOG_XXX
	 * @param string       $message   Сообщение. Может содеражть подстановки (см. {@link sprintf()}
	 * @param mixed        $arg,..    Аргументы для подстановки в сообщение
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public static function log($sender, $priority, $message)
	{
		$logLevel = Eresus_Config::get('eresus.cms.log.level', LOG_ERR);

		if ($priority > $logLevel)
		{
			return;
		}

		$sender = self::sender2string($sender);

		$args = func_get_args(); // Этого требует PHP 5.2
		$message = self::substitute($message, $args);

		$message = $sender . ': ' . $message;

		/* Добавляем сведения о важности */
		if (isset(self::$priorityNames[$priority]))
		{
			$priorityName = self::$priorityNames[$priority];
		}
		else
		{
			$priorityName = 'unknown';
		}

		$message = '[' . $priorityName . '] ' . $message;

		/* Записываем сообщение */
		if (error_log($message) || syslog($priority, $message))
		{
			// Сообщение записано успешно
			return;
		}

		fputs(STDERR, __FUNCTION__ . ": Can not log message!\n");

	}
	//-----------------------------------------------------------------------------

	/**
	 * Записывет в журнал сообщение об исключительной ситуации
	 *
	 * @param Exception $e  Исключение, которое следует записать
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	static public function exception($e)
	{
		$text = self::exception2string($e);
		self::log('Logger', LOG_ERR, $text);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Превращает аргумент $sender в строку
	 *
	 * @param string|array $sender
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	private static function sender2string($sender)
	{
		if (is_array($sender))
		{
			$sender = implode('/', $sender);
		}

		if (empty($sender))
		{
			$sender = 'unknown';
		}

		return $sender;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Превращает исключение в строку
	 *
	 * @param Exception $e
	 * @param int       $level  уровень рекурсии
	 *
	 * @return string
	 *
	 * @since 0.2.0
	 */
	private static function exception2string($e, $level = 0)
	{
		$previous = null;
		if (method_exists($e, 'getPrevious'))
		{
			$previous = $e->getPrevious();
		}

		$result = sprintf(
			"%s in %s at %s\n%s\nBacktrace:\n%s\n",
			get_class($e),
			$e->getFile(),
			$e->getLine(),
			$e->getMessage(),
			$e->getTraceAsString()
		);

		if ($level > 0)
		{
			$indention = str_repeat(' ', $level * 2);
			$result = str_replace("\n", "\n$indention", $result);
			$result = substr($result, 0, -1 * $level * 2);
			$result = $indention . 'Previous ' . $result;
		}

		if ($previous)
		{
			$result .= self::exception2string($previous, $level + 1);
		}

		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Подставляет аргументы в сообщение
	 *
	 * @param string $message  сообщение
	 * @param array  $args     аргументы
	 *
	 * @return string
	 *
	 * @since 0.2.0
	 */
	private static function substitute($message, $args)
	{
		if (count($args) > 3)
		{
			$flatArgs = array();
			for ($i = 3; $i < count($args); $i++)
			{
				if (is_object($args[$i]))
				{
					$args[$i] = get_class($args[$i]);
				}
				$flatArgs []= $args[$i];
			}
			$message = vsprintf($message, $flatArgs);
		}
		return $message;
	}
	//-----------------------------------------------------------------------------
}
