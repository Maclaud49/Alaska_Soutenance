<?php

namespace Alaska\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder


            ->add('passwordNew', RepeatedType::class, array(
                'type'            => PasswordType::class,
                'constraints'     => new Assert\Length(array('min' => 6,'minMessage' => 'Le mot de passe doit être contenir au moins 6 caractères.')),
                'invalid_message' => 'Le mot de passe doit être identique dans les 2 champs.',
                'options'         => array(
                    'required' => true
                ),
                'first_options'   => array(
                    'label'       => 'Votre nouveau mot de passe',
                    'required'    => true,
                    'attr'        => ['placeholder' => 'Au moins 6 caractères'],

                ),
                'second_options'  => array(
                    'label'       => 'Retapez votre nouveau mot de passe',
                    'required'    => true,
                ),
            ));
    }

    public function getName() {
        return 'changePassword';
    }

}