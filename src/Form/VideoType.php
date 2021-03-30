<?php

namespace App\Form;

use App\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('title')
//            ->add('path')
//            ->add('duration')
//            ->add('category')
//            ->add('userThatLike')
            ->add('uploadVideo', FileType::class, [
                'label' => 'Choose a file',
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'video/mp4',
                        ],
                    ]),
                    new NotBlank(),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}
