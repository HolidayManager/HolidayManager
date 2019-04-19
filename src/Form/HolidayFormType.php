<?php

namespace App\Form;

use App\Entity\Holiday;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HolidayFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("startDate", TextType::class,[
                "label" => "Begin Holiday",
                'attr' => ["class" => "dateBegin"]])
            ->add("endDate", TextType::class, [
                "label" => "End Holiday",
                'attr' => ["class" => "dateEnd"]
            ]);

            /*->add(
                'startDate',
                DateType::class,
                [
                    'label' => 'Start Date',
                    'widget' => 'single_text',
                    'attr' => ['class' => 'dateBegin'],
                    'html5' => false
                ]
            )->add('endDate',
                DateType::class,
                [
                    'label' => 'End Date',
                    'widget' => 'single_text',
                    'attr' => ['class' => 'dateEnd'],
                    'html5' => false
                ]);*/

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
