<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use App\Http\Requests\API\User\StoreRequest,
    App\Http\Requests\API\User\UpdateRequest;
use Twilio\Rest\Client;

class UserController extends Controller {

    use ApiResponser;

    public function SendSMS($mobile_number, $body) {
        try {
            // Set Twilio Credentials to .env file
            $client = new Client(config('app.twilio.TWILIO_ACCOUNT_SID'), config('app.twilio.TWILIO_AUTH_TOKEN'));
            $client->messages->create($mobile_number, [
                'from' => config('app.twilio.TWILIO_NUMBER'),
                'body' => $body
            ]);
            return null;
        } catch (\Twilio\Exceptions\EnvironmentException $ex) {
            return $ex->getMessage();
        } catch (\Twilio\Exceptions\RestException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return $this->success(get_paginate_array(new User()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request) {
        $input = $request->only('name', 'phone', 'email', 'password', 'photo');
        $user = User::create($input);
        return $this->success($user, $this->SendSMS($input['phone'], "Congratulation, Your account is created successfully!"));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request) {
        return $this->success(User::find($request->input('user_id')));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request) {
        $user = User::find($request->input('user_id'));
        $user->fill($request->only('name', 'phone', 'password', 'photo'))->save();
        return $this->success($user, "Your account has been updated successfully!");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request) {
        $User = User::find($request->input('user_id'));
        if (!blank($User)) {
            $id = $User->id;
            $phone = $User->phone;
            $User->delete();
            if (substr(php_uname(), 0, 7) == "Windows") {
                $cmd = "php " . base_path() . "\artisan destroyuser:sendemail {$id}";
                pclose(popen("start /B " . $cmd, "r"));
            } else {
                $cmd = "/usr/bin/php " . base_path() . "/artisan destroyuser:sendemail {$id}";
                exec($cmd . " > /dev/null &");
            }
            return $this->success(null, "Your account is deleted successfully!");
        }
        return $this->validation_error("Invalid User.");
    }

}
