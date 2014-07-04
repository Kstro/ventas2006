<?php

namespace hnr\sircimBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use hnr\sircimBundle\Entity\Area;
use hnr\sircimBundle\Form\AreaType;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Area controller.
 *
 * @Route("/area")
 */
class AreaController extends Controller
{

    protected $username = "";

    /**
     * Lists all Area entities.
     *
     * @Route("/{login}/ar", name="area")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($login)
    {

        $permisos = $this->get('mi_sesion')->verificar_sesion($login,'administracion');

        $em = $this->getDoctrine()->getManager();
        // $this->username=$login;

        // $entities = $em->getRepository('hnrsircimBundle:Area')->findAll();
        $query = $em->createQuery('SELECT a FROM hnrsircimBundle:Area a ORDER BY a.arNombre ASC');
        $entities = $query->getResult(); // array of CmsArticle objects
        // $session = new Session();
        // $session->start();
        // $usuario = $session->get($login);
        // if(isset($usuario)){
            
            
            
            // var_dump($usuario);
            // var_dump($usuario);
            // $query = $em->createQuery(
            //     'SELECT os
            //     FROM hnrsircimBundle:Usuario u, hnrsircimBundle:UsuarioRol ur,
            //         hnrsircimBundle:Rol r, hnrsircimBundle:RolOpcionSistema ros,
            //         hnrsircimBundle:OpcionSistema os
            //     WHERE u.id=ur.idUsuario
            //         AND ur.idRol=r.id
            //         AND r.id=ros.idRol
            //         AND ros.idOpcionSistema = os.id
            //         AND u.id=:id'
            //     )->setParameter('id',$usuario->getId());
            
            // $modulos = $query->getResult();

            // $query = $em->createQuery(
            //     'SELECT u
            //     FROM hnrsircimBundle:Usuario u
            //     WHERE u.id=:id'
            //     )->setParameter('id',$id);
            
            // $usuario = $query->getResult();

            
            // var_dump($usuario);
            return array(
                'entities' => $entities,
                'modulos' => $permisos["modulos"],
                'usuario' => $permisos["usuario"],
                'login'=>$login,
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
     * Creates a new Area entity.
     * @Route("/{login}/create", name="area_create", options={"expose"=true})
     * @Method("POST")
     * @Template("hnrsircimBundle:Area:new.html.twig")
     */
    public function createAction(Request $request,$login)
    {
        $permisos = $this->get('mi_sesion')->verificar_sesion($login,'administracion');
        $entity  = new Area();
        //Valores por defecto para las áreas
        $entity->setArEstadoCupo(0);
        $entity->setArEstado(1);
        $entity->setArUsuarioCreacion($login);
        $entity->setArFechaCreacion(new \DateTime ('now'));

        $form = $this->createForm(new AreaType(), $entity);
        $form->bind($request);
        $cupo = $entity->getArCupo();
        if(count($cupo)==0 || $cupo==0){
            $entity->setArCupo(0);
        }
        else{
            $entity->setArEstadoCupo(1);
        }
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            // $entities = $em->getRepository('hnrsircimBundle:Usuario')->findAll();
            
            $this->get('mi_bitacora')->escribirbitacora("Creo el área: '".$entity->getarNombre()."'",$login);

            $em->persist($entity);
            $em->flush();

            // return $this->redirect($this->generateUrl('area_show', array('id' => $entity->getId())));
            // return $this->redirect($this->generateUrl('area',array('login'=>'rorlando' )));
            return $this->redirect($this->generateUrl('area',array('login'=>$login )));
            // return new Response(json_encode($entity));
        }

