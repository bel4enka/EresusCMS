<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Подготовка БД
 *
 * @copyright 2010, Eresus Project, http://eresus.ru/
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
 * @package EresusCMS
 *
 * $Id$
 */


ini_set('display_errors', true);
error_reporting(E_ALL);


/**
 * Класс для чтения настроек
 *
 * @package EresusCMS
 * @since 2.16
 */
class Core
{
	/**
	 * Значения
	 *
	 * @var array
	 */
	public static $values;

	/**
	 * Получает значения и запомниает 'eresus.cms.dsn' в $dsn.
	 *
	 * @param string $key
	 * @param mixed $value
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public static function setValue($key, $value)
	{
		self::$values[$key] = $value;
	}
	//-----------------------------------------------------------------------------
}


$root = dirname(__FILE__) . '/../..';

$conf = $root . '/cfg/main.php';

if (!file_exists($conf))
{
	fputs(STDERR, "Configuration file '$conf' not found!\n");
	exit(-1);
}

/**
 * Подключение настроек
 */
include $conf;

try
{
	/**
	 * Подключение Doctrine
	 */
	include_once $root . '/core/Doctrine.php';
	spl_autoload_register(array('Doctrine', 'autoload'));
	spl_autoload_register(array('Doctrine_Core', 'modelsAutoload'));
	require_once $root . '/core/DBAL/EresusActiveRecord.php';

	Doctrine_Manager::connection(Core::$values['eresus.cms.dsn'], 'doctrine')->
		setCharset('cp1251'); // TODO Убрать после перехода на UTF

	$manager = Doctrine_Manager::getInstance();
	$manager->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, true);
	$manager->setAttribute(Doctrine_Core::ATTR_VALIDATE, Doctrine_Core::VALIDATE_ALL);

	if (isset(Core::$values['eresus.cms.dsn.prefix']))
	{
		$manager->setAttribute(Doctrine_Core::ATTR_TBLNAME_FORMAT,
			Core::$values['eresus.cms.dsn.prefix'] . '%s');
	}

	Doctrine_Core::createTablesFromModels($root . '/core/Model');

	$user = new Eresus_Model_User();
	$user->username = 'root';
	$user->password = '';
	$user->active = true;
	$user->access = 1;
	$user->fullname = 'Главный администратор';
	$user->mail = 'root@example.org';
	$user->save();

	$section = new Eresus_Model_Section();
	$section->name = 'main';
	$section->owner = 0;
	$section->title = 'Главная страница';
	$section->caption = 'Главная';
	$section->active = true;
	$section->access = 5;
	$section->visible = true;
	$section->template = 'default';
	$section->type = 'default';
	$section->content = '<h1>Eresus CMS</h1>';
	$section->save();
}
catch (Exception $e)
{
	fputs(STDERR, get_class($e) .': ' . $e->getMessage() . ' in ' . $e->getFile() . ' on ' .
		$e->getLine() . "\n" . $e->getTraceAsString());
	exit(-1);
}
