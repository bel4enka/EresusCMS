<?php
/**
 * ${product.title}
 *
 * Содержимое раздела
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

namespace Eresus\Html\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Eresus\ORMBundle\AbstractEntity;
use Eresus\CmsBundle\Entity\Section;

/**
 * Содержимое раздела
 *
 * @property      int       $id
 * @property      Section   $section
 * @property      string    $contents
 *
 * @since 4.00
 *
 * @ORM\Entity
 * @ORM\Table(name="eresus_html")
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 */
class Content extends AbstractEntity
{
    /**
     * Идентификатор
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * Раздел
     *
     * @var Section
     *
     * @ORM\OneToOne(targetEntity="Eresus\CmsBundle\Entity\Section")
     */
    protected $parent;

    /**
     * Содержимое
     *
     * @var string
     *
     * @ORM\Column(type="text")
     */
    protected $contents;
}

