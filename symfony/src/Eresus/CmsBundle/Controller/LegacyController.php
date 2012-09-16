<?php

namespace Eresus\CmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LegacyController extends Controller
{
    public function indexAction()
    {
        ob_start();
        include __DIR__ . '/../../../../web/index.php';
        $html = ob_get_clean();
        $response = new \Symfony\Component\HttpFoundation\Response($html);
        return $response;
    }
}
