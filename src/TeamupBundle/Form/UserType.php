<?php

namespace TeamupBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null,array('label' => 'Nombre','attr' => array('class'=>'form-control')))
            ->add('lastname', null,array('label' => 'Apellido','attr' => array('class'=>'form-control')))
            ->add('rut', null,array('label' => 'Rut (sin puntos)', 'required' => true,'attr' => array('class'=>'form-control')))
            ->add('email', EmailType::class,array('label' => 'Email','attr' => array('class'=>'form-control')))
            ->add('brief', null,array('label' => 'Resumen Personal (por ejemplo: formación, experiencia y aporte al equipo)','attr' => array('class'=>'form-control')))
            ->add('profile', EntityType::class, array(
                'label' => 'Perfil',
                'attr' => array('class'=>'js-basic-single-profile'),
                'required' => true,
                'placeholder' => 'Seleccione un perfil',
                'class' => 'TeamupBundle:Profile',
                'choice_label' => 'name',))
            ->add('interests',EntityType::class, array(
                'attr' => array('class'=>'checkboxes_list'),
                'class' => 'TeamupBundle:Interest',
                'choice_label' => 'name',
                'expanded' => true,
                'multiple' => true,
                'label' => 'Áreas de Interés',
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
