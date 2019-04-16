<?php

namespace App\Controller;


use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DefaultController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboard(UserRepository $userRepository):Response
    {
        $user = $this->getUser();
        $userList = null;

        if(in_array('ROLE_ADMIN',$user->getRoles()))
        {
            $userList = $userRepository->findAll();
        }

        return new Response($this->render('dashboard.html.twig', [
            'user' => $user,
            'users' => $userList
        ]));
    }

    /**
     * @Route("/logout",name="app_logout")
     */
    public function logout(){

    }

}
