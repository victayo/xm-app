<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class XMMail extends Mailable
{
    use Queueable, SerializesModels;

    private $startDate;
    private $endDate;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($startDate, $endDate, $subject)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.x-m-mail', ['start_date' => $this->startDate,
        'end_date' => $this->endDate])->subject($this->subject);
    }
}
