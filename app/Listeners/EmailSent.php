<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use jdavidbakr\MailTracker\Events\EmailSentEvent;

class EmailSent
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  EmailSentEvent  $event
     * @return void
     */
    public function handle(EmailSentEvent $event)
    {
        Log::info($event->sent_email);

        $tracker = $event->sent_email;
        $model_id = $tracker->getHeader('X-Model-ID');
        $model = User::find($model_id);

        if ($model) {
            $model->track_email_is_sent = true;
            $model->save();
        }
    }
}
