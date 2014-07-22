<?php

namespace hnr\sircimBundle\Servicios;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * Sesion usuario
 *
 * 
 */
class SesionUsuario
{

    protected $em;
    protected $route;
    protected $tmp;
    protected $area;
    protected $session_area;
    protected $url_archivo = "bundles/hnrsircim/Archivos/";

    public function __construct(EntityManager $em, Router $route,$templateEngine){
        $this->em=$em;
        $this->route=$route;
        $this->tmp=$templateEngine;
    }


    public function verificar_solo_sesion($login,$login_viejo)
    {
        $session = new Session();
        $session->start();
        $usuario = $session->get($login_viejo);
        var_dump($usuario);

        // $usuario->setUsLogin(''.$login);
        // var_dump($usuario);die;
        $opc="";
        if(!isset($usuario)){
            $opc=$this->route->generate('login');
            header("Location: $opc");die;
        }
        else{
            // $session->clear();
            $usuario = $session->set($login,$usuario);
            // $session->set($entities[0]->getUsLogin(), $entities[0]);
            return $login;
        }
        return $login;
        
    }


    public function verificar_sesion($login,$urlOS)
    {
        $session = new Session();
        $session->start();
        $usuario = $session->get($login);
        // var_dump($login);
        
        
        $opc="";
        if(isset($usuario)){
            
            
            // var_dump($opc);
            // $this->redirect($this->generate('login' ));
            
            //$em = $this->getDoctrine()->getManager();
            $entity = $this->em->getRepository('hnrsircimBundle:Usuario')->findByUsLogin($login);
            // var_dump($entity);
            // var_dump($entity);
            $query = $this->em->createQuery(
                'SELECT os
                FROM hnrsircimBundle:Usuario u, hnrsircimBundle:UsuarioRol ur,
                    hnrsircimBundle:Rol r, hnrsircimBundle:RolOpcionSistema ros,
                    hnrsircimBundle:OpcionSistema os
                WHERE u.id=ur.idUsuario
                    AND ur.idRol=r.id
                    AND r.id=ros.idRol
                    AND ros.idOpcionSistema = os.id
                    AND u.id=:id'
                )->setParameter('id',$entity[0]->getId());
            // var_dump($entity);
            $modulos = $query->getResult();
            $acceso = 0;
            $areausuario=$session->get('area');
            for ($i=0; $i < count($modulos); $i++) {
                # code...
                if($modulos[$i]->getOsUrl()==$urlOS){
                    $acceso = 1;
                }
                if (!isset($areausuario)) {
                    # code...
                    if($modulos[$i]->getOsUrl()=="cita"|| $modulos[$i]->getOsUrl()=="estudioradiologico"){
                        $session->set('area',0);
                    }
                }
                
            }
            if ($acceso==0) {
                $opc=$this->route->generate($modulos[0]->getOsUrl(),array('login'=>$login));
                // var_dump($opc);
                header("Location: $opc");die;    
            }
            //var_dump($modulos);
            $permisos = array('usuario'=>$usuario,'modulos'=>$modulos); 
            //var_dump($usuario);
            // var_dump($permisos);
            //return array('usuario'=>$usuario,'modulos'=>$modulos); 
            return $permisos; 
            //return $usuario;
        }
        
        else{
            $opc=$this->route->generate('login');
            header("Location: $opc");die;
            // return $this->redirect($this->generateUrl('login' ));
            
        }

        // $session = new Session();
        // $session->start();
        // $usuario = $session->get($login);
        // if(isset($usuario)){
        //     echo "llega";
        //     // $this->getContext()->getActionStack()->getLastEntry()->getActionInstance()->redirect($this->generateUrl('login' ));
        //     // return $this->redirect($this->generateUrl('login' ));
        //     // return $this->router->redirect('login');
        // }
        // return $usuario;
        // // $em Doctrine\ORM\EntityManager;
        // // $em = EntityManager;
        // $entity  = new Bitacora();
        // $usuario = $this->em->getRepository('hnrsircimBundle:Usuario')->findOneByusLogin($login);
        // $entity->setbiAccion($mensaje);
        // $entity->setbiFecha(new \DateTime ('now'));
        // $entity->setIdUsuario($usuario);
        // // var_dump($entity);
        // $this->em->persist($entity);
        // $this->em->flush();
    }       

