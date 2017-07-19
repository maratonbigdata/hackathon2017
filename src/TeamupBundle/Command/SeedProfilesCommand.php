<?php
// src/BackendBundle/Command/GreetCommand.php
namespace TeamupBundle\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TeamupBundle\Entity\Profile;

class SeedProfilesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('seed:profiles')
            ->setDescription('Crea los perfiles en la base de datos')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {      
        $em = $this->getContainer()->get('doctrine')->getManager();

        $profile = new Profile();
        $profile->setName('Desarrollador');
        $profile->setDescription('El desarrollador de software es un programador que se dedica a uno o más aspectos del proceso de desarrollo de software. Se trata de un ámbito más amplio de la programación.');
        $profile->setIcon('cog');
        $em->persist($profile);
        $em->flush();

        $profile = new Profile();
        $profile->setName('Diseño');
        $profile->setDescription(' Los diseñadores son responsables del desarrollo, en cuanto al proyecto, de un objeto, producto, o concepto.');
        $profile->setIcon('pencil');
        $em->persist($profile);
        $em->flush();

        $profile = new Profile();
        $profile->setName('Comunicaciones');
        $profile->setDescription('Del áea de la comunicación, ya sea en periodismo, Creación Audio Visual, etc.');
        $profile->setIcon('bullhorn');
        $em->persist($profile);
        $em->flush();

        $profile = new Profile();
        $profile->setName('Comercial');
        $profile->setDescription('Del áea de Negocios que reúne competencias en gestión estratégica y gestión operacional, mercadotecnia y negocios, aplicación de métodos cuantitativos para su trabajo y modelos matemáticos en el ámbito de los procesos, finanzas, economí y gestión.');
        $profile->setIcon('usd');
        $em->persist($profile);
        $em->flush();

        $profile = new Profile();
        $profile->setName('Otra Especialidad');
        $profile->setDescription('Alguna especialidad no identificable en las anteriores.');
        $profile->setIcon('th');
        $em->persist($profile);
        $em->flush();
        echo "listo!\n\r";
    }
}