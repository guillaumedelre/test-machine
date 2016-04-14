<?php

namespace AdminBundle\Controller;

use CoreBundle\Entity\AbstractEntity;
use CoreBundle\Entity\Test;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class TestController extends Controller
{
    /**
     * @Route("/test", name="admin_test_index")
     */
    public function indexAction(Request $request)
    {
        $limit = $request->query->get('limit', AbstractEntity::DEFAULT_LIMIT_ADMIN);
        $offset = $request->query->get('offset', 0);

        $data = array(
            'currentPage' => $offset,
            'currentLimit' => $limit,
            'totalPages'  => ceil(count($this->get('core.repository.test')->findAll()) / $limit),
            'tests'      => $this->get('core.repository.test')->findBy([], ['createdAt' => 'DESC'], $limit, $offset * $limit),
        );

        return $this->render('AdminBundle:Test:index.html.twig', $data);
    }

    /**
     * @Route("/test/add", name="admin_test_add")
     */
    public function addAction(Request $request)
    {
        $entity = new Test();

        $this->get('core.form.handler.test')->buildForm($entity);

        if ('POST' === $request->getMethod()) {
            try {
                $categories = $this->get('core.repository.category')->findWhereIdIn($request->request->get('test', [])['categories']);
                $entity->setData(serialize($this->get('core.service.certificationy')->getTest(
                    $request->request->get('test', 20)['nbQuestion'],
                    $categories
                )));
                $entity->setToken(crypt($entity->getEmail().time(), 'SHA256'));
                $this->get('core.form.handler.test')->process($request, $entity);
                $this->get('session')->getFlashBag()->add('success', 'Le test a bien été enregistré.');
            } catch (\Exception $e) {
                $this->get('session')->getFlashBag()->add('danger', $e->getMessage());
            }

            return $this->redirectToRoute('admin_test_index');
        }

        $data = array(
            'form'       => $this->get('core.form.handler.test')->getForm()->createView(),
        );

        return $this->render('AdminBundle:Test:add.html.twig', $data);
    }

    /**
     * @Route("/test/{id}/edit", name="admin_test_edit")
     */
    public function editAction(Request $request, $id)
    {
        /** @var Test $entity */
        $entity = $this->get('core.repository.test')->find($id);

        if (null !== $entity) {
            $this->get('core.form.handler.test')->buildForm($entity);
            if ('POST' === $request->getMethod()) {
                try {
                    $this->get('core.form.handler.test')->process($request, $entity);
                    $this->get('session')->getFlashBag()->add('success', 'Le test a bien été enregistré.');
                    return $this->redirectToRoute('admin_test_index');
                } catch (\Exception $e) {
                    $this->get('session')->getFlashBag()->add('danger', $e->getMessage());
                    return $this->redirectToRoute('admin_test_edit', ['id' => $id]);
                }
            }
        } else {
            $this->get('session')->getFlashBag()->add('danger', "Test #$id introuvable.");
            return $this->redirectToRoute('admin_test_index');
        }

        $data = array(
            "test" => $entity,
            "form" => $this->get('core.form.handler.test')->getForm()->createView(),
        );

        return $this->render('AdminBundle:Test:edit.html.twig', $data);
    }

    /**
     * @Route("/test/{id}/delete", name="admin_test_delete")
     */
    public function deleteAction(Request $request, $id)
    {
        /** @var Test $entity */
        $entity = $this->get('core.repository.test')->find($id);

        if (null !== $entity) {
            $this->getDoctrine()->getManager()->remove($entity);
            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->getFlashBag()->add('info', 'Le test a bien été supprimé.');
        } else {
            $this->get('session')->getFlashBag()->add('danger', "Test #$id introuvable.");
        }

        return $this->redirectToRoute('admin_test_index');
    }
}
