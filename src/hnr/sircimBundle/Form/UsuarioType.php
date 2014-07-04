<?php

namespace hnr\sircimBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            
            ->add('usCorreo','email', array('label'=>'Email','attr'=>array('class'=>'textos')))
            ->add('usLogin',null, array('label'=>'Login',
                'max_length'=>15,
                'attr'=>array('class'=>'textos')))
            ->add('usContrasena', 'repeated', array(
                    'type' => 'password',
                    'first_name'=>'pass',
                    'second_name'=>'confirm',

                    'invalid_message' => 'Las contraseñas deben ser iguales.',

                    'options' => array('attr' => array('class' => 'textos')),
                    'required' => true,

                    'first_options'  => array('label' => ' '),
                    'second_options' => array('label' => ' ')))

            ->add('placas','collection',array(
                'type' => new UsuarioRolType(),
                'label'=>' ',
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                ))            
            // ->add('usActualizarContrasena')


            // ->add('usEstadoActivo')


            // ->add('usEstadoBloqueado')

            // ->add('idempleado')
            // ->add('usUsuarioCreacion')
            // ->add('usUsuarioModificacion')
            // ->add('usFechaCreacion')
            // ->add('usFechaModificacion')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'hnr\sircimBundle\Entity\Usuario'
        ));
    }

    public function getName()
    {
        return 'hnr_sircimbundle_usuariotype';
    }
}
