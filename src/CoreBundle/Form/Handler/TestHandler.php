<?php
/**
 * Created by PhpStorm.
 * User: guillaume
 * Date: 25/02/16
 * Time: 14:08
 */

namespace CoreBundle\Form\Handler;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;

class TestHandler extends AbstractFormHandler
{

    /**
     * @param $entity
     * @param $actionUrl
     * @return Form
     */
    public function buildForm($entity, $actionUrl = '')
    {
        $this->form = $this->formFactory
            ->createBuilder($this->formType, $entity)
            ->setAction($actionUrl)
            ->add('nbQuestion', RangeType::class, array(
                'label' => 'Nombre de questions',
                'attr' => array(
                    'min' => 1,
                    'max' => $this->container->get('core.service.certificationy')->count(),
                    'value' => 20,
                ),
            ))
            ->add('categories', EntityType::class, array(
                'label'    => 'Catégories',
                'expanded' => true,
                'multiple' => true,
                'class'    => 'CoreBundle\Entity\Category',
            ))
            ->add('save', SubmitType::class, array('label_format' => 'Enregistrer', 'attr' => ['class' => "btn btn-success"]))
            ->getForm();

        return $this->form;
    }
}