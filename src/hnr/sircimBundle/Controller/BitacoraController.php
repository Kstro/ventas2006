<?php

namespace hnr\sircimBundle\Controller;

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
 * Bitacora controller.
 *
 * @Route("/bitacora")
 */
class BitacoraController extends Controller
// class Bitacora 
{

    // protected $em;

    // public function __construct(EntityManager $em){
    //     $this->em=$em;
    //     // $this->getDoctrine()->getManager();
        
        
    // }


    /**
     * Lists all Bitacora entities.
     *
     * @Route("/{login}/bitacora", name="bitacora", options={"expose"=true})
     * @Method("GET")
     * @Template()
     */
    public function indexAction($login)
    {
        $em = $this->getDoctrine()->getManager();

        // $entities = $em->getRepository('hnrsircimBundle:Bitacora')->findAll();
        $query = $em->createQuery(
            'SELECT b
            FROM hnrsircimBundle:Bitacora b, hnrsircimBundle:Usuario u
            WHERE u.usLogin=:login AND u.id=b.idUsuario ORDER BY b.biFecha DESC'
            )->setParameter('login',$login);
        $entities = $query->getResult();
        // var_dump($entities);die;
        return array(
            'entities' => $entities,
        );
    }




    /**
     * Lists all Bitacora entities.
     *
     * @Route("/{login}/{accion}/{fecha}/bitacora_filtro", name="bitacora_filtro", options={"expose"=true})
     * @Method("GET")
     * @Template()
     */
    public function index_filtroAction($login,$accion,$fecha)
    {
        $em = $this->getDoctrine()->getManager();

        switch ($accion) {
            case 1:
                $accion="inicio sesión";
                break;
            case 2:
                $accion="Creo";
                break;

            case 3:
                $accion="Modifico";
                break;

            case 4:
                $accion="Elimino";
                break;

            case 5:
                $accion="estado";
                break;
            
            
        }

        // $fecha1 = (substr($fecha, 0,7))."-".(substr($fecha, 8)-1)." 00:00:00";
        // $fecha2 = (substr($fecha, 0,7))."-".(substr($fecha, 8)+1)." 23:59:59";
        $fecha1 = $fecha." 00:00:00";
        $fecha2 = $fecha." 23:59:59";
        // $fecha2 = $fecha." 23:59:59";
        // 2014-02-27 11:39:29

        if($accion!='0' && $fecha!=0){
            // echo "accion y fecha";
            // echo $fecha2;
            $query = $em->createQuery(
            'SELECT b
            FROM hnrsircimBundle:Bitacora b, hnrsircimBundle:Usuario u
            WHERE u.usLogin=:login AND u.id=b.idUsuario AND b.biAccion LIKE :accion AND b.biFecha > :fecha1 AND b.biFecha<:fecha2
            ORDER BY b.biFecha DESC'
            )->setParameters( array('login'=>$login, 'accion'=>'%'.$accion.'%', 'fecha1' => $fecha1,'fecha2'=>$fecha2));

        }
        else{
            if($accion!='0' && $fecha==0){
                // echo "solo accion";
                $query = $em->createQuery(
                'SELECT b
                FROM hnrsircimBundle:Bitacora b, hnrsircimBundle:Usuario u
                WHERE u.usLogin=:login AND u.id=b.idUsuario AND b.biAccion LIKE :accion
                ORDER BY b.biFecha DESC'
                )->setParameters( array('login'=>$login, 'accion'=>'%'.$accion.'%'));

            }
            else{
                if($accion==0 && $fecha!=0){
                    $query = $em->createQuery(
                    'SELECT b
                    FROM hnrsircimBundle:Bitacora b, hnrsircimBundle:Usuario u
                    WHERE u.usLogin=:login AND u.id=b.idUsuario AND b.biFecha > :fecha1 AND b.biFecha<:fecha2
                    ORDER BY b.biFecha DESC'
                    )->setParameters( array('login'=>$login,'fecha1' => $fecha1,'fecha2'=>$fecha2));
                }
                else{
                    // echo "todos";
                    $query = $em->createQuery(
                    'SELECT b
                    FROM hnrsircimBundle:Bitacora b, hnrsircimBundle:Usuario u
                    WHERE u.usLogin=:login AND u.id=b.idUsuario
                    ORDER BY b.biFecha DESC'
                    )->setParameter('login',$login);
                }
            }
        }

        // $entities = $em->getRepository('hnrsircimBundle:Bitacora')->findAll();
        
        $entities = $query->getResult();
        // var_dump($entities);die;
        return array(
            'entities' => $entities,
        );
    }







    // /**
    //  * Creates a new Bitacora entity.
    //  *
    //  * @Route("/", name="bitacora_create")
    //  * @Method("POST")
    //  * @Template("hnrsircimBundle:Bitacora:new.html.twig")
    //  */
    // public function createAction(Request $request)
    // {
    //     $entity  = new Bitacora();
    //     $form = $this->createForm(new BitacoraType(), $entity);
    //     $form->bind($request);

    //     if ($form->isValid()) {
    //         $em = $this->getDoctrine()->getManager();
    //         $em->persist($entity);
    //         $em->flush();

    //         return $this->redirect($this->generateUrl('bitacora_show', array('id' => $entity->getId())));
    //     }

    //     return array(
    //         'entity' => $entity,
    //         'form'   => $form->createView(),
    //     );
    // }

    // /**
    //  * Displays a form to create a new Bitacora entity.
    //  *
    //  * @Route("/new", name="bitacora_new")
    //  * @Method("GET")
    //  * @Template()
    //  */
    // public function newAction()
    // {
    //     $entity = new Bitacora();
    //     $form   = $this->createForm(new BitacoraType(), $entity);

    //     return array(
    //         'entity' => $entity,
    //         'form'   => $form->createView(),
    //     );
    // }

    // /**
    //  * Finds and displays a Bitacora entity.
    //  *
    //  * @Route("/{id}", name="bitacora_show")
    //  * @Method("GET")
    //  * @Template()
    //  */
    // public function showAction($id)
    // {
    //     $em = $this->getDoctrine()->getManager();

    //     $entity = $em->getRepository('hnrsircimBundle:Bitacora')->find($id);

    //     if (!$entity) {
    //         throw $this->createNotFoundException('Unable to find Bitacora entity.');
    //     }

    //     $deleteForm = $this->createDeleteForm($id);

    //     return array(
    //         'entity'      => $entity,
    //         'delete_form' => $deleteForm->createView(),
    //     );
    // }

    // /**
    //  * Displays a form to edit an existing Bitacora entity.
    //  *
    //  * @Route("/{id}/edit", name="bitacora_edit")
    //  * @Method("GET")
    //  * @Template()
    //  */
    // public function editAction($id)
    // {
    //     $em = $this->getDoctrine()->getManager();

    //     $entity = $em->getRepository('hnrsircimBundle:Bitacora')->find($id);

    //     if (!$entity) {
    //         throw $this->createNotFoundException('Unable to find Bitacora entity.');
    //     }

    //     $editForm = $this->createForm(new BitacoraType(), $entity);
    //     $deleteForm = $this->createDeleteForm($id);

    //     return array(
    //         'entity'      => $entity,
    //         'edit_form'   => $editForm->createView(),
    //         'delete_form' => $deleteForm->createView(),
    //     );
    // }

    // /**
    //  * Edits an existing Bitacora entity.
    //  *
    //  * @Route("/{id}", name="bitacora_update")
    //  * @Method("PUT")
    //  * @Template("hnrsircimBundle:Bitacora:edit.html.twig")
    //  */
    // public function updateAction(Request $request, $id)
    // {
    //     $em = $this->getDoctrine()->getManager();

    //     $entity = $em->getRepository('hnrsircimBundle:Bitacora')->find($id);

    //     if (!$entity) {
    //         throw $this->createNotFoundException('Unable to find Bitacora entity.');
    //     }

    //     $deleteForm = $this->createDeleteForm($id);
    //     $editForm = $this->createForm(new BitacoraType(), $entity);
    //     $editForm->bind($request);

    //     if ($editForm->isValid()) {
    //         $em->persist($entity);
    //         $em->flush();

    //         return $this->redirect($this->generateUrl('bitacora_edit', array('id' => $id)));
    //     }

    //     return array(
    //         'entity'      => $entity,
    //         'edit_form'   => $editForm->createView(),
    //         'delete_form' => $deleteForm->createView(),
    //     );
    // }

    // /**
    //  * Deletes a Bitacora entity.
    //  *
    //  * @Route("/{id}", name="bitacora_delete")
    //  * @Method("DELETE")
    //  */
    // public function deleteAction(Request $request, $id)
    // {
    //     $form = $this->createDeleteForm($id);
    //     $form->bind($request);

    //     if ($form->isValid()) {
    //         $em = $this->getDoctrine()->getManager();
    //         $entity = $em->getRepository('hnrsircimBundle:Bitacora')->find($id);

    //         if (!$entity) {
    //             throw $this->createNotFoundException('Unable to find Bitacora entity.');
    //         }

    //         $em->remove($entity);
    //         $em->flush();
    //     }

    //     return $this->redirect($this->generateUrl('bitacora'));
    // }

    // /**
    //  * Creates a form to delete a Bitacora entity by id.
    //  *
    //  * @param mixed $id The entity id
    //  *
    //  * @return Symfony\Component\Form\Form The form
    //  */
    // private function createDeleteForm($id)
    // {
    //     return $this->createFormBuilder(array('id' => $id))
    //         ->add('id', 'hidden')
    //         ->getForm()
    //     ;
    // }
    // public function escribirbitacora($mensaje, $login)
    // {
    //     // $em Doctrine\ORM\EntityManager;
    //     // $em = EntityManager;
    //     $entity  = new Bitacora();
    //     $usuario = $this->em->getRepository('hnrsircimBundle:Usuario')->findOneByusLogin($login);
    //     $entity->setbiAccion($mensaje);
    //     $entity->setbiFecha(new \DateTime ('now'));
    //     $entity->setIdUsuario($usuario);
    //     // var_dump($entity);
    //     $this->em->persist($entity);
    //     $this->em->flush();
    // }       
}
