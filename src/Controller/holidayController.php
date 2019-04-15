<?php

namespace App\Controller;

use App\Entity\Holiday;
use App\Form\HolidayFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class holidayController extends AbstractController
{
    /**
     * @Route("/holiday/create", name="holiday_create", methods={"CREATE"})
     */

    public function createHoliday(Request $request, Environment $twig)
    {
        $holiday = new Holiday();

        $form = $this->createForm(HolidayFormType::class, $holiday,['standalone'=>true]);

        $form->submit($request->request->all());

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($holiday);
            $entityManager->flush();
        }


        return new Response($twig->render('createHoliday.html.twig',['holiday'   => $holiday]));
    }

    /**
     * @Route("/holiday/{holiday}", name="holiday_delete", methods={"DELETE"})
     */

    public function deleteHoliday(Holiday $holiday)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($holiday);
        $manager->flush();
    }
}