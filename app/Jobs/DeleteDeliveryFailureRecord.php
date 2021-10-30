<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DeleteDeliveryFailureRecord implements ShouldQueue
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
        Log::info('-----------DeleteDeliveryFailureRecord-----------');
        $server = config('mail.imap_server');
        $user = config('mail.imap_user');
        $password = config('mail.imap_password');
        $inbox = imap_open("{{$server}}INBOX", $user, $password) or die('Cannot connect: ' . print_r(imap_errors(), true));
        
        Log::info('Start Delete email');
        imap_delete($inbox, 1);

        imap_expunge($inbox);
        imap_close($inbox);
        Log::info('End Delete email');

        Log::info('-----------DeleteDeliveryFailureRecord-----------');
    }
}
