<?php
// src/BackendBundle/Command/GreetCommand.php
namespace TeamupBundle\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TeamupBundle\Entity\User;
use TeamupBundle\Entity\Restorer;

class SeedAdminCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('seed:admin')
            ->setDescription('Crea el admin')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {      
        $em = $this->getContainer()->get('doctrine')->getManager();

        $user = new User();
        $user->setName("Erick");
        $user->setLastname("Svec");
        $user->setRut(16094040);
        $user->setEmail("evsvec@uc.cl");
        $user->setBrief("Administrador");
        $user->setRole("ROLE_ADMIN");
        $user->setIsActive(false);
        $user->setOccupation('Administrador');

        $user->setPassword('aGxJEnmQOYy');

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

        $url = '/activeAccount?token='.$random;

        $em->persist($user);
        $em->flush();

        $message = \Swift_Message::newInstance()
            ->setSubject('Bienvenido a Team Up!')
            ->setFrom('gestionIPre@ing.puc.cl')
            ->setTo(array($user->getEmail()))
            ->setBody('<html>' .
                ' <head></head>' .
                ' <body>' .
                'Hola, has sido agregado como administrador'.
                'Usa este link para terminar tu inscripción y generar tu contraseña: ' .
                '<a href="'.$url.'">'.$url.'</a></br></br>'.
                '(No responda este email)</body>' .
                '</html>',
                'text/html')
        ;
        $this->get('mailer')->send($message);

        
        $em->persist($user);
        $em->flush();
        echo "listo!\n\r";
    }
}