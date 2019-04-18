<?php

namespace App\Form;

use App\Entity\Holiday;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HolidayFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'startDate',
                DateType::class,
                [
                    'label' => 'Start Date',
                    'widget' => 'single_text',
                    'attr' => ['class' => 'js-datepicker']
                ]
            )->add('endDate',
                DateType::class,
                [
                    'label' => 'End Date',
                    'widget' => 'single_text',
                    'attr' => ['class' => 'js-datepicker']
                ]);

        if($options['standalone'] == true) {
            $builder->add("submit", SubmitType::class);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Holiday::class,
            'standalone' => false
        ]);
    }
}