    // Para los métodos que se llaman con ajax
    public function verificar_sesion_ajax($login,$urlOS,$ajax)
    {
        $session = new Session();
        $session->start();
        $usuario = $session->get($login);
        //var_dump($usuario);
        $opc="";
        $permisos = array();
        if(isset($usuario)){
            
            
            $entity = $this->em->getRepository('hnrsircimBundle:Usuario')->findByUsLogin($login);
            $query = $this->em->createQuery(
                'SELECT os
                FROM hnrsircimBundle:Usuario u, hnrsircimBundle:UsuarioRol ur,
                    hnrsircimBundle:Rol r, hnrsircimBundle:RolOpcionSistema ros,
                    hnrsircimBundle:OpcionSistema os
                WHERE u.id=ur.idUsuario
                    AND ur.idRol=r.id
                    AND r.id=ros.idRol
                    AND ros.idOpcionSistema = os.id
                    AND u.id=:id'
                )->setParameter('id',$entity[0]->getId());
            
            $modulos = $query->getResult();
            $acceso = 0;
            for ($i=0; $i < count($modulos); $i++) {
                # code...
                if($modulos[$i]->getOsUrl()==$urlOS){
                    $acceso = 1;
                }
            }
            if ($acceso==0) {
                $opc=$this->route->generate($modulos[0]->getOsUrl(),array('login'=>$login));
                header("Location: $opc");die;    
            }
            $permisos = array('usuario'=>$usuario,'modulos'=>$modulos); 
        }

        if (isset($permisos["usuario"]) && !$ajax) {
            return $this->tmp->render('hnrsircimBundle:Otras:otras.html.twig', array('ajax'=>$ajax));
        }
        if (!isset($permisos["usuario"]) && isset($ajax)) {
            return $this->tmp->render('hnrsircimBundle:Otras:otras.html.twig', array('ajax'=>$ajax));   
        }

        return 1; 
    }       


    public function verificar_sesion_ajax_cuenta($login,$ajax)
    {
        $session = new Session();
        $session->start();
        $usuario = $session->get($login);
        //var_dump($usuario);
        $opc="";
        $permisos = array();
        if(isset($usuario)){
            
            
            // $entity = $this->em->getRepository('hnrsircimBundle:Usuario')->findByUsLogin($login);
            // $query = $this->em->createQuery(
            //     'SELECT os
            //     FROM hnrsircimBundle:Usuario u, hnrsircimBundle:UsuarioRol ur,
            //         hnrsircimBundle:Rol r, hnrsircimBundle:RolOpcionSistema ros,
            //         hnrsircimBundle:OpcionSistema os
            //     WHERE u.id=ur.idUsuario
            //         AND ur.idRol=r.id
            //         AND r.id=ros.idRol
            //         AND ros.idOpcionSistema = os.id
            //         AND u.id=:id'
            //     )->setParameter('id',$entity[0]->getId());
            
            // $modulos = $query->getResult();
            // $acceso = 0;
            // for ($i=0; $i < count($modulos); $i++) {
            //     # code...
            //     if($modulos[$i]->getOsUrl()==$urlOS){
            //         $acceso = 1;
            //     }
            // }
            // if ($acceso==0) {
            //     $opc=$this->route->generate($modulos[0]->getOsUrl(),array('login'=>$login));
            //     header("Location: $opc");die;    
            // }
            $permisos = array('usuario'=>$usuario); 
        }

        if (isset($permisos["usuario"]) && !$ajax) {
            return $this->tmp->render('hnrsircimBundle:Otras:otras.html.twig', array('ajax'=>$ajax));
        }
        if (!isset($permisos["usuario"]) && isset($ajax)) {
            return $this->tmp->render('hnrsircimBundle:Otras:otras.html.twig', array('ajax'=>$ajax));   
        }

        return 1; 
    }       

    public function asignar_area_usuario($area,$login) {
        //$session = new Session();
        //$session->start();
        //$area = $session->set('area',$area);
        
        $file = fopen($this->url_archivo.$login.".txt", "w");
        fwrite($file, $area ."". PHP_EOL);
        fclose($file);
             
        //$_COOKIE['area']=$area;
        //$_COOKIE['area']=$area;
        //$this->area = $area;
        return 0;
    }    

    public function recuperar_area_usuario($login) {
        //$session = new Session();
        //$session->start();
        //$area = $session->get('area');
        //$area = $_COOKIE['area'];
        
        //return $this->area;
        $lineas = file($this->url_archivo.$login.".txt");
        $area=$lineas[0];
        //while(!feof($file)-1) {
          //  $area = fgets($file);
        //}
        

        return $area;

    }

    public function verificar_area_usuario($login) {
        //$session = new Session();
        //$session->start();
        $lineas = file($this->url_archivo.$login.".txt");
        $area=$lineas[0];
        if ($area==0 )
            return true;
        else
            return false;
        //$area = $session->get('area');
        # code...
        
        //$this->session_area->start();
        //$area = $session_area->get('area');
        
    }

}
