<?php

namespace hnr\sircimBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use hnr\sircimBundle\Entity\Usuario;
use hnr\sircimBundle\Entity\Bitacora;
use hnr\sircimBundle\Form\UsuarioType;
use hnr\sircimBundle\Form\UsuarioEditType;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Usuario controller.
 *
 * @Route("/usuario")
 */
class UsuarioController extends Controller
{



    /**
     * Lists all Usuario entities.
     *
     * @Route("/{login}/usr", name="usuario")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($login)
    {

        $permisos = $this->get('mi_sesion')->verificar_sesion($login,'administracion');
        
        $em = $this->getDoctrine()->getManager();

        // $entities = $em->getRepository('hnrsircimBundle:Usuario')->findAll();
        $query = $em->createQuery('SELECT u FROM hnrsircimBundle:Usuario u ORDER BY u.idempleado ASC');

        $entities = $query->getResult(); // array of CmsArticle objects
        // var_dump($entities);
        // var_dump($entities[0]->getPlacas());
        // die;

        // $session = new Session();

        // $session->start();
        // $usuario = $session->get($login);
        // if(isset($usuario)){
            
        $empleados=$this->extraerDatos("\"Idempleado\",\"Nombre\"");
        $empleado=array();
        $entity=array();
        // var_dump($entities);
        for ($i = 0; $i < count($entities); $i++){
            $emp=$entities[$i]->getIdEmpleado();
            for ($j = 0; $j < count($empleados); $j++){
                $emp2=$empleados[$j]['Idempleado'];
                if($emp==$emp2){
                    $empleado[$i]['Nombre']= $empleados[$j]['Nombre'];
                    $empleado[$i]['Idempleado']= $empleados[$j]['Idempleado'];
                    $empleado[$i]['objeto'] = $entities[$i];
                    // $entity[$i] = $entities[$i];
                }
            }
        }

            // usort($entities, cmp_function)
            
            // var_dump($empleado);
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

            sort($empleado);
            

            // var_dump($empleado);

            return array(
                
                'empleados'=> $empleado,    
                'modulos' => $permisos["modulos"],
                'usuario' => $permisos["usuario"],
                // 'usuario' => $usuario,
                'login'  =>$login,
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
     * Creates a new Usuario entity.
     *
     * @Route("/{login}/create", name="usuario_create"), options={"expose"=true})
     * @Method("POST")
     * @Template("hnrsircimBundle:Usuario:new.html.twig")
     */
    public function createAction(Request $request,$login)
    {
        
        $permisos = $this->get('mi_sesion')->verificar_sesion($login,'administracion');

        $em = $this->getDoctrine()->getManager();
        $entity  = new Usuario();
        $entity->setUsUsuarioCreacion($login);
        $entity->setUsFechaCreacion(new \DateTime('now'));
        $entity->setUsActualizarContrasena(1);
        $l=$_POST["listaempleado"];        
        $entity->setIdempleado($l);
        $entity->setUsEstadoActivo(1);
        $entity->setUsEstadoBloqueado(1);
        $empleados=$this->extraerDatos("*");
        $form = $this->createForm(new UsuarioType(), $entity);
    
        $form->bind($request);
        
            
        if ($form->isValid()) {


            $hashpass = hash( 'whirlpool', $entity->getUsContrasena());
            $entity->setUsContrasena($hashpass);
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('mi_bitacora')->escribirbitacora("Creo el usuario: '".$entity->getUsLogin()."'",$login);
            // return $this->redirect($this->generateUrl('usuario'));
            return $this->redirect($this->generateUrl('usuario',array('login'=>$login )));
        }


        
        return array(
            'entity'   => $entity,
            'form'     => $form->createView(),
            'empleados'=> $empleados,   
            'login'    => $login,
        );
    }

