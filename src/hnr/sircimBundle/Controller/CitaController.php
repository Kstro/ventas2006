<?php

namespace hnr\sircimBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use hnr\sircimBundle\Entity\Cita;
use hnr\sircimBundle\Entity\Area;
use hnr\sircimBundle\Entity\Solicitud;
use hnr\sircimBundle\Form\CitaType;
use hnr\sircimBundle\Form\CitaEditType;

/**
 * Cita controller.
 *
 * @Route("/cita")
 */
class CitaController extends Controller
{
    private $area;
    
    public function __construct() {
        $this->area = 1;
    }
    
    /**
     * Lists all Cita entities.
     *
     * @Route("/{login}", name="cita")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($login)
    {
        $permisos = $this->get('mi_sesion')->verificar_sesion($login,'cita');
        $this->get('mi_sesion')->asignar_area_usuario(0,$login);
        
        
        
        
        
        

        
        //setcookie('area',5);
        

        // return $this->render('hnrsircimBundle:Cita:index.html.twig');
         return array(
            'login'  => $login,
            'usuario'  => $login,
            'modulos' => $permisos["modulos"],
        );
    }
    
    /**
     *
     * @Route("/dia/{date}/{login}", name="cita_dia", options={"expose"=true})
     * @Method("GET")
     * @Template("hnrsircimBundle:Cita:dia.html.twig")
     */
    public function CitaDiaAction($date,$login)
    {
        $hoy = date('Y-m-d');
        if($date < $hoy)
                $update = 0;
        else
                $update = 1;
                
        $em = $this->getDoctrine()->getManager();
            
        $dql = "SELECT cita FROM hnrsircimBundle:Cita cita
                JOIN cita.idSolicitud sol
                JOIN sol.idEstudioArea area
                    WHERE sol.id = cita.idSolicitud
                    AND sol.soFecha = :date
                    AND area.idArea = :area
                    ORDER BY sol.soHora";
                
        $entities = $em->createQuery($dql)
                       ->setParameters(array('date' => $date ,'area' => $this->area))
                       ->getResult();
        
        $pac = $this->extraerPacientes(null);
        $paciente = array();
        
        for ($i = 0; $i < count($entities); $i++){
            $emp=$entities[$i]->getIdSolicitud();
            for ($j = 0; $j < count($pac); $j++){
                
                $emp2=$pac[$j]['NumExpediente'];
                
                
                if($emp==$emp2){
                    $paciente[$i] = $pac[$j]['Nombre'];
                }
            }
        
        }
        
        return array(
            'entities'  => $entities,
            'pacientes' => $paciente,
            'update'    => $update,
            'login'     => $login,
        );
    }
    
    /**
     *
     * @Route("/semana/{login}", name="cita_semana", options={"expose"=true})
     * @Method("GET")
     */
    public function CitaSemanaAction($login)
    {
        return $this->render('hnrsircimBundle:Cita:semana.html.twig');
    }
    
    /**
     *
     * @Route("/mes/{f_inicio}/{f_fin}", name="cita_mes", options={"expose"=true})
     * @Method("GET")
     * @Template("hnrsircimBundle:Cita:mes.html.twig")
     */
    public function CitaMesAction($f_inicio, $f_fin)
    {
        $em = $this->getDoctrine()->getManager();
        
        $dql = "SELECT sol.soFecha, COUNT(cita) FROM hnrsircimBundle:Cita cita
                JOIN cita.idSolicitud sol
                JOIN sol.idEstudioArea area
                    WHERE sol.id = cita.idSolicitud
                    AND area.idArea = :area
                    AND sol.soFecha BETWEEN :fecha1 AND :fecha2
                    GROUP BY sol.soFecha
                    ORDER BY sol.soFecha";
                
        $entities = $em->createQuery($dql)
                       ->setParameters(array('area' => $this->area, 'fecha1' => $f_inicio, 'fecha2' => $f_fin))
                       ->getResult();
        
        $dql2 = "SELECT area.arCupo FROM hnrsircimBundle:Area area
                    WHERE area.id = :area";
        
        $cupo =  $em->createQuery($dql2)
                     ->setParameter('area', $this->area)
                     ->getResult();
        
        $cupos_dia= $cupo[0]["arCupo"] * 2;
        //var_dump($cupos_dia);
        //var_dump($entities);
        //$fecha_aux = $entities[0]["soFecha"];
        //var_dump("count :".$entities[6]["1"]."<br/>");
        $dias = array();
        for( $i = 1; $i <= substr($f_fin,-2); $i++){
            for($j = 0; $j < count($entities); $j++){
                if( substr($entities[$j]["soFecha"]->format("Y-m-d"), -2) < 10 )
                    $fecha_aux = substr($entities[$j]["soFecha"]->format("Y-m-d"), -1);
                else 
                    $fecha_aux = substr($entities[$j]["soFecha"]->format("Y-m-d"), -2);
                
                if($i == $fecha_aux && $entities[$j]["1"] > 0){
                    $dias[$i] = "lleno";                    
                    break;
                }
                else{
                    $dias[$i] = "vacio";
                }
            }
        }
                
        //var_dump("cupo: ".$cupo[0]["arCupo"]."<br/>");
        //var_dump("cupo todal x dia: ".($cupo[0]["arCupo"]*24));
        //var_dump($entities);
        return array(
            'dias' => $dias,
        );
    }
    
