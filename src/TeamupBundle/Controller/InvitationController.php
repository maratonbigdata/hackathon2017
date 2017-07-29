<?php

namespace TeamupBundle\Controller;

use TeamupBundle\Entity\Invitation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use TeamupBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;

/**
 * Invitation controller.
 *
 * @Route("invitation")
 */
class InvitationController extends Controller
{
    /**
     * Lists all invitation entities.
     *
     * @Route("/", name="invitation_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        $invitationsSended = $em->getRepository('TeamupBundle:Invitation')->findBySender($currentUser);

        $invitationsRecieved = $em->getRepository('TeamupBundle:Invitation')->findByReciever($currentUser);

        return $this->render('invitation/index.html.twig', array(
            'invitationsSended' => $invitationsSended,
            'invitationsRecieved' => $invitationsRecieved,
        ));
    }

    /**
     * Creates a new invitation entity.
     *
     * @Route("/{id}/new", name="invite")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, User $user)
    {
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        if(!($user->hasTeam() && $user->getTeam()->getStatus == 1))
        {
            return $this->redirectToRoute('home');
        }
        if(!($currentUser->hasTeam() && $currentUser->getTeam()->getStatus == 1))
        {
            return $this->redirectToRoute('home');
        }

        $invitation = new Invitation();

        $invitation->setSender($currentUser);
        $invitation->setReciever($user);
        $invitation->setDate(new \DateTime());
        $invitation->setState(1);

        $em = $this->getDoctrine()->getManager();
        $em->persist($invitation);
        $em->flush();

        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
        $url = $baseurl.'/invitation/'.$invitation->getId();

        if($user->hasTeam())
        {
            foreach ($user->getTeam()->getUsers() as $member) 
            {
                $message = \Swift_Message::newInstance()
                ->setSubject('Les han invitado a un equipo!')
                ->setFrom('gestionIPre@ing.puc.cl')
                ->setTo(array($member->getEmail()))
                ->setBody('<html>' .
                    ' <head></head>' .
                    ' <body>' .
                    ' Hola, se les ha invitado a unirse a un equipo. <br>Para ver la invitación, haga clíck <a href="'.$url.'">aquí</a><br><br>'.
                    'Recuerden que basta con que un miembro acepte la invitación para unir los equipos<br><br>'.
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
                    ' Hola, se le ha invitado a unirse a un equipo. <br>Para ver la invitación, haga clíck <a href="'.$url.'">aquí</a><br><br>'.
                    ' TeamUp'.
                    '</html>',
                    'text/html')
            ;
            $this->get('mailer')->send($message);
        }

        return $this->redirectToRoute('invitation_index');
    }

    /**
     * Finds and displays a invitation entity.
     *
     * @Route("/{id}", name="invitation_show")
     * @Method("GET")
     */
    public function showAction(Invitation $invitation)
    {
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        if(!($invitation->getSender()->getId() == $currentUser->getId() || $invitation->getReciever()->getId() == $currentUser->getId()) )
        {
            return $this->redirectToRoute('home');
        }

        return $this->render('invitation/show.html.twig', array(
            'invitation' => $invitation,
        ));
    }

