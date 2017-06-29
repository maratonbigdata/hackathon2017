<?php

namespace TeamupBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use TeamupBundle\Form\NeededType;

class TeamType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null,array('label' => 'Nombre','attr' => array('class'=>'form-control')))
            ->add('status', ChoiceType::class, array('label' => "Estado",'attr' => array('class'=>'form-control'),
                'choices' => array('Buscando Miembros' => '1', 'Postulado' => '2', 'Aceptado' => '3', 'No Aceptado' => '4'),
                'choices_as_values' => true))
            ->add('neededs',CollectionType::class, array(
                'entry_type'    => NeededType::class,
                'allow_add'     =>  true,
                'allow_delete'  => true,
                'prototype'    => true,
                ))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TeamupBundle\Entity\Team'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'teamupbundle_team';
    }


}
