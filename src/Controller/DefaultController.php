<?php

namespace App\Controller;


use App\DTO\UserSearch;
use App\Entity\Department;
use App\Entity\Holiday;
use App\Entity\Manager;
use App\Entity\User;
use App\Form\HolidayFormType;
use App\Form\SearchUserListFormType;
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




        $userSearched = new UserSearch();

        $searchForm = $this->createForm(SearchUserListFormType::class, $userSearched,["standalone"=>true]);

        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $data = $searchForm->getData();

            $userRepository = $this->getDoctrine()->getRepository(User::class);

            $userList = $userRepository->searchUsers($userSearched, $paginator, $request);

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
      return new Response($twig->render('whoisoff.html.twig',["user"=>$this->getUser()]));
    }



}
