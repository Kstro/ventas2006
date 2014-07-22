<?php

namespace hnr\sircimBundle\Servicios;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use hnr\sircimBundle\Entity\Bitacora;
use hnr\sircimBundle\Entity\Usuario;
use hnr\sircimBundle\Form\BitacoraType;


/**
 * Bitacorasa controller.
 *
 * 
 */
class GuardarBitacora
// class Bitacora 
{

    protected $em;

    public function __construct(EntityManager $em){
        $this->em=$em;
        // $this->getDoctrine()->getManager();
    
    }
    public function escribirbitacora($mensaje, $login)
    {
        // $em Doctrine\ORM\EntityManager;
        // $em = EntityManager;
        $entity  = new Bitacora();
        $usuario = $this->em->getRepository('hnrsircimBundle:Usuario')->findOneByusLogin($login);
        $entity->setbiAccion($mensaje);
        $entity->setbiFecha(new \DateTime ('now'));
        $entity->setIdUsuario($usuario);
        // var_dump($entity);
        $this->em->persist($entity);
        $this->em->flush();
    }       
}
