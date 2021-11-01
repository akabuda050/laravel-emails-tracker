<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MoveDeliveryFailureRecord implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $id = null;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('-----------MoveDeliveryFailureRecord-----------');
        $server = config('mail.imap_server');
        $user = config('mail.imap_user');
        $password = config('mail.imap_password');
        $inbox = imap_open("{{$server}}INBOX", $user, $password) or die('Cannot connect: ' . print_r(imap_errors(), true));

        Log::info('Start Moving email');

        $status = imap_status($inbox, "{{$server}}INBOX.bounced", SA_ALL);
        $mailboxIsFound = $status ? true : false;
        $mailboxName = 'INBOX.bounced';
        if (!$mailboxIsFound) {
            imap_createmailbox($inbox, imap_utf7_encode("{{$server}}$mailboxName"));
        }

        imap_mail_move($inbox, $this->id, $mailboxName);

        imap_expunge($inbox);
        imap_close($inbox);
        Log::info('End Moving email');

        Log::info('-----------MoveDeliveryFailureRecord-----------');
    }
}
