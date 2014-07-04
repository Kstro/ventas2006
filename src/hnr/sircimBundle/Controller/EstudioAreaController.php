<?php

namespace hnr\sircimBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use hnr\sircimBundle\Entity\EstudioArea;
use hnr\sircimBundle\Form\EstudioAreaType;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * EstudioArea controller.
 *
 * @Route("/estudioarea")
 */
class EstudioAreaController extends Controller
{
    /**
     * Lists all EstudioArea entities.
     *
     * @Route("/{login}/est_ar", name="estudioarea")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($login)
    {
        // $em = $this->getDoctrine()->getManager();

        // $entities = $em->getRepository('hnrsircimBundle:EstudioArea')->findAll();

        // return array(
        //     'entities' => $entities,
        // );

        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('hnrsircimBundle:EstudioArea')->findAll();
        $session = new Session();
        $session->start();
        $usuario = $session->get($login);
        if(isset($usuario)){
            
            
            
            // var_dump($usuario);
            // var_dump($usuario);
            $query = $em->createQuery(
                'SELECT os
                FROM hnrsircimBundle:Usuario u, hnrsircimBundle:UsuarioRol ur,
                    hnrsircimBundle:Rol r, hnrsircimBundle:RolOpcionSistema ros,
                    hnrsircimBundle:OpcionSistema os
                WHERE u.id=ur.idUsuario
                    AND ur.idRol=r.id
                    AND r.id=ros.idRol
                    AND ros.idOpcionSistema = os.id
                    AND u.id=:id'
                )->setParameter('id',$usuario->getId());
            
            $modulos = $query->getResult();

            // $query = $em->createQuery(
            //     'SELECT u
            //     FROM hnrsircimBundle:Usuario u
            //     WHERE u.id=:id'
            //     )->setParameter('id',$id);
            
            // $usuario = $query->getResult();

            
            // var_dump($usuario);
            return array(
                'entities'  => $entities,
                'login'     =>  $login,
                'modulos'   => $modulos,
                'usuario'   => $usuario,
            );
        }
        else
        {
            return $this->redirect($this->generateUrl('login' ));
            // return array(

            //    'usuario' => $usuario,
            // );
        }

    }

    /**
     * Creates a new EstudioArea entity.
     *
     * @Route("/", name="estudioarea_create")
     * @Method("POST")
     * @Template("hnrsircimBundle:EstudioArea:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new EstudioArea();

        $entity->setProfile()->setEstadoEstudioArea(1);
        $form = $this->createForm(new EstudioAreaType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('mi_bitacora')->escribirbitacora("Asocio el estudio: '".$entity->getIdEstudio().getEsNombre()."' con el área '".
                $entity->getIdArea().getArNombre()."'",$login);
            // return $this->redirect($this->generateUrl('estudioarea_show', array('id' => $entity->getId())));
            return $this->redirect($this->generateUrl('estudio',array('login'=>'rorlando' )));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new EstudioArea entity.
     *
     * @Route("/new", name="estudioarea_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new EstudioArea();
        $form   = $this->createForm(new EstudioAreaType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a EstudioArea entity.
     *
     * @Route("/{id}", name="estudioarea_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('hnrsircimBundle:EstudioArea')->find($id);
        // $entity = $em->getRepository('hnrsircimBundle:EstudioArea')->findOneByIdEstudio($id);
        // $id=$entity->getId();
        // var_dump($id);die;

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EstudioArea entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing EstudioArea entity.
     *
     * @Route("/{id}/edit", name="estudioarea_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        // $entity = $em->getRepository('hnrsircimBundle:EstudioArea')->findOneByIdEstudio($id);
        $entity = $em->getRepository('hnrsircimBundle:EstudioArea')->find($id);
        // var_dump($entity);die;
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EstudioArea entity.');
        }

        $editForm = $this->createForm(new EstudioAreaType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing EstudioArea entity.
     *
     * @Route("/{id}", name="estudioarea_update")
     * @Method("PUT")
     * @Template("hnrsircimBundle:EstudioArea:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        // $em = $this->getDoctrine()->getManager();

        
        // $entity = $em->getRepository('hnrsircimBundle:EstudioArea')->findOneByIdEstudio($id);

        // if (!$entity) {
        //     throw $this->createNotFoundException('Unable to find EstudioArea entity.');
        // }

        // $deleteForm = $this->createDeleteForm($id);
        // $editForm = $this->createForm(new EstudioAreaType(), $entity);
        // $editForm->bind($request);

        // if ($editForm->isValid()) {
        //     $em->persist($entity);
        //     $em->flush();

        //     return $this->redirect($this->generateUrl('estudioarea_edit', array('id' => $id)));
        // }

        // return array(
        //     'entity'      => $entity,
        //     'edit_form'   => $editForm->createView(),
        //     'delete_form' => $deleteForm->createView(),
        // );


        $em = $this->getDoctrine()->getManager(); 
        $task = $em->getRepository('hnrsircimBundle:EstudioArea')->find($id);

        if (!$task) { 
            throw $this->createNotFoundException('No task found for is '.$id); 
        }

        $originalTags = array();

        // Create an array of the current Tag objects in the database 
        foreach ($task->getPlacas() as $tag) { 
            $originalTags[] = $tag; 
        }

        $editForm = $this->createForm(new EstudioAreaType(),$task);

        $editForm->bind($this->getRequest());
        sleep(1.5);
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

            $em->persist($task); 
            $em->flush();

// redirect back to some edit page 
            // return $this->redirect($this->generateUrl('rol')); 
            return $this->redirect($this->generateUrl('estudioarea',array('login'=>'rorlando' )));
            
        }








    }

    /**
     * Deletes a EstudioArea entity.
     *
     * @Route("/{id}", name="estudioarea_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('hnrsircimBundle:EstudioArea')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find EstudioArea entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('estudioarea'));
    }

    /**
     * Creates a form to delete a EstudioArea entity by id.
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
     * Edita estadio Estudio
     *
     * @Route("/editestadoestudioarea/{id}/{activo}/{login}", name="estudioarea_update_estados", options={"expose"=true})
     * @Method("GET")
     * 
     */
    public function updateestudioareaestadosAction(Request $request, $id, $activo,$login)
    {
        $em = $this->getDoctrine()->getManager();
        


        $entity = $em->getRepository('hnrsircimBundle:EstudioArea')->find($id);
        // $entities = $em->getRepository('hnrsircimBundle:Usuario')->findAll();
        $entity->setEstadoEstudioArea($activo);
        // $entity->setesUsuarioModificacion($this->username);    
        
        $em->persist($entity);
        $em->flush();
        
        // $this->escribirbitacora("'Modificó los estados del estudio '". $nom_estudio."'",$login);


            // return $this->redirect($this->generateUrl('estudio', array('id' => $entity->getId())));
        
        

        return new Response(json_encode($entity));
        
        
    }

}
