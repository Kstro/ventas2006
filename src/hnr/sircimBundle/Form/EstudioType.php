<?php

namespace hnr\sircimBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EstudioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('esNombre','text',array('label'=>'Nombre','attr'=>array('class'=>'textos')))
            ->add('esAbreviatura','text',array('label'=>'Abrevitura','attr'=>array('class'=>'textos')))
            ->add('esTipo','choice',
                array('empty_value' => 'Seleccionar tipo de estudio...',
                    'label'=>'Tipo',
                    'attr'=>array('class'=>'desplegable'),
                    'choices' => array('Convencional' => 'Convencional', 'Especial' => 'Especial'),

                ))
            ->add('esDescripcion','textarea',array('label'=>'Descripción','attr'=>array('class'=>'textos')))
            ->add('profile',new EstudioAreaType(),array('label'=>' '))
            
            // ->add('esUsuarioCreacion',)
            // ->add('esUsuarioModificacion')
            // ->add('esFechaCreacion')
            // ->add('esFechaModificacion')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'hnr\sircimBundle\Entity\Estudio'
        ));
    }

    public function getName()
    {
        return 'hnr_sircimbundle_estudiotype';
    }
}
