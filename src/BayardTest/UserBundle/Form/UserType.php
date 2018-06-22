<?php

namespace BayardTest\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username')
            ->add('password')
            ->add(
                'roles',
                ChoiceType::class,
                array(
                    'choices'   => array(
                        'ROLE_USER'   => 'ROLE_USER',
                        'ROLE_AUTEUR'    => 'ROLE_AUTEUR',
                        'ROLE_MODERATEUR'   => 'ROLE_MODERATEUR',
                        'ROLE_ADMIN'    => 'ROLE_ADMIN',
                        'ROLE_SUPER_ADMIN'    => 'ROLE_SUPER_ADMIN'
                ),
                'multiple'  => true,
                'expanded'  => true
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BayardTest\UserBundle\Entity\User'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'bayardtest_userbundle_user';
    }
}
