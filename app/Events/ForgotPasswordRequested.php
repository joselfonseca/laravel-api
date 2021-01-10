<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordRequested
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }
}
