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
use hnr\sircimBundle\Controller\BitacoraController;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Login controller.
 *
 * @Route("/login")
 */
class SecurityController extends Controller
{

    protected $url_archivo = "bundles/hnrsircim/Archivos/";
    
    /**
     * Definimos las rutas para el login
     * @Route("/", name="login")
     * 
     */


    public function loginAction()
    {
        $error="";
        return $this->render('hnrsircimBundle:Security:login.html.twig', array(
            // el último nombre de usuario ingresado por el usuario
            // 'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        ));

    }


     /**
     * Definimos las rutas para el login:
     * @Route("/login_check", name="login_check")
     * 
     */        

    public function login_checkAction()
    {
        
        $login=0;
        $contraseña=0;
        $hashpass=0;

        if(isset($_POST["_username"])){
            $login = $_POST["_username"];
            $contraseña = $_POST["_password"];
            $hashpass = hash( 'whirlpool', $contraseña);
        }
        // else{
        //     if(isset($session->get($login_cambiopass) )){
                
        //     }
        // }
        
        // var_dump($hashpass);

        $em = $this->getDoctrine()->getManager();
        
        // // $entities = $em->getRepository('hnrsircimBundle:Usuario')->findBy(
        // //     array('usLogin' => $login,
        // //           'usContrasena' => $contraseña));

        // // $query = $em->createQuery(
        // //     'SELECT u
        // //     FROM hnrsircimBundle:Usuario u, hnrsircimBundle:UsuarioRol ur
        // //     WHERE u.id=ur.idUsuario AND u.usLogin=:login AND u.usContrasena=:contrasena'
        // //     )->setParameters(array(
        // //     'login' =>$login, 
        // //     'contrasena' => $contraseña,
        // //     ));

        $error="";

        // $entities = $query->getResult();
        
        
        // var_dump($this->generateUrl('diagnostico'));
        $query = $em->createQuery(
            'SELECT u
            FROM hnrsircimBundle:Usuario u
            WHERE u.usLogin=:login AND u.usContrasena=:contrasena'
            )->setParameters(array('login'=>$login,'contrasena'=>$hashpass));

        $entities = $query->getResult();

        if(!$entities){
            $error="La contraseña o el usuario son incorrectas";
        }
        else{
            if($entities[0]->getUsEstadoActivo()==0){
                $error="El usuario no esta activo";
            }
            else{
                if($entities[0]->getUsEstadoBloqueado()==0){
                    $error="El usuario esta bloqueado";
                }
            }   

        }



        $query = $em->createQuery(
            'SELECT os.osUrl
            FROM hnrsircimBundle:Usuario u, hnrsircimBundle:UsuarioRol ur, 
                hnrsircimBundle:Rol r, hnrsircimBundle:RolOpcionSistema ros, 
                hnrsircimBundle:OpcionSistema os
            WHERE u.id=ur.idUsuario 
                AND ur.idRol=r.id 
                AND r.id=ros.idRol 
                AND ros.idOpcionSistema = os.id
                AND u.usLogin=:login
                AND u.usContrasena=:contrasena AND ur.usroPredeterminado=TRUE'
            )->setParameters(array('login'=>$login,'contrasena'=>$hashpass));

        $modulo = $query->getResult();



        if ($error==""){
            $session = new Session();
            $session->start();

            // set and get session attributes
            $session->set($entities[0]->getUsLogin(), $entities[0]);
            // $session->set('area', -1);
            // $session->get('name');
            // var_dump($session->get('name'));
            // set flash messages
            // $session->getFlashBag()->add('notice', 'Profile updated');

            // $_POST["id"]=$entities[0]->getId();
            // var_dump($_POST["id"]);
            
            if($entities[0]->getUsActualizarContrasena()==0){
                // $bitacora = new BitacoraController();
                // var_dump($bitacora);
                // $bitacora->escribirbitacora("Inicio sesión",$login);
                $this->get('mi_bitacora')->escribirbitacora("'".$login."' inicio sesión",$login);
                $file = fopen($this->url_archivo.$login.".txt", "w");
                fwrite($file, "0". PHP_EOL);
                fclose($file);
                if($modulo[0]['osUrl']=='administracion'){
                    return $this->redirect($this->generateUrl(''.$modulo[0]['osUrl'],array('login'=>$login,'login'=>$login )));
                }
                else{
                    return $this->redirect($this->generateUrl(''.$modulo[0]['osUrl'],array('login'=>$login )));    
                }
                
            }
            else{
                // return $this->redirect($this->generateUrl('cambiarpass'));   
                $this->get('mi_bitacora')->escribirbitacora("'".$login."' inicio sesión",$login);
                return $this->redirect($this->generateUrl('cambiarpass',array('login'=>$login )));
            }
            // return $this->redirect($this->generateUrl('administracion',array('login'=>$entities[0]->getUsLogin() )));
            // $error="El usuario o la contraseña son incorrectas";
            // return $this->render('hnrsircimBundle:Security:login.html.twig', array(
            //     'error' => $error,
            // )); 
        }
        else
        {
            // $error="El usuario o la contraseña son incorrectas";

            return $this->render('hnrsircimBundle:Security:login.html.twig', array(
                'error'         => $error,
            ));            
        }

    }

    /**
     * Lists all Usuario entities.
     *
     * @Route("/logout/", name="logout", options={"expose"=true})
     * @Method("GET")
     * @Template()
     */
    public function logoutAction(){
        
        $session = new Session();
        $session->start();
        
        // $session->remove($login);
        // $this->get('mi_bitacora')->escribirbitacora($login.' finalizó sesión',$login);
        $session->clear();
        
        // session_destroy();
        // $bitacora = new BitacoraController();
        // $bitacora->escribirbitacora("Finalizó sesión",$login);
        return $this->redirect($this->generateUrl('login'));
    }

    /**
     * Muestra el formulario para contraseña después de login, si es requerido
     *
     * @Route("/cambiarcontraseña/{login}", name="cambiarpass", options={"expose"=true})
     * @Method("GET")
     * @Template()
     */
    public function passAction($login){
        $session = new Session();
        $session->start();
        $usuario = $session->get($login);
        
        if(isset($usuario)){            
            return $this->render('hnrsircimBundle:Security:pass.html.twig', array(
                'login'         => $login,
            )); 
        }
        else{
            return $this->redirect($this->generateUrl('login'));
        }

    }


    /**
     * Continua al sistema luego de cambiar contraseña
     *
     * @Route("/continuar/{login}", name="continuar_sistema", options={"expose"=true})
     * @Method("POST")
     * @Template()
     */
    public function continuarAction($login){

        // $this->login_check($login);
        $session = new Session();
        $session->start();
        $usuario = $session->get($login);
        
        if(isset($usuario)){

            
            
            $contraseña = $_POST["pass1"];
            $hashpass = hash( 'whirlpool', $contraseña);

            $em = $this->getDoctrine()->getManager();

            $query = $em->createQuery('SELECT u FROM hnrsircimBundle:Usuario u WHERE u.usLogin = :usuario ORDER BY u.idempleado ASC')->setParameter('usuario',$login);
            $entity = $query->getResult(); // array of CmsArticle objects
            // $entity = $em->getRepository('hnrsircimBundle:Usuario')->findByusLogin($login);
            
            $entity[0]->setUsContrasena($hashpass);
            $entity[0]->setUsActualizarContrasena(0);
            // var_dump($entity);die;
            $em = $this->getDoctrine()->getManager();

            $em->persist($entity[0]);
            $em->flush();

            $query = $em->createQuery(
            'SELECT os.osUrl
            FROM hnrsircimBundle:Usuario u, hnrsircimBundle:UsuarioRol ur, 
                hnrsircimBundle:Rol r, hnrsircimBundle:RolOpcionSistema ros, 
                hnrsircimBundle:OpcionSistema os
            WHERE u.id=ur.idUsuario 
                AND ur.idRol=r.id 
                AND r.id=ros.idRol 
                AND ros.idOpcionSistema = os.id
                AND u.usLogin=:login
                AND u.usContrasena=:contrasena AND ur.usroPredeterminado=TRUE'
            )->setParameters(array('login'=>$login,'contrasena'=>$hashpass));

            $modulo = $query->getResult();

            $this->get('mi_bitacora')->escribirbitacora($login.' modificó su contraseña por primera vez',$login);
            return $this->redirect($this->generateUrl(''.$modulo[0]['osUrl'],array('login'=>$login )));

        }
        // return $this->render('hnrsircimBundle:Security:pass.html.twig', array('login',$login

        
        // ));
    }


    // public function verificarLogin($login){
    //     $session = new Session();
    //     $session->start();
    //     $usuario = $session->get($login);
    //     if(isset($usuario)){
    //         return false;
    //     }
    //     else{
        
    //         return $this->redirect($this->generateUrl('login' ));
            
    //     }
    // }




}