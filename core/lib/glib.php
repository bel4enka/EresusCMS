<?php
/**
 * Библиотека для работы с изображениями
 * 
 * Система управления контентом Eresus™ 2
 * © 2007, Eresus Group, http://eresus.ru/
 *
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 * @version 0.0.1
 * @modified 2007-09-24
 */

/**
 * Создание миниатюры
 *
 * @param string $srcFile  Исходный файл
 * @param string $dstFile  Файл миниатюры
 * @param int    $width    Ширина миниатюры
 * @param int    $height   Высота миниатюры
 * @param mixed  $fill     Заполнение фона
 * 
 * @return bool Результат
 */
function thumbnail($srcFile, $dstFile, $width, $height, $fill = null, $quality = 80)
{
	$result = false;
	$type = getimagesize($srcFile);
	switch ($type[2]) {
		case IMG_GIF: $src = imageCreateFromGIF($srcFile); break;
		case IMG_JPG:
		case IMG_JPEG: $src = imageCreateFromJPEG($srcFile); break;
		case IMG_PNG: $src = imageCreateFromPNG($srcFile); break;
	}
	if ($src) {
		$sW = imageSX($src);
		$sH = imageSY($src);
		$resizer = ($sW/$width > $sH/$height) ? ($sW / $width) : ($sH / $height);
		$dW = floor($sW / $resizer);
		$dH = floor($sH / $resizer);
		if (is_null($fill)) {
			$dst = imageCreateTrueColor($dW, $dH);
			imageCopyResampled($dst, $src, 0, 0, 0, 0, $dW, $dH, $sW, $sH);
		} else {
			$dst = imageCreateTrueColor($width, $height);
			if ($fill[0] == '#') {
				$R = hexdec(substr($fill, 1, 2));
				$G = hexdec(substr($fill, 3, 2));
				$B = hexdec(substr($fill, 5, 2));
			} else {
				$fill = explode(',', $fill);
				$R = trim($fill[0]);
				$G = trim($fill[1]);
				$B = trim($fill[2]);
			}
		imagefill($dst, 0, 0, imagecolorallocate($dst, $R, $G, $B));
		imageCopyResampled($dst, $src, round(($width-$dW)/2), round(($height-$dH)/2), 0, 0, $dW, $dH, $sW, $sH);
		}
		$result = ImageJPEG($dst, $dstFile, $quality);
		ImageDestroy($src);
		ImageDestroy($dst);
	}
	return $result;
}

?>