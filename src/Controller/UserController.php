<?php

namespace App\Controller;

use App\Entity\Manager;
use App\Entity\User;
use App\Form\UserFormType;
use App\Mailer\RegistrationMailer;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Twig\Environment;

class UserController extends AbstractController
{
    /**
     * @Route("/create/user", name="user_create")
     */
    public function createUser(Request $request,
                             UserPasswordEncoderInterface $passwordEncoder,
                             RegistrationMailer $mailer,
                                Environment $twig): Response
    {
        $user = new User();




        $form = $this->createForm(UserFormType::class, $user,['standalone'=>true]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $user->setActivationToken(Uuid::uuid4());
            $user->setActive(false);
            $user->setReferenceYear(new \DateTime("Y"));

            $mailer->sendMail($user);


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            if(in_array('ROLE_MANAGER',$user->getRoles()))
            {
                $manager = new Manager();

                $manager->setDepartment($user->getDepartment());
                $manager->setManagerUser($user->getId());

            }

            // do anything else you need here, like send an email

            return new Response($twig->render('User/createduser.html.twig',['user'   => $user]));
        }

        return new Response($this->render('User/newuser.html.twig', [
            'createForm' => $form->createView(),
        ]));
    }

    /**
     * @Route("/user/activate/{token}", name="activate_user")
     */
    public function activateToken(
        string $token,
        TokenStorageInterface $tokenStorage,
        Environment $twig
    ){

        $manager = $this->getDoctrine()->getManager();

        $userRepository = $manager->getRepository(User::class);

        $user = $userRepository->findOneByActivationToken($token);

        if(!$user)
        {
            throw new NotFoundHttpException('User not found');
        }

        $user->setActivationToken($token)
            ->setActive(true);

        $manager->flush();

        $tokenStorage->setToken(new UsernamePasswordToken($user,null,'main',$user->getRoles()));

        return new Response($twig->render('User/activateduser.html.twig',["user" => $user]));
    }
}
