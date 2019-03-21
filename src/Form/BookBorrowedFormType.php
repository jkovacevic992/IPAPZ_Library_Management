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
use App\Repository\BookRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookBorrowedFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'book',
            EntityType::class,
            [
                'class' => Book::class,
                'choice_label' => 'name',
                'query_builder' => function (BookRepository $bookRepository) {
                    return $bookRepository->getAvailableBooks();
                }

            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => BorrowedBooks::class,
            ]
        );
    }
}
