<?php


namespace App\Controller;


use App\Entity\Holiday;
use App\Entity\User;
use App\Repository\HolidayRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class apiController extends AbstractController
{
    /**
     * @Route("/calendar/show/holidays",name="show_calendar_holidays",methods={'GET'})
     */
    public function showCalendarHolidays(Request $request,HolidayRepository $holidayRep, UserRepository $userRep){
        $holidayRep = $this->getDoctrine()->getManager()->getRepository(Holiday::class);
        $userRep = $this->getDoctrine()->getRepository(User::class);

        $start = $request->query->get('start');
        $end = $request->query->get('end');

        $qb = $holidayRep->createQueryBuilder('h')
            ->andWhere('h.start_date=:start')
            ->andWhere('h.end_date=:end')
            ->andWhere('h.status=:status')
            ->setParameter('start', $start)
            ->setParameter('end',$end)
            ->setParameter('status','a')
            ->getQuery();

        $holidays =  $qb->execute();


        $api = [];

        foreach($holidays as $holiday)
        {
            $api[] = [

                    'resourceId'         =>     $holiday->getUser()->getId(), /*this id will be replaced by the uuid of the user comming from the database*/
                    "title"      =>     '',
                    'start'      =>     substr($holiday->getStartDate(),0,10) . 'T'  . substr($holiday->getStartDate(),10,8),
                    'end'        =>     substr($holiday->getEndDate(),0,10) . 'T'  . substr($holiday->getEndDate(),10,8)

            ];
        }

        return $this->json($api);
    }
}