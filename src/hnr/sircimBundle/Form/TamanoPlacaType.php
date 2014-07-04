<?php

namespace hnr\sircimBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TamanoPlacaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tpTamano',null,array('label' => 'Tamaño','attr'=>array('class'=>'textos')))
            // ->add('tpEstado','choice',
            //         array('label' => 'Estado','empty_value'=>'Seleccione...','choices' => array('1' => 'Activo', '0' => 'Inactivo'),
            //         'required'  => true))
            // ->add('tpUsuarioCreacion')
            // ->add('tpUsuarioModificacion')
            // ->add('tpFechaCreacion')
            // ->add('tpFechaModificacion')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'hnr\sircimBundle\Entity\TamanoPlaca'
        ));
    }

    public function getName()
    {
        return 'hnr_sircimbundle_tamanoplacatype';
    }
}
