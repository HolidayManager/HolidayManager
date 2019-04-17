<?php

namespace App\Form;

use App\Entity\Department;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Role\RoleHierarchy;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {



        $builder
            ->add('username',TextType::class,['label' => 'Username'])
            ->add('roles', ChoiceType::class,[
                'expanded'  =>  false,
                'multiple'  =>  false,
                'choices'   => [
                    'Employee' => 'ROLE_USER',
                    'Manager'   => 'ROLE_MANAGER',
                    'Admin' =>  'ROLE_ADMIN'
                ]
            ])
            ->add('password',RepeatedType::class,[
                'type'      =>  PasswordType::class,
                'label'   =>    'Password',
                'first_options' =>  ['label'    =>  'Password'],
                'second_options' =>  ['label'    =>  'Repeat Password'],
        ])
            ->add('firstname',TextType::class,["label" =>   'Firstname'])
            ->add('lastname', TextType::class, ['label' =>  'Lastname'])
            ->add('email',EmailType::class, ['label'    =>  'Email'])
            ->add('birthDate', DateType::class, ['label'    =>  'Birth Date'])
            ->add('startDate',DateType::class, ['label' =>  'Begin Date'])
            ->add('holidayLeft', IntegerType::class, ['label'   =>  'Holidays Left'])
            ->add('department',EntityType::class, [
                'class'     =>      Department::class,
                'choice_label'  =>  'label',
                'expanded'      =>  false,
                'multiple'      =>  false,
                'label'     =>      'Department'

            ]);
        $builder->get('roles')->resetViewTransformers();
        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    // transform the array to a string
                    return count($rolesArray)? $rolesArray[0]: null;
                },
                function ($rolesString) {
                    // transform the string back to an array
                    return $rolesString;
                }
            ));

        $builder->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event){
            $data = $event->getData();
            $data->setRoles([$data->getRoles()]);
        });

        if($options['standalone']==true)
        {
            $builder->add('submit',SubmitType::class);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'standalone' => false
        ]);
    }
}
