<?php

namespace App\Controller;

use App\DTO\searchHoliday;
use App\DTO\UserSearch;
use App\Entity\Holiday;
use App\Entity\Manager;
use App\Entity\User;
use App\Form\SearchHolidayFormType;
use App\Form\UserFormType;
use App\Mailer\RegistrationMailer;
use Knp\Component\Pager\PaginatorInterface;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
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
     * @var Logger
     */
    private $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    /**
     * @Route("/user/create", name="user_create")
     */
    public function createUser(Request $request,
                             UserPasswordEncoderInterface $passwordEncoder,
                             RegistrationMailer $mailer,
                               Environment $twig): Response
    {
        $user = new User();




        $form = $this->createForm(UserFormType::class, $user,['standalone'=>true]);

        $form->handleRequest($request);

        $user->setActivationToken(Uuid::uuid4());
        $user->setActive(false);
        $user->setReferenceYear(new \DateTime("Y"));

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $user->setActivationToken(Uuid::uuid4());
            $user->setActive(false);
            $user->setReferenceYear(new \DateTime("Y"));

            $month = new \DateTime($user->getStartDate()->format("Y-m-d"));
            $endYear = new \DateTime(date("Y-m-d",strtotime("Dec 31")));

            $interval = $endYear->diff($month);

            $user->setHolidayLeft($interval->format("%m")*2);


            if(in_array('ROLE_MANAGER',$user->getRoles()))
            {
                $manager = new Manager();


                $manager->setDepartment($form->get('manageDep')->getData());
                $manager->setManagerUser($user);


                $entityManager->persist($manager);
                $entityManager->flush();
            }



            $mailer->sendMail($user);

            $entityManager->persist($user);
            $entityManager->flush();



            // do anything else you need here, like send an email

            return new Response($twig->render('User/createduser.html.twig',['user'   => $user]));
        }

        return $this->render('User/newuser.html.twig', [
            'createForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/activate/user/{token}", name="activate_user")
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

    /**
     * @Route("/user/delete/{user}",name="delete_user")
     */
    public function deleteUser(User $user,Environment $twig)
    {
        $manager = $this->getDoctrine()->getManager();

        if($user)
        {
            if(in_array("ROLE_MANAGER", $user->getRoles()))
            {
                $managerUser = $manager->getRepository(Manager::class)->findOneByManagerUser($user);

                $manager->remove($managerUser);

            }

            $holidayRepo = $this->getDoctrine()->getRepository(Holiday::class);

            $holidays = $holidayRepo->findByUser($user);

            foreach ($holidays as $holiday){
                $manager->remove($holiday);
            }

            $manager->remove($user);

            $manager->flush();

            return new Response($twig->render('User/deleteConfirmation.html.twig',['user'   =>  $user]));
        }

        return new Response($twig->render('deleteError.html.twig'));

    }

    /**
     * @Route("/user/update/{user}",name="update_user")
     */
    public function updateUser(User $user,Environment $twig, Request $request,
                               UserPasswordEncoderInterface $passwordEncoder)
    {
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


            $user->setReferenceYear(new \DateTime("Y"));




            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            /*if(in_array('ROLE_MANAGER',$user->getRoles()))
            {
                $manager = new Manager();

                $manager->setDepartment($user->getDepartment());
                $manager->setManagerUser($user->getId());

            }
            */
            // do anything else you need here, like send an email

            return new Response($twig->render('User/updateConfirmation.html.twig',['user'   => $user]));
        }

        return $this->render('User/newuser.html.twig', [
            'createForm' => $form->createView(),
        ]);

    }

    /**
     * @Route("/show/user/{user}",name="show_userinfo")
     */
    public function showUserInfo(User $user, Environment $twig)
    {
        return new Response($twig->render('User/showUser.html.twig',["user"=>$user]));
    }


    /**
     * @Route("/search/users",name="search_holiday", methods={"POST"})
     */
    public function searchHoliday(Request $request){
        $searchedHoliday = new searchHoliday();

        $form = $this->createForm(SearchHolidayFormType::class,$searchedHoliday);

        $form->submit($request->request->all());

        $form->handleRequest($request);

        //if($form->isSubmitted() && $form->isValid()) {

            $holidayRepo = $this->getDoctrine()->getRepository(Holiday::class);
            $usersRepo = $this->getDoctrine()->getRepository(User::class);

            $holidays = $holidayRepo->searchedHoliday($searchedHoliday);

            $holidayArray = [];
            $userArray = [];
            $users = [];

            foreach ($holidays as $holiday) {
                $holidayArray[] = [
                    'resourceId' => $holiday->getUser()->getId(),
                    'start' => $holiday->getStartDate(),
                    'end' => $holiday->getEndDate(),
                    'title' => 'Holiday'
                ];
            }

            $searchedUser = new UserSearch();

            $searchedUser->firstname = $searchedHoliday->firstname;
            $searchedUser->lastname = $searchedHoliday->lastname;
            $searchedUser->roles = $searchedHoliday->role;
            $searchedUser->department = $searchedHoliday->department;

            $users = $usersRepo->searchUsersCalendar($searchedUser);

            foreach($users as $user){
                $userArray[] = [
                    'id' => $user->getId(),
                    'building'  => $user->getDepartment()->getLabel(),
                    'title'  => $user->getFirstname() . " " . $user->getLastname()
                ];

            }

            $res[] = [
                'count_result' => count($holidays),
                'holidays' => $holidayArray,
                'users' => $userArray

            ];


            $this->logger->info($searchedHoliday->startDate->format("d-m-Y"));

            return $this->json($res);
        //}
    }
}
