<?php

namespace AppBundle\Controller;

use CoreBundle\Entity\AbstractEntity;
use CoreBundle\Entity\Test;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TestController extends Controller
{
    /**
     * @Route("/test/{id}/launch", name="app_test_launch")
     */
    public function launchAction(Request $request, $id)
    {
        /** @var Test $entity */
        $entity = $this->get('core.repository.test')->find($id);

        if (null === $entity) {
            $this->get('session')->getFlashBag()->add('danger', "Test #$id introuvable.");

            return $this->redirectToRoute('app_admin_index');
        }

        return $this->render('AppBundle:Test:launch.html.twig', array(
            'test' => unserialize($entity->getData()),
        ));
    }
}
