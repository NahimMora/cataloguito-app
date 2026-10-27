<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;

class OrderRequested extends Mailable
{
    public function __construct(public Order $order) {}

    public function build(): self
    {
        return $this->subject('Nueva solicitud '.$this->order->displayNumber().' · '.$this->order->shop->name)->view('emails.order');
    }
}
