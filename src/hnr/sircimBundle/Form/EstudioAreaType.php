<?php

namespace hnr\sircimBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use \hnr\sircimBundle\Entity\Estudio;
use \hnr\sircimBundle\Repositorio\AreaRepository;


class EstudioAreaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ->add('idEstudio',null,
            //     array('label'=>' ',
            //           'empty_value'=>'Seleccione un estudio...',
            //           'attr'=>array('class'=>'desplegable'),
            //           'required'=>true
            //         ))
            // ->add('estadoEstudioArea','text',array('label'=>'Estado área'))
            // ->add('idEstudio', new EstudioType(),
            //       array('label' => 'false',
            //             ))
            // ->add('idArea',null,
            //     array('label'=>' ',
            //           'empty_value'=>'Seleccione un área...',
            //           'attr'=>array('class'=>'desplegable'),
            //           'required'=>true
            //         ))

            ->add('idArea', 'entity', 
                  array( 'label'         => '',
                         'empty_value'   => 'Seleccione un área...',
                         'attr'          => array('class' => 'desplegable'),
                         'class'         => 'hnrsircimBundle:Area',
                         'property'      => 'arNombre',
                         'query_builder' => function(AreaRepository $ar) {
                                                return $ar->createQueryBuilder('a')
                                                  ->where('a.arEstado =1')
                                                  ->orderBy('a.id', 'ASC');
                                              }
                       ))
            
            ->add('placas','collection',array(
                
                'type' => new HorarioType(),
                'label' =>' ',
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                ))   

            
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'hnr\sircimBundle\Entity\EstudioArea'
        ));
    }

    public function getName()
    {
        return 'hnr_sircimbundle_estudioareatype';
    }
}
