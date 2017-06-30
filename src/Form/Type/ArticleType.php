<?php

namespace Alaska\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints as Assert;

class ArticleType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('title', TextType::class, array(
                    'label'       => 'Titre',

                    'required'    => true,
                    'constraints' => array(
                        new Assert\NotBlank(), 
                        new Assert\Length(array(
                        'min' => 5,'max' => 100,
                        'minMessage' => 'Le titre doit faire au moins 5 lettres',
                        'maxMessage' => 'Le titre doit faire au plus 100 lettres'
                        ))),
                ))
                ->add('content', TextareaType::class, array(
                    'label'       => 'Contenu',
                    'label_attr' => array('id' => 'editor'),
                    'required'    => true,
                    'constraints' => new Assert\NotBlank(array('message' => 'Votre article n\a pas de contenu'))))

                ->add('visible', CheckboxType::class, array(
                    'label'       => 'Publier',
                    'required'    => false,
                    ))

                ->add('chapter', NumberType::class, array(
                    'label'       => 'Chapitre',
                    'required'    => true,
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Range(array(
                            'min' => 1,
                            'minMessage' => 'Le numéro du chapitre doit être au moins de 1')
                        ))))
        ;
    }

    public function getName() {
        return 'article';
    }

}
