<?php

namespace hnr\sircimBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CitaType extends AbstractType
{
    private $area;
    private $regiones; 
    private $posiciones;
    private $servicios;
    
    public function __construct($area, $regiones, $posiciones, $servicios) {
        $this->area   = $area;
        $this->regiones   = $regiones;
        $this->posiciones = $posiciones;
        $this->servicios  = $servicios;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ciEdadPaciente',null,
                  array('label'     => 'Edad:',
                        'read_only' => true,
                       ))
            /*->add('ciEstado',null,
                  array('label' => 'Estado',
                       ))*/
            ->add('idSolicitud', new SolicitudType($this->area, $this->regiones, $this->posiciones, $this->servicios),
                  array('label' => false,
                       ));        
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'hnr\sircimBundle\Entity\Cita'
        ));
    }

    public function getName()
    {
        return 'hnr_sircimbundle_citatype';
    }
}
