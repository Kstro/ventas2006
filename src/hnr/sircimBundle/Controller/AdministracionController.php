<?php

namespace hnr\sircimBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use hnr\sircimBundle\Entity\Usuario;
use hnr\sircimBundle\Controller\SecurityController;
use hnr\sircimBundle\Form\UsuarioType;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Administracion controller.
 *
 * @Route("/administracion")
 */
class AdministracionController extends Controller
{



    /**
     * Lists all Usuario entities.
     *
     * @Route("/{login}/adm", name="administracion")
     * @Method("GET")
     * @Template()
     */
    public function administracionAction($login)
    {
        $em = $this->getDoctrine()->getManager();
        
        $entities = $em->getRepository('hnrsircimBundle:Usuario')->findAll();
        // $sesion1 = new SecurityController();
        // $sesion1->verificarLogin($login);
        //$session = new Session();
        // $session->start();
        //$usuario = $session->get($login);
        $permisos = $this->get('mi_sesion')->verificar_sesion($login,'administracion');
        // $this->get('mi_sesion')->verificar_sesion($login,'administracion');
        //array_push($permisos, $this->get('mi_sesion')->verificar_sesion($login));
        //var_dump($usuario);
        // if(isset($permisos["usuario"])){
        //if(isset($permisos["usuario"])){
        //if(count($permisos)!=0){
        //if($permisos[0]['usuario']!=''){
        //if($this->get('mi_sesion')->verificar_sesion($login)){
        
        // $empleados=$this->extraerDatos("\"Idempleado\",\"Nombre\"");
        // $empleado=array();
        
        // // var_dump($entities);
        // for ($i = 0; $i < count($entities); $i++){
        //     $emp=$entities[$i]->getIdEmpleado();
        //     for ($j = 0; $j < count($empleados); $j++){
        //         $emp2=$empleados[$j]['Idempleado'];
        //         if($emp==$emp2){
        //             $empleado[$i]['Nombre']= $empleados[$j]['Nombre'];
        //             $empleado[$i]['Idempleado']= $empleados[$j]['Idempleado'];
        //         }
        //     }
        // }
        
        // // $modulos[0]=$permisos["modulos"];
        // //var_dump($modulos);
        // sort($empleado);
        // var_dump($usuario);
        return array(
           'entities' => $entities,
           'login'=> $login,    
           'modulos' => $permisos["modulos"],
           'usuario' => $permisos["usuario"],

        );
        // }
        // else
        // {
        //     return $this->redirect($this->generateUrl('login' ));
        //     // return array(

        //     //    'usuario' => $usuario,
        //     // );
        // }
    }


    /**
     * 
     *
     * @Route("/opciones", name="administracion_opciones")
     * @Method("GET")
     * @Template()
     */
    public function opcionesAction(){
        return array();
    }


    private function extraerDatos($campos){
        $campos=$campos;
        $conn = $this->get('doctrine.dbal.siap_connection'); 
        $empleados = $conn->fetchAll("select ".$campos." from vw_empleado_info order by \"Nombre\" ASC");
        return $empleados;
    }

    /**
     * 
     *
     * @Route("/info", name="info_sircim", options={"expose"=true})
     * @Method("GET")
     * @Template()
     */
    public function infoAction(){
        return array();
    }    


}
