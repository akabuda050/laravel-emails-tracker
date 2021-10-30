<?php

namespace App\Console\Commands;

use App\Jobs\ProcessDeliveryFailure;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckDeliveryFailure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:delivery:failure';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process failed emails';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param  \App\Support\DripEmailer  $drip
     * @return mixed
     */
    public function handle()
    {
        Log::info('-----------NEW CHECK-----------');
        Log::info('-----------CheckDeliveryFailurCommand-----------');
        $server = config('mail.imap_server');
        $user = config('mail.imap_user');
        $password = config('mail.imap_password');
        $inbox = imap_open("{{$server}}INBOX", $user, $password) or die('Cannot connect: ' . print_r(imap_errors(), true));

        /* grab emails */
        $emails = imap_search($inbox, 'SUBJECT "Message Delivery Failure"', 2);

        /* if emails are returned, cycle through each... */
        if ($emails) {
            /* put the newest emails on top */
            rsort($emails);

            /* for every email... */
            foreach ($emails as $email_number) {
                $message = imap_fetchbody($inbox, $email_number, 2);
                $pattern = '/[a-z0-9_\-\+\.]+@[a-z0-9\-]+\.([a-z]{2,4})(?:\.[a-z]{2})?/i';
                preg_match_all($pattern, $message, $matches);
                Log::info($matches);

                if (filter_var($matches[0][0], FILTER_VALIDATE_EMAIL)) {
                    Log::info('Start ProcessDeliveryFailure.');
                    ProcessDeliveryFailure::dispatch(trim(rtrim($matches[0][0])), $email_number)->onQueue('emails-failure');
                }
            }
        }

        imap_close($inbox);

        Log::info('-----------CheckDeliveryFailurCommand-----------');
    }
}
