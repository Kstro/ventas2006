<?php

namespace hnr\sircimBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use hnr\sircimBundle\Entity\Usuario;
use hnr\sircimBundle\Form\UsuarioType;
use hnr\sircimBundle\Controller\BitacoraController;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Bitacora
 *
 * @ORM\Table(name="bitacora")
 * @ORM\Entity
 */
class Bitacora extends Controller
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="bitacora_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="bi_accion", type="string", length=50, nullable=false)
     */
    private $biAccion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="bi_fecha", type="datetime", nullable=false)
     */
    private $biFecha;

    /**
     * @var \Usuario
     *
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_usuario", referencedColumnName="id")
     * })
     */
    private $idUsuario;



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set biAccion
     *
     * @param string $biAccion
     * @return Bitacora
     */
    public function setBiAccion($biAccion)
    {
        $this->biAccion = $biAccion;
    
        return $this;
    }

    /**
     * Get biAccion
     *
     * @return string 
     */
    public function getBiAccion()
    {
        return $this->biAccion;
    }

    /**
     * Set biFecha
     *
     * @param \DateTime $biFecha
     * @return Bitacora
     */
    public function setBiFecha($biFecha)
    {
        $this->biFecha = $biFecha;
    
        return $this;
    }

    /**
     * Get biFecha
     *
     * @return \DateTime 
     */
    public function getBiFecha()
    {
        return $this->biFecha;
    }

    /**
     * Set idUsuario
     *
     * @param \hnr\sircimBundle\Entity\Usuario $idUsuario
     * @return Bitacora
     */
    public function setIdUsuario(\hnr\sircimBundle\Entity\Usuario $idUsuario = null)
    {
        $this->idUsuario = $idUsuario;
    
        return $this;
    }

    /**
     * Get idUsuario
     *
     * @return \hnr\sircimBundle\Entity\Usuario 
     */
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }



    public function escribirbitacora($mensaje, $login)
    {
        //$entity  = new Bitacora();
        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository('hnrsircimBundle:Usuario')->findOneByusLogin($login);
        $this->setbiAccion($mensaje);
        $this->setbiFecha(new \DateTime ('now'));
        $this->setIdUsuario($usuario);
        $em->persist($this);
        $em->flush();
    } 


    

}