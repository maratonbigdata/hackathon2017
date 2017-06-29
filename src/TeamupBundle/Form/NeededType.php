<?php

namespace TeamupBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class NeededType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity', null,array('label' => 'Cantidad','attr' => array('class'=>'form-control')))
            ->add('profile', EntityType::class, array(
                'label' => 'Perfil',
                'attr' => array('class'=>'js-basic-single-profile'),
                'required' => true,
                'placeholder' => 'Seleccione un perfil',
                'class' => 'TeamupBundle:Profile',
                'choice_label' => 'name',))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TeamupBundle\Entity\Needed'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'teamupbundle_needed';
    }


}
