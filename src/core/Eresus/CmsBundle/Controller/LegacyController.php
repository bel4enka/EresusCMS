<?php

namespace Eresus\CmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Eresus_CMS;
use Eresus\CmsBundle\Kernel;

class LegacyController extends Controller
{
    public function indexAction()
    {
        /** @var Eresus_CMS $app */
        $app = new Eresus_CMS($this->container);
        $this->container->set('app', $app);

        /** @var Kernel $kernel */
        $kernel = $this->get('kernel');
        /* Подключение старого ядра */
        include $kernel->getRootDir() . '/core/kernel-legacy.php';

        $response = $app->main();

        return $response;
    }

}
