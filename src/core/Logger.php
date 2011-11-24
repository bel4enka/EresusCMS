<?php
/**
 * ${product.title}
 *
 * Средство журналирования
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
 * Средство журналирования
 *
 * Для примеров использования см. {@link log()} и {@link exception()}
 *
 * Сообщения записываются в журнал при помощи функции
 * {@link http://php.net/manual/en/function.error-log.php error_log()}. Если эта функция возвращает
 * false, производится попытка записать сообщение через вызов {@link syslog() syslog()}. Если и
 * syslog() вернёт false, то будет выведено сообщение об ошибке в STDERR.
 *
 * <b>Настройка журнала</b>
 *
 * Расположение журнала задаётся переменной {@link error_log error_log}.
 *
 * Минимальный порог важности сообщения, при котором оно будет записано в журнал задаётся
 * параметром «eresus.cms.log.level» через {@link Eresus_Config::set()}. По умолчанию это LOG_ERR.
 *
 * @package Eresus
 *
 * @since 2.17
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
	 * <b>Уровни важности сообщений</b> (в порядке уменьшения):
	 * - LOG_EMERG — использование системы невозможно
	 * - LOG_ALERT — требуется немедленное вмешательство
	 * - LOG_CRIT — критическая ситуация
	 * - LOG_ERR — ошибка
	 * - LOG_WARNING — предупреждение
	 * - LOG_NOTICE — замечание
	 * - LOG_INFO — информационное сообщение
	 * - LOG_DEBUG — отладочное сообщение
	 *
	 * <i>В Microsoft™ Windows® значение констант LOG_EMERG и LOG_CRIT совпадает с LOG_ALERT, а
	 * констант LOG_NOTICE и LOG_DEBUG с LOG_INFO. Значения LOG_ERR соответствует LOG_WARNING в Linux,
	 * а LOG_WARNING соответствует LOG_NOTICE.</i>
	 *
	 * <b>Примеры</b>
	 *
	 * Пример 1:
	 *
	 * <code>
	 * function my_function($filename)
	 * {
	 *   Eresus_Logger::log(__FUNCTION__, LOG_DEBUG, 'Reading from "%s"', $filename);
	 * }
	 *
	 * my_function('test.txt');
	 * </code>
	 *
	 * Запишет в лог примерно следующее:
	 *
	 * <samp>
	 * [02-06-09 22:26:04] [debug] my_function: Reading from "test.txt"
	 * </samp>
	 *
	 * Пример 2
	 *
	 * <code>
	 * class MyClass
	 * {
	 *   function myMethod($filename)
	 *   {
	 *     Eresus_Logger::log(__METHOD__, LOG_DEBUG, 'Reading from "%s"', $filename);
	 *   }
	 * }
	 *
	 * $obj = new MyClass();
	 * $obj->myMethod('test.txt');
	 * </code>
	 *
	 * Запишет в лог примерно следующее:
	 *
	 * <samp>
	 * [02-06-09 22:26:04] [debug] MyClass::myMethod: Reading from "test.txt"
	 * </samp>
	 *
	 * Пример 3
	 *
	 * <code>
	 * class BaseClass
	 * {
	 * 	function myMethod($filename)
	 * 	{
	 *  	Eresus_Logger::log(array(get_class($this), __METHOD__), LOG_DEBUG,
	 *  		'Reading from "%s"', $filename);
	 * 	}
	 * }
	 *
	 * class MyClass extends MyClass
	 * {
	 * }
	 *
	 * $obj = new MyClass();
	 * $obj->myMethod('test.txt');
	 * </code>
	 *
	 * Запишет в лог примерно следующее:
	 *
	 * <samp>
	 * [02-06-09 22:26:04] [debug] MyClass/BaseClass::myMethod: Reading from "test.txt"
	 * </samp>
	 *
	 * @param string|array $sender    Отправитель сообщения. Используйте __METHOD__,
	 *                                array(get_class($this), __METHOD__) или __FUNCTION__
	 * @param int          $priority  Важность сообщения. См. константы LOG_XXX
	 * @param string       $message   Сообщение. Может содеражть подстановки (см. {@link sprintf()}
	 * @param mixed        $arg,..    Аргументы для подстановки в сообщение
	 *
	 * @return void
	 *
	 * @since 2.17
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

		//@codeCoverageIgnoreStart
		fputs(STDERR, __FUNCTION__ . ": Can not log message!\n");
		//@codeCoverageIgnoreEnd

		//@codeCoverageIgnoreStart
	}
	//@codeCoverageIgnoreEnd
	//-----------------------------------------------------------------------------

	/**
	 * Записывет в журнал сообщение об исключительной ситуации
	 *
	 * Запись производится вызовом {@link log()} с важностью LOG_ERR.
	 *
	 * @param Exception $e  Исключение, которое следует записать
	 *
	 * @return void
	 *
	 * @since 2.17
	 * @uses exception2string()
	 * @uses log()
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
	 * @since 2.17
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
	 * @since 2.17
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
	 * @since 2.17
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
