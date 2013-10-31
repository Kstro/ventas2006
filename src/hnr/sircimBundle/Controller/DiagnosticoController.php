<?php

namespace hnr\sircimBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use hnr\sircimBundle\Entity\Diagnostico;
use hnr\sircimBundle\Form\DiagnosticoType;

/**
 * Diagnostico controller.
 *
 * @Route("/diagnostico")
 */
class DiagnosticoController extends Controller
{
    /**
     * Lists all Diagnostico entities.
     *
     * @Route("/", name="diagnostico")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('hnrsircimBundle:Diagnostico')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Diagnostico entity.
     *
     * @Route("/", name="diagnostico_create")
     * @Method("POST")
     * @Template("hnrsircimBundle:Diagnostico:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Diagnostico();
        $form = $this->createForm(new DiagnosticoType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('diagnostico_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Diagnostico entity.
     *
     * @Route("/new", name="diagnostico_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Diagnostico();
        $form   = $this->createForm(new DiagnosticoType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Diagnostico entity.
     *
     * @Route("/{id}", name="diagnostico_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('hnrsircimBundle:Diagnostico')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Diagnostico entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Diagnostico entity.
     *
     * @Route("/{id}/edit", name="diagnostico_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('hnrsircimBundle:Diagnostico')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Diagnostico entity.');
        }

        $editForm = $this->createForm(new DiagnosticoType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Diagnostico entity.
     *
     * @Route("/{id}", name="diagnostico_update")
     * @Method("PUT")
     * @Template("hnrsircimBundle:Diagnostico:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('hnrsircimBundle:Diagnostico')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Diagnostico entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new DiagnosticoType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('diagnostico_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Diagnostico entity.
     *
     * @Route("/{id}", name="diagnostico_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('hnrsircimBundle:Diagnostico')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Diagnostico entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('diagnostico'));
    }

    /**
     * Creates a form to delete a Diagnostico entity by id.
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
}
