<?php
/**
 * Заглушки встроенных классов Eresus
 *
 * @package Eresus
 * @subpackage Tests
 */

use Mekras\TestDoubles\UniversalStub;
use Mekras\TestDoubles\MockFacade;

require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * Заглушка для класса Eresus_Plugin
 *
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_Plugin extends UniversalStub
{
}

/**
 * Заглушка для класса ContentPlugin
 *
 * @package Eresus
 * @subpackage Tests
 */
class ContentPlugin extends Eresus_Plugin
{
}

/**
 * Заглушка для класса Eresus_CMS
 *
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_CMS extends MockFacade
{
}

/**
 * Заглушка для класса Eresus_CMS_Exception
 *
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_CMS_Exception extends Exception
{
}

/**
 * Заглушка для класса Eresus_DB
 *
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_DB extends MockFacade
{
}

/**
 * Заглушка для класса Eresus_Kernel
 *
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_Kernel extends MockFacade
{
}

/**
 * Заглушка для класса Eresus_ORM
 *
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_ORM extends MockFacade
{
}

/**
 * Заглушка для класса Eresus_ORM_Entity
 *
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_ORM_Entity extends UniversalStub
{
}

/**
 * Заглушка для класса Eresus_ORM_Table
 *
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_ORM_Table extends UniversalStub
{
}

/**
 * Заглушка для класса ezcQuery
 *
 * @package Eresus
 * @subpackage Tests
 */
class ezcQuery extends UniversalStub
{
}

/**
 * Заглушка для класса ezcQuerySelect
 *
 * @package Eresus
 * @subpackage Tests
 */
class ezcQuerySelect extends ezcQuery
{
    const ASC = 'ASC';
    const DESC = 'DESC';
}

