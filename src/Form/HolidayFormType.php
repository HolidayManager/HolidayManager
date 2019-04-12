<?php

namespace App\Form;

use App\Entity\Holiday;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
            ->add('startDate', TextType::class, ['label' => 'Start Date'])
            ->add('endDate', TextType::class, ['label' => 'End Date'])
            ->add('dateRequest', TextType::class, ['label' => 'Date Request']);
        
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
