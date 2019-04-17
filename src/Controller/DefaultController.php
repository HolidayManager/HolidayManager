<?php

namespace App\Controller;


use App\Entity\Holiday;
use App\Form\HolidayFormType;
use App\Repository\UserRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;


class DefaultController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboard(UserRepository $userRepository, Request $request, Environment $twig):Response
    {
        $user = $this->getUser();
        $userList = null;

        if(in_array('ROLE_ADMIN',$user->getRoles()))
        {
            $userList = $userRepository->findAll();
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


        return new Response($this->render('dashboard.html.twig', [
            'user' => $user,
            'users' => $userList,
            'formHoliday' => $form->createView()
        ]));
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
