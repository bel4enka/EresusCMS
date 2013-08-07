<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2004-2007, Михаил Красильников <mihalych@vsepofigu.ru>
 * @copyright 2007-2008, Eresus Project, http://eresus.ru/
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
 */

/**
 * Создание изображения из файла
 *
 * @param string $filename Имя файла
 * @return mixed Дескриптор или false
 */
function imageCreateFromFile($filename)
{
    $type = getimagesize($filename);
    switch ($type[2])
    {
        case IMAGETYPE_GIF:
            $result = imageCreateFromGIF($filename);
            break;
        case IMAGETYPE_JPEG:
        case IMAGETYPE_JPEG2000:
            $result = imageCreateFromJPEG($filename);
            break;
        case IMAGETYPE_PNG:
            $result = imageCreateFromPNG($filename);
            break;
        default:
            switch (substr($type['mime'], 6))
            {
                case 'gif':
                    $result = imageCreateFromGIF($filename);
                    break;
                case 'jpeg':
                    $result = imageCreateFromJPEG($filename);
                    break;
                case 'png':
                    $result = imageCreateFromPNG($filename);
                    break;
                default:
                    $result = false;
            }
    }
    return $result;
}

/**
 * Сохранение изображения в файл заданного формата
 *
 * @param resource $image     Изображение
 * @param string $filename  Имя файла
 * @param int $format         Формат файла
 * @return mixed Дескриптор или false
 */
function imageSaveToFile($image, $filename, $format)
{
    $result = false;
    switch ($format)
    {
        case IMAGETYPE_GIF:
            $result = imageGIF($image, $filename);
            break;
        case IMAGETYPE_JPEG:
        case IMAGETYPE_JPEG2000:
            $quality = func_num_args() > 3 ? func_get_arg(3) : 80;
            $result = imageJPEG($image, $filename, $quality);
            break;
        case IMAGETYPE_PNG:
            $quality = func_num_args() > 3 ? func_get_arg(3) : 7;
            $filters = func_num_args() > 4 ? func_get_arg(4) : 0;
            $result = imagePNG($image, $filename, $quality, $filters);
            break;
    }
    return $result;
}


/**
 * Создание миниатюры
 *
 * @param string $srcFile  Исходный файл
 * @param string $dstFile  Файл миниатюры
 * @param int $width    Ширина миниатюры
 * @param int $height   Высота миниатюры
 * @param mixed $fill     Заполнение фона
 *
 * @return bool Результат
 */
function thumbnail($srcFile, $dstFile, $width, $height, $fill = null, $quality = 80)
{
    $result = false;
    $src = imageCreateFromFile($srcFile);
    if ($src)
    {
        $sW = imageSX($src);
        $sH = imageSY($src);
        $resizer = ($sW / $width > $sH / $height) ? ($sW / $width) : ($sH / $height);
        $dW = floor($sW / $resizer);
        $dH = floor($sH / $resizer);
        if (is_null($fill))
        {
            $dst = imageCreateTrueColor($dW, $dH);
            imageCopyResampled($dst, $src, 0, 0, 0, 0, $dW, $dH, $sW, $sH);
        }
        else
        {
            $dst = imageCreateTrueColor($width, $height);
            if ($fill[0] == '#')
            {
                $R = hexdec(substr($fill, 1, 2));
                $G = hexdec(substr($fill, 3, 2));
                $B = hexdec(substr($fill, 5, 2));
            }
            else
            {
                $fill = explode(',', $fill);
                $R = trim($fill[0]);
                $G = trim($fill[1]);
                $B = trim($fill[2]);
            }
            imagefill($dst, 0, 0, imagecolorallocate($dst, $R, $G, $B));
            imageCopyResampled($dst, $src, round(($width - $dW) / 2), round(($height - $dH) / 2), 0, 0, $dW, $dH, $sW, $sH);
        }
        $result = ImageJPEG($dst, $dstFile, $quality);
        ImageDestroy($src);
        ImageDestroy($dst);
    }
    return $result;
}

/**
 * Изменение формата изображения
 *
 * @param string $srcFilename  Имя исходного файла
 * @param string $dstFilename  Имя файла назначения
 * @param int $format       Формат файла назначения
 * @return bool  Результат выполнения
 */
function imageConvert($srcFile, $dstFile, $format = IMG_JPG)
{
    $src = imageCreateFromFile($srcFile);
    if ($src)
    {
        $result = imageSaveToFile($src, $dstFile, $format);
    }
    else
    {
        $result = false;
    }
    return $result;
}

