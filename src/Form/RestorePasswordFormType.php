<?php

namespace App\Form;

use App\DTO\RestorePassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class RestorePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class,[
                'type'      =>  PasswordType::class,
                'constraints'   =>   new Regex(
                    [
                        'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&,])[A-Za-z\d@$!%*?&, ]{8,}$/',
                        'message' => "Password have to contain minimum eight characters, at least one uppercase letter, one lowercase letter, one number and one special character"

                    ]),
                'label'   =>    'New Password',
                'first_options' =>  ['label'    =>  'Password'],
                'second_options' =>  ['label'    =>  'Repeat Password']
            ]);

        if($options['standalone']==true){
            $builder->add("Reset",SubmitType::class);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'    => RestorePassword::class,
            'standalone'    =>  false
            // Configure your form options here
        ]);
    }
}
