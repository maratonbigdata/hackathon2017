<?php

namespace TeamupBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use TeamupBundle\Entity\User;
use TeamupBundle\Entity\Restorer;
use TeamupBundle\Form\UserType;

class SecurityController extends Controller
{
	/**
     * @Route("/login", name="login_route")
     */
    public function loginAction(Request $request)
	{
	    $authenticationUtils = $this->get('security.authentication_utils');

	    // get the login error if there is one
	    $error = $authenticationUtils->getLastAuthenticationError();

	    // last username entered by the user
	    $lastUsername = $authenticationUtils->getLastUsername();

	    return $this->render(
	        'security/login.html.twig',
	        array(
	            // last username entered by the user
	            'last_username' => $lastUsername,
	            'error'         => $error
	        )
	    );
	}

    /**
     * Creates a new User entity by signin.
     *
     * @Route("/signed", name="sign_done")
     * @Method({"GET", "POST"})
     */
    public function signedAction(Request $request)
    {
        return $this->render('security/signed.html.twig', array(
        ));
    }

	/**
     * @Route("/resetPassword", name="reset_password_route")
     * @Method({"GET", "POST"})
     */
    public function resetPasswordAction(Request $request)
	{
	    $em = $this->getDoctrine()->getManager();

	    $token = $request->query->get('token');

	    $restorer = $em->getRepository('TeamupBundle:Restorer')->findOneByAuth(md5($token));

	    if(is_null($restorer))//we finished and redirect, no output
	    {
	    	return $this->redirectToRoute('home');
	    }

	    $user = $restorer->getUser();

	    $resetForm = $this->createFormBuilder($user)
	    	->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options'  => array('label' => 'Contraseña','attr' => array('disabled' => false, 'class'=>'form-control')),
                'second_options' => array('label' => 'Repita Contraseña','attr' => array('disabled' => false, 'class'=>'form-control')),
                ))
            ->getForm();
        $form = $request->get('form');

	    if (!is_null($form) && $form["plainPassword"]["first"] == $form["plainPassword"]["second"]) 
	    {
	    	$password = $this->get('security.password_encoder')
                ->encodePassword($user, $form["plainPassword"]["first"]);
            $user->setPassword($password);
            $user->setIsActive(true);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->remove($restorer);
            $em->flush();

            return $this->redirectToRoute('home');
	    }

	    return $this->render(
	        'security/resetPassword.html.twig',
	        array(
	        	'reset_form' => $resetForm->createView(),
	        )
	    );
	}

    /**
     * @Route("/activeAccount", name="active-account_route")
     * @Method({"GET", "POST"})
     */
    public function activeAccountAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $token = $request->query->get('token');

        $restorer = $em->getRepository('TeamupBundle:Restorer')->findOneByAuth(md5($token));

        if(is_null($restorer))//we finished and redirect, no output
        {
            return $this->redirectToRoute('backend_homepage');
        }

        $user = $restorer->getUser();

        $resetForm = $this->createFormBuilder($user)
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options'  => array('label' => 'Contraseña','attr' => array('disabled' => false, 'class'=>'form-control')),
                'second_options' => array('label' => 'Repita Contraseña','attr' => array('disabled' => false, 'class'=>'form-control')),
                ))
            ->getForm();
        $form = $request->get('form');

        if (!is_null($form) && $form["plainPassword"]["first"] == $form["plainPassword"]["second"]) 
        {
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $form["plainPassword"]["first"]);
            $user->setPassword($password);

            $user->setIsActive(true);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->remove($restorer);
            $em->flush();

            return $this->redirectToRoute('backend_homepage');
        }

        return $this->render(
            'security/activeAccount.html.twig',
            array(
                'reset_form' => $resetForm->createView(),
            )
        );
    }


	/**
     * @Route("/lostPassword", name="lost_password_route")
     */
    public function lostPasswordAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();

	    $reset = false;
	    if($request->request->get('reset') == "true")
	    	$reset = true;

	    if($reset)
	    {
	    	$user = $em->getRepository('TeamupBundle:User')->findOneByEmail($request->request->get('email'));
	    	if(!is_null($user))
	    	{
	    		$rb = uniqid(rand(), true);
	    		$random = md5($user->getEmail().$rb);

	    		//guardar en la base de datos
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
	    		$url = $baseurl.'/resetPassword?token='.$random;

	    		$message = \Swift_Message::newInstance()
					->setSubject('Recuperación de contraseña')
					->setFrom('gestionIPre@ing.puc.cl')
					->setTo(array($user->getEmail()))
					->setBody('<html>' .
					    ' <head></head>' .
					    ' <body>' .
					    ' Hola, usa este link para recuperar tu contraseña: ' .
					    '<a href="'.$url.'">'.$url.'</a>'.
                        'Este link funciona solo una vez, si tiene problemas pide una nueva recuperación.</br>'.
					    ' Si no pediste recuperar contraseña omite este email. (No responda este email)</body>' .
					    '</html>',
					    'text/html')
				;
				$this->get('mailer')->send($message);
	    	}
	    }


	    return $this->render(
	        'security/lostPassword.html.twig',	
	        array(
	            // last username entered by the user
	            'reset' => $reset,
	        )
	    );
	}

    /**
     * @Route("/login_check", name="login_check")
     */
    public function loginCheckAction()
    {
        // this controller will not be executed,
        // as the route is handled by the Security system
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {
        $this->container->get('security.context')->setToken(null);

        return $this->redirect($this->generateUrl('login_route'));
    }

    /**
     * Creates a new User entity by signup.
     *
     * @Route("/signUp", name="signUp_route")
     * @Method({"GET", "POST"})
     */
    public function signUp(Request $request)
    {
        if($this->get('security.token_storage')->getToken()->getUser() != "anon.")
            return $this->redirectToRoute('teamup_homepage');

        $user = new User();

        $user->setRole('ROLE_USER');

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new UserType($em),$user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try
            {
                $user->setIsActive(false);
                $user->setRole('ROLE_USER');

                // Encode the password (you could also do this via Doctrine listener)
                $password = $this->get('security.password_encoder')
                    ->encodePassword($user, 'aGxJEnmQOYy');
                $user->setPassword($password);

                $em = $this->getDoctrine()->getManager();

                $rb = uniqid(rand(), true);
                $random = md5($user->getEmail().$rb);

                //guardar en la base de datos
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

                $message = \Swift_Message::newInstance()
                    ->setSubject('Bienvenido a Team Up, Activa tu cuenta')
                    ->setFrom('gestionIPre@ing.puc.cl')
                    ->setTo(array($user->getEmail()))
                    ->setBody('<html>' .
                        ' <head></head>' .
                        ' <body>' .
                        ' Hola, usa este link para terminar tu inscripción y generar tu contraseña: ' .
                        '<a href="'.$url.'">'.$url.'</a></br>'.
                        ' Si no pediste una cuenta nueva, te invitamos a conocernos. (No responda este email)</body>' .
                        '</html>',
                        'text/html')
                ;
                $this->get('mailer')->send($message);
                //////
            }
            catch(UniqueConstraintViolationException $e)
            {
                $this->addFlash(
                    'notice',
                    array(
                        'alert' => 'danger',// danger, warning, info, success
                        'title' => 'Duplicado: ',
                        'message' => 'Uno o mas datos pertenecen a un usuario existente, intente nuevamente o Inicie Sesión'
                    )
                );

                return $this->redirectToRoute('signIn_route');
            } 

            return $this->redirectToRoute('sign_done', array('id' => $user->getId()));
        }

        return $this->render('security/signUp.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

}
