<?php

namespace App\Entity;

class BookingContainer
{
    public Booking $booking;

    public array $forms;

    /* workflow marking store */
    private $currentPlace;


    public function __construct(Booking $booking, array $forms)
    {
        $this->booking = $booking;
        $this->forms = $forms;
    }

    public function getCurrentPlace()
    {
        return $this->currentPlace;
    }

    public function setCurrentPlace($currentPlace, $context = [])
    {
        $this->currentPlace = $currentPlace;
    }
}