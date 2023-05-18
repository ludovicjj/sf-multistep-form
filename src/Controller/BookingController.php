<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\BookingContainer;
use App\Form\Handler\MultiStepHandler;
use App\Form\Type\Step1Type;
use App\Form\Type\Step2Type;
use App\Form\Type\Step3Type;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    #[Route('/book', name: 'app_booking')]
    public function book(
        Request $request,
        MultiStepHandler $stepHandler,
    ): Response
    {
        $session = $request->getSession();

        $step = $session->get('step', 'step1');
        $booking = $session->get('booking');

        if ($booking === null) {
            $booking = new Booking();
        }

        $form = null;
        $forms = [
            'step1' => ['class' => Step1Type::class, 'data' => $booking, 'options' => []],
            'step2' => ['class' => Step2Type::class, 'data' => $booking, 'options' => []],
            'step3' => ['class' => Step3Type::class, 'data' => null, 'options' => []],
        ];


        $bookingContainer = new BookingContainer($booking, $forms);
        $bookingContainer->setCurrentPlace($step);

        $formDef = $forms[$bookingContainer->getCurrentPlace()] ?? null;

        if ($formDef) {
            $form = $this->createForm($formDef['class'], $formDef['data'], $formDef['options']);
            $form->handleRequest($request);

            if ($stepHandler->handle($form, $bookingContainer)) {
                return $this->redirectToRoute('app_booking');
            }

        }

        //$transitions = $workflow->getEnabledTransitions($bookingContainer);
        //dump($session->get('booking'));

        return $this->render('booking/booking.html.twig', [
            'booking' => $booking,
            'form' => $form,
            'bookingContainer' => $bookingContainer,
        ]);
    }

    #[Route('/book/new', name: 'app_booking_add')]
    public function addBook(Request $request): RedirectResponse
    {
        $session = $request->getSession();
        $session->remove('booking');
        $session->remove('step');

        return $this->redirectToRoute('app_booking');
    }
}