    /**
     * Displays a form to create a new Usuario entity.
     *
     * @Route("/new/{login}", name="usuario_new")
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

        $entity = new Usuario();
        $em = $this->getDoctrine()->getManager();
        $usuarios = $em->getRepository('hnrsircimBundle:Usuario')->findAll();
        
        $form   = $this->createForm(new UsuarioType(), $entity);
        $empleados=$this->extraerDatos("*");
        // var_dump($empleados);
        // die;

        //Ocultar los nombres de empleados que ya tienen usuario
        $keys_borrar=array("");
        for ($i=0; $i < count($empleados); $i++) { 
            
            for ($j=0; $j < count($usuarios) ; $j++) { 
                if(($usuarios[$j]->getIdEmpleado())==($empleados[$i]['Idempleado'])) {
                    // unset($empleados[$i]);
                    // $i++;
                    // unset($empleados[$i]);
                    // $key[$i]=$i;
                    array_push($keys_borrar, $i);
                    
                }    
            }
            
        }
        // var_dump($keys_borrar);
        // die;
        unset($keys_borrar[0]);
        for ($i=1; $i < count($keys_borrar)+1; $i++) { 
            unset($empleados[$keys_borrar[$i]]);
        }

        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'empleados' => $empleados,
            'login'  =>$login,
        );
    }

    /**
     * Finds and displays a Usuario entity.
     *
     * @Route("/{id}/{login}/mostrar", name="usuario_show")
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

        $entity = $em->getRepository('hnrsircimBundle:UsuarioRol')->findByidUsuario($id);
        $empleados=$this->extraerDatos("*");
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Usuario entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'empleados'   => $empleados,
            'login'  =>$login,
        );
    }

    /**
     * Displays a form to edit an existing Usuario entity.
     *
     * @Route("/{id}/edit/{login}/{opc}", name="usuario_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id,$login,$opc)
    {

        $ajax = $this->getRequest()->isXmlHttpRequest();
        $permisos = $this->get('mi_sesion')->verificar_sesion_ajax($login,'administracion',$ajax);
        if($permisos!=1){
            // echo $permisos;
            die($permisos);
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('hnrsircimBundle:Usuario')->find($id);
        // var_dump($entity);
        $empleados=$this->extraerDatos("*");
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Usuario entity.');
        }
        
        $originalPlacas = array();

        // Create an array of the current Tag objects in the database 
        foreach ($entity->getPlacas() as $placa) { $originalPlacas[] = $placa; }

        $editForm = $this->createForm(new UsuarioEditType(), $entity); 
        $deleteForm = $this->createDeleteForm($id);

        // filter $originalTags to contain tags no longer present 
        foreach ($entity->getPlacas() as $placa) { 
            foreach ($originalPlacas as $key => $toDel) { 
                if ($toDel->getId() === $placa->getId()) {
                    unset($originalPlacas[$key]); 
                    
                } } }




        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'empleados' => $empleados,
            'login'  =>$login,
            'opc' =>$opc,
        );
    }

    /**
     * Displays a form to edit an existing Usuario entity.
     *
     * @Route("/edit_cuenta/{login}/{opc}", name="usuario_cuenta")
     * @Method("GET")
     * @Template()
     */
    public function editcuentaAction($login,$opc)
    {

        $ajax = $this->getRequest()->isXmlHttpRequest();
        $permisos = $this->get('mi_sesion')->verificar_sesion_ajax_cuenta($login,$ajax);
        if($permisos!=1){
        //     // echo $permisos;
            die($permisos);
        }

        $em = $this->getDoctrine()->getManager();

        // $id=33;

        $entity = $em->getRepository('hnrsircimBundle:Usuario')->findByUsLogin($login);
        // $entity = $em->getRepository('hnrsircimBundle:Usuario')->find($id);
        $entity = $entity[0];
        // var_dump($entity);
        $empleados=$this->extraerDatos("*");
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Usuario entity.');
        }
        
        $originalPlacas = array();

        // Create an array of the current Tag objects in the database 
        foreach ($entity->getPlacas() as $placa) { $originalPlacas[] = $placa; }

        $editForm = $this->createForm(new UsuarioEditType(), $entity); 
        // $deleteForm = $this->createDeleteForm($id);

