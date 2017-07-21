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

        $invitations = $em->getRepository('TeamupBundle:Invitation')->findBySender($currentUser);

        return $this->render('invitation/index.html.twig', array(
            'invitations' => $invitations,
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

        $message = \Swift_Message::newInstance()
            ->setSubject('Invitaci? a grupo')
            ->setFrom('gestionIPre@ing.puc.cl')
            ->setTo(array($user->getEmail()))
            ->setBody('<html>' .
                ' <head></head>' .
                ' <body>' .
                ' Hola, se le ha invitado a unirse a un grupo. <br>Para ver la invitación, haga clíck <a href="'.$url.'">aquí</a><br><br>'.
                ' TeamUp'.
                '</html>',
                'text/html')
        ;
        $this->get('mailer')->send($message);

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

        return $this->render('invitation/show.html.twig', array(
            'invitation' => $invitation,
        ));
    }
}
