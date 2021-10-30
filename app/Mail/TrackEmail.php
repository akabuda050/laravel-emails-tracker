<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TrackEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    private $model = null;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->from('info@redbit.club', 'RedBit Club');
        $this->subject('Welcome To RedBit Club!');

        $this->withSwiftMessage(function ($message) {
            $message->getHeaders()->addTextHeader('X-Model-ID', $this->model->id);
        });
        
        return $this->markdown('emails.check', ['modelId' => $this->model->id]);
    }
}
