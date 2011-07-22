<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2004, ProCreat Systems, http://procreat.ru/
 * @copyright 2007, Eresus Project, http://eresus.ru/
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
 * @package UI
 *
 * $Id$
 */

$messages = array(
	'en' => array(
		'header' => 'Error occured!',
		'location' => 'Error location',
		'stack' => 'Call stack',
		'additional' => 'Additional info',
		'production' => 'Site currently unavailable. We apologize.',
),
	'ru' => array(
		'header' => 'Сбой на сайте!',
		'location' => 'Место возникновения ошибки',
		'stack' => 'Стек вызовов',
		'additional' => 'Дополнительные сведения',
		'production' => 'Сайт временно недоступен. Приносим свои извинения.',
	),
);


$local = $messages['ru'];
if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
{
	$hal = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
	$langs = array();
	foreach ($hal as $value)
	{
		list($code, $priority) = explode(';', $value);
		if (@$priority)
		{
			$priority = substr($priority, 2);
		}
		else
		{
			$priority = 1;
		}
		$langs[$code] = $priority;
	}
	arsort($langs);
	$langs = array_keys($langs);
	foreach ($langs as $lang)
	{
		if (isset($messages[$lang]))
		{
			$local = $messages[$lang];
			break;
		}
	}
}

?>
<!DOCTYPE html>

<html>
  <head>
    <meta charset="UTF-8">
    <title><?php echo $local['header'];?></title>
    <style type="text/css">
    	html,
    	body
    	{
    		height: 100%;
    		margin: 0;
    		padding: 0;
    		width: 100%;
			}
			h1
			{
    		-moz-box-shadow: 0 2px 4px #000;
    		-webkit-box-shadow: 0 2px 4px #000;
				background: #a00;
				box-shadow: 0 2px 4px #000;
				color: #fff;
				margin: 0;
				padding: .2em .5em;
			}
			h2,
			h3
			{
				margin: .2em 0;
			}
			.report
			{
				padding: .2em .5em;
			}
			.message
			{
				color: #f00;
				font-weight: bold;
				padding: .5em 0;
			}
			.location
			{
				font-family: monospace;
			}
			.code,
			.trace
			{
				border: solid 1px #000;
				margin: 0 0 1em;
				padding: .5em 0;
			}
				.code code
				{
					display: block;
				}
				.code .error-line
				{
					background-color: #faa;
				}
			.trace
			{
				padding: .5em 1em;
			}

    </style>
  </head>
<body>
	<h1><?php echo $local['header'];?></h1>
	<div class="report">
<?php
if (isset($error))
{
	echo '<h2>' . get_class($error) . '</h2>';
	if (class_exists('Eresus_Config', false) && Eresus_Config::get('eresus.cms.debug', false))
	{
		?>
		<div class="message"><?php echo $error->getMessage();?></div>
		<h3><?php echo $local['location'];?></h3>
		<div class="location">
			<?php echo $error->getFile();?>:
			<?php echo $error->getLine();?>
		</div>
		<div class="code">
		<?php
		$lines = file($error->getFile());
		$firstLine = $error->getLine() > 5 ? $error->getLine() - 5 : 0;
		$lastLine = $error->getLine() + 4 < count($lines) ? $error->getLine() + 4 : count($lines);
		for ($i = $firstLine; $i < $lastLine; $i++)
		{
			$s = highlight_string('<?php' . $lines[$i], true);
			$s = preg_replace('/&lt;\?php/', '', $s, 1);
			if ($i == $error->getLine() - 1)
			{
				$s = preg_replace('/(<\w+)/', '$1 class="error-line"', $s);
			}
			echo $s;
		}
		?>
		</div>
		<h3><?php echo $local['stack'];?></h3>
		<pre class="trace"><?php echo $error->getTraceAsString();?></pre>
		<h3><?php echo $local['additional'];?></h3>
		<?php
	}
	elseif ($error instanceof DomainException)
	{
		echo '<p>' . $error->getMessage() . '</p>';
	}
}
else
{
	echo '<p>' . $local['production'] . '</p>';
}
?>
	</div>
</body>
</html>