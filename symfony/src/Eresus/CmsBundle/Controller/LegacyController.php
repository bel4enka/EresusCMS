<?php

namespace Eresus\CmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Eresus_Kernel;

class LegacyController extends Controller
{
    public function indexAction()
    {
        ob_start();
        Eresus_Kernel::initStatic();
        Eresus_Kernel::exec('Eresus_CMS');
        $html = ob_get_clean();
        $response = new \Symfony\Component\HttpFoundation\Response($html);
        return $response;
    }
}
