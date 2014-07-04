<?php

namespace hnr\sircimBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EstudioRadTamPlacaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idTamano', null,
                  array('label'       => false,
                        'empty_value' => 'Seleccione...',
                        'attr'        => array('class' => 'placas_tamaño')))                
            ->add('ertpAceptadas', null,
                  array('label' => false,                        
                        'attr'  => array('class' => 'placas_aceptadas', 'placeholder' => 'Ingrese Número')))
            ->add('ertpDescartadas', null,
                  array('label' => false,
                        'attr'  => array('class' => 'placas_descartadas', 'placeholder' => 'Ingrese Número')))
            /*->add('idEstudioRadiologico')*/
            ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'hnr\sircimBundle\Entity\EstudioRadTamPlaca'
        ));
    }

    public function getName()
    {
        return 'hnr_sircimbundle_estudioradtamplacatype';
    }
}
