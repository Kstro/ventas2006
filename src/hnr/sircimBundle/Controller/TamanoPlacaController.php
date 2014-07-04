<?php

namespace hnr\sircimBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use hnr\sircimBundle\Entity\TamanoPlaca;
use hnr\sircimBundle\Entity\Bitacora;
use hnr\sircimBundle\Form\TamanoPlacaType;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * TamanoPlaca controller.
 *
 * @Route("/tamanoplaca")
 */
class TamanoPlacaController extends Controller
{
    protected $username = "MCastro";
    /**
     * Lists all TamanoPlaca entities.
     *
     * @Route("/{login}/tmp", name="tamanoplaca")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($login)
    {
        $permisos = $this->get('mi_sesion')->verificar_sesion($login,'administracion');
        $em = $this->getDoctrine()->getManager();

        

        $query = $em->createQuery('SELECT tp FROM hnrsircimBundle:TamanoPlaca tp ORDER BY tp.tpTamano ASC');
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
                'login'=>$login,
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
     * Creates a new TamanoPlaca entity.
     *
     * @Route("/{login}", name="tamanoplaca_create", options={"expose"=true})
     * @Method("POST")
     * @Template("hnrsircimBundle:TamanoPlaca:new.html.twig")
     */
    public function createAction(Request $request,$login)
    {
        
        $permisos = $this->get('mi_sesion')->verificar_sesion($login,'administracion');

        $entity  = new TamanoPlaca();
        $entity->setTpUsuarioCreacion($login);    
        $entity->setTpFechaCreacion(new \DateTime ('now'));
        $entity->setTpEstado(1);
    
        $form = $this->createForm(new TamanoPlacaType(), $entity);
        $form->bind($request);
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            $em->persist($entity);
            $em->flush();
            $this->get('mi_bitacora')->escribirbitacora("Creo el tamaño de placa: '".$entity->getTpTamano()."'",$login);
            // return $this->redirect($this->generateUrl('tamanoplaca', array('id' => $entity->getId())));
            return $this->redirect($this->generateUrl('tamanoplaca',array('login'=>$login )));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'login'=>$login,
        );
    }

    /**
     * Displays a form to create a new TamanoPlaca entity.
     *
     * @Route("/new/{login}", name="tamanoplaca_new")
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

        $entity = new TamanoPlaca();
        $form   = $this->createForm(new TamanoPlacaType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'login'=>$login,
        );
    }

    /**
     * Finds and displays a TamanoPlaca entity.
     *
     * @Route("/{id}/{login}/mostrar", name="tamanoplaca_show")
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

        $entity = $em->getRepository('hnrsircimBundle:TamanoPlaca')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TamanoPlaca entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'login'=>$login,
        );
    }


    /**
     * Displays a form to edit an existing TamanoPlaca entity.
     *
     * @Route("/{id}/edit/{login}", name="tamanoplaca_edit")
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
        $entity = $em->getRepository('hnrsircimBundle:TamanoPlaca')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TamanoPlaca entity.');
        }
        $editForm = $this->createForm(new TamanoPlacaType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'login'=>$login,
        );
    }

    /**
     * Edits an existing TamanoPlaca entity.
     *
     * @Route("/{id}/update/{login}", name="tamanoplaca_update", options={"expose"=true})
     * @Method("PUT")
     * @Template("hnrsircimBundle:TamanoPlaca:edit.html.twig")
     */
    public function updateAction(Request $request, $id,$login)
    {

        $permisos = $this->get('mi_sesion')->verificar_sesion($login,'administracion');

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('hnrsircimBundle:TamanoPlaca')->find($id);
        $tp_viejo = $entity->getTpTamano();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TamanoPlaca entity.');
        }
        $entity->setTpUsuarioModificacion($login);    
        $entity->setTpFechaModificacion(new \DateTime ('now'));
        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new TamanoPlacaType(), $entity);
        $editForm->bind($request);
        
        if ($editForm->isValid()) {
            $this->get('mi_bitacora')->escribirbitacora("Modifico el tamaño de placa: '".$tp_viejo."' a '".$entity->getTpTamano()."'",$login);
            $em->persist($entity);
            $em->flush();

            // return $this->redirect($this->generateUrl('tamanoplaca', array('id' => $id)));
            return $this->redirect($this->generateUrl('tamanoplaca',array('login'=>$login )));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'login'=>$login,
        );
    }

    /**
     * Deletes a TamanoPlaca entity.
     *
     * @Route("/{id}", name="tamanoplaca_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('hnrsircimBundle:TamanoPlaca')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find TamanoPlaca entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('tamanoplaca'));
    }

    /**
     * Creates a form to delete a TamanoPlaca entity by id.
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
     * Edits an existing Tamano Placa entity.
     *
     * @Route("/editestado/{id}/{activo}/{login}", name="tamano_update_estados", options={"expose"=true})
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
            
            $entity = $em->getRepository('hnrsircimBundle:TamanoPlaca')->find($id);
            $estado_viejo = $entity->getTpEstado();
            
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
            $entity->setTpEstado($activo);
            $entity->setTpUsuarioModificacion($login);    
            $entity->setTpFechaModificacion(new \DateTime ('now'));
            $this->get('mi_bitacora')->escribirbitacora("Modifico el estado de la placa: '".$entity->getTpTamano()."' de '".$estado_viejo."' a '".$estado_nuevo."'",$login);
            $em->persist($entity);
            $em->flush();
        
        return new Response(json_encode($regs));
        
        
    }

    /**
     * Edits an existing Tamano Placa entity.
     *
     * @Route("/consultartamano/{tam}", name="tamano_consultar", options={"expose"=true})
     * @Method("GET")
     * 
     */
    public function consultartamanoAction(Request $request, $tam)
    {
        $tam = strtolower($tam);
        $em = $this->getDoctrine()->getManager();
        // $entity = $em->getRepository('hnrsircimBundle:TamanoPlaca')->findOneBytpTamano($tam);
        $dql = "SELECT tp FROM hnrsircimBundle:TamanoPlaca tp
                WHERE tp.tpTamano = :tam";
        
        $tamanos['regs'] = $em->createQuery($dql)
                ->setParameter('tam', $tam)
                ->getArrayResult();
        // $tamanos['regs']=$entity;
        return new Response(json_encode($tamanos));
        
        
    }
}
