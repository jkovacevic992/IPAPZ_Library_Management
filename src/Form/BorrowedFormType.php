<?php
/**
 * Created by PhpStorm.
 * User: ipa
 * Date: 19.02.19.
 * Time: 19:05
 */

namespace App\Form;

use App\Entity\Borrowed;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\BookBorrowedFormType;

class BorrowedFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'borrowDate',
                DateType::class,
                [
                    'widget' => 'single_text',
                    'attr' => ['class' => 'js-datepicker'],
                    'html5' => false,
                    'format' => 'dd.MM.yyyy.'
                ]
            )
            ->add(
                'returnDate',
                DateType::class,
                [
                    'widget' => 'single_text',
                    'attr' => ['class' => 'js-datepicker'],
                    'html5' => false,
                    'format' => 'dd.MM.yyyy.'
                ]
            )
            ->add(
                'user',
                EntityType::class,
                [
                    'class' => User::class,
                    'choice_label' => function ($user) {
                        /**
                         * @var User $user
                         */
                        return $user->getFirstName() . ' ' . $user->getLastName();
                    },


                ]
            )
            ->add(
                'borrowedBooks',
                CollectionType::class,
                [
                    'entry_type' => BookBorrowedFormType::class,
                    'entry_options' => ['label' => false],
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'label' => false

                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Borrowed::class,
            ]
        );
    }
}