        // filter $originalTags to contain tags no longer present 
        foreach ($entity->getPlacas() as $placa) { 
            foreach ($originalPlacas as $key => $toDel) { 
                if ($toDel->getId() === $placa->getId()) {
                    unset($originalPlacas[$key]); 
                    
                } } }




        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            // 'delete_form' => $deleteForm->createView(),
            'empleados' => $empleados,
            'login'  =>$login,
            'opc' =>$opc,
        );
    }

    /**
     * Edits an existing Usuario entity.
     *
     * @Route("/{id}/update/{login}/{opc}/{rolpredet}/{login_viejo}", name="usuario_update", options={"expose"=true})
     * @Method("PUT")
     * @Template("hnrsircimBundle:Usuario:edit.html.twig")
     */
    public function updateAction(Request $request, $id,$login,$opc,$rolpredet,$login_viejo)
    {
        if ($opc==1) {
            $permisos = $this->get('mi_sesion')->verificar_sesion($login,'administracion');
        }
        else{
            $this->get('mi_sesion')->verificar_solo_sesion($login,$login_viejo);
        }
           
                $em = $this->getDoctrine()->getManager(); 
                $task = $em->getRepository('hnrsircimBundle:Usuario')->find($id);
                $contrasena = $task->getUsContrasena();
                $hashpass ="";
                $task->setUsUsuarioModificacion($login);    
                $task->setUsFechaModificacion(new \DateTime ('now'));
                $us_viejo = $task->getUsLogin();

                if (!$task) { 
                    throw $this->createNotFoundException('No usuario found for is '.$id); 
                }

                $originalTags = array();

                // Create an array of the current Tag objects in the database 
                foreach ($task->getPlacas() as $tag) { 
                    $originalTags[] = $tag; 
                }

                $editForm = $this->createForm(new UsuarioEditType(),$task);
                // var_dump($task->getUsContrasena());die;
                
                // var_dump($contrasena." ".$hashpass);die;
                $editForm->bind($this->getRequest());
                

                // var_dump($task->getUsContrasena());
                // die;
                if(count($task->getUsContrasena() )==0){
                    $task->setUsContrasena($contrasena);
                    // echo "Sin hash ".$task->getUsContrasena();
                    // die;
                }
                else{
                    $hashpass = hash( 'whirlpool', $task->getUsContrasena());
                    $task->setUsContrasena($hashpass);
                    if ($opc==1) {
                        $task->setUsActualizarContrasena(1);    
                    }
                    else{
                        $task->setUsActualizarContrasena(0);
                    }
                    
                    
                }

                

                if ($editForm->isValid()) {
                    
                // filter $originalTags to contain tags no longer present 
                    foreach ($task->getPlacas() as $tag) { 
                        foreach ($originalTags as $key => $toDel) { 
                            if ($toDel->getId() === $tag->getId()) { 
                                unset($originalTags[$key]); 
                                
                            } 
                        } 
                    }
                

                // remove the relationship between the tag and the Task 
                    foreach ($originalTags as $tag) { 
                    // remove the Task from the Tag // 
                    //$tag->getPlacas()->removeElement($task);
                

        // if it were a ManyToOne relationship, remove the relationship like this // 
                    //$tag->setTask(null);
                
                        
                        
                         // if you wanted to delete the Tag entirely, you can also do that 
                        
                        $em->persist($tag);
                        $em->remove($tag);

                        
                    }

                    $em->persist($task); 
                    $em->flush();

                    // Actualizar el rol predeterminado
                    // Debido a un problema en el formulario cuando se edita un usuario y se borra el rol predeterminado que tenia y se le asigna otro, no se actualizaba,
                    // por lo que se optó actualizar "manualmente" el rol predeterminado del usuario.
                    if($rolpredet!=0){
                        $query = $em->createQuery(
                            'SELECT ur
                            FROM hnrsircimBundle:Usuario u, hnrsircimBundle:UsuarioRol ur
                            WHERE u.id=ur.idUsuario 
                                AND ur.idRol=:rolpredet AND u.id=:id'
                            )->setParameters(array('rolpredet'=>$rolpredet, 'id'=>$id));
                        $entity = $query->getResult();
                        // var_dump($entity);
                        $entity[0]->setUsroPredeterminado(true);
                        $em->persist($entity[0]);
                        $em->flush();
                    }
                    
                    // Fin actualización "manual" de rol predeterminado

                    if($us_viejo==$task->getUsLogin()){
                        if($opc==0){
                            $this->get('mi_bitacora')->escribirbitacora("Modifico información de cuenta",$login);
                        }
                        else{
                            $this->get('mi_bitacora')->escribirbitacora("Modifico información del usuario: '".$task->getUsLogin()."'",$login);    
                        }
                        
                    }
                    else{
                        $this->get('mi_bitacora')->escribirbitacora("Modifico nombre e información del usuario: '".$us_viejo."' a '".$task->getUsLogin()."'",$login);
                    }
                    return $this->redirect($this->generateUrl('usuario',array('login'=>$login )));    
            }

    // redirect back to some edit page 
                // return $this->redirect($this->generateUrl('rol')); 
                    sleep(3);
                
            
        

        // var_dump($editForm->getErrors());die;
        // $entity['regs']=3;
        // return new Response(json_encode($task));
        // return array( 'entity' => $entity, 'edit_form' => $editForm->createView(), 'delete_form' => $deleteForm->createView(), );

        
    }


    /**
     * Edita los estados de un usuario.
     *
     * @Route("/editestado/{id}/{activo}/{bloqueado}/{login}", name="usuario_update_estados", options={"expose"=true})
     * @Method("GET")
     * 
     */
    public function updateestadosAction(Request $request, $id, $activo, $bloqueado,$login)
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
            
            $entity = $em->getRepository('hnrsircimBundle:Usuario')->find($id);
            // $entities = $em->getRepository('hnrsircimBundle:Usuario')->findAll();
            $entity->setUsEstadoActivo($activo);

            $entity->setUsEstadoBloqueado($bloqueado);

            $em->persist($entity);
            $this->get('mi_bitacora')->escribirbitacora("Modifico estado del usuario: '".$entity->getUsLogin()."'",$login);
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
        // return new Response(json_encode($entity));
        
        
    }









    /**
     * Deletes a Usuario entity.
     *
     * @Route("/{id}", name="usuario_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('hnrsircimBundle:Usuario')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Usuario entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('usuario'));
    }

    /**
     * Creates a form to delete a Usuario entity by id.
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

    private function extraerDatos($campos){
        $campos=$campos;
        $conn = $this->get('doctrine.dbal.siap_connection'); 
        $empleados = $conn->fetchAll("select ".$campos." from vw_empleado_info order by \"Nombre\" ASC");
        return $empleados;
    }


    /**
     * Consultar si el un correo ya existe
     *
     * @Route("/consultarcorreo/{email}", name="correo_consultar", options={"expose"=true})
     * @Method("GET")
     * 
     */
    public function consultarcorreoAction(Request $request, $email)
    {
        $email = strtolower($email);
        $em = $this->getDoctrine()->getManager();
        // $entity = $em->getRepository('hnrsircimBundle:TamanoPlaca')->findOneBytpTamano($tam);
        $dql = "SELECT u FROM hnrsircimBundle:Usuario u
                WHERE u.usCorreo = :email";
        
        $correo['regs'] = $em->createQuery($dql)
                ->setParameter('email', $email)
                ->getArrayResult();
        // $tamanos['regs']=$entity;
        return new Response(json_encode($correo));
        
        
    }


    /**
     * Consultar si un login ya existe
     *
     * @Route("/consultarlogin/{login}", name="login_consultar", options={"expose"=true})
     * @Method("GET")
     * 
     */
    public function consultarloginAction(Request $request, $login)
    {
        $login = strtolower($login);
        $em = $this->getDoctrine()->getManager();
        // $entity = $em->getRepository('hnrsircimBundle:TamanoPlaca')->findOneBytpTamano($tam);
        $dql = "SELECT u FROM hnrsircimBundle:Usuario u
                WHERE u.usLogin = :login";
        
        $nombreusuario['regs'] = $em->createQuery($dql)
                ->setParameter('login', $login)
                ->getArrayResult();
        // $tamanos['regs']=$entity;
        return new Response(json_encode($nombreusuario));
        
        
    }
}
