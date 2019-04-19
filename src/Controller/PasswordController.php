<?php


namespace App\Controller;


use App\DTO\RestorePassword;
use App\Entity\User;
use App\Form\RestorePasswordFormType;
use App\Mailer\PasswordMailer;
use App\Repository\UserRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Twig\Environment;

class PasswordController extends AbstractController
{
    /**
     * @Route("/password/reset")
     */
    public function resetPassword(Environment $twig, Request $request, UserRepository $userRepository,PasswordMailer $mailer){

        $defaultData = ['message' => 'Type your email here'];

        $form = $this->createFormBuilder($defaultData)
            ->add('email', EmailType::class,["constraints"=>[
                new NotBlank()
            ],
                'label' =>  "Email:"])
            ->add("submit",SubmitType::class,["label"=>'Sent'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // data is an array with "name", "email", and "message" keys
            $data = $form->getData();

            $user = $userRepository->findOneBy(["email"=>$data["email"]]);

            if($user)
            {
                $user->setActivationToken(Uuid::uuid4());


                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                $mailer->sendMail($user);

                return new Response($twig->render('User/emailPasswordRestoreSent.html.twig',["user"=>$user]));

            }

        }
        return new Response($twig->render('User/emailPasswordReset.html.twig',["resetPasswordEmailForm"=>$form->createView()]));
    }

    /**
     * @Route("password/restore/{token}",name="restore_password")
     */
    public function restorePassword(Environment $twig, $token,UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepository, Request $request){
        $defaultData = ['message' => 'Type your password here'];

        $restoreData = new RestorePassword();

        $form = $this->createForm(RestorePasswordFormType::class,$restoreData,["standalone"=>true]);

        $form->handleRequest($request);

        $user = $userRepository->findOneByActivationToken($token);


        if ($form->isSubmitted() && $form->isValid()) {
            // data is an array with "name", "email", and "message" keys
            if ($user) {
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $restoreData->password
                    )
                );


                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();


                return new Response($twig->render('User/restorePassword.html.twig', ["user" => $user]));
            }


        }
        else if($user){
            return new Response($twig->render('User/formConfirmPasswordRestore.html.twig',['formRestorePass'=>$form->createView()]));
        }

        return new Response($twig->render('User/errorRestorePassword.html.twig'));
    }

}