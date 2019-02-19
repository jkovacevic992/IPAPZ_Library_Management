<?php
/**
 * Created by PhpStorm.
 * User: ipa
 * Date: 19.02.19.
 * Time: 19:05
 */

namespace App\Form;


use App\Entity\Book;
use App\Entity\Borrowed;
use App\Entity\Customer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BorrowedFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('borrowDate')
            ->add('returnDate')
            ->add('book', EntityType::class,[
                'class' => Book::class,
                'choice_label' => 'name'
            ])
            ->add('customer', EntityType::class,[
                'class' => Customer::class,
                'choice_label' => 'firstName'
            ])


        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Borrowed::class,
        ]);
    }
}