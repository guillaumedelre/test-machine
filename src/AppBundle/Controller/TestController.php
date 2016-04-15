<?php

namespace AppBundle\Controller;

use CoreBundle\Entity\Test;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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

            return $this->redirectToRoute('admin_default_index');
        }

        if (null === $entity->getStartedAt()) {
            $entity->setStartedAt(new \DateTime());
            $this->get('core.repository.test')->save($entity);
        }

        return $this->render('AppBundle:Test:launch.html.twig', array(
            'test' => $entity,
            'set' => unserialize($entity->getData()),
            'deadline' => date("Y-m-d H:i:s", strtotime($entity->getStartedAt()->format('Y/m/d H:i:s')) + $entity->getNbMinutes() * 60),
        ));
    }

    /**
     * @Route("/test/{id}/finish", name="app_test_finish")
     * @Method("POST")
     */
    public function finishAction(Request $request, $id)
    {
        /** @var Test $entity */
        $entity = $this->get('core.repository.test')->find($id);

        if (null === $entity) {
            $this->get('session')->getFlashBag()->add('danger', "Test #$id introuvable.");

            return $this->redirectToRoute('admin_default_index');
        }

        $entity->setEndedAt(new \DateTime());
        $this->get('core.repository.test')->save($entity);

        $results = $this->get('core.service.certificationy')->validate($request, $entity);

        $scoring = 0;

        foreach($results as $result) {
            $scoring = ($result[2]) ? $scoring + 1 : $scoring;
        }

        return $this->render('AppBundle:Test:finish.html.twig', array(
            'answers' => $request->request->get('answers'),
            'test' => $entity,
            'set' => unserialize($entity->getData()),
            'scoring' => $scoring,
            'results' => $results,
        ));
    }
}
