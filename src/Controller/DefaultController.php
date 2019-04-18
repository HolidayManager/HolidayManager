<?php

namespace App\Controller;


use App\Entity\Department;
use App\Entity\Holiday;
use App\Entity\Manager;
use App\Form\HolidayFormType;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\Logger;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;


class DefaultController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboard(UserRepository $userRepository, Request $request, Environment $twig, PaginatorInterface $paginator):Response
    {
        $user = $this->getUser();
        $userList = null;
        $pending = null;

        if(in_array('ROLE_ADMIN',$user->getRoles()))
        {
            $userList = $userRepository->findPaginated(
              $request, $paginator
            );
        }

        if(in_array('ROLE_MANAGER',$user->getRoles()))
        {
            $holidayRep = $this->getDoctrine()->getRepository(Holiday::class);
            $managerRepo = $this->getDoctrine()->getRepository(Manager::class);


            $manager = $managerRepo->findOneByManagerUser($user);

            $pending = $holidayRep->getPending($manager->getDepartment()->getId());





        }



        $holiday = new Holiday();

        $form = $this->createForm(HolidayFormType::class, $holiday,['standalone'=>true]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $holiday->setDateRequest(new \DateTime());
            $holiday->setUser($this->getUser());
            $holiday->setStatus('p');

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($holiday);
            $entityManager->flush();
        }



        //$searchForm['name'], 'searchForm';
        $defaultData = ['message' => 'Search users'];

        $searchForm = $this->createFormBuilder($defaultData)
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
            ->add('firstname', TextType::class, ["label"    =>  "Firstname"])
            ->add('lastname', TextType::class, ["label"     =>  "Lastname"])
            ->add('submit', SubmitType::class, ['label' => 'Search'])
            ->getForm();

        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            
        }

        return $this->render('dashboard.html.twig', [
            'user' => $user,
            'users' => $userList,
            'formHoliday' => $form->createView(),
            'pending' => $pending,
            'searchBar' => $searchForm->createView()
        ]);

    }

    /**
     * @Route("/logout",name="app_logout")
     */
    public function logout(){

    }

    /**
    * @Route("/whoisoff", name="whoisoff")
    */

    public function whoisoff(Environment $twig) {
      return new Response($twig->render('whoisoff.html.twig'));
    }



}
