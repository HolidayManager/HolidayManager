<?php

namespace App\Controller;


use App\Entity\Holiday;
use App\Entity\Manager;
use App\Form\HolidayFormType;
use App\Repository\HolidayRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
            $holidayRep = $this->getDoctrine()->getRepository(HolidayRepository::class);

            $pending = $holidayRep->getPending($user->getDepartment()->getId());





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

        return $this->render('dashboard.html.twig', [
            'user' => $user,
            'users' => $userList,
            'formHoliday' => $form->createView(),
            'pending' => $pending
        ]);

        $defaultData = ['message' => 'Type your message here'];
//        $searchForm['name'], 'searchForm';
        $searchForm = $this->createFormBuilder($defaultData)
            ->add('department', ChoiceType::class)
            ->add('roles', ChoiceType::class)
            ->add('submit', SubmitType::class, ['label' => 'Search'])
            ->getForm();
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {

        }

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
