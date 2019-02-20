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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BorrowedFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('borrowDate')
            ->add('returnDate')
            ->add('customer', EntityType::class,[
                'class' => Customer::class,
                'choice_label' => 'firstName'
            ]);
        $builder->add('books', CollectionType::class, [
            'entry_type' => BookBorrowedFormType::class,
            'entry_options' => ['label' => false],
            'allow_add' => true,
            'by_reference' => false,
            'label' => false,

        ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Borrowed::class,
        ]);
    }
}