<?php
/**
 * Абстрактный контроллер АИ
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

namespace Eresus\CmsBundle\Controller;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Eresus\CmsBundle\EresusCmsBundle;
use Eresus\CmsBundle\Kernel;
use Eresus\CmsBundle\Repository\SectionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Абстрактный контроллер АИ
 *
 * @since 4.0.0
 */
abstract class AdminAbstractController extends Controller
{
    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        if (null !== $container)
        {
            /** @var Registry $doctrine */
            $doctrine = $this->get('doctrine');
            /** @var EntityManager $em */
            $em = $doctrine->getManager();
            /** @var SectionRepository $repo */
            $repo = $em->getRepository('EresusCmsBundle:Section');
            /** @var Kernel $kernel */
            $kernel = $this->get('kernel');
            /** @var EresusCmsBundle $bundle */
            $bundle = $kernel->getBundle('EresusCmsBundle');
            $bundle->setGlobalVar('rootSection', $repo->getRoot());
        }
    }
}

