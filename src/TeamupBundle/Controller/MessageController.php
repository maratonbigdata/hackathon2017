<?php

namespace TeamupBundle\Controller;

use TeamupBundle\Entity\Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use TeamupBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;

/**
 * Message controller.
 *
 * @Route("message")
 */
class MessageController extends Controller
{
    /**
     * Lists all message entities.
     *
     * @Route("/", name="message_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();
        $usersWithMessages = $em->getRepository('TeamupBundle:Message')->usersWithMessages($currentUser);

        return $this->render('message/index.html.twig', array(
            'usersWithMessages' => $usersWithMessages,
        ));
    }

    /**
     * Creates a new message entity.
     *
     * @Route("/new", name="message_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $message = new Message();
        $form = $this->createForm('TeamupBundle\Form\MessageType', $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();

            return $this->redirectToRoute('message_show', array('id' => $message->getId()));
        }

        return $this->render('message/new.html.twig', array(
            'message' => $message,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a message entity.
     *
     * @Route("/{id}", name="message_show")
     * @Method("GET")
     */
    public function showAction(Message $message)
    {
        $deleteForm = $this->createDeleteForm($message);

        return $this->render('message/show.html.twig', array(
            'message' => $message,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing message entity.
     *
     * @Route("/{id}/edit", name="message_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Message $message)
    {
        $deleteForm = $this->createDeleteForm($message);
        $editForm = $this->createForm('TeamupBundle\Form\MessageType', $message);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('message_edit', array('id' => $message->getId()));
        }

        return $this->render('message/edit.html.twig', array(
            'message' => $message,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a message entity.
     *
     * @Route("/{id}", name="message_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Message $message)
    {
        $form = $this->createDeleteForm($message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($message);
            $em->flush();
        }

        return $this->redirectToRoute('message_index');
    }

    /**
     * Creates a form to delete a message entity.
     *
     * @param Message $message The message entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Message $message)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('message_delete', array('id' => $message->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Finds and displays a chat entity.
     *
     * @Route("/{id}/chat", name="chat")
     * @Method({"GET", "POST"})
     */
    public function chatAction(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        $messages = $em->getRepository('TeamupBundle:Message')->chatMessages($user, $currentUser);

        $message = new Message();
        $form = $this->createForm('TeamupBundle\Form\MessageType', $message);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $em = $this->getDoctrine()->getManager();
            $message->setSender($currentUser);
            $message->setReciever($user);
            $message->setSendedTimestamp(new \DateTime());
            $em->persist($message);
            $em->flush();
        }

        $message = new Message();

        $lastId = 0;
        if(count($messages) > 0 )
        {
            $lastId = end($messages)->getId();
        }

        return $this->render('message/chat.html.twig', array(
            'user' => $user,
            'lastId' => $lastId,
            'messages' => $messages,
            'form' => $form->createView(),
        ));
    }

    /**
     * Ajax, update messages.
     *
     * @Route("/ajax/{message}/{user}/update_messages", name="ajax_update_messages")
     * @Method({"GET", "POST"})
     */
    public function updateMessagesAction(Request $request, Message $message, User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        $messages = $em->getRepository('TeamupBundle:Message')->findLatest($message, $user, $currentUser);

        //prepare the response, e.g.
        $response = array();
        foreach ($messages as $message)
        {
            $sender = 1;
            if($message->getSender()->getId() == $user->getId())
            {
                $sender = 0;
            }

            array_push($response, array('id' => $message->getId(), 'txt' => $message->getMsg(), 'time' => $message->getSendedTimestamp()->format('Y-m-d H:i:s'), 'sender' => $sender, 'initials' => $message->getSender()->getInitials() ));
        }

        //you can return result as JSON
        return new Response(json_encode($response)); 
    }
}
