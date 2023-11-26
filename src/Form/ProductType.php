<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Products;
use App\Repository\CategoriesRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['required' => true, 'label' => 'Nom'])
            ->add('description', TextareaType::class, ['required' => true, 'label' => 'Description'])
            ->add('price', IntegerType::class, ['required' => true, 'label' => 'Prix'])
            ->add('quantity', IntegerType::class, ['required' => true, 'label' => 'Prix'])
            ->add('categories', EntityType::class, [
                'required' => false,
                'class' => Categories::class,
                'label' => 'Categorie',
                'query_builder' => function (CategoriesRepository $cat) {
                    return $cat->createQueryBuilder('c')->where("c.id IS NOT NULL")->orderBy('c.name', 'ASC');
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}
