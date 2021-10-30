<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $this->readAndCreate('data/emails/casino1.txt', 'casino');
        $this->readAndCreate('data/emails/forex.txt', 'forex');
        $this->readAndCreate('data/emails/investors.txt', 'investors');
    }

    /**
     * 
     */
    private function readAndCreate($path, $type)
    {
        $content = fopen(Storage::path($path), 'r');
        $i = 0;
        while (!feof($content)) {
            $line = trim(rtrim(fgets($content)));
            echo $line . "\n";
            if (
                filter_var($line, FILTER_VALIDATE_EMAIL) &&
                !User::where('email', '=', $line)->first() &&
                strpos($line, 'abuse') === false &&
                strpos($line, 'info') === false &&
                strpos($line, 'support') === false
            ) {
                User::factory([
                    'name' => $line,
                    'email' => $line,
                    'user_type' => $type,
                    'email_verified_at' => null,
                ])->create();
            }
        }

        fclose($content);
    }
}
