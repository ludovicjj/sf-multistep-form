<?php

namespace App\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Workflow\Event\GuardEvent;

class BookingFormSubscriber implements EventSubscriberInterface
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.booking.guard.to_confirmed' => ['gardConfirmed'],
        ];
    }

    public function gardConfirmed(GuardEvent $event): void
    {
        $bookingContainer = $event->getSubject();
        $booking = $bookingContainer->booking;


        $email = (new Email())
            ->from('no-reply@booking.com')
            ->to($booking->getEmail())
            ->subject('Récapitulatif réservation')
            ->html('<p>Votre Reservation a bien été prise en compte !</p>');
        ;

        $this->mailer->send($email);
    }
}