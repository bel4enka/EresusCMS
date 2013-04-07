#!/usr/bin/env php
<?php
/**
 * Определяет системные требования текущей версии Eresus
 *
 * @version ${product.version}
 *
 * @copyright 2012, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license http://www.gnu.org/licenses/gpl.txt  GPL License 3
 * @author Михаил Красильников <m.krasilnikov@yandex.ru>
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
 */

require 'Bartlett/PHP/CompatInfo/Autoload.php';

$source = realpath(__DIR__ . '/..');

$excludeRoot = str_replace('/', '\/', $source);

$options = array(
	//'cacheDriver' => 'null',
	'recursive' => true,
	//'consoleProgress' => true,
	'exclude' => array(
		'files' => array(
			str_replace('/', '\/', __FILE__),
			'.*\/[Tt]ests\/.*',
			'.*\/SDK\/.*',
		),
	),
);

try
{
	ob_start();
	@$report = new PHP_CompatInfo_Report_Xml($source, $options, array());
	$xml = ob_get_clean();
	$xml = new SimpleXMLElement($xml);

	$nodes = $xml->xpath('//extension');
	$ext = array();
	foreach ($nodes as $node)
	{
		$ext[strval($node['name'])] = true;
	}

	$ext = array_keys($ext);
	$ext = array_diff($ext, array('Core', 'SPL', 'standard'));
	natcasesort($ext);

	foreach ($ext as $name)
	{
		echo
			str_repeat('-', strlen($name)) . PHP_EOL .
			$name . PHP_EOL .
			str_repeat('-', strlen($name)) . PHP_EOL;
		$files = $xml->xpath('//file[extensions/extension/@name="' . $name . '"]');
		foreach ($files as $file)
		{
			echo $file['name'] . PHP_EOL;
		}
	}

	echo '-------------------------------------------------' . PHP_EOL;
	echo 'Версия PHP: ' . strval($xml->versions->min) . PHP_EOL;
	echo 'Расширения: ' . implode(', ', $ext) . PHP_EOL;
}
catch (PHP_CompatInfo_Exception $e)
{
	die($e->getMessage() . PHP_EOL);
}