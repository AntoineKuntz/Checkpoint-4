<?php

namespace App\Form;

use App\Entity\Blog;
use App\Entity\Keyword;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use\Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class EditBlogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('title')
        ->add('content', CKEditorType::class)
        ->add('Keyword', EntityType::class,[
            'class' => Keyword::class,
            'label' => 'Mot-Clés',
            'multiple' => true,
            'expanded' => true

        ])
        ->add('Category', EntityType::class,[
            'class' => Category::class,
            'label' => 'Catégories',
            'multiple' => true,
            'expanded' => true

        ])
    ;
}

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Blog::class,
        ]);
    }
}
