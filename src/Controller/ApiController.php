<?php


namespace App\Controller;


use App\Entity\Holiday;
use App\Entity\User;
use App\Repository\HolidayRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    /**
     * @Route("/calendar/show/holidays",name="show_calendar_holidays",methods={"GET"})
     */
    public function showCalendarHolidays(Request $request)
    {
        $start = $request->query->get('start');
        $end = $request->query->get('end');
        $holidayRep = $this->getDoctrine()->getManager()->getRepository(Holiday::class);

        $holidays =  $holidayRep->getHolidayIn(
            substr($start,0,strpos($start, 'T')),
            substr($end,0,strpos($end, 'T'))
        );

        $api = [];

        foreach($holidays as $holiday)
        {
            $api[] = [

                    'resourceId'         =>     $holiday->getUser()->getId(), //this id will be replaced by the uuid of the user comming from the database
                    "title"      =>     'Holiday',
                    'start'      =>     $holiday->getStartDate()->format('Y-m-d') . 'T'  . $holiday->getStartDate()->format('H:i:s') . '+00:00',
                    'end'      =>     $holiday->getEndDate()->format('Y-m-d') . 'T'  . $holiday->getEndDate()->format('H:i:s') . '+00:00'

            ];
        }


        return $this->json($api);
    }

    /**
     * @Route("/calendar/show/users",name="show_calendar_department")
     */

    public function showUCalendarsers(Request $request)
    {
        $userRepo = $this->getDoctrine()->getManager()->getRepository(User::class);

        $users = $userRepo->findByDepartment($this->getUser()->getDepartment());

        $api = [];

        foreach($users as $user)
        {
            $api[] = [
                'id' => $user->getId(),
                'building'  => $user->getDepartment()->getLabel(),
                'title' => $user->getFirstname() . " " . $user->getLastname()
            ];
        }

        return $this->json($api);

    }
}