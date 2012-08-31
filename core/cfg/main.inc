<?
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# Система управления контентом Eresus™
# Версия 2.00rc2
# © 2004-2006, ProCreat Systems
# http://procreat.ru/
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# Файл конфигурации
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

# Настройки MySQL
define('dbHost', ''); # хост сервера MySQL
define('dbUser', ''); # имя пользователя для MySQL
define('dbPswd', ''); # пароль для дуступа к MySQL
define('dbName', ''); # Имя базы данных
define('dbPrefix', ''); # Префикс имени базы данных

define('SESSION_TIMEOUT', 30); # Таймаут сессии в минутах
define('NOTIFICATIONS_ENABLED', true); # Глобально управляет отправлением извещений

# Региональные настройки
define('GMT_ZONE', +3);
define('MULTILANGUAGE', false); # Включить поддержку многоязычности
define('DEFAULT_LANGUAGE', 'ru'); # Язык по умолчанию

define('execScript', 'exec.php'); # Виртуальный скрипт

define('DEBUG_MODE', false); # Включить функции отладки
if (constant('DEBUG_MODE')) {
  error_reporting(E_ALL);
  define('DEBUG_LOG_FILENAME', '/tmp/logs/debug.log'); # Имя лог-файла отладки
  define('DEBUG_SENT_FILENAME', '/tmp/logs/sent.log'); # Имя лог-файла отправленной почты
} else error_reporting(E_NONE);
?>