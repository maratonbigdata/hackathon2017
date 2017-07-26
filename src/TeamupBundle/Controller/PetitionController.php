<?php

namespace TeamupBundle\Controller;

use TeamupBundle\Entity\Petition;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Petition controller.
 *
 * @Route("petition")
 */
class PetitionController extends Controller
{
    /**
     * Lists all petition entities.
     *
     * @Route("/", name="petition_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $petitions = $em->getRepository('TeamupBundle:Petition')->findAll();

        return $this->render('petition/index.html.twig', array(
            'petitions' => $petitions,
        ));
    }

    /**
     * Creates a new petition entity.
     *
     * @Route("/{id}/new", name="ask")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, User $user)
    {
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();
        $petition = new Petition();

        $petition->setSender($currentUser);
        $petition->setReciever($user);
        $petition->setDate(new \DateTime());
        $petition->setState(1);

        $em = $this->getDoctrine()->getManager();
        $em->persist($petition);
        $em->flush();

        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
        $url = $baseurl.'/petition/'.$petition->getId();

        if($user->hasTeam())
        {
            foreach ($user->getTeam()->getUsers() as $member) 
            {
                $message = \Swift_Message::newInstance()
                ->setSubject('Les han solicitado unirse a su equipo!')
                ->setFrom('gestionIPre@ing.puc.cl')
                ->setTo(array($member->getEmail()))
                ->setBody('<html>' .
                    ' <head></head>' .
                    ' <body>' .
                    ' Hola, un participante les ha solicitado a unirse a su equipo. <br>Para ver la solicitud, haga clíck <a href="'.$url.'">aquí</a><br><br>'.
                    'Recuerden que basta con que un miembro acepte la petición<br><br>'.
                    ' TeamUp'.
                    '</html>',
                    'text/html')
            ;
            $this->get('mailer')->send($message);
            }
        }
        else
        {
            $message = \Swift_Message::newInstance()
                ->setSubject('Te han invitado a un equipo!')
                ->setFrom('gestionIPre@ing.puc.cl')
                ->setTo(array($user->getEmail()))
                ->setBody('<html>' .
                    ' <head></head>' .
                    ' <body>' .
                    ' Hola, un participante le ha solicitado a unirse a un equipo. <br>Para ver la solicitud, haga clíck <a href="'.$url.'">aquí</a><br><br>'.
                    ' TeamUp'.
                    '</html>',
                    'text/html')
            ;
            $this->get('mailer')->send($message);
        }

        return $this->redirectToRoute('petition_index');
    }

    /**
     * Finds and displays a petition entity.
     *
     * @Route("/{id}", name="petition_show")
     * @Method("GET")
     */
    public function showAction(Petition $petition)
    {

        return $this->render('petition/show.html.twig', array(
            'petition' => $petition,
        ));
    }

