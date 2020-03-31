<?php


namespace App\Model\Currency\Graph;


use App\Repository\CurrencyRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Форма для построения графика
 * @package App\Model\Currency\Graph
 */
class Form extends AbstractType
{
    private $cr;

    public function __construct(CurrencyRepository $cr)
    {
        $this->cr = $cr;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $arr = $this->cr->findNumCodes();
        if ($arr) {
            foreach ($arr as $item) {
                $valutes[$item['char_code']] = $item['num_code'];
            }
        }

        $builder
            ->add('num_code', ChoiceType::class, [
                'choices' => $valutes,
                'required' => true,
                'placeholder' => 'Код валюты',
            ])
            ->add('date_from', DateType::class, ['required' => true, 'attr' => [
                'placeholder' => 'Дата с',
            ]])
            ->add('date_till', DateType::class, ['required' => true, 'attr' => [
                'placeholder' => 'Дата по',
            ]]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Filter::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}