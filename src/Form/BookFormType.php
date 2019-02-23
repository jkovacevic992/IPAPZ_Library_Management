<?php
/**
 * Created by PhpStorm.
 * User: josip
 * Date: 19.02.19.
 * Time: 09:00
 */

namespace App\Form;


use App\Entity\Book;
use App\Entity\Genre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BookFormType
 * @package App\Form
 */
class BookFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('author')
            ->add('genre', EntityType::class,[
                'class' => Genre::class,
                'choice_label' => 'name'

            ])


        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}