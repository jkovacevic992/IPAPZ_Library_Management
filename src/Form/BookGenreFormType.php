<?php
/**
 * Created by PhpStorm.
 * User: inchoo
 * Date: 3/13/19
 * Time: 8:12 AM
 */

namespace App\Form;

use App\Entity\BookGenre;
use App\Entity\Genre;
use App\Repository\GenreRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookGenreFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'genre',
            EntityType::class,
            [
                'class' => Genre::class,
                'choice_label' => 'name',
                'query_builder' => function (GenreRepository $genreRepository) {
                    return $genreRepository->getAvailableGenres();
                }

            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => BookGenre::class,
            ]
        );
    }
}
