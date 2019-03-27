<?php
/**
 * Created by PhpStorm.
 * User: josip
 * Date: 19.02.19.
 * Time: 09:00
 */

namespace App\Form;

use App\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use App\Form\BookGenreFormType;

/**
 * Class BookFormType
 *
 * @package App\Form
 */
class BookFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('author')
            ->add(
                'bookGenre',
                CollectionType::class,
                [
                    'entry_type' => BookGenreFormType::class,
                    'entry_options' => ['label' => false],
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'label' => false,
                    'constraints' => array(
                        new Count(
                            array(
                            'min' => 1,
                            'minMessage' => 'At least 1 choice is required',
                            )
                        ),
                    ),]
            )
            ->add('summary')
            ->add(
                'images',
                FileType::class,
                [
                    'required' => false,
                    'multiple' => true,

                ]
            )
            ->add(
                'quantity',
                IntegerType::class,
                [
                    'required' => true
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Book::class,
            ]
        );
    }
}
