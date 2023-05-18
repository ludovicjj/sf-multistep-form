<?php

namespace App\Form\Handler;

use App\Entity\Booking;
use App\Entity\BookingContainer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Workflow\WorkflowInterface;

class MultiStepHandler
{
    public function __construct(
        private RequestStack $requestStack,
        private EntityManagerInterface $entityManager,
        private WorkflowInterface $bookingStateMachine
    )
    {
    }


    public function handle(FormInterface $form, BookingContainer $bookingContainer): bool
    {
        $session = $this->requestStack->getSession();

        if ($form->isSubmitted()) {
            if ($bookingContainer->getCurrentPlace() === 'step2' && $form->get('prev')->isClicked()) {
                $session->set('step', 'step1');

                return true;
            }

            if ($bookingContainer->getCurrentPlace() === 'step3' && $form->get('prev')->isClicked()) {
                $session->set('step', 'step2');

                return true;
            }

            if ($form->isSubmitted() && $form->isValid()) {
                if ($form->getData() instanceof Booking) {
                    $session->set('booking', $form->getData());
                }

                if ($bookingContainer->getCurrentPlace() === 'step1') {
                    $session->set('step', 'step2');

                    return true;
                }

                if ($bookingContainer->getCurrentPlace() === 'step2') {
                    $session->set('step', 'step3');

                    return true;
                }

                if ($bookingContainer->getCurrentPlace() === 'step3') {
                    $this->bookingStateMachine->apply($bookingContainer, 'to_confirmed');
                    $session->set('step', 'confirmed');

                    $booking = $session->get('booking');
                    $this->entityManager->persist($booking);
                    $this->entityManager->flush();

                    return true;
                }
            }
        }

        return false;
    }
}
