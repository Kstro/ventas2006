<?php

namespace hnr\sircimBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use hnr\sircimBundle\Entity\Diagnostico;
use hnr\sircimBundle\Controller\BitacoraController;
use hnr\sircimBundle\Entity\EstudioRadiologico;
use hnr\sircimBundle\Form\DiagnosticoType;
use Symfony\Component\HttpFoundation\Session\Session;

use hnr\sircimBundle\dcm\dicom_convert as dicom_convert;


 define('TOOLKIT_DIR', '/home/mario/NetbeansProjects/dcmtk/bin');

/**
 * Diagnostico controller.
 *
 * @Route("/diagnostico")
 */
class DiagnosticoController extends Controller
{

    var $file = '';  
    var $jpg_file = '';
    var $tn_file = '';
    var $jpg_quality = 100;
    var $tn_size = 125;

    /**
     * Lists all Diagnostico entities.
     *
     * @Route("/{login}/dig", name="diagnostico")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($login)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('hnrsircimBundle:Rol')->findAll();
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
            $query = $em->createQuery(
                    'SELECT DISTINCT est
                    FROM hnrsircimBundle:Estudio est'
                    );
                
                $estudios = $query->getResult();
            // $query = $em->createQuery(
            //     'SELECT u
            //     FROM hnrsircimBundle:Usuario u
            //     WHERE u.id=:id'
            //     )->setParameter('id',$id);
            
            // $usuario = $query->getResult();
            
            // var_dump($usuario);
            return array(
                'entities' => $entities,    
                'modulos' => $modulos,
                'usuario' => $usuario,
                'login' => $login,
                'estudios' => $estudios,
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
     * Creates a new Diagnostico entity.
     *
     * @Route("/{id}", name="diagnostico_create")
     * @Method("POST")
     * @Template("hnrsircimBundle:Diagnostico:new.html.twig")
     */
    public function createAction(Request $request,$id)
    {
        $entity  = new Diagnostico();
        
        $idempleado= $this->MostrarEmp();
        $e= $_POST["empleados"];
        $entity -> setIdempleado($e);
        $em = $this->getDoctrine()->getManager();
        $entity_er = $em->getRepository('hnrsircimBundle:EstudioRadiologico')->find($id);
        
        
        $entity->setIdEstudioRadiologico($entity_er);
     
        $form = $this->createForm(new DiagnosticoType(), $entity);
        $form->bind($request);
        
        if ($form->isValid()) {
          
                $em = $this->getDoctrine()->getManager();      
          
                $em->persist($entity);
                #throw $this->createNotFoundException('La transcripcion ya existe, seleccione otro estudio');
            
                $em->flush();
                // return $this->redirect($this->generateUrl('diagnostico',array('login'));
                return $this->redirect($this->generateUrl('diagnostico',array('login'=>'aelena')));
           
              #$this->get('session')->setFlash('error',$e->getMessage()); 
             #echo $e;
             #return $this ->redirect($this->generateUrl('diagnostico_new'));
             #return $this->render('hnrsircimBundle:Diagnostico:new.html.twig',array('form'   => $form->createView(),'idempleado' => $idempleado, 'message'=>$e));      
             #return new Response('<html><body><table style="border-spacing: 5px; margin-right: 5px; border-collapse: separate"> 
              #                     <tr style="font-weight: bold">
               #                     '.$exc.'<br><button type="submit" onclick="history.back();">
                #                    volver
                 #                  </button></tr></table></body></html>');        
            
        }

        return array(
            'entity' => $entity, 
            'form'   => $form->createView(),
            'idempleado' => $idempleado,
            'entity_er'=> $entity_er,
                );
    }

