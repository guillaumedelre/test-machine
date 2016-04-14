<?php

namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class TestController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Retourne une collection paginée
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Récupère une collection paginée",
     *  section="Test",
     *  output="CoreBundle\Entity\Test",
     *  statusCodes={
     *      200="Ok",
     *      400="Bad Request",
     *      500="Internal error"
     *  }
     * )
     *
     * @Rest\QueryParam(name="field", allowBlank=false, default="id", description="Field to sort")
     * @Rest\QueryParam(name="sort", requirements="(asc|desc)", allowBlank=false, default="desc", description="Sort direction")
     * @Rest\QueryParam(name="limit", requirements="\d+", default="20", strict=true, nullable=true, description="Item count limit")
     * @Rest\QueryParam(name="page", requirements="\d+", default="0", strict=true, nullable=true, description="Current page of collection")
     */
    public function cgetAction(ParamFetcher $paramFetcher)
    {
        $limit   = $paramFetcher->get('limit');
        $page    = $paramFetcher->get('page');
        $sort    = $paramFetcher->get('sort');
        $field   = $paramFetcher->get('field');
        $collection = $this->get('core.repository.test')->findBy([], [$field => $sort], $limit, $page * $limit);
        if(!is_array($collection)) {
            throw $this->createNotFoundException();
        }
        return View::create($collection, Codes::HTTP_OK);
    }
    /**
     * Retourne l'élément dont l'id est passé en paramètre
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Récupère l'élément dont l'id est passé en paramètre",
     *  section="Test",
     *  output="CoreBundle\Entity\Test",
     *  requirements={
     *      {"name"="id", "dataType"="integer", "required"=true, "description"="Id de l'élément"}
     *  },
     *  statusCodes={
     *      200="Ok",
     *      404="Not found",
     *      500="Internal error"
     *  }
     * )
     */
    public function getAction($id)
    {
        $entity = $this->get('core.repository.test')->find($id);
        if(!is_object($entity)) {
            throw $this->createNotFoundException();
        }
        return View::create($entity, Codes::HTTP_OK);
    }
}