#!/usr/bin/env php
<?php
/**
 * Скачивает jQuery, jQueryUI и создаёт тему используя API jqueryui.com
 *
 * @version ${product.version}
 *
 * @copyright 2013, Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license http://www.gnu.org/licenses/gpl.txt	GPL License 3
 * @author Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author Михаил Красильников <mk@dvaslona.ru>
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

$jqueryUiVersion = '1.10.2';

$components = array('core', 'widget', 'mouse', 'position', 'draggable', 'droppable', 'resizable',
    'selectable', 'sortable', 'accordion', 'autocomplete', 'button', 'datepicker', 'dialog', 'menu',
    'progressbar', 'slider', 'spinner', 'tabs', 'tooltip', 'effect', 'effect-blind',
    'effect-bounce', 'effect-clip', 'effect-drop', 'effect-explode', 'effect-fade', 'effect-fold',
    'effect-highlight', 'effect-pulsate', 'effect-scale', 'effect-shake', 'effect-slide',
    'effect-transfer');



$theme = array(
    'ffDefault'=> 'Verdana,Helvetica,sans-serif',
    'fwDefault' => 'bold',
    'fsDefault' => '13px',
    'cornerRadius' => '3px',
    'bgColorHeader' => '#B8CFEE',
    'bgTextureHeader' => 'glass',
    'bgImgOpacityHeader' => '100',
    'borderColorHeader' => '#99bbe8',
    'fcHeader' => '#15428b',
    'iconColorHeader' => '#15428b',
    'bgColorContent' => '#ffffff',
    'bgTextureContent' => 'flat',
    'bgImgOpacityContent' => '75',
    'borderColorContent' => '#99BBE8',
    'fcContent' => '#222222',
    'iconColorContent' => '#222222',
    'bgColorDefault' => '#B8CFEE',
    'bgTextureDefault' => 'glass',
    'bgImgOpacityDefault' => '100',
    'borderColorDefault' => '#99bbe8',
    'fcDefault' => '#15428b',
    'iconColorDefault' => '#15428b',
    'bgColorHover' => '#759e1a',
    'bgTextureHover' => 'glass',
    'bgImgOpacityHover' => '100',
    'borderColorHover' => '#bed789',
    'fcHover' => '#ffffff',
    'iconColorHover' => '#ffffff',
    'bgColorActive' => '#759e1a',
    'bgTextureActive' => 'glass',
    'bgImgOpacityActive' => '50',
    'borderColorActive' => '#bed789',
    'fcActive' => '#ffffff',
    'iconColorActive' => '#454545',
    'bgColorHighlight' => '#e8f6ca',
    'bgTextureHighlight' => 'glass',
    'bgImgOpacityHighlight' => '55',
    'borderColorHighlight' => '#759e1a',
    'fcHighlight' => '#363636',
    'iconColorHighlight' => '#2e83ff',
    'bgColorError' => '#fef1ec',
    'bgTextureError' => 'glass',
    'bgImgOpacityError' => '95',
    'borderColorError' => '#cd0a0a',
    'fcError' => '#cd0a0a',
    'iconColorError' => '#cd0a0a',
    'bgColorOverlay' => '#aaaaaa',
    'bgTextureOverlay' => 'flat',
    'bgImgOpacityOverlay' => '0',
    'opacityOverlay' => '30',
    'bgColorShadow' => '#aaaaaa',
    'bgTextureShadow' => 'flat',
    'bgImgOpacityShadow' => '0',
    'opacityShadow' => '30',
    'thicknessShadow' => '8px',
    'offsetTopShadow' => '-8px',
    'offsetLeftShadow' => '-8px',
    'cornerRadiusShadow' => '8px',
);

/**
 * Рекурсивно удаляет папку
 *
 * @param string $folder
 */
function remove($folder)
{
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder,
        FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path)
    {
        /** @var SplFileInfo $path */
        $path->isFile() ? unlink($path->getPathname()) : rmdir($path->getPathname());
    }
    rmdir($folder);

}


include_once 'HTTP/Request2.php';

$config = array(
    'follow_redirects' => true,
    'ssl_verify_peer' => false,
);
$req = new HTTP_Request2('http://download.jqueryui.com/download',
    HTTP_Request2::METHOD_POST, $config);
$req
    ->addPostParameter('theme-folder-name', 'eresus')
    ->addPostParameter('version', $jqueryUiVersion)
    ->addPostParameter('scope', '');

foreach ($components as $component)
{
    $req->addPostParameter($component, 'on');
}

$themeEncoded = array();
foreach ($theme as $key => $value)
{
    $themeEncoded []= $key . '=' . urlencode($value);
}
$themeEncoded = implode('&', $themeEncoded);
$req->addPostParameter('theme', $themeEncoded);

$response = $req->send();
if ($response->getStatus() != 200)
{
    die('Request unsuccessful. Response from server: ' . $response->getStatus() . ' '
        . $response->getReasonPhrase() . PHP_EOL);
}

$content = $response->getBody();
$disposition = $response->getHeader('content-disposition');

$folder = __DIR__ . '/../vendor/components/jqueryui';
$filename = "$folder/jquery-ui.zip";
if (!is_dir($folder))
{
    mkdir($folder, 0755, true);
}
else
{
    remove($folder);
    mkdir($folder, 0755);
}

file_put_contents($filename, $content);

$zip = new ZipArchive();
$zip->open($filename);
$zip->extractTo($folder);
$zip->close();
unlink($filename);

$source = "$folder/jquery-ui-$jqueryUiVersion.custom";
rename("$source/js/jquery-ui-$jqueryUiVersion.custom.min.js",
    "$folder/jquery-ui.min.js");
rename("$source/development-bundle/ui/minified/i18n/jquery-ui-i18n.min.js",
    "$folder/jquery-ui-i18n.min.js");
rename("$source/css/eresus/jquery-ui-$jqueryUiVersion.custom.min.css",
    "$folder/jquery-ui.min.css");
rename("$source/css/eresus/images", "$folder/images");

remove($source);

