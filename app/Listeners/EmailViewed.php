<?php

namespace App\Listeners;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use jdavidbakr\MailTracker\Events\ViewEmailEvent;

class EmailViewed
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
     * @param  ViewEmailEvent  $event
     * @return void
     */
    public function handle(ViewEmailEvent $event)
    {
        Log::info($event->sent_email);

        $tracker = $event->sent_email;
        $model_id = $tracker->getHeader('X-Model-ID');
        $model = User::find($model_id);

        if ($model) {
            $model->email_is_alive = true;
            $model->save();
        }
    }
}
