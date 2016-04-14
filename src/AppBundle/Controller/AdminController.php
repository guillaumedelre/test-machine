<?php

namespace AppBundle\Controller;

use CoreBundle\Entity\AbstractEntity;
use CoreBundle\Entity\Test;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
    /**
     * @Route("/admin/", name="app_admin_index")
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

        return $this->render('AppBundle:Default:index.html.twig', $data);
    }

    /**
     * @Route("/admin/tests/add", name="app_admin_add")
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

            return $this->redirectToRoute('app_admin_index');
        }

        $data = array(
            'form'       => $this->get('core.form.handler.test')->getForm()->createView(),
        );

        return $this->render('AppBundle:Default:add.html.twig', $data);
    }

    /**
     * @Route("/admin/tests/{id}/edit", name="app_admin_edit")
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
                    return $this->redirectToRoute('app_admin_index');
                } catch (\Exception $e) {
                    $this->get('session')->getFlashBag()->add('danger', $e->getMessage());
                    return $this->redirectToRoute('app_admin_edit', ['id' => $id]);
                }
            }
        } else {
            $this->get('session')->getFlashBag()->add('danger', "Article #$id introuvable.");
            return $this->redirectToRoute('app_admin_index');
        }
        $data = array(
            "test" => $entity,
            "form" => $this->get('core.form.handler.test')->getForm()->createView(),
        );
        return $this->render('AppBundle:Default:edit.html.twig', $data);
    }

    /**
     * @Route("/admin/tests/{id}/delete", name="app_admin_delete")
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
        return $this->redirectToRoute('app_admin_index');
    }
}
