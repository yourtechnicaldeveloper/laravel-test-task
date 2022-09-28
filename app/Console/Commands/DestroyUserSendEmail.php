<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\API\UserController;
class DestroyUserSendEmail extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'destroyuser:sendemail {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $user = User::withTrashed()->find($this->argument('user'));
        if (!blank($user)) {
            // Write External Email API Code Here
            Mail::send('email.destroy_user', ['user' => $user], function ($message) use ($user) {
                $message->to($user->email, $user->name)->subject('Account Deleted');
            });
            if (count(Mail::failures()) <= 0) {
                (new UserController)->SendSMS($user->phone, "Your account is deleted successfully!");
            }
        }
    }

}
