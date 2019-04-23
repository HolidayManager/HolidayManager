<?php

namespace App\Controller;


use App\DTO\HolidayRequest;
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
     * @Route("/",name="homepage")
     */
    public function homepage(){
        return $this->render('homepage.html.twig');
    }

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboard(UserRepository $userRepository, Request $request, Environment $twig, PaginatorInterface $paginator):Response
    {
        $user = $this->getUser();
        $userList = null;
        $pending = null;
        $infoHoliday = [];
        $lastRequestHoliday = null;

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

        if(in_array("ROLE_USER",$user->getRoles())){
            $holidayInfo = $this->getDoctrine()->getManager()->getRepository(Holiday::class);

            $lastRequestHoliday = $holidayInfo->lastRequested($user);

            $info = [
                "holidaySpent" => count($holidayInfo->spentCurrentYear($user)),
                "holidayInProgram"=> count($holidayInfo->toSpentYear($user)),
                "holidayLeft"   => $user->getHolidayLeft()
            ];
        }



        $holiday = new Holiday();
        $holidayFeedback=null;

        $holidayRequest = new HolidayRequest();

        $form = $this->createForm(HolidayFormType::class, $holidayRequest,['standalone'=>true]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $startDate = clone $holidayRequest->startDate;
            $endDate = clone $holidayRequest->endDate;

            $today=new \DateTime();

            if($startDate>=$today&&$endDate>=$startDate)
            {

                /*if(\DateTime::createFromFormat('m/d/Y', $startDate) !== false &&
                    \DateTime::createFromFormat('m/d/Y', $endDate) !== false)
                {*/

                    $countHolidays=0;
                    while($startDate<=$endDate){
                        if($startDate->format("N")!="6"||$startDate->format("N")!="7")
                            $countHolidays++;
                        $startDate->add(new \DateInterval("P1D"));
                    }



                    if($countHolidays<=$user->getHolidayLeft())
                    {
                        $holiday->setDateRequest(new \DateTime());
                        $holiday->setUser($this->getUser());
                        $holiday->setStatus('p');

                        $holiday->setStartDate($holidayRequest->startDate);
                        $holiday->setEndDate($holidayRequest->endDate);

                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->persist($holiday);
                        $entityManager->flush();

                        $holidayFeedback = true;

                    }

                //}

            }

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
            'searchBar' => $searchForm->createView(),
            'infoHoliday'   => $info,
            'holidayFeedback' => $holidayFeedback,
            'lastRequestedHoliday' => $lastRequestHoliday
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
