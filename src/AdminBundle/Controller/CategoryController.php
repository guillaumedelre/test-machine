<?php

namespace AdminBundle\Controller;

use CoreBundle\Entity\AbstractEntity;
use CoreBundle\Entity\Category;
use CoreBundle\Entity\Test;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends Controller
{
    /**
     * @Route("/category", name="admin_category_index")
     */
    public function indexAction(Request $request)
    {
        $limit = $request->query->get('limit', AbstractEntity::DEFAULT_LIMIT_ADMIN);
        $offset = $request->query->get('offset', 0);

        $data = array(
            'currentPage'  => $offset,
            'currentLimit' => $limit,
            'totalPages'   => ceil(count($this->get('core.repository.category')->findAll()) / $limit),
            'categories'   => $this->get('core.repository.category')->findBy([], ['createdAt' => 'DESC'], $limit, $offset * $limit),
        );

        return $this->render('AdminBundle:Category:index.html.twig', $data);
    }

    /**
     * @Route("/category/add", name="admin_category_add")
     */
    public function addAction(Request $request)
    {
        $entity = new Category();

        $this->get('core.form.handler.category')->buildForm($entity);

        if ('POST' === $request->getMethod()) {
            try {
                $this->get('core.form.handler.category')->process($request, $entity);
                $this->get('session')->getFlashBag()->add('success', 'La catégorie a bien été enregistré.');
            } catch (\Exception $e) {
                $this->get('session')->getFlashBag()->add('danger', $e->getMessage());
            }

            return $this->redirectToRoute('admin_category_index');
        }

        $data = array(
            'form' => $this->get('core.form.handler.category')->getForm()->createView(),
        );

        return $this->render('AdminBundle:Category:add.html.twig', $data);
    }

    /**
     * @Route("/category/{id}/edit", name="admin_category_edit")
     */
    public function editAction(Request $request, $id)
    {
        /** @var Category $entity */
        $entity = $this->get('core.repository.category')->find($id);

        if (null !== $entity) {
            $this->get('core.form.handler.category')->buildForm($entity);
            if ('POST' === $request->getMethod()) {
                try {
                    $this->get('core.form.handler.category')->process($request, $entity);
                    $this->get('session')->getFlashBag()->add('success', 'La catégorie a bien été enregistré.');
                    return $this->redirectToRoute('admin_test_index');
                } catch (\Exception $e) {
                    $this->get('session')->getFlashBag()->add('danger', $e->getMessage());
                    return $this->redirectToRoute('admin_category_edit', ['id' => $id]);
                }
            }
        } else {
            $this->get('session')->getFlashBag()->add('danger', "Catégorie #$id introuvable.");
            return $this->redirectToRoute('admin_category_index');
        }

        $data = array(
            "test" => $entity,
            "form" => $this->get('core.form.handler.category')->getForm()->createView(),
        );

        return $this->render('AdminBundle:Category:edit.html.twig', $data);
    }

    /**
     * @Route("/category/{id}/delete", name="admin_category_delete")
     */
    public function deleteAction(Request $request, $id)
    {
        /** @var Category $entity */
        $entity = $this->get('core.repository.test')->find($id);

        if (null !== $entity) {
            $this->getDoctrine()->getManager()->remove($entity);
            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->getFlashBag()->add('info', 'La catégorie a bien été supprimé.');
        } else {
            $this->get('session')->getFlashBag()->add('danger', "Catégorie #$id introuvable.");
        }

        return $this->redirectToRoute('admin_category_index');
    }
}
