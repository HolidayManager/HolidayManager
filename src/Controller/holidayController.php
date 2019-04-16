<?php

namespace App\Controller;

use App\Entity\Holiday;
use App\Form\HolidayFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class holidayController extends AbstractController
{
    /**
     * @Route("/holiday/create", name="holiday_create")
     */

    public function createHoliday(Request $request, Environment $twig)
    {
        $holiday = new Holiday();

        $form = $this->createForm(HolidayFormType::class, $holiday,['standalone'=>true]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $holiday->setDateRequest(\DateTime());
            $holiday->setUser($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($holiday);
            $entityManager->flush();
        }


        return new Response($twig->render('dashboard.html.twig',['formHoliday'   => $form]));
    }

    /**
     * @Route("/holiday/delete/{holiday}", name="holiday_delete")
     */

    public function deleteHoliday(Holiday $holiday)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($holiday);
        $manager->flush();
    }


}