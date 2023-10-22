<?php

namespace App\Form;

use App\Entity\Product;
use App\Form\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ProductType extends AbstractType
{

    private function getConfiguration(string $label, string $placeholder, array $options=[]): array
    {
        return array_merge_recursive([
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder
            ]
        ], $options
        );
    }   

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, $this->getConfiguration('Nom:','Nom du produit'))
            ->add('slug', TextType::class, $this->getConfiguration('Slug:', 'Fait automatiquement',[
                'required' => false
            ]))
            ->add('price', MoneyType::class, $this->getConfiguration('Prix:', 'Prix du produit'))
            ->add('description', TextareaType::class, $this->getConfiguration('Description', 'Description du produit'))
            ->add('type', TextType::class, $this->getConfiguration('Type:','Type du produit'))
            ->add('marque', TextType::class, $this->getConfiguration('Marque:','Marque du produit'))
            ->add('coverImage', UrlType::class, $this->getConfiguration('Url:','Url de l\'image de votre produit'))
            ->add('images', CollectionType::class, [
                'entry_type' => ImageType::class, // on vient chercher le formulaire qu'on a créer pour les images
                'allow_add' => true, // pour le data_prototype
                'allow_delete' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
