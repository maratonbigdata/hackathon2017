<?php

namespace TeamupBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function indexAction()
    {
    	$info = "";
    	$currentUser = $this->get('security.token_storage')->getToken()->getUser();
    	if($currentUser != 'anon.')
    	{
    		if(!$currentUser->hasTeam())
    		{
    			$info = "Aún no tienes un equipo, puedes crear un nuevo equipo en el menú o utilizar el buscador para pedir unirte a uno";
    		}
    		else
    		{
    			switch ($currentUser->getTeam()->getStatus()) 
		        {
		            case 1:
		                $info = 'Tu y tu equipo están buscando miembros, revisa las peticiones y solicitudes en el menú superior. No olvides postular en la vista de tu equipo!';
		                break;

		            case 2:
		                $info = 'Ya han postulado, ahora solo debes esperar un email nuestro que confirme que han sido aceptados para participar';
		                break;

		            case 3:
		                $info = 'Han sido aceptados!!! les esperamos en nuestra Maratón de BigData';
		                break;

		            case 4:
		                $info = 'Lo lamentamos pero no han sido aceptados, ya tendremos más concursos donde podrás participar';
		                break;

		            case 5:
		                $info = 'Tus compañeros aún no han confirmado la postulación';
		                break;

		            default:
		                $info = 'Bienvenido! si tienes dudas contáctate con nosotros';
		                break;
		        }

    		}
    	}
        return $this->render('TeamupBundle:Default:index.html.twig', array(
            'info' => $info,
        ));
    }
}