        return array(   
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Area entity.
     *
     * @Route("/new/{login}", name="area_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction($login)
    {

        $ajax = $this->getRequest()->isXmlHttpRequest();
        $permisos = $this->get('mi_sesion')->verificar_sesion_ajax($login,'administracion',$ajax);
        if($permisos!=1){
            // echo $permisos;
            die($permisos);
        } 

        $entity = new Area();
        $form   = $this->createForm(new AreaType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'login' =>$login,
        );
    }

    /**
     * Finds and displays a Area entity.
     *
     * @Route("/{id}/{login}/mostrar", name="area_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id,$login)
    {

        $ajax = $this->getRequest()->isXmlHttpRequest();
        $permisos = $this->get('mi_sesion')->verificar_sesion_ajax($login,'administracion',$ajax);
        if($permisos!=1){
            // echo $permisos;
            die($permisos);
        } 

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('hnrsircimBundle:Area')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Area entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Area entity.
     *
     * @Route("/{id}/edit/{login}", name="area_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id,$login)
    {

        $ajax = $this->getRequest()->isXmlHttpRequest();
        $permisos = $this->get('mi_sesion')->verificar_sesion_ajax($login,'administracion',$ajax);
        if($permisos!=1){
            // echo $permisos;
            die($permisos);
        } 

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('hnrsircimBundle:Area')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Area entity.');
        }

        $editForm = $this->createForm(new AreaType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'login'       => $login,
        );
    }

    /**
     * Edits an existing Area entity.
     *
     * @Route("/{id}/update/{login}", name="area_update", options={"expose"=true})
     * @Method("PUT")
     * @Template("hnrsircimBundle:Area:edit.html.twig")
     */
    public function updateAction(Request $request, $id, $login)
    {

        $permisos = $this->get('mi_sesion')->verificar_sesion($login,'administracion');

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('hnrsircimBundle:Area')->find($id);
        $ar_viejo = $entity->getArNombre();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Area entity.');
        }
        $entity->setArUsuarioModificacion($login);
        $entity->setArFechaModificacion(new \DateTime ('now'));
        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new AreaType(), $entity);
        $editForm->bind($request);
        $cupo = $entity->getArCupo();

        if(count($cupo)==0 || $cupo==0){
            $entity->setArCupo(0);
            $entity->setArEstadoCupo(0);
        }
        else{
            $entity->setArEstadoCupo(1);
        }
        // var_dump($login);die;
        if ($editForm->isValid()) {

            $em->persist($entity);
            $em->flush();
            if($ar_viejo==$entity->getArNombre()){
                $this->get('mi_bitacora')->escribirbitacora("Modifico información del área: '".$entity->getArNombre()."'",$login);
            }
            else{
                $this->get('mi_bitacora')->escribirbitacora("Modifico nombre e información del área: '".$ar_viejo."' a '".$entity->getArNombre()."'",$login);
            }
            
            // return $this->redirect($this->generateUrl('area', array('id' => $id)));
            return $this->redirect($this->generateUrl('area',array('login'=>$login )));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }


    /**
     * Edits an existing Area entity.
     *
     * @Route("/editareaestado/{id}/{cupo}/{activo}/{login}", name="area_update_estados", options={"expose"=true})
     * @Method("GET")
     * 
     */
    public function updateestadosAction(Request $request, $id, $cupo, $activo,$login)
    {

        
        
        $ajax = $this->getRequest()->isXmlHttpRequest();
        $permisos = $this->get('mi_sesion')->verificar_sesion_ajax($login,'administracion',$ajax);
        $regs["regs"]=0;
        if($permisos!=1){
            // echo $permisos;
            $regs["regs"]=1;
            return new Response(json_encode($regs));
        }
            $em = $this->getDoctrine()->getManager();
            
            $entity = $em->getRepository('hnrsircimBundle:Area')->find($id);
            // $entities = $em->getRepository('hnrsircimBundle:Usuario')->findAll();
            $estado_ar_viejo = $entity->getArEstado();
            $estado_arcp_viejo = $entity->getArEstadoCupo();

            if($estado_ar_viejo==$activo){
                if ($estado_arcp_viejo==1) {
                    # code...
                    $estado_ar_viejo='Activo';
                    $estado_ar_nuevo='Inactivo';
                }
                else{
                    $estado_ar_viejo='Inactivo';
                    $estado_ar_nuevo='Activo';   
                }
                $this->get('mi_bitacora')->escribirbitacora("Modifico el estado del cupo del área: '".$entity->getArNombre()."' de ".$estado_ar_viejo."' a '".$estado_ar_nuevo."'",$login);
            }
            else{
                if ($estado_ar_viejo==1) {
                    # code...
                    $estado_ar_viejo='Activo';
                    $estado_ar_nuevo='Inactivo';
                }
                else{
                    $estado_ar_viejo='Inactivo';
                    $estado_ar_nuevo='Activo';   
                }
                $this->get('mi_bitacora')->escribirbitacora("Modifico el estado del área: '".$entity->getArNombre()."' de '".$estado_ar_viejo."' a '".$estado_ar_nuevo."'",$login);
            }

            

            $entity->setArEstadoCupo($cupo);
            $entity->setArEstado($activo);
            $entity->setArUsuarioModificacion($login);    
            $entity->setArFechaModificacion(new \DateTime ('now'));
            

            $em->persist($entity);
            $em->flush();
        
        // $empleados=$this->extraerDatos("\"Idempleado\",\"Nombre\"");
        // $empleado=array();
        
        // for ($i = 0; $i < count($entities); $i++){
        //     $emp=$entities[$i]->getIdEmpleado();
        //     for ($j = 0; $j < count($empleados); $j++){
                
        //         $emp2=$empleados[$j]['Idempleado'];
                
                
        //         if($emp==$emp2){
        //             $empleado[$i]= $empleados[$j]['Nombre'];
        //         }
        //     }
        
        // }
        return new Response(json_encode($regs));
        
        
        
    }


    /**
     * Deletes a Area entity.
     *
     * @Route("/{id}", name="area_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('hnrsircimBundle:Area')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Area entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('area'));
    }

    /**
     * Creates a form to delete a Area entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }



    /**
     * Consulta el nombre del area
     *
     * @Route("/consultarnomarea/{nom}", name="consultar_nom_area", options={"expose"=true})
     * @Method("GET")
     * 
     */
    public function consultarnombreareaAction(Request $request, $nom)
    {
        $nom = ucwords(strtolower($nom));
        $em = $this->getDoctrine()->getManager();
        // $entity = $em->getRepository('hnrsircimBundle:TamanoPlaca')->findOneBytpTamano($tam);
        $dql = "SELECT ar FROM hnrsircimBundle:Area ar
                WHERE ar.arNombre = :nom";
        
        $nombre['regs'] = $em->createQuery($dql)
                ->setParameter('nom', $nom)
                ->getArrayResult();
        
        return new Response(json_encode($nombre));
        
    }


   
    
    
}
