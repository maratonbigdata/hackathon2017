<?php
// src/BackendBundle/Command/GreetCommand.php
namespace TeamupBundle\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TeamupBundle\Entity\Interest;

class SeedInterestsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('seed:interests')
            ->setDescription('Crea los intereses en la base de datos')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {      
        $em = $this->getContainer()->get('doctrine')->getManager();

        $interest = new Interest();
        $interest->setName('Construcción');
        $em->persist($interest);
        $em->flush();

        $interest = new Interest();
        $interest->setName('Biología');
        $em->persist($interest);
        $em->flush();

        $interest = new Interest();
        $interest->setName('Seguros');
        $em->persist($interest);
        $em->flush();

        $interest = new Interest();
        $interest->setName('Salud y Belleza');
        $em->persist($interest);
        $em->flush();

        $interest = new Interest();
        $interest->setName('Veterinaria y Mascotas');
        $em->persist($interest);
        $em->flush();

        $interest = new Interest();
        $interest->setName('Administración');
        $em->persist($interest);
        $em->flush();

        $interest = new Interest();
        $interest->setName('Ventas');
        $em->persist($interest);
        $em->flush();

        $interest = new Interest();
        $interest->setName('Ingeniería');
        $em->persist($interest);
        $em->flush();

        $interest = new Interest();
        $interest->setName('Técnologías de la Información');
        $em->persist($interest);
        $em->flush();

        $interest = new Interest();
        $interest->setName('Contabilidad');
        $em->persist($interest);
        $em->flush();

        $interest = new Interest();
        $interest->setName('Logística, Transporte y Distribución');
        $em->persist($interest);
        $em->flush();

        $interest = new Interest();
        $interest->setName('Recursos Humanos');
        $em->persist($interest);
        $em->flush();

        $interest = new Interest();
        $interest->setName('Mercadotecnia y Publicidad');
        $em->persist($interest);
        $em->flush();

        $interest = new Interest();
        $interest->setName('Leyes y Derecho');
        $em->persist($interest);
        $em->flush();

        $interest = new Interest();
        $interest->setName('Diseño');
        $em->persist($interest);
        $em->flush();

        $interest = new Interest();
        $interest->setName('Educación');
        $em->persist($interest);
        $em->flush();

        $interest = new Interest();
        $interest->setName('Turismo, Hotelería');
        $em->persist($interest);
        $em->flush();

        $interest = new Interest();
        $interest->setName('Gastronomía');
        $em->persist($interest);
        $em->flush();

        $interest = new Interest();
        $interest->setName('Salud/Medicina');
        $em->persist($interest);
        $em->flush();

        $interest = new Interest();
        $interest->setName('Comunicaciones');
        $em->persist($interest);
        $em->flush();

        $interest = new Interest();
        $interest->setName('Música');
        $em->persist($interest);
        $em->flush();

        $interest = new Interest();
        $interest->setName('Cine y Películas');
        $em->persist($interest);
        $em->flush();

        $interest = new Interest();
        $interest->setName('Deportes');
        $em->persist($interest);
        $em->flush();

        echo "listo!\n\r";
    }
}