    /**
     * Changes petition state.
     *
     * @Route("/{id}/{state}/state", name="change_petition_state")
     * @Method("GET")
     */
    public function changeStateAction(Request $request, Petition $petition, $state)
    {
        /*if($petition->getState() == $state || $state == 1)
        {
            return $this->redirectToRoute('petition_show', array('id' => $petition->getId()));    
        }*/
        
        $em = $this->getDoctrine()->getManager();
        $petition->setState($state);
        $em->persist($petition);
        $em->flush();

        $team = $petition->getSender()->GetTeam();

        if($petition->getReciever()->hasTeam())
        {
            $petitionsTeam = $em->getRepository('TeamupBundle:Petition')->findOthersOfSameRecieverTeam($petition);

            foreach ($petitionsTeam as $petitionTeam) 
            {
                $petitionTeam->setState($state);
                $em->persist($petitionTeam);
                $em->flush();
            }
        }

        switch ($petition->getState())
        {
            case 2: //aceptada
                //acciones preguntados
                if($petition->getReciever()->hasTeam())
                {
                    foreach ($petition->getReciever()->getTeam()->getUsers() as $member) 
                    {
                        //agregar al equipo
                        $member->setTeam($team);
                        $em->persist($member);
                        $em->flush();

                        //enviar email
                        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
                        $url = $baseurl.'/team/'.$team->getId();
                        $message = \Swift_Message::newInstance()
                            ->setSubject('Has sido agregado a un equipo!')
                            ->setFrom('gestionIPre@ing.puc.cl')
                            ->setTo(array($member->getEmail()))
                            ->setBody('<html>' .
                                ' <head></head>' .
                                ' <body>' .
                                ' Hola, '.$petition->getReciever()->getFullName().' ha aceptado la invitación para unirse a '.$team->getName().', todos los miembros de su equipo anterior se han unido al nuevo equipo. <br>Para ver el nuevo equipo, haga clíck <a href="'.$url.'">aquí</a><br><br>'.
                                ' TeamUp'.
                                '</html>',
                                'text/html')
                        ;
                        $this->get('mailer')->send($message);
                    }
                }
                else
                {
                    //agregar al equipo
                    $petition->getReciever()->setTeam($team);
                    $em->persist($petition);
                    $em->flush();
                }

                //acciones invitadores
                foreach ($petition->getSender()->getTeam()->getUsers() as $user) 
                {
                    if($petition->getReciever()->hasTeam())
                    {
                        //enviar email
                        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
                        $url = $baseurl.'/team/'.$team->getId();
                        $message = \Swift_Message::newInstance()
                            ->setSubject('Han aceptado tu invitación!')
                            ->setFrom('gestionIPre@ing.puc.cl')
                            ->setTo(array($user->getEmail()))
                            ->setBody('<html>' .
                                ' <head></head>' .
                                ' <body>' .
                                ' Hola, '.$petition->getReciever()->getFullName().' ha aceptado la invitación de '.$petition->getSender()->getFullName().' para unirse a '.$team->getName().'. Todos los miembros de tu equipo se han unido a su equipo. <br>Para ver el equipo, haga clíck <a href="'.$url.'">aquí</a><br><br>'.
                                ' TeamUp'.
                                '</html>',
                                'text/html')
                        ;
                        $this->get('mailer')->send($message);
                    }
                    else
                    {
                        //enviar email
                        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
                        $url = $baseurl.'/team/'.$team->getId();
                        $message = \Swift_Message::newInstance()
                            ->setSubject('Han aceptado tu invitación!')
                            ->setFrom('gestionIPre@ing.puc.cl')
                            ->setTo(array($user->getEmail()))
                            ->setBody('<html>' .
                                ' <head></head>' .
                                ' <body>' .
                                ' Hola, '.$petition->getReciever()->getFullName().' ha aceptado la invitación de '.$petition->getSender()->getFullName().' para unirse a '.$team->getName().'y ha sido agregado al equipo. <br>Para ver el equipo, haga clíck <a href="'.$url.'">aquí</a><br><br>'.
                                ' TeamUp'.
                                '</html>',
                                'text/html')
                        ;
                        $this->get('mailer')->send($message);
                    }
                }
                break;
            case 3: //rechazada
                //enviar email de rechazo
                foreach ($petition->getSender()->getTeam()->getUsers() as $user) 
                {
                    if($petition->getReciever()->hasTeam())
                    {
                        //enviar email
                        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
                        $url = $baseurl.'/team/'.$team->getId();
                        $message = \Swift_Message::newInstance()
                            ->setSubject('Han rechazado tu invitación!')
                            ->setFrom('gestionIPre@ing.puc.cl')
                            ->setTo(array($user->getEmail()))
                            ->setBody('<html>' .
                                ' <head></head>' .
                                ' <body>' .
                                ' Hola, '.$petition->getReciever()->getFullName().' ha rechazado la invitación de '.$petition->getSender()->getFullName().' para unirse a '.$team->getName().'. Ningún miembros de su equipo se han unido al suyo.<br>Para ver el equipo, haga clíck <a href="'.$url.'">aquí</a><br><br>'.
                                ' TeamUp'.
                                '</html>',
                                'text/html')
                        ;
                        $this->get('mailer')->send($message);
                    }
                    else
                    {
                        //enviar email
                        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
                        $url = $baseurl.'/team/'.$team->getId();
                        $message = \Swift_Message::newInstance()
                            ->setSubject('Han aceptado tu invitación!')
                            ->setFrom('gestionIPre@ing.puc.cl')
                            ->setTo(array($user->getEmail()))
                            ->setBody('<html>' .
                                ' <head></head>' .
                                ' <body>' .
                                ' Hola, '.$petition->getReciever()->getFullName().' ha rechazado la invitación de '.$petition->getSender()->getFullName().' para unirse a '.$team->getName().'. <br>Para ver el equipo, haga clíck <a href="'.$url.'">aquí</a><br><br>'.
                                ' TeamUp'.
                                '</html>',
                                'text/html')
                        ;
                        $this->get('mailer')->send($message);
                    }
                }
                break;
            case 4: //re enviada
                //acciones invitados
                if($petition->getReciever()->hasTeam())
                {
                    foreach ($petition->getReciever()->getTeam()->getUsers() as $member) 
                    {
                        //enviar email
                        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
                        $url = $baseurl.'/petition/'.$petition->getId();
                        $message = \Swift_Message::newInstance()
                            ->setSubject('Te han vuelto a invitar a un equipo!')
                            ->setFrom('gestionIPre@ing.puc.cl')
                            ->setTo(array($member->getEmail()))
                            ->setBody('<html>' .
                                ' <head></head>' .
                                ' <body>' .
                                ' Hola, '.$petition->getReciever()->getFullName().' ha aceptado la invitación para unirse a '.$team->getName().', basta con que uno de los miembros de tu actual equipo acepte para que todos los miembros de tu equipo anterior sean unido al nuevo equipo. <br>Para ver la invitación, haga clíck <a href="'.$url.'">aquí</a><br><br>'.
                                ' TeamUp'.
                                '</html>',
                                'text/html')
                        ;
                        $this->get('mailer')->send($message);
                    }
                }
                else
                {
                    //agregar al equipo
                    $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
                    $url = $baseurl.'/petition/'.$petition->getId();
                    $message = \Swift_Message::newInstance()
                        ->setSubject('Te han vuelto a invitar a un equipo!')
                        ->setFrom('gestionIPre@ing.puc.cl')
                        ->setTo(array($member->getEmail()))
                        ->setBody('<html>' .
                            ' <head></head>' .
                            ' <body>' .
                            ' Hola, '.$petition->getReciever()->getFullName().' ha aceptado la invitación para unirse a '.$team->getName().'. <br>Para ver la invitación, haga clíck <a href="'.$url.'">aquí</a><br><br>'.
                            ' TeamUp'.
                            '</html>',
                            'text/html')
                    ;
                    $this->get('mailer')->send($message);
                }
                break;
        }

        return $this->redirectToRoute('petition_show', array('id' => $petition->getId()));
    }
}
