<?
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# Система управления контентом Eresus™
# Версия 2.08
# © 2004-2007, ProCreat Systems
# http://procreat.ru/
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# Файл конфигурации
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

# Настройки MySQL
define('dbHost', 'localhost'); # хост сервера MySQL
define('dbUser', 'root'); # имя пользователя для MySQL
define('dbPswd', 'mysql'); # пароль для доступа к MySQL
define('dbName', ''); # Имя базы данных
define('dbPrefix', ''); # Префикс имени базы данных

define('SESSION_TIMEOUT', 30); # Таймаут сессии в минутах
define('NOTIFICATIONS_ENABLED', true); # Глобально управляет отправлением извещений

# Региональные настройки
define('GMT_ZONE', +3); # НЕ ИЗМЕНЯТЬ!
define('MULTILANGUAGE', false); # НЕ ИЗМЕНЯТЬ! Включить поддержку многоязычности
define('DEFAULT_LANGUAGE', 'ru'); # НЕ ИЗМЕНЯТЬ! Язык по умолчанию

define('execScript', 'exec.php'); # Виртуальный скрипт

# Раскомментировать для отмены автоопределения
# define('DOCUMENT_ROOT', '/var/www/public_html/'); 

define('DEBUG_MODE', true); # Включить функции отладки
if (constant('DEBUG_MODE')) {
  error_reporting(E_ALL);
  define('DEBUG_LOG_FILENAME', './../data/.debug'); # Имя лог-файла отладки
  define('DEBUG_SENT_FILENAME', './../data/.sent'); # Имя лог-файла отправленной почты
} else error_reporting(0);
?>