    /**
     * Changes invitation state.
     *
     * @Route("/{id}/{state}/state", name="change_invitation_state")
     * @Method("GET")
     */
    public function changeStateAction(Request $request, Invitation $invitation, $state)
    {
        if(!($invitation->getSender()->getId() == $currentUser->getId() || $invitation->getReciever()->getId() == $currentUser->getId()) )
        {
            return $this->redirectToRoute('home');
        }
        
        if($petition->getState() == $state || $state == 1 || $petition->getState() == 5)
        {
            return $this->redirectToRoute('invitation_show', array('id' => $invitation->getId()));    
        }
        
        $em = $this->getDoctrine()->getManager();
        $invitation->setState($state);
        $em->persist($invitation);
        $em->flush();

        $team = $invitation->getSender()->GetTeam();

        if($invitation->getReciever()->hasTeam())
        {
            $invitationsTeam = $em->getRepository('TeamupBundle:Invitation')->findOthersOfSameRecieverTeam($invitation);

            foreach ($invitationsTeam as $invitationTeam) 
            {
                $invitationTeam->setState($state);
                $em->persist($invitationTeam);
                $em->flush();
            }
        }

        switch ($invitation->getState())
        {
            case 2: //aceptada
                //acciones invitados
                if($invitation->getReciever()->hasTeam())
                {
                    foreach ($invitation->getReciever()->getTeam()->getUsers() as $member) 
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
                                ' Hola, '.$invitation->getReciever()->getFullName().' ha aceptado la invitación para unirse a '.$team->getName().', todos los miembros de su equipo anterior se han unido al nuevo equipo. <br>Para ver el nuevo equipo, haga clíck <a href="'.$url.'">aquí</a><br><br>'.
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
                    $invitation->getReciever()->setTeam($team);
                    $em->persist($invitation);
                    $em->flush();
                }

                //acciones invitadores
                foreach ($invitation->getSender()->getTeam()->getUsers() as $user) 
                {
                    if($invitation->getReciever()->hasTeam())
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
                                ' Hola, '.$invitation->getReciever()->getFullName().' ha aceptado la invitación de '.$invitation->getSender()->getFullName().' para unirse a '.$team->getName().'. Todos los miembros de tu equipo se han unido a su equipo. <br>Para ver el equipo, haga clíck <a href="'.$url.'">aquí</a><br><br>'.
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
                                ' Hola, '.$invitation->getReciever()->getFullName().' ha aceptado la invitación de '.$invitation->getSender()->getFullName().' para unirse a '.$team->getName().'y ha sido agregado al equipo. <br>Para ver el equipo, haga clíck <a href="'.$url.'">aquí</a><br><br>'.
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
                foreach ($invitation->getSender()->getTeam()->getUsers() as $user) 
                {
                    if($invitation->getReciever()->hasTeam())
                    {
                        //enviar email
                        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
                        $url = $baseurl.'/team/'.$team->getId();
                        $message = \Swift_Message::newInstance()
                            ->setSubject('Han rechazado tu invitación')
                            ->setFrom('gestionIPre@ing.puc.cl')
                            ->setTo(array($user->getEmail()))
                            ->setBody('<html>' .
                                ' <head></head>' .
                                ' <body>' .
                                ' Hola, '.$invitation->getReciever()->getFullName().' ha rechazado la invitación de '.$invitation->getSender()->getFullName().' para unirse a '.$team->getName().'. Ningún miembros de su equipo se han unido al suyo.<br>Para ver el equipo, haga clíck <a href="'.$url.'">aquí</a><br><br>'.
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
                            ->setSubject('Han rechazado tu invitación')
                            ->setFrom('gestionIPre@ing.puc.cl')
                            ->setTo(array($user->getEmail()))
                            ->setBody('<html>' .
                                ' <head></head>' .
                                ' <body>' .
                                ' Hola, '.$invitation->getReciever()->getFullName().' ha rechazado la invitación de '.$invitation->getSender()->getFullName().' para unirse a '.$team->getName().'. <br>Para ver el equipo, haga clíck <a href="'.$url.'">aquí</a><br><br>'.
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
                if($invitation->getReciever()->hasTeam())
                {
                    foreach ($invitation->getReciever()->getTeam()->getUsers() as $member) 
                    {
                        //enviar email
                        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
                        $url = $baseurl.'/invitation/'.$invitation->getId();
                        $message = \Swift_Message::newInstance()
                            ->setSubject('Te han vuelto a invitar a un equipo!')
                            ->setFrom('gestionIPre@ing.puc.cl')
                            ->setTo(array($member->getEmail()))
                            ->setBody('<html>' .
                                ' <head></head>' .
                                ' <body>' .
                                ' Hola, '.$invitation->getReciever()->getFullName().' ha aceptado la invitación para unirse a '.$team->getName().', basta con que uno de los miembros de tu actual equipo acepte para que todos los miembros de tu equipo anterior sean unido al nuevo equipo. <br>Para ver la invitación, haga clíck <a href="'.$url.'">aquí</a><br><br>'.
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
                    $url = $baseurl.'/invitation/'.$invitation->getId();
                    $message = \Swift_Message::newInstance()
                        ->setSubject('Te han vuelto a invitar a un equipo!')
                        ->setFrom('gestionIPre@ing.puc.cl')
                        ->setTo(array($member->getEmail()))
                        ->setBody('<html>' .
                            ' <head></head>' .
                            ' <body>' .
                            ' Hola, '.$invitation->getReciever()->getFullName().' ha aceptado la invitación para unirse a '.$team->getName().'. <br>Para ver la invitación, haga clíck <a href="'.$url.'">aquí</a><br><br>'.
                            ' TeamUp'.
                            '</html>',
                            'text/html')
                    ;
                    $this->get('mailer')->send($message);
                }
                break;
        }

        return $this->redirectToRoute('invitation_show', array('id' => $invitation->getId()));
    }
}
