<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="app_default_index")
     */
    public function indexAction()
    {
        $data = array(
            'count' => $this->get('core.service.certificationy')->count(),
            'categories' => $this->get('core.service.certificationy')->getCategories(),
        );

        return $this->render('AppBundle:Default:index.html.twig', $data);
    }

    /**
     * @Route("/launch", name="app_default_launch")
     * @Method("POST")
     */
    public function launchAction(Request $request)
    {
        $data['test'] = $this->get('core.service.certificationy')->launch(
            $request->request->get('number', 20),
            $request->request->get('categories', [])
        );

        return $this->render('AppBundle:Default:launch.html.twig', $data);
    }
}
