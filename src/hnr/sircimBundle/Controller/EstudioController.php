<?php

namespace hnr\sircimBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use hnr\sircimBundle\Entity\Estudio;
use hnr\sircimBundle\Form\EstudioType;
use hnr\sircimBundle\Form\EstudioAreaType;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Estudio controller.
 *
 * @Route("/estudio")
 */
class EstudioController extends Controller
{
    /**
     * Lists all Estudio entities.
     *
     * @Route("/{login}/est", name="estudio")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($login)
    {

        $permisos = $this->get('mi_sesion')->verificar_sesion($login,'administracion');

        $em = $this->getDoctrine()->getManager();

        // $entities = $em->getRepository('hnrsircimBundle:Estudio')->findAll();
        $query = $em->createQuery('SELECT ea FROM hnrsircimBundle:Estudio e, hnrsircimBundle:EstudioArea ea, hnrsircimBundle:Horario h 

            WHERE e.id=ea.idEstudio AND ea.id=h.idEstudioArea ORDER BY e.esNombre ASC

            ');

        $entities = $query->getResult(); // array of CmsArticle objects
        // var_dump($entities);die;
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
                'entities'  => $entities,
                'login'     => $login,
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
     * Creates a new Estudio entity.
     *
     * @Route("/{login}/create", name="estudio_create", options={"expose"=true})
     * @Method("GET")
     * @Template("hnrsircimBundle:Estudio:new.html.twig")
     */
    public function createAction(Request $request,$login)
    {

        $permisos = $this->get('mi_sesion')->verificar_sesion($login,'administracion');

        $entity  = new Estudio();
        $entity->setesEstado(1);

        $entity->setesUsuarioCreacion($login);
        $entity->setesFechaCreacion(new \DateTime ('now'));
        $form = $this->createForm(new EstudioType(), $entity);
        $form->bind($request);
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('mi_bitacora')->escribirbitacora("Creo el estudio: '".$entity->getEsNombre()."'",$login);
            // return $this->redirect($this->generateUrl('estudio', array('id' => $entity->getId())));
            return $this->redirect($this->generateUrl('estudio',array('login'=>$login )));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'login'     => $login,
        );
    }

    /**
     * Displays a form to create a new Estudio entity.
     *
     * @Route("/new/{login}", name="estudio_new")
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

        $entity = new Estudio();
        $form   = $this->createForm(new EstudioType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'login'     => $login,
        );
    }

    /**
     * Finds and displays a Estudio entity.
     *
     * @Route("/{id}/{login}/mostrar", name="estudio_show")
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

        $entity = $em->getRepository('hnrsircimBundle:Estudio')->find($id);
        $estudioarea = $em->getRepository('hnrsircimBundle:EstudioArea')->findByidEstudio($id);
        $id = $estudioarea[0]->getId();
        $horario = $em->getRepository('hnrsircimBundle:Horario')->findByidEstudioArea($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Estudio entity.');
        }
        // var_dump($estudioarea);die;
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'login'     => $login,
            'horario' => $horario,
        );
    }

    /**
     * Displays a form to edit an existing Estudio entity.
     *
     * @Route("/{id}/{login}/edit", name="estudio_edit")
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

        $entity = $em->getRepository('hnrsircimBundle:Estudio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Estudio entity.');
        }

        $editForm = $this->createForm(new EstudioType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'login'         =>$login,
        );
    }

    /**
     * Edits an existing Estudio entity.
     *
     * @Route("/{id}/update/{login}", name="estudio_update", options={"expose"=true})
     * @Method("PUT")
     * @Template("hnrsircimBundle:Estudio:edit.html.twig")
     */
    public function updateAction(Request $request, $id, $login)
    {

        $permisos = $this->get('mi_sesion')->verificar_sesion($login,'administracion');

        // $em = $this->getDoctrine()->getManager();

        // $entity = $em->getRepository('hnrsircimBundle:Estudio')->find($id);
        // $es_viejo = $entity->getEsNombre();

        // $entity->setesUsuarioModificacion($login);
        // $entity->setesFechaModificacion(new \DateTime ('now'));
        // if (!$entity) {
        //     throw $this->createNotFoundException('Unable to find Estudio entity.');
        // }

        // $deleteForm = $this->createDeleteForm($id);
        // $editForm = $this->createForm(new EstudioType(), $entity);
        // $editForm->bind($request);
        // if ($editForm->isValid()) {
        //     $em->persist($entity);
        //     $em->flush();

        //     // $this->escribirbitacora("'Modificó el estudio '". $nom_estudio."'",$login);
        //     if($es_viejo==$entity->getEsNombre()){
        //         $this->escribirbitacora("Modifico información del estudio: '".$entity->getEsNombre()."'",$login);
        //     }
        //     else{
        //         $this->escribirbitacora("Modifico nombre e información del estudio: '".$es_viejo."' a '".$entity->getEsNombre()."'",$login);
        //     }
        //     // return $this->redirect($this->generateUrl('estudio', array('id' => $id)));
        //     return $this->redirect($this->generateUrl('estudio',array('login'=>'rorlando' )));
        // }

        // return array(
        //     'entity'      => $entity,
        //     'edit_form'   => $editForm->createView(),
        //     'delete_form' => $deleteForm->createView(),
        //     'login'       => $login,
        // );



        $em = $this->getDoctrine()->getManager(); 
        $task = $em->getRepository('hnrsircimBundle:Estudio')->find($id);

        if (!$task) { 
            throw $this->createNotFoundException('No task found for is '.$id); 
        }

        $originalTags = array();

        // Create an array of the current Tag objects in the database 
        foreach ($task->getProfile()->getPlacas() as $tag) { 
            $originalTags[] = $tag; 
        }

        $editForm = $this->createForm(new EstudioType(),$task);

        $editForm->bind($this->getRequest());
        
        if ($editForm->isValid()) {

        // filter $originalTags to contain tags no longer present 
            foreach ($task->getProfile()->getPlacas() as $tag) { 
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

            $em->persist($task); 
            $em->flush();

// redirect back to some edit page 
            // return $this->redirect($this->generateUrl('rol')); 
            // return $this->redirect($this->generateUrl('estudioarea',array('login'=>'rorlando' )));

            return $this->redirect($this->generateUrl('estudio',array('login'=>$login )));

        }


        else{
            var_dump($editForm->getErrors());
            die;

        }


    }

    /**
     * Deletes a Estudio entity.
     *
     * @Route("/{id}", name="estudio_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('hnrsircimBundle:Estudio')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Estudio entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('estudio'));
    }

    /**
     * Creates a form to delete a Estudio entity by id.
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
     * Edita estado Estudio
     *
     * @Route("/editestadoestudio/{id}/{activo}/{login}", name="estudio_update_estados", options={"expose"=true})
     * @Method("GET")
     * 
     */
    public function updateestudioestadosAction(Request $request, $id, $activo,$login)
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
        


        $entity = $em->getRepository('hnrsircimBundle:EstudioArea')->find($id);
        $nom_estudio= $entity->getIdEstudio()->getEsNombre();

        $estado_viejo = $entity->getEstadoEstudioArea();
        
        if ($estado_viejo==1) {
            # code...
            $estado_viejo='Activo';
            $estado_nuevo='Inactivo';
        }
        else{
            $estado_viejo='Inactivo';
            $estado_nuevo='Activo';   
        }
        $estudio=$entity->getIdEstudio();
        $entity->setEstadoEstudioArea($activo);
        $estudio->setEsUsuarioModificacion($login);
        $estudio->setEsFechaModificacion(new \DateTime ('now'));

        // $entities = $em->getRepository('hnrsircimBundle:Usuario')->findAll();
        
        // $entity->setesUsuarioModificacion($this->username);    
        
        $em->persist($entity);
        $em->flush();
        
        $this->get('mi_bitacora')->escribirbitacora("Modifico el estado del estudio: '".$entity->getIdEstudio()->getEsNombre()."' del área: '".$entity->getIdArea()->getArNombre()."' de '".$estado_viejo."' a '".$estado_nuevo."'",$login);

        


            // return $this->redirect($this->generateUrl('estudio', array('id' => $entity->getId())));
        
        
        return new Response(json_encode($regs));
        // return new Response(json_encode($entity));
        
        
    }


    /**
     * Consulta el nombre del estudio
     *
     * @Route("/consultarnomestudio/{nom}", name="consultar_nom_estudio", options={"expose"=true})
     * @Method("GET")
     * 
     */
    public function consultarnombreAction(Request $request, $nom)
    {
        
        $em = $this->getDoctrine()->getManager();
        // $entity = $em->getRepository('hnrsircimBundle:TamanoPlaca')->findOneBytpTamano($tam);
        $dql = "SELECT est FROM hnrsircimBundle:Estudio est
                WHERE est.esNombre = :nom";
        
        $nombre['regs'] = $em->createQuery($dql)
                ->setParameter('nom', $nom)
                ->getArrayResult();
        
        return new Response(json_encode($nombre));
        
    }

    /**
     * Consulta la abreviatura del estudio
     *
     * @Route("/consultarabreestudio/{abre}", name="consultar_abre_estudio", options={"expose"=true})
     * @Method("GET")
     * 
     */
    public function consultarnabreviaturaAction(Request $request, $abre)
    {
        
        $em = $this->getDoctrine()->getManager();
        // $entity = $em->getRepository('hnrsircimBundle:TamanoPlaca')->findOneBytpTamano($tam);
        $dql = "SELECT est FROM hnrsircimBundle:Estudio est
                WHERE est.esAbreviatura = :abre";
        
        $abreviatura['regs'] = $em->createQuery($dql)
                ->setParameter('abre', $abre)
                ->getArrayResult();
        
        return new Response(json_encode($abreviatura));
        
    }

    /**
     * Consulta si el estudio ya existe en dicha area
     *
     * @Route("/consultarestudio/{area}/{estudio}/{abreviatura}", name="estudio_consultar", options={"expose"=true})
     * @Method("GET")
     * 
     */
    public function consultarestudioAction(Request $request, $area,$estudio,$abreviatura)
    {
        
        $estudio = ucwords(strtolower($estudio));
        $abreviatura = strtoupper($abreviatura);
        $em = $this->getDoctrine()->getManager();
        // $entity = $em->getRepository('hnrsircimBundle:TamanoPlaca')->findOneBytpTamano($tam);
        
        $dql = "SELECT ea FROM hnrsircimBundle:EstudioArea ea, hnrsircimBundle:Estudio e
                WHERE e.id=ea.idEstudio
                AND ea.idArea = :area AND e.esNombre=:estudio AND e.esAbreviatura=:abreviatura
                ";

        // $dql = "SELECT ea FROM hnrsircimBundle:EstudioArea ea
        //         WHERE ea.idEstudio = :est
        //         AND ea.idArea = :area
        //         ";
        
        $estudioarea['regs'] = $em->createQuery($dql)
                // ->setParameter('est' => $est,'area'=>$area)
                ->setParameters(array('estudio'=>$estudio,'area'=>$area,'abreviatura'=>$abreviatura))
                ->getArrayResult();
        
        return new Response(json_encode($estudioarea));
        
        
    }

   
}
