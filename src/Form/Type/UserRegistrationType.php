<?php

namespace Alaska\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints as Assert;

class UserRegistrationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('username', TextType::class, array(
                'label'       => "Pseudo",
                'required'    => true,
                'constraints' => new Assert\NotBlank(array('message' => 'Merci de renseigner le pseudo')),
            ))

            ->add('email', TextType::class, array(
                'label'       => "Email",
                'required'    => true,
                'constraints' => array(new Assert\Email(array('message' => 'L\'adresse email n\'est pas correcte')), new Assert\NotBlank(array('message' => 'l\'adresse email est vide'))),
            ))

            ->add('password', RepeatedType::class, array(
                'type'            => PasswordType::class,
                'constraints'     => new Assert\Length(['min' => 5]),
                'invalid_message' => 'Le mot de passe doit être identique dans les 2 champs.',
                'options'         => array(
                    'required' => true
                ),
                'first_options'   => array(
                    'label'       => 'Mot de passe',
                    'required'    => true,
                    'attr'        => ['placeholder' => 'Au moins 6 caractères'],
                ),
                'second_options'  => array(
                    'label'       => 'Retapez votre mot de passe',
                    'attr'        => ['placeholder' => 'Le même mot de passe'],
                    'required'    => true,
                ),
            ));
    }

    public function getName() {
        return 'user';
    }

}