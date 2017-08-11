<?php

namespace TeamupBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserSimpleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null,array('label' => 'Nombre','attr' => array('class'=>'form-control')))
            ->add('lastname', null,array('label' => 'Apellido','attr' => array('class'=>'form-control')))
            ->add('email', EmailType::class,array('label' => 'Email','attr' => array('class'=>'form-control')))
            ->add('profile', EntityType::class, array(
                'label' => 'Perfil',
                'attr' => array('class'=>'js-basic-single-profile'),
                'required' => true,
                'placeholder' => 'Seleccione un perfil',
                'class' => 'TeamupBundle:Profile',
                'choice_label' => 'name',))
            ->add('occupation', ChoiceType::class, array(
                'label' => 'Ocupación',
                'choices'  => array('Estudiante Pregrado' => 'Estudiante Pregrado', 'Estudiante Postgrado' => 'Estudiante Post-grado'),
                'placeholder' => 'Seleccione un perfil',
                'choices_as_values' => true,
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('university', ChoiceType::class, array(
                'label' => 'Universidad/IP/CFT',
                'choices'  => array(
                    'PONTIFICIA UNIVERSIDAD CATOLICA DE CHILE' => 'PONTIFICIA UNIVERSIDAD CATOLICA DE CHILE',
                    'PONTIFICIA UNIVERSIDAD CATOLICA DE VALPARAISO' => 'PONTIFICIA UNIVERSIDAD CATOLICA DE VALPARAISO',
                    'UNIVERSIDAD ACADEMIA DE HUMANISMO CRISTIANO' => 'UNIVERSIDAD ACADEMIA DE HUMANISMO CRISTIANO',
                    'UNIVERSIDAD ADOLFO IBAÑEZ' => 'UNIVERSIDAD ADOLFO IBAÑEZ',
                    'UNIVERSIDAD ADVENTISTA DE CHILE' => 'UNIVERSIDAD ADVENTISTA DE CHILE',
                    'UNIVERSIDAD ALBERTO HURTADO' => 'UNIVERSIDAD ALBERTO HURTADO',
                    'UNIVERSIDAD ANDRES BELLO' => 'UNIVERSIDAD ANDRES BELLO',
                    'UNIVERSIDAD ARTURO PRAT' => 'UNIVERSIDAD ARTURO PRAT',
                    'UNIVERSIDAD AUSTRAL DE CHILE' => 'UNIVERSIDAD AUSTRAL DE CHILE',
                    'UNIVERSIDAD AUTONOMA DE CHILE' => 'UNIVERSIDAD AUTONOMA DE CHILE',
                    'UNIVERSIDAD BERNARDO OHIGGINS' => 'UNIVERSIDAD BERNARDO OHIGGINS',
                    'UNIVERSIDAD BOLIVARIANA' => 'UNIVERSIDAD BOLIVARIANA',
                    'UNIVERSIDAD CATOLICA DE LA SANTISIMA CONCEPCION' => 'UNIVERSIDAD CATOLICA DE LA SANTISIMA CONCEPCION',
                    'UNIVERSIDAD CATOLICA DE TEMUCO' => 'UNIVERSIDAD CATOLICA DE TEMUCO',
                    'UNIVERSIDAD CATOLICA DEL MAULE' => 'UNIVERSIDAD CATOLICA DEL MAULE',
                    'UNIVERSIDAD CATOLICA DEL NORTE' => 'UNIVERSIDAD CATOLICA DEL NORTE',
                    'UNIVERSIDAD CATOLICA SILVA HENRIQUEZ' => 'UNIVERSIDAD CATOLICA SILVA HENRIQUEZ',
                    'UNIVERSIDAD CENTRAL DE CHILE' => 'UNIVERSIDAD CENTRAL DE CHILE',
                    'UNIVERSIDAD CHILENO BRITANICA DE CULTURA' => 'UNIVERSIDAD CHILENO BRITANICA DE CULTURA',
                    'UNIVERSIDAD DE ACONCAGUA' => 'UNIVERSIDAD DE ACONCAGUA',
                    'UNIVERSIDAD DE ANTOFAGASTA' => 'UNIVERSIDAD DE ANTOFAGASTA',
                    'UNIVERSIDAD DE ARTE Y CIENCIAS SOCIALES ARCIS' => 'UNIVERSIDAD DE ARTE Y CIENCIAS SOCIALES ARCIS',
                    'UNIVERSIDAD DE ARTES, CIENCIAS Y COMUNICACION - UNIACC' => 'UNIVERSIDAD DE ARTES, CIENCIAS Y COMUNICACION - UNIACC',
                    'UNIVERSIDAD DE ATACAMA' => 'UNIVERSIDAD DE ATACAMA',
                    'UNIVERSIDAD DE AYSEN' => 'UNIVERSIDAD DE AYSEN',
                    'UNIVERSIDAD DE CONCEPCION' => 'UNIVERSIDAD DE CONCEPCION',
                    'UNIVERSIDAD DE CHILE' => 'UNIVERSIDAD DE CHILE',
                    'UNIVERSIDAD DE LA FRONTERA' => 'UNIVERSIDAD DE LA FRONTERA',
                    'UNIVERSIDAD DE LA SERENA' => 'UNIVERSIDAD DE LA SERENA',
                    'UNIVERSIDAD DE LAS AMERICAS' => 'UNIVERSIDAD DE LAS AMERICAS',
                    'UNIVERSIDAD DE LOS ANDES' => 'UNIVERSIDAD DE LOS ANDES',
                    'UNIVERSIDAD DE LOS LAGOS' => 'UNIVERSIDAD DE LOS LAGOS',
                    'UNIVERSIDAD DE MAGALLANES' => 'UNIVERSIDAD DE MAGALLANES',
                    'UNIVERSIDAD DE OHIGGINS' => 'UNIVERSIDAD DE OHIGGINS',
                    'UNIVERSIDAD DE PLAYA ANCHA DE CIENCIAS DE LA EDUCACION' => 'UNIVERSIDAD DE PLAYA ANCHA DE CIENCIAS DE LA EDUCACION',
                    'UNIVERSIDAD DE SANTIAGO DE CHILE' => 'UNIVERSIDAD DE SANTIAGO DE CHILE',
                    'UNIVERSIDAD DE TALCA' => 'UNIVERSIDAD DE TALCA',
                    'UNIVERSIDAD DE TARAPACA' => 'UNIVERSIDAD DE TARAPACA',
                    'UNIVERSIDAD DE VALPARAISO' => 'UNIVERSIDAD DE VALPARAISO',
                    'UNIVERSIDAD DE VIÑA DEL MAR' => 'UNIVERSIDAD DE VIÑA DEL MAR',
                    'UNIVERSIDAD DEL BIO-BIO' => 'UNIVERSIDAD DEL BIO-BIO',
                    'UNIVERSIDAD DEL DESARROLLO' => 'UNIVERSIDAD DEL DESARROLLO',
                    'UNIVERSIDAD DEL PACIFICO' => 'UNIVERSIDAD DEL PACIFICO',
                    'UNIVERSIDAD DIEGO PORTALES' => 'UNIVERSIDAD DIEGO PORTALES',
                    'UNIVERSIDAD FINIS TERRAE' => 'UNIVERSIDAD FINIS TERRAE',
                    'UNIVERSIDAD GABRIELA MISTRAL' => 'UNIVERSIDAD GABRIELA MISTRAL',
                    'UNIVERSIDAD IBEROAMERICANA DE CIENCIAS Y TECNOLOGIA, UNICYT' => 'UNIVERSIDAD IBEROAMERICANA DE CIENCIAS Y TECNOLOGIA, UNICYT',
                    'UNIVERSIDAD LA REPUBLICA' => 'UNIVERSIDAD LA REPUBLICA',
                    'UNIVERSIDAD LOS LEONES' => 'UNIVERSIDAD LOS LEONES',
                    'UNIVERSIDAD MAYOR' => 'UNIVERSIDAD MAYOR',
                    'UNIVERSIDAD METROPOLITANA DE CIENCIAS DE LA EDUCACION' => 'UNIVERSIDAD METROPOLITANA DE CIENCIAS DE LA EDUCACION',
                    'UNIVERSIDAD MIGUEL DE CERVANTES' => 'UNIVERSIDAD MIGUEL DE CERVANTES',
                    'UNIVERSIDAD PEDRO DE VALDIVIA' => 'UNIVERSIDAD PEDRO DE VALDIVIA',
                    'UNIVERSIDAD SAN SEBASTIAN' => 'UNIVERSIDAD SAN SEBASTIAN',
                    'UNIVERSIDAD SANTO TOMAS' => 'UNIVERSIDAD SANTO TOMAS',
                    'UNIVERSIDAD SEK' => 'UNIVERSIDAD SEK',
                    'UNIVERSIDAD TECNICA FEDERICO SANTA MARIA' => 'UNIVERSIDAD TECNICA FEDERICO SANTA MARIA',
                    'UNIVERSIDAD TECNOLOGICA DE CHILE INACAP' => 'UNIVERSIDAD TECNOLOGICA DE CHILE INACAP',
                    'UNIVERSIDAD TECNOLOGICA METROPOLITANA' => 'UNIVERSIDAD TECNOLOGICA METROPOLITANA',
                    'UNIVERSIDAD UCINF' => 'UNIVERSIDAD UCINF',
                    'IP AGRARIO ADOLFO MATTHEI' => 'IP AGRARIO ADOLFO MATTHEI',
                    'IP AIEP' => 'IP AIEP',
                    'IP CARLOS CASANUEVA' => 'IP CARLOS CASANUEVA',
                    'IP CIISA' => 'IP CIISA',
                    'IP CHILENO-BRITANICO DE CULTURA' => 'IP CHILENO-BRITANICO DE CULTURA',
                    'IP DE ARTE Y COMUNICACION ARCOS' => 'IP DE ARTE Y COMUNICACION ARCOS',
                    'IP DE ARTES ESCENICAS KAREN CONNOLLY' => 'IP DE ARTES ESCENICAS KAREN CONNOLLY',
                    'IP DE CIENCIAS DE LA COMPUTACION ACUARIO DATA' => 'IP DE CIENCIAS DE LA COMPUTACION ACUARIO DATA',
                    'IP DE CIENCIAS Y ARTES INCACEA' => 'IP DE CIENCIAS Y ARTES INCACEA',
                    'IP DE CIENCIAS Y EDUCACION HELEN KELLER' => 'IP DE CIENCIAS Y EDUCACION HELEN KELLER',
                    'IP DE CHILE' => 'IP DE CHILE',
                    'IP DEL COMERCIO' => 'IP DEL COMERCIO',
                    'IP DEL VALLE CENTRAL' => 'IP DEL VALLE CENTRAL',
                    'IP DIEGO PORTALES' => 'IP DIEGO PORTALES',
                    'IP DR. VIRGINIO GOMEZ G.' => 'IP DR. VIRGINIO GOMEZ G.',
                    'IP DUOC UC' => 'IP DUOC UC',
                    'IP EATRI INSTITUTO PROFESIONAL' => 'IP EATRI INSTITUTO PROFESIONAL',
                    'IP ESCUELA DE CINE DE CHILE' => 'IP ESCUELA DE CINE DE CHILE',
                    'IP ESCUELA DE CONTADORES AUDITORES DE SANTIAGO' => 'IP ESCUELA DE CONTADORES AUDITORES DE SANTIAGO',
                    'IP ESCUELA MODERNA DE MUSICA' => 'IP ESCUELA MODERNA DE MUSICA',
                    'IP ESUCOMEX' => 'IP ESUCOMEX',
                    'IP INACAP' => 'IP INACAP',
                    'IP INSTITUTO DE ESTUDIOS BANCARIOS GUILLERMO SUBERCASEAUX' => 'IP INSTITUTO DE ESTUDIOS BANCARIOS GUILLERMO SUBERCASEAUX',
                    'IP INSTITUTO INTERNACIONAL DE ARTES CULINARIAS Y SERVICIOS' => 'IP INSTITUTO INTERNACIONAL DE ARTES CULINARIAS Y SERVICIOS',
                    'IP INSTITUTO NACIONAL DEL FUTBOL' => 'IP INSTITUTO NACIONAL DEL FUTBOL',
                    'IP INSTITUTO SUPERIOR DE ARTES Y CIENCIAS DE LA COMUNICACION' => 'IP INSTITUTO SUPERIOR DE ARTES Y CIENCIAS DE LA COMUNICACION',
                    'IP IPG' => 'IP IPG',
                    'IP LA ARAUCANA' => 'IP LA ARAUCANA',
                    'IP LATINOAMERICANO DE COMERCIO EXTERIOR' => 'IP LATINOAMERICANO DE COMERCIO EXTERIOR',
                    'IP LIBERTADOR DE LOS ANDES' => 'IP LIBERTADOR DE LOS ANDES',
                    'IP LOS LAGOS' => 'IP LOS LAGOS',
                    'IP LOS LEONES' => 'IP LOS LEONES',
                    'IP MAR FUTURO' => 'IP MAR FUTURO',
                    'IP PROJAZZ' => 'IP PROJAZZ',
                    'IP PROVIDENCIA' => 'IP PROVIDENCIA',
                    'IP SANTO TOMAS' => 'IP SANTO TOMAS',
                    'IP VERTICAL' => 'IP VERTICAL',
                    'CFT ALFA' => 'CFT ALFA',
                    'CFT ALPES' => 'CFT ALPES',
                    'CFT ANDRES BELLO' => 'CFT ANDRES BELLO',
                    'CFT BARROS ARANA' => 'CFT BARROS ARANA',
                    'CFT CAMARA DE COMERCIO DE SANTIAGO' => 'CFT CAMARA DE COMERCIO DE SANTIAGO',
                    'CFT CENCO' => 'CFT CENCO',
                    'CFT CENTRO TECNOLOGICO SUPERIOR INFOMED' => 'CFT CENTRO TECNOLOGICO SUPERIOR INFOMED',
                    'CFT DE ENAC' => 'CFT DE ENAC',
                    'CFT DE ENSEÑANZA DE ALTA COSTURA PAULINA DIARD' => 'CFT DE ENSEÑANZA DE ALTA COSTURA PAULINA DIARD',
                    'CFT DE LA INDUSTRIA GRAFICA - INGRAF' => 'CFT DE LA INDUSTRIA GRAFICA - INGRAF',
                    'CFT DE TARAPACA' => 'CFT DE TARAPACA',
                    'CFT DEL MEDIO AMBIENTE' => 'CFT DEL MEDIO AMBIENTE',
                    'CFT EDUCAP' => 'CFT EDUCAP',
                    'CFT ESANE DEL NORTE' => 'CFT ESANE DEL NORTE',
                    'CFT ESCUELA CULINARIA FRANCESA - ECOLE' => 'CFT ESCUELA CULINARIA FRANCESA - ECOLE',
                    'CFT ESCUELA DE ARTES APLICADAS OFICIOS DEL FUEGO' => 'CFT ESCUELA DE ARTES APLICADAS OFICIOS DEL FUEGO',
                    'CFT ESTUDIO PROFESOR VALERO' => 'CFT ESTUDIO PROFESOR VALERO',
                    'CFT ICEL' => 'CFT ICEL',
                    'CFT INACAP' => 'CFT INACAP',
                    'CFT INSTITUTO CENTRAL DE CAPACITACION EDUCACIONAL ICCE' => 'CFT INSTITUTO CENTRAL DE CAPACITACION EDUCACIONAL ICCE',
                    'CFT INSTITUTO SUPERIOR ALEMAN DE COMERCIO INSALCO' => 'CFT INSTITUTO SUPERIOR ALEMAN DE COMERCIO INSALCO',
                    'CFT INSTITUTO SUPERIOR DE ESTUDIOS JURIDICOS CANON' => 'CFT INSTITUTO SUPERIOR DE ESTUDIOS JURIDICOS CANON',
                    'CFT INSTITUTO TECNOLOGICO DE CHILE - I.T.C.' => 'CFT INSTITUTO TECNOLOGICO DE CHILE - I.T.C.',
                    'CFT IPROSEC' => 'CFT IPROSEC',
                    'CFT JUAN BOHON' => 'CFT JUAN BOHON',
                    'CFT LAPLACE' => 'CFT LAPLACE',
                    'CFT LOS LAGOS' => 'CFT LOS LAGOS',
                    'CFT LOTA-ARAUCO' => 'CFT LOTA-ARAUCO',
                    'CFT LUIS ALBERTO VERA' => 'CFT LUIS ALBERTO VERA',
                    'CFT MANPOWER' => 'CFT MANPOWER',
                    'CFT MASSACHUSETTS' => 'CFT MASSACHUSETTS',
                    'CFT PROANDES' => 'CFT PROANDES',
                    'CFT PRODATA' => 'CFT PRODATA',
                    'CFT PROFASOC' => 'CFT PROFASOC',
                    'CFT PROTEC' => 'CFT PROTEC',
                    'CFT SAN AGUSTIN DE TALCA' => 'CFT SAN AGUSTIN DE TALCA',
                    'CFT SANTO TOMAS' => 'CFT SANTO TOMAS',
                    'CFT TEODORO WICKEL KLUWEN' => 'CFT TEODORO WICKEL KLUWEN',
                    'CFT UCEVALPO' => 'CFT UCEVALPO'
                ),
                'placeholder' => 'Seleccione tu institución',
                'choices_as_values' => true,
                'expanded' => false,
                'multiple' => false,
                'attr' => array('class'=>'js-basic-single-profile'),
            ))
            ->add('carrer', ChoiceType::class, array(
                'label' => 'Carrera',
                'choices'  => array(
                    'Ingeniería Civil' => 'Ingeniería Civil',
                    'Ingeniería Comercial/Negocios' => 'Ingeniería Comercial/Negocios',
                    'Diseño' => 'Diseño',
                    'Arquitectura' => 'Arquitectura',
                    'Artes/Música/Teatro' => 'Artes/Música/Teatro',
                    'Ciencias' => 'Ciencias',
                    'Psicología' => 'Psicología',
                    'Comunicaciones / Periodismo' => 'Comunicaciones / Periodismo',
                    'Sociología' => 'Sociología',
                    'Derecho' => 'Derecho',
                    'Educación' => 'Educación',
                    'Agronomía/Forestal' => 'Agronomía/Forestal',
                    'Medicina' => 'Medicina',
                    'Enfermería' => 'Enfermería',
                    'Kinesiología' => 'Kinesiología',
                    'Construcción Civil' => 'Construcción Civil',
                    'Otros' => 'Otros'
                ),
                'placeholder' => 'Seleccione tu carrera',
                'choices_as_values' => true,
                'expanded' => false,
                'multiple' => false,
                'attr' => array('class'=>'js-basic-single-profile'),
            ))

        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TeamupBundle\Entity\User',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'teamupbundle_user';
    }
}