<?php

namespace App\Form;

use App\DTO\searchHoliday;

use App\Entity\Department;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchHolidayFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate',DateType::class,[
                'widget'    => 'single_text',
                'format'=>'dd-MM-yyyy'
            ])
            ->add('endDate',DateType::class,[
                'widget'    => 'single_text',
                'format'=>'dd-MM-yyyy'
            ])
            ->add('department', EntityType::class,[
                'class' =>  Department::class
            ])
            ->add('role', TextType::class)
            ->add('firstname', TextType::class, [
                'required'=> false
            ])
            ->add('lastname', TextType::class, [
                'required'=> false
            ]);


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => searchHoliday::class
        ]);
    }
}
