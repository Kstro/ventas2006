<?php

namespace hnr\sircimBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RolOpcionSistemaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ->add('idRol')
            ->add('idOpcionSistema',null,array(
                'label'=>' ',
                'required'=>true,
                'empty_value'=>'Seleccione un módulo...',
                'attr'=>array('class'=>'desplegable') 
                ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'hnr\sircimBundle\Entity\RolOpcionSistema'
        ));
    }

    public function getName()
    {
        return 'hnr_sircimbundle_rolopcionsistematype';
    }
}
