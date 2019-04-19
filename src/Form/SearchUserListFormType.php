<?php

namespace App\Form;

use App\DTO\UserSearch;
use App\Entity\Department;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchUserListFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('department', EntityType::class, [
                "class" =>  Department::class,
                "choice_label"  =>  "label",
                "expanded"  =>  false,
                "multiple"  =>  false
            ])
            ->add('roles', ChoiceType::class, [
                'expanded'  =>  false,
                'multiple'  =>  false,
                'choices'   => [
                    'Employee' => 'ROLE_USER',
                    'Manager'   => 'ROLE_MANAGER',
                    'Admin' =>  'ROLE_ADMIN'
                ]
            ])
            ->add('firstname', TextType::class, ["label"    =>  "Firstname", "required"=>false])
            ->add('lastname', TextType::class, ["label"     =>  "Lastname", "required"=>false])
        ;

        if($options['standalone']==true)
        {
            $builder->add("search", SubmitType::class,["label"=>"Search"]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserSearch::class,
            'standalone' => false
        ]);
    }
}