    /**
     * Displays a form to create a new Diagnostico entity.
     *
     * @Route("/new/{id}", name="diagnostico_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction($id)
    {
        $entity = new Diagnostico();
       $em = $this->getDoctrine()->getManager();
       
       
      $entity_er = $em->getRepository('hnrsircimBundle:EstudioRadiologico')->find($id);
        
    
       $form = $this->createForm(new DiagnosticoType(), $entity);
        
       
       $idempleado = $this->MostrarEmp();
   
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'idempleado' =>$idempleado,
            'entity_er' => $entity_er,          
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
        // $em = $this->getDoctrine()->getManager();
      
        // $entity = $em->getRepository('hnrsircimBundle:Diagnostico')->find($id);
        // $idemp = $entity->getIdempleado();
        // $conn = $this -> get('doctrine.dbal.siap_connection');
        // $idempleado = $conn -> fetchAll("SELECT \"Nombre\" 
        //                                 from vw_empleado_info where \"Cargo\" = 'Tecnico' and \"Idempleado\" = '$idemp'");
        
       

        // if (!$entity) {
        //     throw $this->createNotFoundException('Unable to find Diagnostico entity');
        // }

        // $deleteForm = $this->createDeleteForm($id);
       
        

        // return array(
        //     'entity'      => $entity,
        //     'empl' => $idempleado,
        //     'delete_form' => $deleteForm->createView(),
        // );

        $em = $this->getDoctrine()->getManager();
      
        $entity = $em->getRepository('hnrsircimBundle:Diagnostico')->find($id);
        
       
        $num_registro = $em 
           ->createQuery("SELECT s
                         FROM hnrsircimBundle:Solicitud s, hnrsircimBundle:EstudioRadiologico er, 
                              hnrsircimBundle:Diagnostico d
                         WHERE d.idEstudioRadiologico=er.id and er.idSolicitud=s.id and d.id=$id")
        ->getResult(); 
        


        $idemp = $entity->getIdempleado();
        $conn = $this -> get('doctrine.dbal.siap_connection');
        $idempleado = $conn -> fetchAll("SELECT \"Nombre\" 
                                        from vw_empleado_info where \"Cargo\" = 'Tecnico' and \"Idempleado\" = '$idemp'");
        
       

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Diagnostico entity');
        }

        $deleteForm = $this->createDeleteForm($id);
       
        

        return array(
            'entity'      => $entity,
            'empl' => $idempleado,
            'delete_form' => $deleteForm->createView(),
            'num_registro' => $num_registro,
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
            throw $this->createNotFoundException('no existe diagnostico');
        }
   
       $idempleado = $this->MostrarEmp();
        
       $idemp = $entity->getIdempleado();
     
       $conn = $this -> get('doctrine.dbal.siap_connection');  
       $idempleado2 = $conn -> fetchAll("SELECT \"Nombre\" 
                                        from vw_empleado_info where \"Cargo\" = 'Tecnico' and \"Idempleado\" = '$idemp'");
       $editForm = $this->createForm(new DiagnosticoType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'empl_actual' => $idempleado2,
            'empl' => $idempleado,
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
        // $em = $this->getDoctrine()->getManager();

        // $entity = $em->getRepository('hnrsircimBundle:Diagnostico')->find($id);

        // if (!$entity) {
        //     throw $this->createNotFoundException('Unable to find Diagnostico entity.');
        // }

        // $deleteForm = $this->createDeleteForm($id);
        // $editForm = $this->createForm(new DiagnosticoType(), $entity);
        // $editForm->bind($request);

        // if ($editForm->isValid()) {
        //     $em->persist($entity);
        //     $em->flush();

        //     // return $this->redirect($this->generateUrl('diagnostico_edit', array('id' => $id)));
        //     return $this->redirect($this->generateUrl('diagnostico',array('login'=>'rorlando')));
        // }

        // return array(
        //     'entity'      => $entity,
        //     'edit_form'   => $editForm->createView(),
        //     'delete_form' => $deleteForm->createView(),
        // );



      $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('hnrsircimBundle:Diagnostico')->find($id);
       
    
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Diagnostico entity');
        }

        $deleteForm = $this->createDeleteForm($id);
 
        
        $editForm = $this->createForm(new DiagnosticoType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
        $em->persist($entity);
        
       
        $idempleado= $this->MostrarEmp();
        $e= $_POST["empleados"];
        
        $conn2 = $this -> get('doctrine.dbal.default_connection'); 
        $resu=$conn2->  
        
                fetchAll("UPDATE Diagnostico SET idempleado='$e' WHERE id=$id");
      
        
            $em->flush();
    $this->get('session')->getFlashBag()->set(
        'success',
        array(
      'title' => 'Editado!',
      'message' => 'Transcripcion modificada satisfactoriamente.'
        )
    );
            // return $this->redirect($this->generateUrl('diagnostico'));
              // return $this->redirect($this->generateUrl('diagnostico',array('login'=>'rorlando')));
              return $this->redirect($this->generateUrl('diagnostico',array('login'=>'aelena')));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'empl' => $idempleado,
            
            
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

        // return $this->redirect($this->generateUrl('diagnostico'));
        return $this->redirect($this->generateUrl('diagnostico',array('login'=>'aelena')));
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

    // public function expAction()
    // {
    //     $entity = new Diagnostico();
        
    //     $em = $this->getDoctrine()->getManager();   
    //     return array(
    //         'entity' => $entity,
    //         'citas' => $citas,
    //         'exp'=>$exp,
    //         'estudios' => $estudios,
        
    //     ); 
        
    // }

    /**
     * Muestra la pantalla para ingresar un expediente de paciente
     *
     * @Route("/expediente_historial/{login}", name="diagnostico_historial", options={"expose"=true})
     * @Method("GET")
     * @Template("")
     */
    public function expAction($login){
        // $session = new Session();
        // $session->start();
        // $usuario = $session->get($login);
        // if(isset($usuario)){

        $permisos = $this->get('mi_sesion')->verificar_sesion($login,'diagnostico_historial');

            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                    'SELECT os
                    FROM hnrsircimBundle:Usuario u, hnrsircimBundle:UsuarioRol ur,
                        hnrsircimBundle:Rol r, hnrsircimBundle:RolOpcionSistema ros,
                        hnrsircimBundle:OpcionSistema os
                    WHERE u.id=ur.idUsuario
                        AND ur.idRol=r.id
                        AND r.id=ros.idRol
                        AND ros.idOpcionSistema = os.id
                        AND u.usLogin=:id'
                    )->setParameter('id',$login);
                
