<?php

namespace TeamupBundle\Controller;

use TeamupBundle\Entity\Petition;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use TeamupBundle\Entity\User;

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

        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        $petitionsSended = $em->getRepository('TeamupBundle:Petition')->findBySender($currentUser);

        $petitionsRecieved = $em->getRepository('TeamupBundle:Petition')->findByReciever($currentUser);

        return $this->render('petition/index.html.twig', array(
            'petitionsSended' => $petitionsSended,
            'petitionsRecieved' => $petitionsRecieved,
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

        if(!($user->hasTeam() && $user->getTeam()->getStatus() == 1))
        {
            return $this->redirectToRoute('home');
        }
        if(!($currentUser->hasTeam() && $currentUser->getTeam()->getStatus() == 1))
        {
            return $this->redirectToRoute('home');
        }

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
                    ' Hola, un participante les ha solicitado a unirse a su equipo. <br>Para ver la solicitud, haga cl?k <a href="'.$url.'">aqu?/a><br><br>'.
                    'Recuerden que basta con que un miembro acepte la petici?<br><br>'.
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
                ->setSubject('Nueva Solicitud!')
                ->setFrom('gestionIPre@ing.puc.cl')
                ->setTo(array($user->getEmail()))
                ->setBody('<html>' .
                    ' <head></head>' .
                    ' <body>' .
                    ' Hola, un participante le ha solicitado a unirse a un equipo. <br>Para ver la solicitud, haga cl?k <a href="'.$url.'">aqu?/a><br><br>'.
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
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        if(!($petition->getSender()->getId() == $currentUser->getId() || $petition->getReciever()->getId() == $currentUser->getId()) )
        {
            return $this->redirectToRoute('home');
        }

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
        if(!($petition->getSender()->getId() == $currentUser->getId() || $petition->getReciever()->getId() == $currentUser->getId()) )
        {
            return $this->redirectToRoute('home');
        }

        if($petition->getState() == $state || $state == 1 || $petition->getState() == 5)
        {
            return $this->redirectToRoute('petition_show', array('id' => $petition->getId()));    
        }
        
        $em = $this->getDoctrine()->getManager();
        $petition->setState($state);
        $em->persist($petition);
        $em->flush();

        $recievedTeam = $petition->getReciever()->GetTeam();

        if($petition->getReciever()->hasTeam())
        {
            $petitionsTeam = $em->getRepository('TeamupBundle:Petition')->findOthersOfSameSenderTeam($petition);

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
                //acciones sended
                if($petition->getSender()->hasTeam())
                {
                    foreach ($petition->getSender()->getTeam()->getUsers() as $member) 
                    {
                        //agregar al equipo
                        $member->setTeam($recievedTeam);
                        $em->persist($member);
                        $em->flush();

                        //enviar email
                        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
                        $url = $baseurl.'/team/'.$recievedTeam->getId();
                        $message = \Swift_Message::newInstance()
                            ->setSubject('Han sido aceptados en el equipo '.$recievedTeam->getName())
                            ->setFrom('gestionIPre@ing.puc.cl')
                            ->setTo(array($member->getEmail()))
                            ->setBody('<html>' .
                                ' <head></head>' .
                                ' <body>' .
                                ' Hola, '.$petition->getReciever()->getFullName().' ha aceptado la solicitud para unirse a '.$recievedTeam->getName().', todos los miembros de tu equipo se han unido en conjunto. <br>Para ver tu nuevo equipo, haga clíck <a href="'.$url.'">aquí</a><br><br>'.
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
                    $sender = $petition->getSender();
                    $sender->setTeam($recievedTeam);
                    $em->persist($sender);
                    $em->flush();
                }

                //acciones recivier
                foreach ($petition->getReciever()->getTeam()->getUsers() as $user) 
                {
                    if($petition->getSender()->hasTeam())
                    {
                        //enviar email
                        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
                        $url = $baseurl.'/team/'.$recievedTeam->getId();
                        $message = \Swift_Message::newInstance()
                            ->setSubject('Han aceptado tu solicitud!')
                            ->setFrom('gestionIPre@ing.puc.cl')
                            ->setTo(array($user->getEmail()))
                            ->setBody('<html>' .
                                ' <head></head>' .
                                ' <body>' .
                                ' Hola, '.$petition->getReciever()->getFullName().' ha aceptado la solicitud de '.$petition->getSender()->getFullName().' para unirse a tu equipo '.$recievedTeam->getName().'. El usuario y sus miembros se han unido a '.$recievedTeam->getName().'. <br>Para ver el equipo, haga clíck <a href="'.$url.'">aquí</a><br><br>'.
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
                        $url = $baseurl.'/team/'.$recievedTeam->getId();
                        $message = \Swift_Message::newInstance()
                            ->setSubject('Han aceptado tu solicitud!')
                            ->setFrom('gestionIPre@ing.puc.cl')
                            ->setTo(array($user->getEmail()))
                            ->setBody('<html>' .
                                ' <head></head>' .
                                ' <body>' .
                                ' Hola, '.$petition->getReciever()->getFullName().' ha aceptado la solicitud de '.$petition->getSender()->getFullName().' para unirse a tu equipo '.$recievedTeam->getName().'y ha sido agregado su equipo. <br>Para ver el equipo, haga clíck <a href="'.$url.'">aquí</a><br><br>'.
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
                    if($petition->getSender()->hasTeam())
                    {
                        //enviar email
                        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
                        $url = $baseurl.'/team/'.$petition->getSender()->getTeam()->getId();
                        $message = \Swift_Message::newInstance()
                            ->setSubject('Han rechazado tu solicitud')
                            ->setFrom('gestionIPre@ing.puc.cl')
                            ->setTo(array($user->getEmail()))
                            ->setBody('<html>' .
                                ' <head></head>' .
                                ' <body>' .
                                ' Hola, '.$petition->getReciever()->getFullName().' ha rechazado la solicitud de '.$petition->getSender()->getFullName().' para unirse a '.$recievedTeam->getName().'. Ningún miembros de tu equipo se han unido al suyo.<br>Para ver tu equipo, haz clíck <a href="'.$url.'">aquí</a><br><br>'.
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
                        $url = $baseurl.'/team/'.$recievedTeam->getId();
                        $message = \Swift_Message::newInstance()
                            ->setSubject('Han rechazado tu solicitud')
                            ->setFrom('gestionIPre@ing.puc.cl')
                            ->setTo(array($user->getEmail()))
                            ->setBody('<html>' .
                                ' <head></head>' .
                                ' <body>' .
                                ' Hola, '.$petition->getReciever()->getFullName().' ha rechazado la solicitud de '.$petition->getSender()->getFullName().' para unirse a '.$recievedTeam->getName().'<br><br>'.
                                ' TeamUp'.
                                '</html>',
                                'text/html')
                        ;
                        $this->get('mailer')->send($message);
                    }
                }
                break;
        }

        return $this->redirectToRoute('petition_show', array('id' => $petition->getId()));
    }
}
