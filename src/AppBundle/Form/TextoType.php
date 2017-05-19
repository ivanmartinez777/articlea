<?php

namespace AppBundle\Form;

use AppBundle\Entity\Texto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Vich\UploaderBundle\Form\Type\VichImageType;





class TextoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titulo', TextType::class)
            ->add('cuerpo', TextareaType::class)
            ->add('tags', CollectionType::class, array(
                'entry_type' => TagType::class
            ))
            ->add('imageFile', VichImageType::class,[
                'required'=> false,
                'allow_delete'=>true,
                'download_link'=>false,
            ])
        ;

    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'=> Texto::class

        ]);
    }

    public function getName()
    {
        return 'app_bundle_text_type';
    }
}
