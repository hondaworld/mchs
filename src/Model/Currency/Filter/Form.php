<?php


namespace App\Model\Currency\Filter;


use App\Repository\CurrencyRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    private $cr;

    public function __construct(CurrencyRepository $cr)
    {
        $this->cr = $cr;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $valutes = [];
        $arr = $this->cr->findNumCodes();
        if ($arr) {
            foreach ($arr as $item) {
                $valutes[$item['char_code']] = $item['num_code'];
            }
        }

        $builder
            ->add('num_code', ChoiceType::class, [
                'choices' => $valutes,
                'required' => false, 'attr' => [
                    'placeholder' => 'Код валюты',
                    'onchange' => 'this.form.submit()',
                ]])
            ->add('dateofadded', DateType::class, ['required' => false, 'attr' => [
                'placeholder' => 'Дата',
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