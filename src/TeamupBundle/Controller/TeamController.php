<?php

namespace TeamupBundle\Controller;

use TeamupBundle\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use TeamupBundle\Entity\User;
use TeamupBundle\Entity\Restorer;

/**
 * Team controller.
 *
 * @Route("team")
 */
class TeamController extends Controller
{
    /**
     * Lists all team entities.
     *
     * @Route("/", name="team_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $teams = $em->getRepository('TeamupBundle:Team')->findAll();

        return $this->render('team/index.html.twig', array(
            'teams' => $teams,
        ));
    }

    /**
     * Lists all team entities.
     *
     * @Route("/TeamMatcher", name="teams_finder")
     * @Method("GET")
     */
    public function finderAction()
    {
        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        $teams = $em->getRepository('TeamupBundle:Team')->wantedTeams($currentUser);

        $teams_array = array();

        foreach ($teams as $team) 
        {
            array_push($teams_array, array($team,$team->getMatchScore($currentUser->getTeam())));
        }

        usort($teams_array, function ($a,$b){
            return $b[1]-$a[1];
        });

        return $this->render('team/finder.html.twig', array(
            'teams_array' => $teams_array,
        ));
    }

    /**
     * Creates a new team entity.
     *
     * @Route("/new", name="team_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();
        if(strcmp($currentUser->getRole(), "ROLE_ADMIN") != 0 && !is_null($currentUser->getTeam()) )// si no es admin y tiene un equipo creado, redirigimos
        {
            return $this->redirectToRoute('home');
        }

        $team = new Team();
        $form = $this->createForm('TeamupBundle\Form\TeamType', $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $team->setCreated(new \DateTime());
            $team->setModified(new \DateTime());
            $em = $this->getDoctrine()->getManager();

            foreach ($team->getNeededs() as $needed) 
            {
                $needed->setTeam($team);
                $em->persist($needed);
                $em->flush();
            }
            $team->setModified(new \DateTime());
            if(strcmp($currentUser->getRole(), "ROLE_ADMIN") != 0) //si no es admin
            {
                $currentUser->setTeam($team);
            }
            $em->persist($team);
            $em->flush();

            return $this->redirectToRoute('team_edit', array('id' => $team->getId()));
        }

        return $this->render('team/new.html.twig', array(
            'team' => $team,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a team entity.
     *
     * @Route("/{id}", name="team_show")
     * @Method("GET")
     */
    public function showAction(Team $team)
    {
        $deleteForm = $this->createDeleteForm($team);

        return $this->render('team/show.html.twig', array(
            'team' => $team,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing team entity.
     *
     * @Route("/{id}/edit", name="team_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Team $team)
    {
        $originalNeededs = new ArrayCollection();

        foreach ($team->getNeededs() as $needed) 
        {
            $originalNeededs->add($needed);
        }

        $deleteForm = $this->createDeleteForm($team);
        $editForm = $this->createForm('TeamupBundle\Form\TeamType', $team);
        $editForm->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($editForm->isSubmitted() && $editForm->isValid()) 
        {
            foreach ($originalNeededs as $needed) 
            {
                if(false === $team->getNeededs()->contains($needed))
                {
                    $needed->setTeam(null);
                    $em->remove($needed);
                    $em->flush();
                }
            }

            foreach ($team->getNeededs() as $needed) 
            {
                $needed->setTeam($team);
                $em->persist($needed);
                $em->flush();
            }
            $team->setModified(new \DateTime());
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('team_edit', array('id' => $team->getId()));
        }

        return $this->render('team/edit.html.twig', array(
            'team' => $team,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a team entity.
     *
     * @Route("/{id}", name="team_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Team $team)
    {
        $form = $this->createDeleteForm($team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($team);
            $em->flush();
        }

        return $this->redirectToRoute('team_index');
    }

    /**
     * Creates a form to delete a team entity.
     *
     * @param Team $team The team entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Team $team)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('team_delete', array('id' => $team->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Displays a form to edit users from an existing team entity.
     *
     * @Route("/{id}/editUsers", name="team_users_edit")
     * @Method({"GET", "POST"})
     */
    public function editUsersAction(Request $request, Team $team)
    {
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $user = new User();
        $form = $this->createForm('TeamupBundle\Form\UserType', $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            try
            {
                $user->setTeam($team);
                $user->setIsActive(false);
                $user->setRole('ROLE_USER');

                // Encode the password (you could also do this via Doctrine listener)
                $password = $this->get('security.password_encoder')
                    ->encodePassword($user, 'aGxJEnmQOYy');
                $user->setPassword($password);

                $rb = uniqid(rand(), true);
                $random = md5($user->getEmail().$rb);

                $restorer = $em->getRepository('TeamupBundle:Restorer')->findOneByUser($user);
                if(is_null($restorer))
                {
                    $restorer = new Restorer();
                }

                $restorer->setUser($user);
                $restorer->setTime(new \DateTime());
                $restorer->setAuth(md5($random));
                $em->persist($restorer);
                $em->flush();

                $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
                $url = $baseurl.'/activeAccount?token='.$random;

                $em->persist($user);
                $em->flush();

                $message = \Swift_Message::newInstance()
                    ->setSubject('Bienvenido a Team Up!')
                    ->setFrom('gestionIPre@ing.puc.cl')
                    ->setTo(array($user->getEmail()))
                    ->setBody('<html>' .
                        ' <head></head>' .
                        ' <body>' .
                        'Hola, '.$currentUser->getFullName().' te ha agregado a su equipo. </br>'.
                        'Usa este link para terminar tu inscripción y generar tu contraseña: ' .
                        '<a href="'.$url.'">'.$url.'</a></br></br>'.
                        '(No responda este email)</body>' .
                        '</html>',
                        'text/html')
                ;
                $this->get('mailer')->send($message);
                $user = new User();
            }
            catch(UniqueConstraintViolationException $e)
            {
                $this->addFlash(
                    'notice',
                    array(
                        'alert' => 'danger',// danger, warning, info, success
                        'title' => 'Duplicado: ',
                        'message' => 'Uno o mas datos pertenecen a un usuario existente, intente nuevamente'
                    )
                );
            }
        }

        return $this->render('team/editUsers.html.twig', array(
            'team' => $team,
            'form' => $form->createView(),
        ));
    }

    /**
     * Eliminates a user from a team.
     *
     * @Route("/{tid}-{uid}/eliminateUser", name="team_eliminate_user")
     * @Method({"GET", "POST"})
     */
    public function eliminateAction($tid, $uid)
    {
        $em = $this->getDoctrine()->getManager();

        $team = $em->getRepository('TeamupBundle:Team')->find($tid);
        $user = $em->getRepository('TeamupBundle:User')->find($uid);

        $user->setTeam(null);
        $em->persist($team);
        $em->flush();

        $team->removeUser($user);
        $em->persist($user);
        $em->flush();
        
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        $message = \Swift_Message::newInstance()
                    ->setSubject('Actualización TeamUp')
                    ->setFrom('gestionIPre@ing.puc.cl')
                    ->setTo(array($user->getEmail()))
                    ->setBody('<html>' .
                        ' <head></head>' .
                        ' <body>' .
                        'Hola, '.$currentUser->getFullName().' te ha elliminado del equipo. </br>'.
                        'Esperamos que esto no sea un inconveniente para participar de nuestra hackathon, ingresa a <a href="http://www.maratonbigdata.cl">TeamUp</a> y busca tu nuevo equipo!' .
                        '</br></br>'.
                        '(No responda este email)</body>' .
                        '</html>',
                        'text/html')
                ;
        $this->get('mailer')->send($message);


        return $this->redirectToRoute('team_users_edit', array('id' => $team->getId()));
    }

    /**
     * Apply team.
     *
     * @Route("/{id}/apply", name="apply_team")
     * @Method({"GET", "POST"})
     */
    public function applyAction(Team $team)
    {
        $em = $this->getDoctrine()->getManager();

        // use security $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        //eliminar todas las invitaciones enviadas
        //eliminar todas las invitaciones Recibidas

        //eliminar todas las solicitudes enviadas
        //eliminar todas las solicitudes Recibidas

        // cambiar estado

        //por cada usuario

        // enviar mensaje
        $message = \Swift_Message::newInstance()
                    ->setSubject('Actualización TeamUp')
                    ->setFrom('gestionIPre@ing.puc.cl')
                    ->setTo(array($user->getEmail()))
                    ->setBody('<html>' .
                        ' <head></head>' .
                        ' <body>' .
                        'Hola, '.$currentUser->getFullName().' te ha elliminado del equipo. </br>'.
                        'Esperamos que esto no sea un inconveniente para participar de nuestra hackathon, ingresa a <a href="http://www.maratonbigdata.cl">TeamUp</a> y busca tu nuevo equipo!' .
                        '</br></br>'.
                        '(No responda este email)</body>' .
                        '</html>',
                        'text/html')
                ;
        $this->get('mailer')->send($message);


        return $this->redirectToRoute('team_show', array('id' => $team->getId()));
    }
}
