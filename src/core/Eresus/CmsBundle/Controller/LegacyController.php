<?php

namespace Eresus\CmsBundle\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Eresus_SuccessException;
use Eresus_Kernel;
use Eresus_CMS;

class LegacyController extends Controller
{
    public function indexAction()
    {
        /** @var Eresus_CMS $app */
        $app = new Eresus_CMS();
        Eresus_Kernel::sc()->set('app', $app);

        ob_start();
        try
        {
            /** @var Eresus_Kernel $kernel */
            $kernel = $this->get('kernel');
            /* Подключение старого ядра */
            include $kernel->getRootDir() . '/core/kernel-legacy.php';

            /* Общая инициализация */
            $app->main();
        }
        catch (Eresus_SuccessException $e)
        {
        }

        $html = ob_get_clean();
        $response = new \Symfony\Component\HttpFoundation\Response($html);
        return $response;
    }

}
