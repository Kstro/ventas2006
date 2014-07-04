<?php

namespace hnr\sircimBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EstudioRegionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            
            
            ->add('idEstudio',
                  'entity',
                  array( 'class'=>'hnrsircimBundle:Estudio',
                         'property'=>'esNombre',
                         'label' => 'Estudio',
                         'empty_value' => 'Seleccionar estudio...',
                         'required' => true,

                       )
                 )
            ->add('idRegion',null,
                array('label' => 'Región del estudio',
                    'empty_value' => 'Seleccione una región...',
                    'required' => true
                ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'hnr\sircimBundle\Entity\EstudioRegion'
        ));
    }

    public function getName()
    {
        return 'hnr_sircimbundle_estudioregiontype';
    }
}
