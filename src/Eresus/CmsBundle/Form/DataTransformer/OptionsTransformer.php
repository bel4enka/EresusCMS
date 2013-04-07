<?php
/**
 * ${product.title}
 *
 * Трансформер опций
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
 */

namespace Eresus\CmsBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Трансформер опций
 *
 * @since 4.00
 */
class OptionsTransformer implements DataTransformerInterface
{
    /**
     * Превращает массив опций в текст
     *
     * @param array $options
     *
     * @return string
     */
    public function transform($options)
    {
        $text = '';
        if (is_array($options))
        {
            foreach ($options as $key => $value)
            {
                $text .= "$key=$value\n";
            }
        }
        return $text;
    }

    /**
     * Превращает текст в массив опций
     *
     * @param  string $text
     *
     * @return array
     */
    public function reverseTransform($text)
    {
        $options = array();
        $text = explode("\n", trim($text));
        foreach ($text as $line)
        {
            list ($key, $value) = explode('=', $line);
            $options[$key] = $value;
        }
        return $options;
    }
}