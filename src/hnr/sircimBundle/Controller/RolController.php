<?php

namespace hnr\sircimBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use hnr\sircimBundle\Entity\Rol;
use hnr\sircimBundle\Entity\Bitacora;
use hnr\sircimBundle\Form\RolType;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Rol controller.
 *
 * @Route("/rol")
 */
class RolController extends Controller
{
    
    // private $fechaCreacion = new \DateTime('now');


    /**
     * Lists all Rol entities.
     *
     * @Route("/{login}/rol", name="rol")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($login)
    {
        $permisos = $this->get('mi_sesion')->verificar_sesion($login,'administracion');
        $em = $this->getDoctrine()->getManager();

        // $entities = $em->getRepository('hnrsircimBundle:Rol')->findAll();
        // $entities = $em->getRepository('hnrsircimBundle:Usuario')->findAll();
        

        
        $query = $em->createQuery('SELECT r FROM hnrsircimBundle:Rol r ORDER BY r.roNombre ASC');
        $entities = $query->getResult();
        
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
                'login' => $login,
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
     * Creates a new Rol entity.
     *
     * @Route("/{login}/create", name="rol_create")
     * @Method("POST")
     * @Template("hnrsircimBundle:Rol:new.html.twig")
     */
    public function createAction(Request $request,$login)
    {

        $permisos = $this->get('mi_sesion')->verificar_sesion($login,'administracion');

        $entity  = new Rol();
        $entity->setRoUsuarioCreacion($login);
        $entity->setRoFechaCreacion(new \DateTime('now'));
        $entity->setRoModificable(1);
        $entity->setRoEstado(1);
        $form = $this->createForm(new RolType(), $entity);
        $form->bind($request);
        // $error['valor']=[0];
        if ($form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();

            $em->persist($entity);
            $em->flush();
            // $bitacora = new BitacoraController();
            // $bitacora->escribirbitacora("Creo el rol: '".$entity->getroNombre()."'",$login);;
            $this->get('mi_bitacora')->escribirbitacora("Creo el rol: '".$entity->getroNombre()."'",$login);
                // $error['valor']=[0];
                // return $this->redirect($this->generateUrl('rol'));
            return $this->redirect($this->generateUrl('rol',array('login'=>$login )));    
            

        }
        else{
            $error['valor']=[1];    
            return new Response(json_encode(array('regs' => $error)));
        }
        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
        
        // $ent['regs']=null;
        // return new Response(json_encode($ent));
        // return array(
        //     'entity' => $entity,
        //     'form'   => $form->createView(),
        // );
    }

