<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DefaultMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * DefaultMail constructor.
     * @param $data
     *
     * return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->view('emails.default')->subject($this->data['subject'])->with(['title' => $this->data['subject'],'content' => $this->data['content'],'name' => $this->data['name']]);

        if(!is_null($this->data['file'])){
          $mail->attach($this->data['file'], [
            'as' => $this->data['fileName'],
            'mime' => 'application/pdf',
          ]);
        }

        return $mail;
    }
}