    /**
     *
     * @Route("/citaspaciente/{query}/{registro}", name="citas_paciente", options={"expose"=true})
     * @Method("GET")
     * @Template("hnrsircimBundle:Cita:cipaciente.html.twig")
     */
    public function CitasPaciente($query,$registro)
    {
        $em = $this->getDoctrine()->getManager();
        
        $entities = $em->createQuery($query)
                       ->setParameter('area', $this->area)
                       ->getResult();
        
        return array(
            'entities'  => $entities,
            'paciente'  => $this->extraerPacientes($registro),
            'fecha_actual' => date('Y-m-d'),
        );
    }
    
    /**
     * Creates a new Cita entity.
     *
     * @Route("/create/{login}/create", name="cita_create")
     * @Method("POST")
     * @Template("hnrsircimBundle:Cita:new.html.twig")
     */
    public function createAction(Request $request,$login)
    {
        // $permisos = $this->get('mi_sesion')->verificar_sesion($login,'cita');
        $entity  = new Cita();
        
        $form = $this->createForm(new CitaType($this->area, $this->getRegionesCh(), $this->getPosicionesCh(), $this->getServiciosCh()) , $entity);
        $form->bind($request);
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setCiEstado('Pendiente');
            $entity->getIdSolicitud()->setSoTipo(1);
            try{
                $em->persist($entity);
                $em->flush();
                
                // $this->get('mi_bitacora')->escribirbitacora("Creo una cita para el paciente ".$entity->getIdSolicitud()->getIdMntExpediente()." para la fecha ".$entity->getIdSolicitud()->getSoFecha()." a las ".$entity->getIdSolicitud()->getSoFecha(),$login);
                
                return $this->redirect($this->generateUrl('cita',array('login' => $login )));
                // return $this->redirect($this->generateUrl('cita',array('login' => $login )));    
            }catch(\Doctrine\DBAL\DBALException $e){
                //var_dump($e);
            }
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            
        );
    }

    /**
     * Displays a form to create a new Cita entity.
     *
     * @Route("/new/{login}", name="cita_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction($login)
    {
        //var_dump($this->area);
        
        $entity = new Cita();
        $form   = $this->createForm(new CitaType($this->area, $this->getRegionesCh(), $this->getPosicionesCh(), $this->getServiciosCh()), $entity);
        
        /*$form = $this->createFormBuilder($entity)
                ->add('idMntExpediente', null, array('label' => 'Registro'))
                ->getForm();*/
        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'login' => $login,
        );
    }

    /**
     * Finds and displays a Cita entity.
     *
     * @Route("/show/{id}/{nombre}", name="cita_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id, $nombre)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('hnrsircimBundle:Cita')->find($id);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cita entity.');
        }
        
        //$expediente = $entity->getIdSolicitud()->getIdMntExpediente();
        //$pac = $this->extraerPacientes($expediente);
        
        //$paciente[0] = $pac[0]['Nombre'];
        
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'nombre'      => $nombre,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Cita entity.
     *
     * @Route("/{id}/edit/{login}", name="cita_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id,$login)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('hnrsircimBundle:Cita')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cita entity.');
        }

        $editForm = $this->createForm(new CitaEditType($this->area, $this->getRegionesCh(), $this->getPosicionesCh(), $this->getServiciosCh()), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'login' =>$login,
        );
    }
    
    /**
     *
     * @Route("/actualizarestado/{id}/{estado}", name="update_estado", options={"expose"=true})
     * @Method("GET")
     * @Template()
     */
    public function UpdateEstadoAction($id, $estado)
    {
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('hnrsircimBundle:Cita')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cita entity.');
        }

        $entity->setCiEstado($estado);
        $em->persist($entity);
        $em->flush();
        
        // $this->get('mi_bitacora')->escribirbitacora("Modifico el estado ".$estado." de la cita programada para el paciente ".$entity->getIdSolicitud()->getIdMntExpediente()." para la fecha ".$entity->getIdSolicitud()->getSoFecha()." a las ".$entity->getIdSolicitud()->getSoFecha(),$login);
    }

    /**
     * Edits an existing Cita entity.
     *
     * @Route("/{id}", name="cita_update")
     * @Method("PUT")
     * @Template("hnrsircimBundle:Cita:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        // $permisos = $this->get('mi_sesion')->verificar_sesion($login,'cita');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('hnrsircimBundle:Cita')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cita entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new CitaEditType($this->area, $this->getRegionesCh(), $this->getPosicionesCh(), $this->getServiciosCh()), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            
            // $this->get('mi_bitacora')->escribirbitacora("Modifico una cita programada para el paciente ".$entity->getIdSolicitud()->getIdMntExpediente()." para la fecha ".$entity->getIdSolicitud()->getSoFecha()." a las ".$entity->getIdSolicitud()->getSoFecha(),$login);
            
            return $this->redirect($this->generateUrl('cita'));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Cita entity.
     *
     * @Route("/{id}", name="cita_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('hnrsircimBundle:Cita')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Cita entity.');
            }

            $em->remove($entity);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('cita',array('login' => $login )));
        // return $this->redirect($this->generateUrl('cita'));
    }

    /**
     * Creates a form to delete a Cita entity by id.
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
     * @Route("/regiones/get", name="get_regiones", options={"expose"=true})
     * @Method("GET")
     */
    public function getRegionesAction() {
        
        $request = $this->getRequest();
        $nomEstudio = $request->get('nomEstudio');
        $em = $this->getDoctrine()->getEntityManager();
        
        $dql = "SELECT reg.reNombre FROM hnrsircimBundle:EstudioRegion es_reg 
                JOIN es_reg.idRegion reg                    
                JOIN es_reg.idEstudio es
                    WHERE reg.id = es_reg.idRegion
                    AND es.id = es_reg.idEstudio
                    AND es.esNombre = :nomEstudio";
        
        $regiones['regs'] = $em->createQuery($dql)
                ->setParameter('nomEstudio', $nomEstudio)
                ->getArrayResult();
        
        return new Response(json_encode($regiones));
    }
    
    /**
     * @Route("/posiciones/get", name="get_posiciones", options={"expose"=true})
     * @Method("GET")
     */
    public function getPosicionesAction() {
        
        $request = $this->getRequest();
        $nomEstudio = $request->get('nomEstudio');
        $em = $this->getDoctrine()->getEntityManager();
        
        $dql = "SELECT po.poNombre FROM hnrsircimBundle:EstudioPosicion es_po 
                JOIN es_po.idPosicion po                    
                JOIN es_po.idEstudio es
                    WHERE po.id = es_po.idPosicion
                    AND es.id = es_po.idEstudio
                    AND es.esNombre = :nomEstudio";
        
        $regiones['regs'] = $em->createQuery($dql)
                ->setParameter('nomEstudio', $nomEstudio)
                ->getArrayResult();
        
        return new Response(json_encode($regiones));
    }
    
    public function getServiciosCh() {
        
        $conn = $this->get('doctrine.dbal.siap_connection');
        
        $result = $conn->fetchAll( "SELECT \"NombreAtencion\" FROM vw_servicio_info" );
        
        for ( $i=0; $i < count($result); $i++){
            $servicios[ $result[$i]['NombreAtencion'] ] = $result[$i]['NombreAtencion'];
        }
        
        return $servicios;
       
    }
    
    public function getRegionesCh(){
        
        $regiones = array();
        
        $em = $this->getDoctrine()->getEntityManager();

        $result = $em->getRepository('hnrsircimBundle:Region')->findAll();
        
        foreach ($result as $reg){
            $regiones[ $reg->getReNombre() ] = $reg->getReNombre();
        }
        
        return $regiones;
        
    }
    
    public function getPosicionesCh(){
        
        $posiciones = array();
        
        $em = $this->getDoctrine()->getEntityManager();

        $result = $em->getRepository('hnrsircimBundle:Posicion')->findAll();
        
        foreach ($result as $pos){
            $posiciones[ $pos->getPoNombre() ] = $pos->getPoNombre();
        }
        
        return $posiciones;
        
    }
 
    /**
     * @Route("/dias/get", name="get_dias", options={"expose"=true})
     * @Method("GET")
     */
    public function getDiasAction() {
        
        $request = $this->getRequest();
        $idEstudio = $request->get('idEstudio');
        $em = $this->getDoctrine()->getEntityManager();
                
        $dql = "SELECT DISTINCT ho.hoDia FROM hnrsircimBundle:Horario ho 
                    WHERE ho.idEstudioArea = :idEstudio";
        
        $regiones['regs'] = $em->createQuery($dql)
                ->setParameter('idEstudio', $idEstudio)
                ->getArrayResult();
        
        return new Response(json_encode($regiones));
    }
    
    /**
     * @Route("/horas/get/{estudio}/{dia}", name="get_horas", options={"expose"=true})
     * @Method("GET")
     */
    public function getHorasAction($estudio,$dia) {
        
        //$request   = $this->getRequest()->query->all();
        //$idEstudio = $request['idEstudio'];
        
        //$dia       = $request['dia'];
        //$idEstudio = $request->query->all();        
        //$dia = $request->request->get('dia');
        
        $em = $this->getDoctrine()->getEntityManager();
                
        $dql = "SELECT ho.hoDia, ho.hoHoraInicio, ho.hoHoraFin FROM hnrsircimBundle:Horario ho
                    WHERE ho.idEstudioArea = :idEstudio
                    AND ho.hoDia = :dia";
        
        $regiones['regs'] = $em->createQuery($dql)
                ->setParameters(array('idEstudio'=>$estudio, 'dia'=>$dia))
                ->getArrayResult();
                
        return new Response(json_encode($regiones));
    }
    
    public function extraerPacientes($expediente){
        $conn = $this->get('doctrine.dbal.siap_connection');
        if ($expediente == null){
            $pacientes = $conn->fetchAll( "SELECT \"NumExpediente\",\"Nombre\" FROM vw_paciente_info" );
        }
        else{
            $pacientes = $conn->fetchAll( "SELECT \"NumExpediente\",\"Nombre\",\"Edad\" FROM vw_paciente_info WHERE \"NumExpediente\"='".$expediente."'" );
        }
             
        return $pacientes;
    }
    
    /**
     * @Route("/estado_cupos/get/{dia}", name="get_estado_cupos", options={"expose"=true})
     * @Method("GET")
     */
    public function getEstadoCuposAction($dia) {
        
        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT COUNT(cita) FROM hnrsircimBundle:Cita cita
                JOIN cita.idSolicitud sol
                JOIN sol.idEstudioArea area
                    WHERE sol.id = cita.idSolicitud
                    AND area.idArea = :area
                    AND sol.soFecha = :dia
                    GROUP BY sol.soFecha";
                
        $entities = $em->createQuery($dql)
                       ->setParameters(array('area' => $this->area, 'dia' => $dia))
                       ->getResult();
               
        $dql2 = "SELECT area.arCupo FROM hnrsircimBundle:Area area
                    WHERE area.id = :area";
        
        $cupo =  $em->createQuery($dql2)
                     ->setParameter('area', $this->area)
                     ->getResult();
        
        $cupos_dia= $cupo[0]["arCupo"] * 2;
        $count = (count($entities) == 0 ? 0:$entities[0][1]);
        $estado['regs']['edo'] = ( $count > 0 ? 'lleno':'vacio');
        
        return new Response(json_encode($estado));
    }
    
     
    
    /** 
     * Edits an existing Tamano Placa entity. 
     * 
     * 
     * @Route("/consultarci/{expediente}/{fecha}/{hora}", name="ci_consultar", options={"expose"=true}) 
     * @Method("GET")
     */ 
    public function consultarciAction($expediente,$fecha,$hora) { 
         
        $em = $this->getDoctrine()->getManager(); 
          
        $dql = "SELECT COUNT(sol) FROM hnrsircimBundle:Solicitud sol
                       WHERE sol.soTipo = 1
                       AND sol.idMntExpediente = :exp
                       AND sol.soFecha = :fecha
                       AND sol.soHora = :hora";
            
        $entities = $em->createQuery($dql) 
                       ->setParameters(array('exp'=>$expediente,'fecha'=>$fecha,'hora'=>$hora)) 
                       ->getResult(); 
            
        $reg['regs']['edo'] = $entities[0][1];
        
        return new Response(json_encode($reg)); 
   }
   
   /** 
     * Consultar el número de citas mayor por cada media hora. 
     * 
     * 
     * @Route("/consultarcinum/{fecha_inicio}/{fecha_fin}", name="ci_consultar_num", options={"expose"=true}) 
     * @Method("GET")
     */ 
    public function consultarcinumAction($fecha_inicio,$fecha_fin) { 
         
        $em = $this->getDoctrine()->getManager(); 
        
        $a = array('07:00','07:30','08:00','08:30','9:00','9:30','10:00','10:30','11:00','11:30','12:00','12:30','13:00','13:30','14:00','14:30','15:00','22:00');
        $b = array();
        
        foreach ($a as $hora){
            //var_dump($hora);
            $dql =  "SELECT sol.soHora, COUNT(sol) cnt
                     FROM hnrsircimBundle:Solicitud sol
                        JOIN sol.idEstudioArea area       
                            WHERE sol.soTipo = 1
                            AND area.idArea = :area
                            AND sol.soFecha BETWEEN :fecha_inicio AND :fecha_fin
                            AND sol.soHora = :hora
                            GROUP BY sol.soFecha, sol.soHora
                            ORDER BY cnt desc";
            
            $entities = $em->createQuery($dql) 
                        ->setParameters(array('area' => $this->area,'fecha_inicio'=>$fecha_inicio,'fecha_fin'=>$fecha_fin, 'hora'=>$hora)) 
                        ->getResult(); 
            
            array_push($b, array('hora' => $hora, 'cnt' => (count($entities) > 0 ? $entities[0]["cnt"] : 0)) );
            //(count($entities) > 0 ? var_dump($entities[0]["cnt"]) : var_dump(0));
            //var_dump($entities);
        }
        //var_dump($b);
        
                
        $dql = "SELECT sol.soHora, sol.soFecha, sol.idMntExpediente, estudio.esAbreviatura ,cita.ciEstado FROM hnrsircimBundle:Cita cita
                JOIN cita.idSolicitud sol
                JOIN sol.idEstudioArea area 
                JOIN area.idEstudio estudio
                       WHERE sol.id = cita.idSolicitud
                       AND estudio.id = area.idEstudio
                       AND sol.soFecha BETWEEN :fecha_inicio AND :fecha_fin
                       AND area.idArea = :area
                       ORDER BY sol.soFecha ASC, sol.soHora ASC";
            
        $entities = $em->createQuery($dql) 
                       ->setParameters(array('area' => $this->area,'fecha_inicio'=>$fecha_inicio,'fecha_fin'=>$fecha_fin)) 
                       ->getResult();
        
        //var_dump($entities);
        
        //$reg['regs']['edo'] = (count($entities) > 0 ? $entities[0]["maximum"] : 0);
        if(count($entities) > 0)
            $reg = array( 'success' => true, 'cnt' => $b, 'regs' => $entities );
        else
            $reg = array( 'success' => false );
        
        /*if(count($entities) > 0)
            $reg = array(
                'success' => true,
                'regs' => $entities[0]["maximum"]
            );
        else
            $reg = array(
                'success' => false
            );*/
        //var_dump($reg);
        
        return new Response( json_encode($reg) ); 
   }
   
}
