<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class OrderFailedMail extends Mailable
{
    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function build()
    {
        return $this->subject('Payment Failed')
            ->view('emails.order-failed');
    }
}
