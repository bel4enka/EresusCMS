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
 * @author Mikhail Krasilnikov <mk@procreat.ru>
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



/**
 * Класс для чтения настроек
 *
 * @package EresusCMS
 * @since 2.2x
 */
class Core
{
	/**
	 * DSN
	 *
	 * @var string
	 */
	public static $dsn;

	/**
	 * Получает значения и запомниает 'eresus.cms.dsn' в $dsn.
	 *
	 * @param string $key
	 * @param mixed $value
	 *
	 * @return void
	 *
	 * @since 2.2x
	 */
	public static function setValue($key, $value)
	{
		if ($key == 'eresus.cms.dsn')
		{
			self::$dsn = $value;
		}
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

/**
 * Подключение Doctrine
 */
include_once $root . '/core/Doctrine.php';
spl_autoload_register(array('Doctrine', 'autoload'));

try
{
	Doctrine_Manager::connection(Core::$dsn, 'doctrine');
	$manager = Doctrine_Manager::getInstance();
	$manager->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, true);
	$manager->setAttribute(Doctrine_Core::ATTR_VALIDATE, Doctrine_Core::VALIDATE_ALL);
	Doctrine_Core::createTablesFromModels($root . '/core/models');

	$user = new User();
	$user->login = 'root';
	$user->hash = md5(md5(''));
	$user->active = true;
	$user->access = 1;
	$user->name = 'Главный администратор';
	$user->mail = 'root@example.org';
	$user->save();

	$section = new Section();
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
	fputs(STDERR, get_class($e) .': ' . $e->getMessage() . "\n");
	exit(-1);
}
