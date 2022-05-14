<?php

namespace App\Mail;

use App\Models\NewsLetter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsLetterController extends Mailable
{
    use Queueable, SerializesModels;

    public $newsLetter;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(NewsLetter $newsLetter)
    {
        $this->newsLetter = $newsLetter;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.template',[
            //'titre' => 'Inscription à la formation '.$info[0]->title,
            'titre' => 'NewsLetters!',
            'description' => 'Merci pour votre inscription à notre newsletter'
        ])
            ->subject("TEAM VASIA Newsletter");
    }
}
