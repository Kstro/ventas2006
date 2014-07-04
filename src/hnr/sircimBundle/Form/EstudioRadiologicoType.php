<?php

namespace hnr\sircimBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EstudioRadiologicoType extends AbstractType
{
    private $area;
    private $regiones; 
    private $posiciones;
    private $servicios;
    private $empleados;
    
    public function __construct($area, $regiones, $posiciones, $servicios, $empleados) {
        $this->area       = $area;
        $this->regiones   = $regiones;
        $this->posiciones = $posiciones;
        $this->servicios  = $servicios;
        $this->empleados  = $empleados;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('erEdadPaciente', null, 
                  array('label'     => 'Edad:',
                        'read_only' => true,
                        'attr'      => array('class' => 'regular'),
                       ))
            ->add('idempleado','choice',
                  array('label'       => 'Técnico:',
                        'empty_value' => 'Seleccione...',
                        'attr'        => array('class' => 'regular'),
                        'choices'     => $this->empleados,
                       ))
            ->add('idSolicitud', new SolicitudType($this->area, $this->regiones, $this->posiciones, $this->servicios),
                  array('label' => false,
                        ))
            ->add('placas', 'collection', array(
                  'type'        => new EstudioRadTamPlacaType(),
                  'allow_add'   => true,
                  'allow_delete'=> true,
                  'by_reference'=> false
                  ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'hnr\sircimBundle\Entity\EstudioRadiologico'
        ));
    }

    public function getName()
    {
        return 'hnr_sircimbundle_estudioradiologicotype';
    }
}
