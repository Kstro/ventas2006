<?php

namespace hnr\sircimBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Session\Session;
//use minsal\academicaAnBudle\Entity\Asignatura;
//use minsal\academicaAnBudle\form\AsignaturaType;
use hnr\sircimBundle\Util\JasperReport\JasperClient as JasperClient;

include (__DIR__ . '/../Util/JasperReport/_jasperserverreports.php');
/**
 * Report Controller
 * 
 * @Route("/reportes")
 */


class ReporteController extends Controller {
/**
     * Lists all Reporte entities.
     *
     * @Route("/{login}/rpt", name="reportes")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($login)
    {
        
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('hnrsircimBundle:Usuario')->findAll();
        $session = new Session();
        $session->start();
        
        $usuario = $session->get($login);
        if(isset($usuario)){
            
            $empleados=$this->extraerDatos("\"Idempleado\",\"Nombre\"");
            $empleado=array();
            
            // var_dump($entities);
            for ($i = 0; $i < count($entities); $i++){
                $emp=$entities[$i]->getIdEmpleado();
                for ($j = 0; $j < count($empleados); $j++){
                    
                    $emp2=$empleados[$j]['Idempleado'];
                    
                    
                    if($emp==$emp2){
                        $empleado[$i]['Nombre']= $empleados[$j]['Nombre'];
                        $empleado[$i]['Idempleado']= $empleados[$j]['Idempleado'];
                    }
                }
            
            }
            
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

            sort($empleado);
            // var_dump($usuario);
            return array(
                'entities' => $entities,
                'empleados'=> $empleado,    
               'modulos' => $modulos,
               'usuario' => $usuario,
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
     * @Route("/reportes/{report_name}/{report_format}", name="mostrar_reporte")
     * @Template()
     */
    public function showAction($report_name, $report_format) {
        //$report_format='pdf';
        // $jasper_url = JASPER_URL;
        $jasper_url = JASPER_URL;
        $jasper_username = JASPER_USER;
        $jasper_password = JASPER_PASSWORD;
        $report_unit = "/reports/" . $report_name;
        $report_params = array();

        $client = new JasperClient($jasper_url, $jasper_username, $jasper_password);
        $contentType = (($report_format == 'HTML') ? 'text' : 'application') .
                '/' . strtolower($report_format);

        $result = $client->requestReport($report_unit, $report_format, $report_params);

        $response = new Response();
        $response->headers->set('Content-Type', $contentType);
        //if (strtoupper($report_format) != 'HTML')
        //$response->headers->set('Content-disposition', 'attachment; filename="' . $report_name . '.' . strtolower($report_format) . '"');
        $response->setContent($result);

        return $response;
    }

    /**
     * @Route("/reportediagnostico/{report_name}/{report_format}/{registro}/{estudio_radiologico}/{nombre}", name="_report_paciente")
     */
    public function pacienteAction($report_name, $report_format, $registro, $estudio_radiologico,$nombre) {
        //$report_format='pdf';
        $jasper_url = JASPER_URL;
        $jasper_username = JASPER_USER;
        $jasper_password = JASPER_PASSWORD;
        $report_unit = "/reports/" . $report_name;

       // $request = $this->getRequest();
       // $registro = $request->get('registro');
       // $estudio_radiologico = $request->get('estudio_radiologico');
        $report_params = array('registro' => $registro, 'estudio_radiologico' => $estudio_radiologico);

        $client = new JasperClient($jasper_url, $jasper_username, $jasper_password);

        $contentType = (($report_format == 'HTML') ? 'text' : 'application') .
                '/' . strtolower($report_format);

        $result = $client->requestReport($report_unit, $report_format, $report_params);

        $response = new Response();
        $response->headers->set('Content-Type', $contentType);
        /*  if (strtoupper($report_format) != 'HTML')
          $response->headers->set('Content-disposition', 'attachment; filename="' . $report_name . '.' . strtolower($report_format) . '"'); */
        $response->setContent($result);

        return $response;
    }


    /**
     * @Route("/reporte_pdf", name="_report_mostrar", options={"expose"=true})
     */
    
    public function mostrarAction() {
        //$report_format='pdf';
        $jasper_url = JASPER_URL;
        $jasper_username = JASPER_USER;
        $jasper_password = JASPER_PASSWORD;
        $report_unit = "/reports/" . $report_name;
        $report_name="libro_de_censo";
        $report_format="pdf";

        $fechainicio = "'".$fechainicio."'";
        $fechafin = "'".$fechafin."'";
        echo $fechafin;
       // $request = $this->getRequest();
       // $registro = $request->get('registro');
       // $estudio_radiologico = $request->get('estudio_radiologico');
        $report_params = array('fechainicio' => $fechainicio, 'fechafin' => $fechafin, 'emergencia'=>$emergencia,'central'=>$central,'especialidades'=>$especialidades,'resonancia'=>$resonancia);

        $client = new JasperClient($jasper_url, $jasper_username, $jasper_password);

        $contentType = (($report_format == 'HTML') ? 'text' : 'application') .
                '/' . strtolower($report_format);

        $result = $client->requestReport($report_unit, $report_format, $report_params);

        $response = new Response();
        $response->headers->set('Content-Type', $contentType);
        /*  if (strtoupper($report_format) != 'HTML')
          $response->headers->set('Content-disposition', 'attachment; filename="' . $report_name . '.' . strtolower($report_format) . '"'); */
        $response->setContent($result);

        return $response;
    }



    private function extraerDatos($campos){
        $campos=$campos;
        $conn = $this->get('doctrine.dbal.siap_connection'); 
        $empleados = $conn->fetchAll("select ".$campos." from vw_empleado_info order by \"Nombre\" ASC");
        return $empleados;
    }

    
    /**
     * Displays a form to create a new Diagnostico entity.
     *
     * @Route("/reporte/", name="menu_reporte", options={"expose"=true})
     * @Method("GET")
     * @Template("")
     */

    // /**
    // * @Route("/reportes/{report_name}/{report_format}", name="_exportar_reporte", options={"expose"=true})
    // */
   /* public function exportarReporteAction($report_name, $report_format) {
        //$report_format='pdf';
        $jasper_url = JASPER_URL;
        $jasper_username = JASPER_USER;
        $jasper_password = JASPER_PASSWORD;
        $report_unit = "/reports/" . $report_name;

        /*$request = $this->getRequest();
         $fecha_inicio = $request->get('fecha_inicio');
         $fecha_fin = $request->get('fecha_fin'); 
        $report_params = array('fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin);
        
        $report_params = array();
        $client = new JasperClient($jasper_url, $jasper_username, $jasper_password);

        $contentType = (($report_format == 'HTML') ? 'text' : 'application') .
                '/' . strtolower($report_format);

        $result = $client->requestReport($report_unit, $report_format, $report_params);

        $response = new Response();
        $response->headers->set('Content-Type', $contentType);
          if (strtoupper($report_format) != 'PDF')//para cuando sea una hoja de calculo, en este informe sólo  están las opciones PDF y hoja de cálculo
          $response->headers->set('Content-disposition', 'attachment; filename="' . $report_name . '.' . strtolower($report_format) . '"');
        $response->setContent($result);

        return $response;
    }*/

}

?>