                $modulos = $query->getResult();
            
            $query = $em->createQuery(
                    'SELECT DISTINCT est
                    FROM hnrsircimBundle:Estudio est'
                    );
                
                $estudios = $query->getResult();

            return array(
                'usuario'=>$login,
                'login'=>$login,
                'modulos'=>$modulos,
                'estudios'=>$estudios,
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
     * Displays a form to create a new Diagnostico entity.
     *
     * @Route("/expediente_historial/{exp}/{estudio}/{fecha}", name="diagnostico_exp", options={"expose"=true})
     * @Method("GET")
     * @Template("")
     */
    public function historialAction($exp,$estudio,$fecha)
    {
        $exp=$exp;
        $estudio=$estudio."";
        $fecha=$fecha;
        //var_dump(filter_var($exp,FILTER_SANITIZE_SPECIAL_CHARS));
        // $exp="34090-12";
        $entity = new Diagnostico();
        $em = $this->getDoctrine()->getManager();
        // $entities = $em->getRepository('hnrsircimBundle:Diagnostico')->findAll();
        // echo $estudio;
        // echo $fecha;
        // $citas = $em->getRepository('hnrsircimBundle:Solicitud')->findBy(array('soTipo'=>'1', 'idMntExpediente'=>$exp));
        // $this->forward('hnrsircim.bitacora.controller:indexAction');
        // $this->get('hnrsircim.bitacora.controller:indexAction');
        
        //Devuelve todos los estudios del paciente



        // $query = $em->createQuery(
        //             'SELECT c
        //             FROM    hnrsircimBundle:Estudio e, hnrsircimBundle:EstudioArea ea,
        //                     hnrsircimBundle:Solicitud s, hnrsircimBundle:Cita c
        //             WHERE   e.id=ea.idEstudio AND ea.id=s.idEstudioArea OR e.esNombre=:nomestudio AND
        //                     s.id= c.idSolicitud AND s.soTipo = 1 AND s.idMntExpediente= :exp OR
        //                     s.soFecha=:fecha
        //             ORDER BY s.soFecha DESC, s.soHora DESC'
        //         )->setParameters(array('exp'=>$exp,'fecha'=>$fecha,'nomestudio'=>$estudio));
        //         $citas = $query->getResult();
        //             // var_dump($citas);



        //         $query = $em->createQuery(
        //             'SELECT est
        //             FROM    hnrsircimBundle:Estudio e, hnrsircimBundle:EstudioArea ea,
        //                     hnrsircimBundle:Solicitud s, hnrsircimBundle:EstudioRadiologico est
        //             WHERE   e.id=ea.idEstudio AND ea.id=s.idEstudioArea OR e.esNombre=:nomestudio AND
        //                     s.id= est.idSolicitud AND s.soTipo = 2 AND s.idMntExpediente= :exp OR s.soFecha=:fecha
        //             ORDER BY s.soFecha DESC, s.soHora DESC'
        //         )->setParameters(array('exp'=>$exp,'fecha'=>$fecha,'nomestudio'=>$estudio));
        //         $estudios = $query->getResult();


        if($estudio=="0" && $fecha==0){
            
            $query = $em->createQuery(
            'SELECT c
            FROM hnrsircimBundle:Solicitud s, hnrsircimBundle:Cita c
            WHERE s.id= c.idSolicitud AND s.soTipo = 1 AND s.idMntExpediente= :exp
            ORDER BY s.soFecha DESC, s.soHora DESC'
            )->setParameter('exp',$exp);
            $citas = $query->getResult();
            // var_dump($citas);



            $query = $em->createQuery(
                'SELECT est
                FROM hnrsircimBundle:Solicitud s, hnrsircimBundle:EstudioRadiologico est
                WHERE s.id= est.idSolicitud AND s.soTipo = 2 AND s.idMntExpediente= :exp
                ORDER BY s.soFecha DESC, s.soHora DESC'
            )->setParameter('exp',$exp);
            $estudios = $query->getResult();

        }
        // Devuelve todos los estudios del paciente de la fecha seleccionada
        if($estudio=="0" && $fecha!=0){
          $query = $em->createQuery(
          'SELECT c
          FROM hnrsircimBundle:Solicitud s, hnrsircimBundle:Cita c
          WHERE s.id= c.idSolicitud AND s.soTipo = 1 AND s.idMntExpediente= :exp AND s.soFecha=:fecha
          ORDER BY s.soFecha DESC, s.soHora DESC'
          )->setParameters(array('exp'=>$exp,'fecha'=>$fecha));
          $citas = $query->getResult();
          // var_dump($cit
          $query = $em->createQuery(
              'SELECT est
              FROM hnrsircimBundle:Solicitud s, hnrsircimBundle:EstudioRadiologico est
              WHERE s.id= est.idSolicitud AND s.soTipo = 2 AND s.idMntExpediente= :exp AND s.soFecha=:fecha
              ORDER BY s.soFecha DESC, s.soHora DESC'
          )->setParameters(array('exp'=>$exp,'fecha'=>$fecha));
          $estudios = $query->getResult();
        }
        //Devuelve todos los estudios del paciente con el estudio seleccionado
        if($estudio!="0" && $fecha==0){
          $query = $em->createQuery(
              'SELECT c
              FROM    hnrsircimBundle:Estudio e, hnrsircimBundle:EstudioArea ea,
                      hnrsircimBundle:Solicitud s, hnrsircimBundle:Cita c
              WHERE   e.id=ea.idEstudio AND ea.id=s.idEstudioArea AND e.esNombre=:nomestudio AND
                      s.id= c.idSolicitud AND s.soTipo = 1 AND s.idMntExpediente= :exp
              ORDER BY s.soFecha DESC, s.soHora DESC'
          )->setParameters(array('exp'=>$exp,'nomestudio'=>$estudio));
          $citas = $query->getResult();
              // var_dump($citas);



          $query = $em->createQuery(
              'SELECT est
              FROM    hnrsircimBundle:Estudio e, hnrsircimBundle:EstudioArea ea,
                      hnrsircimBundle:Solicitud s, hnrsircimBundle:EstudioRadiologico est
              WHERE   e.id=ea.idEstudio AND ea.id=s.idEstudioArea AND e.esNombre=:nomestudio AND
                      s.id= est.idSolicitud AND s.soTipo = 2 AND s.idMntExpediente= :exp
              ORDER BY s.soFecha DESC, s.soHora DESC'
          )->setParameters(array('exp'=>$exp,'nomestudio'=>$estudio));
          $estudios = $query->getResult();               
      }
      // Devuelve los estudios y citas del paciente 
      if($estudio!="0" && $fecha!=0){
        $query = $em->createQuery(
            'SELECT c
            FROM    hnrsircimBundle:Estudio e, hnrsircimBundle:EstudioArea ea,
                    hnrsircimBundle:Solicitud s, hnrsircimBundle:Cita c
            WHERE   e.id=ea.idEstudio AND ea.id=s.idEstudioArea AND e.esNombre=:nomestudio AND
                    s.id= c.idSolicitud AND s.soTipo = 1 AND s.idMntExpediente= :exp AND s.soFecha=:fecha
            ORDER BY s.soFecha DESC, s.soHora DESC'
        )->setParameters(array('exp'=>$exp,'fecha'=>$fecha,'nomestudio'=>$estudio));
        $citas = $query->getResult();
            // var_dump($citas);
        $query = $em->createQuery(
            'SELECT est
            FROM    hnrsircimBundle:Estudio e, hnrsircimBundle:EstudioArea ea,
                    hnrsircimBundle:Solicitud s, hnrsircimBundle:EstudioRadiologico est
            WHERE   e.id=ea.idEstudio AND ea.id=s.idEstudioArea AND e.esNombre=:nomestudio AND
                    s.id= est.idSolicitud AND s.soTipo = 2 AND s.idMntExpediente= :exp AND s.soFecha=:fecha
            ORDER BY s.soFecha DESC, s.soHora DESC'
        )->setParameters(array('exp'=>$exp,'fecha'=>$fecha,'nomestudio'=>$estudio));
        $estudios = $query->getResult();               
      }             


        
        //var_dump($estudios);
        
        if (count($estudios)!=0 || count($citas)!=0) {
            
            // var_dump($estudios);
            // $estudiosrad = $em->getRepository('hnrsircimBundle:EstudioRadiologico')->findBy(array('soTipo'=>$estudios);
            $pacientes=$this->extraerDatos("\"Edad\",\"Nombre\",\"NumExpediente\"");
            $paciente=array();
            // var_dump($estudios);
            for ($i = 0; $i < count($estudios); $i++){
                $emp=$estudios[$i]->getIdSolicitud()->getIdMntExpediente();

                for ($j = 0; $j < count($pacientes); $j++){
                    
                    $emp2=$pacientes[$j]['NumExpediente'];
                    // var_dump($pacientes[$j]['NumExpediente']);
                    
                    if($emp==$emp2){
                        $paciente[$i]['Nombre']= $pacientes[$j]['Nombre'];
                        $paciente[$i]['Edad']= $pacientes[$j]['Edad'];
                    }
                }
            }
            // var_dump($estudios);
            // var_dump($paciente);
            return array(
                'entity' => $entity,
                'citas' => $citas,
                'exp'=>$exp,
                'estudios' => $estudios,
                'paciente'=>$paciente,
            );
        }
        else{
            return array(
                'citas' => $citas,
                'estudios' => $estudios,
                'fecha'=>$fecha,
                'nomestudio'=>$estudio,
            );
        }
         
    }


    /**
     * Displays a form to create a new Diagnostico entity.
     *
     * @Route("/expediente_trans/{exp}/{estudio}/{fecha}/{login}", name="diagnostico_trans", options={"expose"=true})
     * @Method("GET")
     * @Template("")
     */
    public function transcripcionAction($exp, $estudio, $fecha, $login)
    {
        $em = $this->getDoctrine()->getManager();
        #$num_exp = 9053-08;
        $num_exp = $exp;
        $session = new Session();
        $session->start();
        

        if($estudio=="0" && $fecha==0){
            $entity_di= $em->getRepository('hnrsircimBundle:Diagnostico')->findAll();
            $entities3 = $em 
           ->createQuery("SELECT e
                         FROM hnrsircimBundle:Solicitud s, hnrsircimBundle:EstudioRadiologico e, 
                              hnrsircimBundle:EstudioArea ea, hnrsircimBundle:Estudio es
                         WHERE e.idSolicitud=s.id and s.idEstudioArea=ea.id and ea.idEstudio=es.id 
                               and s.soTipo=2 and s.idMntExpediente= '$num_exp' Order By s.soFecha desc, s.soHora desc")
            ->getResult();            
        }
        // Devuelve todos los estudios del paciente de la fecha seleccionada
        if($estudio=="0" && $fecha!=0){
            $entity_di= $em->getRepository('hnrsircimBundle:Diagnostico')->findAll();            
            $entities3 = $em 
           ->createQuery("SELECT e
                         FROM hnrsircimBundle:Solicitud s, hnrsircimBundle:EstudioRadiologico e, 
                              hnrsircimBundle:EstudioArea ea, hnrsircimBundle:Estudio es
                         WHERE e.idSolicitud=s.id and s.idEstudioArea=ea.id and ea.idEstudio=es.id 
                               and s.soTipo=2 and s.idMntExpediente= '$num_exp' and s.soFecha='$fecha' Order By s.soFecha desc, s.soHora desc")
            ->getResult();         
        }

        //Devuelve todos los estudios del paciente con el estudio seleccionado
        if($estudio!="0" && $fecha==0){    
            $entity_di= $em->getRepository('hnrsircimBundle:Diagnostico')->findAll();            
            $entities3 = $em 
           ->createQuery("SELECT e
                         FROM hnrsircimBundle:Solicitud s, hnrsircimBundle:EstudioRadiologico e, 
                              hnrsircimBundle:EstudioArea ea, hnrsircimBundle:Estudio es
                         WHERE e.idSolicitud=s.id and s.idEstudioArea=ea.id and ea.idEstudio=es.id 
                               and s.soTipo=2 and s.idMntExpediente= '$num_exp' and es.esNombre='$estudio' Order By s.soFecha desc, s.soHora desc")
            ->getResult(); 
        } 


        if($estudio!="0" && $fecha!=0){            
            $entity_di= $em->getRepository('hnrsircimBundle:Diagnostico')->findAll();            
            $entities3 = $em 
           ->createQuery("SELECT e
                         FROM hnrsircimBundle:Solicitud s, hnrsircimBundle:EstudioRadiologico e, 
                              hnrsircimBundle:EstudioArea ea, hnrsircimBundle:Estudio es
                         WHERE e.idSolicitud=s.id and s.idEstudioArea=ea.id and ea.idEstudio=es.id 
                               and s.soTipo=2 and s.idMntExpediente= '$num_exp' and es.esNombre='$estudio' and s.soFecha='$fecha' Order By s.soFecha desc, s.soHora desc")
            ->getResult();              
      }                     
        $conn = $this -> get('doctrine.dbal.siap_connection');
                $expediente = $conn -> fetchAll("SELECT \"NumExpediente\", \"Nombre\", \"Edad\", \"Sexo\"
                                                 FROM vw_paciente_info where \"NumExpediente\" = '$num_exp'");        
        $estudios = $em
            ->createQuery("SELECT count(s.id) as estudio
                           FROM hnrsircimBundle:Solicitud s
                           WHERE s.soTipo=2 and s.idMntExpediente= '$num_exp'")
                ->getResult();  
         $citas = $em
            ->createQuery("SELECT count(s.id) as cita
                           FROM hnrsircimBundle:Solicitud s
                           WHERE s.soTipo=1 and s.idMntExpediente='$num_exp'")
                ->getResult();  
        
        return array(
            
            'expediente' => $expediente,
            'estudios' => $estudios,
            'citas' => $citas,
            'entities3' => $entities3,
            'entity_di' => $entity_di,
            
      
        );
        
         

        
        
    }


    /*
    *
    *
    *
    */
    public function extraerPacientes(){
        $conn = $this->get('doctrine.dbal.siap_connection'); 
        $pacientes = $conn->fetchAll( "SELECT \"NumExpediente\",\"Nombre\" FROM vw_paciente_info" );
        return $pacientes; 

    }

    /**
     * 
     *
     * @Route("/expediente_imagen/{exp}/{modalidad}/{fecha}/{hora}/{id}/{opcion}", name="diagnostico_img_trans", options={"expose"=true})
     * @Method("GET")
     * @Template("")
     */
    public function imagentransAction($exp,$modalidad,$fecha,$hora,$id,$opcion){
        
        $expediente=$exp;
        $modalidad=$modalidad;
        $fecha=$fecha;
        $hora=$hora;
        $id=$id;
        $em = $this->getDoctrine()->getManager();
        $imagen="";
        $g=0;
        $error="";
        $estudioradiologico = $em->getRepository('hnrsircimBundle:EstudioRadiologico')->find($id);
        $diagnostico = $em->getRepository('hnrsircimBundle:Diagnostico')->findOneByidEstudioRadiologico($id);
        $idempleado= $this->MostrarEmp();
        if ($diagnostico) {
            for ($i = 0; $i < count($idempleado); $i++) {
                if($idempleado[$i]['Idempleado']==$diagnostico->getidempleado()){
                    $idempleado=$idempleado[$i]['Nombre'];
                }
            }
        }    
        // var_dump($diagnostico);die;
        // var_dump($idempleado);
        // $empleado['Nombre']=$idempleado;
        $empleado['Nombre']=$idempleado;
        // var_dump($empleado);
        try{
          $conn = $this->get('doctrine.dbal.pacs_connection');    
        
        // var_dump($co}nn);
        $imagen = $conn->fetchAll("
            select * 
            from vw_paciente_pacs_info 
            where pat_id='".$expediente."' 
            and mods_in_study='".$modalidad."' 
            and date_trunc('day', study_datetime) = '".$fecha."'
            and extract(hour from study_datetime) = ".substr($hora, 0,2)."
            and extract(minute from study_datetime)=".substr($hora, 3,2)."");
            for ($i = 0; $i < count($imagen); $i++) {
                $url = 'http://192.168.1.21/'.$imagen[$i]['filepath']; 
                $g=basename($url); 
                //Definir ruta de archivo
                $this->file = "bundles/hnrsircim/Archivos/id_medico/".dirname($imagen[$i]['filepath'])."/";    
                if (!file_exists($this->file)) {
                    mkdir($this->file,0755,true);
                }
                //Establecer nombre del archivo
                $this->file=$this->file.$g;
                // var_dump($this->file.".jpg");
                if(!is_file($g)){
                    if(!file_exists($this->file.".jpg")){
                        $fp=fopen ($this->file, "w");
                        $ch=curl_init($url);
                        curl_setopt ($ch,CURLOPT_FILE, $fp);
                        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,60);
                        curl_exec ($ch);
                        curl_close ($ch);
                        fclose($fp); 
                        //Conversion de archivos
                        if(!$this->file) {
                          print "USAGE: ./dcm_to_jpg.php <FILE>\n";
                          exit;
                        }
                        if(!file_exists($this->file)) {
                          print $this->file.": no existe!\n";
                          exit;
                        }
                        $job_start = time();
                        //$d = new dicom_convert();
                        //$d->file = $file;
                        //$d->dcm_to_jpg();
                        //$d->multiframe_to_video("mp4",24,"./video_temp");
                        // system("ls -lsh $this->file*");
                        $job_end = time();
                        $job_time = $job_end - $job_start;
                        //print "<br>Archivo creado con &eacute;xito en $job_time seconds.\n";
                        //echo " <br>
                        $this->dcm_to_jpg();
                        $this->dcm_to_tn();
                        set_time_limit(20);
                        //Fin de la conversión de archivos
                        
                        unlink($this->file); //ELimina los archivos DICOM y deja solo las imágenes jpg
                    }
                }
                else{
                    echo "Archivo no encontrado";   
                }
            }
        }
        catch(\PDOException $e){
          // var_dump($e);
          $error="El servidor de imágenes no responde, intente más tarde";
        }

                //var_dump($imagen);
            switch ($opcion) {
                case 0:
                    return array(
                        'estudioradiologico' => $estudioradiologico,
                        'diagnostico' => $diagnostico,
                        'imagenes' => $imagen,
                        'archivo' => $g,
                        'error'=>$error,
                        'empleado'=>$empleado,
                    );
                    break;
                case 1:
                    return $this->render('hnrsircimBundle:Diagnostico:imagen1.html.twig', array(
                        'estudioradiologico' => $estudioradiologico,
                        'diagnostico' => $diagnostico,
                        'imagenes' => $imagen,
                        'archivo' => $g,
                        'error'=>$error,
                        'empleado'=>$empleado,
                    ));

                    break;
                case 2:
                    return $this->render('hnrsircimBundle:Diagnostico:imagen2.html.twig', array(
                        'estudioradiologico' => $estudioradiologico,
                        'diagnostico' => $diagnostico,
                        'imagenes' => $imagen,
                        'archivo' => $g,
                        'error'=>$error,
                        'empleado'=>$empleado,
                    ));

                    break;
            }

            
    }




    /*
    *
    *
    *
    */
    private function extraerDatos($campos){
        $campos=$campos;
        $conn = $this->get('doctrine.dbal.siap_connection'); 
        $pacientes = $conn->fetchAll("select ".$campos." from vw_paciente_info");        
        return $pacientes;
    }


     private function MostrarEmp(){
     $conn = $this -> get('doctrine.dbal.siap_connection');
        $idempleado = $conn -> fetchAll("SELECT \"Idempleado\", \"Nombre\" from 
                                       vw_empleado_info where \"Cargo\" = 'Tecnico'"); 
  
        
        return $idempleado;
  }







    function dcm_to_jpg() {

        $filesize = 0; 
        
        $this->jpg_file = $this->file . '.jpg';
        
        $convert_cmd = TOOLKIT_DIR . "/dcmj2pnm +oj +Jq " . $this->jpg_quality . " --use-window 1 \"" . $this->file . "\" \"" . $this->jpg_file . "\"";
        $out = $this->Execute($convert_cmd);
        system($convert_cmd);

        if(file_exists($this->jpg_file)) {
          $filesize = filesize($this->jpg_file);
        }

        if($filesize < 10) {
          $convert_cmd = TOOLKIT_DIR . "/dcmj2pnm +Wm +oj +Jq " . $this->jpg_quality . " \"" . $this->file . "\" \"" . $this->jpg_file . "\"";
          $out = $this->Execute($convert_cmd);
        }

        return($this->jpg_file);

    }


    function dcm_to_tn() {
        $filesize = 0;
        $this->tn_file = $this->file . '_tn.jpg';
        $this->tn_size = 800;

        $convert_cmd = TOOLKIT_DIR . "/dcmj2pnm +oj +Jq 75 +Sxv " . $this->tn_size . " --use-window 1 \"" . $this->file . "\" \"" . $this->tn_file . "\"";
        // $out = Execute($convert_cmd);
        $out = $this->Execute($convert_cmd);
        system($convert_cmd);
        if(file_exists($this->tn_file)) {
          $filesize = filesize($this->tn_file);
        }

        if($filesize < 10) {
          $convert_cmd = TOOLKIT_DIR . "/dcmj2pnm +Wm +oj +Jq 75 +Sxv  " . $this->tn_size . " \"" . $this->file . "\" \"" . $this->tn_file . "\"";
          // $out = Execute($convert_cmd);
          $out = $this->Execute($convert_cmd);
        }

        return($this->tn_file);
    }


    function Execute($command) {

        $command .= ' 2>&1';

        $handle = popen($command, 'r');
        
        $log = '';

        while (!feof($handle)) {
            $line = fread($handle, 1024);
            $log .= $line;
            
        }
        pclose($handle);

        return $log;
    }


    

}
