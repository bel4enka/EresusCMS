<?php

namespace Eresus\Html\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Eresus\CmsBundle\HTTP\Request;

class DefaultContentClientController extends Controller
{
    /**
     * @param \Eresus\CmsBundle\HTTP\Request $request
     * @return string
     */
    public function indexAction(Request $request)
    {
        return sprintf('Привет, %s!',
            $request->query->has('name') ? $request->query->get('name') : 'незнакомец');
    }

}
