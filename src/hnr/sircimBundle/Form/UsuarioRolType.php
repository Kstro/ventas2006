<?php

namespace hnr\sircimBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use hnr\sircimBundle\Repositorio\RolRepository;

class UsuarioRolType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('usroPredeterminado','radio',array(
                'label'=>' ',
                'required' => false,
                'attr'=>array('class'=>'btn_radio')
                                ))
            // ->add('idUsuario')
            // ->add('idRol',null,array(
            //     'label'=>' ',
            //     'required'=>true,
            //     'empty_value'=>'Seleccione un rol...',
            //     'attr'=>array('class'=>'desplegable')
            //     ))

            ->add('idRol', 'entity', 
                  array( 'label'         => '',
                         'empty_value'   => 'Seleccione un rol...',
                         'attr'          => array('class' => 'desplegable'),
                         'class'         => 'hnrsircimBundle:Rol',
                         'property'      => 'roNombre',
                         'query_builder' => function(RolRepository $ro) {
                                                return $ro->createQueryBuilder('r')
                                                  ->where('r.roEstado =1')
                                                  ->orderBy('r.id', 'ASC');
                                              }
                       ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'hnr\sircimBundle\Entity\UsuarioRol'
        ));
    }

    public function getName()
    {
        return 'hnr_sircimbundle_usuarioroltype';
    }
}
