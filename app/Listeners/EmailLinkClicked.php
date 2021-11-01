<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use jdavidbakr\MailTracker\Events\LinkClickedEvent;
use jdavidbakr\MailTracker\Model\SentEmailUrlClicked;

class EmailLinkClicked
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
     * @param  LinkClickedEvent  $event
     * @return void
     */
    public function handle(LinkClickedEvent $event)
    {
        $tracker = $event->sent_email;
        Log::info($tracker);

        $sentEmailUrlClicked = SentEmailUrlClicked::where('hash', '=', $tracker->hash)->first();
        Log::info(($sentEmailUrlClicked->url));

        $model_id = $tracker->getHeader('X-Model-ID');
        $model = User::find($model_id);

        if ($model) {
        }
    }
}
