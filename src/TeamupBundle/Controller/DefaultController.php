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

    /**
     * @Route("/sendEmail", name="email_sender")
     */
    public function sendEmailAction()
    {
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();
        if($currentUser->getEmail() != 'evsvec@uc.cl')
        {
            return $this->redirectToRoute('home');    
        }
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('TeamupBundle:User')->findAll();
        $cc = 'maratonbigdata@uc.cl';

        foreach ($users as $user) 
        {
            try {
                if ($user->hasTeam())
                {
                    // tiene equipo y vemos estado
                    switch ($user->getTeam()->getStatus()) 
                    {
                        case 1:
                        case 5:
                            $message = \Swift_Message::newInstance()
                                ->setSubject('No olvides postular!')
                                ->setFrom('maratonbigdata@uc.cl')
                                ->setTo(array($user->getEmail()))
                                ->setCc(array($cc))
                                ->setBody('<html>' .
                                    ' <head></head>' .
                                    ' <body>' .
                                    'Estimado(a) '.$user->getName().','.
                                    '<br>'.
                                    '<br>'.
                                    'Te recordamos que aún tienes pendiente el “postular” a tu equipo al Entel Maratón Big Data 2017, acción que debes realizar entrando a la opción “Mi Equipo” en el menú superior de la plataforma de inscripción y, una vez reunidos los requisitos de contar con al menos dos y máximo cinco integrantes, hacer clic en el botón postular hasta las 20 horas del lunes 11 de septiembre.'.
                                    '<br><br>'.
                                    'Posterior a esto la organización revisará la propuesta del equipo y enviará un correo de aceptación o rechazo para participar en la actividad.'.
                                    '<br><br>'.
                                    'Para la competencia deben llevar sus propios equipos (notebooks, desktop, laptops, etc),  podrán utilizar la base de datos de Twitter por medio de su API para desarrolladores, además de <strong>cualquier otra de carácter abierto</strong> que ustedes estimen necesaria.'.
                                    '<br><br>'.
                                    'Atentamente,'.
                                    '<br><br>'.
                                    'Equipo organizador Entel Maratón Big Data 2017.'.
                                    '</html>',
                                    'text/html')
                                ;
                                $this->get('mailer')->send($message);
                            break;

                        case 2:

                            $team = $user->getTeam();
                            $team->setStatus(3);

                            $em->persist($team);
                            $em->flush();

                            $message = \Swift_Message::newInstance()
                                ->setSubject('Felicitaciones, ya estás participando!')
                                ->setFrom('maratonbigdata@uc.cl')
                                ->setTo(array($user->getEmail()))
                                ->setCc(array($cc))
                                ->setBody('<html>' .
                                    ' <head></head>' .
                                    ' <body>' .
                                    'Estimado(a) '.$user->getName().','.
                                    '<br><br>'.
                                    'Tenemos el agrado de informarles que su grupo ha sido aceptado para participar en el Entel Maratón Big Data 2017.'.
                                    '<br><br>'.
                                    'Los miembros del equipo deberán presentarse a la competencia el día miércoles 13 de septiembre de 2017 a las 8:30 AM en el auditorio del Centro de Innovación Andrónico Luksic del Campus San Joaquín de la Pontificia Universidad Católica de Chile, ubicado en Vicuña Mackenna 4860, Comuna de Macul. Deberán llevar una identificación con su nombre que los acredite como estudiantes o en proceso de titulación de alguna carrera de pregrado de universidad, instituto profesional, centro de formación técnica o programa de posgrado.'.
                                    '<br><br>'.
                                    'La competencia se desarrollará de manera ininterrumpida hasta las 15:30 horas del jueves 14 de septiembre. La organización estará a cargo de la alimentación de los participantes. La actividad tiene carácter gratuito.'.
                                    '<br><br>'.
                                    'Para la competencia deben llevar sus propios equipos (notebooks, desktop, laptops, etc),  podrán utilizar la base de datos de Twitter por medio de su API para desarrolladores, además de <strong>cualquier otra de carácter abierto</strong> que ustedes estimen necesaria.'.
                                    '<br><br>'.
                                    'Atentamente,'.
                                    '<br><br>'.
                                    'Equipo organizador Entel Maratón Big Data 2017.'.
                                    '</html>',
                                    'text/html')
                                ;
                            $this->get('mailer')->send($message);
                            break;
                    }
                }
                else
                {
                    $message = \Swift_Message::newInstance()
                        ->setSubject('Completa tu postulación!')
                        ->setFrom('maratonbigdata@uc.cl')
                        ->setTo(array($user->getEmail()))
                        ->setCc(array($cc))
                        ->setBody('<html>' .
                            ' <head></head>' .
                            ' <body>' .
                            'Estimado(a) '.$user->getName().','.
                            '<br><br>'.
                            'Te recordamos que para finalizar tu proceso de postulación al Entel Maratón Big Data 2017, debes crear o solicitar unirte a un equipo en el enlace: http://dev.investigacion.ing.uc.cl/team/TeamMatcher y luego hacer clic en el botón postular, acción que debes realizar entrando a la opción “Mi Equipo” en el menú superior de la plataforma de inscripción. Para esto tienes hasta las 20 horas del lunes 11 de septiembre.'.
                            '<br><br>'.
                            'Posterior a esto la organización revisará la propuesta del equipo y enviará un correo de aceptación o rechazo para participar en la actividad.'.
                            '<br><br>'.
                            'Para la competencia deben llevar sus propios equipos (notebooks, desktop, laptops, etc),  podrán utilizar la base de datos de Twitter por medio de su API para desarrolladores, además de <strong>cualquier otra de carácter abierto</strong> que ustedes estimen necesaria.'.
                            '<br><br>'.
                            'Atentamente,'.
                            '<br><br>'.
                            'Equipo organizador Entel Maratón Big Data 2017.'.
                            '</html>',
                            'text/html')
                        ;
                    $this->get('mailer')->send($message);
                    
                }
            }
            catch (Exception $e) 
            {
                echo 'Excepción capturada: ',  $e->getMessage(), "\n";
            }
            
        }


        return $this->redirectToRoute('home');    
    }
}
