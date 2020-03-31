<?php


namespace App\Form;


use App\Handlers\CurrencyHandler;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Форма для добавление курса валют
 * @package App\Form
 */
class CurrencyHandlerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateofadded', DateType::class, ['label' => 'Дата'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CurrencyHandler::class,
        ]);
    }
}