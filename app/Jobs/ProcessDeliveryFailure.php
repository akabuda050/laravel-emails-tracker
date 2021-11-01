<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessDeliveryFailure implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $address = null;
    private $id = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($address, $id)
    {
        $this->address = $address;
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('-----------ProcessDeliveryFailure-----------');
        Log::info($this->address);

        if (filter_var($this->address, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', '=', $this->address);
            if ($user) {
                Log::info('Start delete processing.');
                $user->email_is_alive = false;
                $user->delete();

                Log::info('User marked as deleted.');
            }

            Log::info('Start MoveDeliveryFailureRecord.');
            MoveDeliveryFailureRecord::dispatch($this->id);
        }

        Log::info('-----------ProcessDeliveryFailure-----------');
    }
}
