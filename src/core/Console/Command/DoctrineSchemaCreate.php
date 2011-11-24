<?php
/**
 * ${product.title}
 *
 * Создание схемы в БД
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
 * Создание схемы в БД
 *
 * @package Eresus
 * @since 2.17
 */
class Eresus_Console_Command_DoctrineSchemaCreate extends Eresus_Console_Command
{
	/**
	 * Выполняет команду
	 *
	 * @return int  код завершения
	 *
	 * @since 2.17
	 */
	public function execute()
	{
		Doctrine_Core::createTablesFromModels();

		/* TODO: Переделать через YAML */
		$user = new Eresus_Entity_User();
		$user->username = 'root';
		$user->password = '';
		$user->active = true;
		$user->access = 1;
		$user->fullname = 'Главный администратор';
		$user->mail = 'root@example.org';
		$user->save();

		$section = new Eresus_Entity_Section();
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

		return 0;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Настройка команды
	 *
	 * @return void
	 *
	 * @since 2.17
	 */
	protected function configure()
	{
		$this->
			setName('doctrine:schema:create')->
			setDescription('Executes (or dumps) the SQL needed to generate the database schema');
	}
	//-----------------------------------------------------------------------------
}