    /**
     * Displays a form to create a new Rol entity.
     *
     * @Route("/new/{login}", name="rol_new")
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

        $entity = new Rol();
        $form   = $this->createForm(new RolType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'login' => $login,
        );
    }

    /**
     * Finds and displays a Rol entity.
     *
     * @Route("/{id}/{login}/mostrar", name="rol_show")
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

        $entity = $em->getRepository('hnrsircimBundle:RolOpcionSistema')->findByidRol($id);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Rol entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
// var_dump($entity);die;
        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'login' => $login,
        );
    }

    /**
     * Displays a form to edit an existing Rol entity.
     *
     * @Route("/{id}/edit/{login}", name="rol_edit")
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

        $entity = $em->getRepository('hnrsircimBundle:Rol')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Rol entity.');
        }

        $editForm = $this->createForm(new RolType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'login' => $login,
        );
    }

    /**
     * Edits an existing Rol entity.
     *
     * @Route("/{id}/update/{login}", name="rol_update", options={"expose"=true})
     * @Method("PUT")
     * @Template("hnrsircimBundle:Rol:edit.html.twig")
     */
    public function updateAction(Request $request, $id,$login)
    {

        $permisos = $this->get('mi_sesion')->verificar_sesion($login,'administracion');

        // $em = $this->getDoctrine()->getManager();

        // $entity = $em->getRepository('hnrsircimBundle:Rol')->find($id);

        // if (!$entity) {
        //     throw $this->createNotFoundException('Unable to find Rol entity.');
        // }
        // $entity->setRoUsuarioModificacion($this->usCreacion);
        // $entity->setRoFechaModificacion(new \DateTime('now'));
        // $deleteForm = $this->createDeleteForm($id);
        // $editForm = $this->createForm(new RolType(), $entity);
        // $editForm->bind($request);

        // if ($editForm->isValid()) {
        //     $em->persist($entity);
        //     $em->flush();

        //     // return $this->redirect($this->generateUrl('rol'));
        //     return $this->redirect($this->generateUrl('rol',array('login'=>'rorlando' )));
        // }

        // return array(
        //     'entity'      => $entity,
        //     'edit_form'   => $editForm->createView(),
        //     'delete_form' => $deleteForm->createView(),
        // );





        $em = $this->getDoctrine()->getManager(); 
        $task = $em->getRepository('hnrsircimBundle:Rol')->find($id);
        $task->setRoUsuarioModificacion($login);    
        $task->setRoFechaModificacion(new \DateTime ('now'));
        $ro_viejo = $task->getRoNombre();
        if (!$task) { 
            throw $this->createNotFoundException('No Rol found for is '.$id); 
        }

        $originalTags = array();

        // Create an array of the current Tag objects in the database 
        foreach ($task->getPlacas() as $tag) { 
            $originalTags[] = $tag; 
        }

        $editForm = $this->createForm(new RolType(),$task);

        $editForm->bind($this->getRequest());
        
        if ($editForm->isValid()) {

        // filter $originalTags to contain tags no longer present 
            foreach ($task->getPlacas() as $tag) { 
                foreach ($originalTags as $key => $toDel) { 
                    if ($toDel->getId() === $tag->getId()) { 
                        unset($originalTags[$key]); 
                        
                    } } }
        

        // remove the relationship between the tag and the Task 
            foreach ($originalTags as $tag) { 
            // remove the Task from the Tag // 
            //$tag->getPlacas()->removeElement($task);
        

// if it were a ManyToOne relationship, remove the relationship like this // 
            //$tag->setTask(null);
        

                $em->persist($tag);

// if you wanted to delete the Tag entirely, you can also do that 
                $em->remove($tag); 
            
            }

            if($ro_viejo==$task->getRoNombre()){
                $this->get('mi_bitacora')->escribirbitacora("Modifico información del rol: '".$task->getRoNombre()."'",$login);
            }
            else{
                $this->get('mi_bitacora')->escribirbitacora("Modifico nombre e información del rol: '".$ro_viejo."' a '".$task->getRoNombre()."'",$login);
            }

            $em->persist($task); 
            $em->flush();



// redirect back to some edit page 
            // return $this->redirect($this->generateUrl('rol')); 
            return $this->redirect($this->generateUrl('rol',array('login'=>$login )));
            
        } 
        $entity['regs']=3;
        // return new Response(json_encode($task));
        // return array( 'entity' => $entity, 'edit_form' => $editForm->createView(), 'delete_form' => $deleteForm->createView(), );











    }

    /**
     * Deletes a Rol entity.
     *
     * @Route("/{id}", name="rol_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('hnrsircimBundle:Rol')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Rol entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('rol'));
    }

    /**
     * Creates a form to delete a Rol entity by id.
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
     * Edits an existing Rol entity.
     *
     * @Route("/editestado/{id}/{activo}/{login}", name="rol_update_estados", options={"expose"=true})
     * @Method("GET")
     * 
     */
    public function updateestadosAction(Request $request, $id, $activo,$login)
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
        
        $entity = $em->getRepository('hnrsircimBundle:Rol')->find($id);
        // $entities = $em->getRepository('hnrsircimBundle:Usuario')->findAll();
        


        $estado_viejo = $entity->getRoEstado();
        
        if ($estado_viejo==1) {
            # code...
            $estado_viejo='Activo';
            $estado_nuevo='Inactivo';
        }
        else{
            $estado_viejo='Inactivo';
            $estado_nuevo='Activo';   
        }

        // $entities = $em->getRepository('hnrsircimBundle:Usuario')->findAll();
        $entity->setRoEstado($activo);
        $entity->setRoUsuarioModificacion($login);    
        $entity->setRoFechaModificacion(new \DateTime ('now'));
        $this->get('mi_bitacora')->escribirbitacora("Modifico el estado del rol: '".$entity->getRoNombre()."' de '".$estado_viejo."' a '".$estado_nuevo."'",$login);
        $em->persist($entity);
        $em->flush();
        
        return new Response(json_encode($regs));
        
        
    }


    /**
     * Consultar si el un rol ya existe
     *
     * @Route("/consultarrol/{rol}", name="rol_consultar", options={"expose"=true})
     * @Method("GET")
     * 
     */
    public function consultarrolAction(Request $request, $rol)
    {
        $rol = ucwords(strtolower($rol));
        $em = $this->getDoctrine()->getManager();
        // $entity = $em->getRepository('hnrsircimBundle:TamanoPlaca')->findOneBytpTamano($tam);
        $dql = "SELECT r FROM hnrsircimBundle:Rol r
                WHERE r.roNombre = :rol";
        
        $nombre['regs'] = $em->createQuery($dql)
                ->setParameter('rol', $rol)
                ->getArrayResult();
        // $tamanos['regs']=$entity;
        return new Response(json_encode($nombre));
        
        
    }

}
