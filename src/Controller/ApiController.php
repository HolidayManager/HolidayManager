<?php


namespace App\Controller;


use App\Entity\Holiday;
use App\Repository\HolidayRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    /**
     * @Route("/calendar/show/holidays",name="show_calendar_holidays",methods={"GET"})
     */
    public function showCalendarHolidays(Request $request){
        $holidayRep = $this->getDoctrine()->getManager()->getRepository(Holiday::class);
        $holidays =  $holidayRep->getHolidayIn(
            substr($request->query->get('start'),0,11),
            substr($request->query->get('end'),0,11)
        );

        $api = [];

        foreach($holidays as $holiday)
        {
            $api[] = [

                    'resourceId'         =>     $holiday->getUser()->getId(), /*this id will be replaced by the uuid of the user comming from the database*/
                    "title"      =>     '',
                    'start'      =>     $holiday->getStartDate()->format('Y-m-d') . 'T'  . $holiday->getStartDate()->format('H:i:s') . '+00:00',
                    'end'      =>     $holiday->getEndDate()->format('Y-m-d') . 'T'  . $holiday->getEndDate()->format('H:i:s') . '+00:00'

            ];
        }

        return $this->json($api);
    }
}