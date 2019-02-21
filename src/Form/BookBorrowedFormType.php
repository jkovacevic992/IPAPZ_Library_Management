<?php
/**
 * Created by PhpStorm.
 * User: josip
 * Date: 20.02.19.
 * Time: 14:32
 */

namespace App\Form;


use App\Entity\Book;
use App\Entity\BorrowedBooks;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookBorrowedFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('book', EntityType::class,[
            'class' => Book::class,
            'choice_label' => 'name',

        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BorrowedBooks::class,
        ]);
    }
}