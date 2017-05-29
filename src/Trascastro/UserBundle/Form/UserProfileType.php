<?php

namespace Trascastro\UserBundle\Form;

use AppBundle\Form\RevistaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('categoria', EntityType::class, array(
                'class' => 'AppBundle:Categoria',
                'choice_label' => 'nombre',

            ))
            ->add('imageFile', VichImageType::class,[
                'required'=> false,
                'allow_delete'=>true,
                'download_link'=>false,
            ])
            ->add('descripcion', TextType::class)

            ->add('revista', RevistaType::class);

        ;

    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';

    }

    public function getBlockPrefix()
    {
        return 'user_bundle_user_profile_type';
    }


}
