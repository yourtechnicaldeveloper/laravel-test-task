<?php

namespace App\Traits;

use Carbon\Carbon;

/*
  |--------------------------------------------------------------------------
  | Api Responser Trait
  |--------------------------------------------------------------------------
  |
  | This trait will be used for any response we sent to clients.
  |
 */

trait ApiResponser {

    private $response = null;
    private $code = 200;

    private function set_variables(int $code = 200, string $message = null, $data = null, $extra_data = []) {
        $current_api = get_current_api();
        $object_response_apis = object_response_apis();
        $response_data = (in_array($current_api, $object_response_apis) && blank((array) $data)) ? (object) [] : ((in_array($code, [200])) ? replace_null_with_empty_string($data) : collect($data)->toArray());
        $this->code = intval($code);
        $this->response = array_merge([
            "status" => $this->code,
            "message" => trim($message),
            "data" => $response_data
                ], replace_null_with_empty_string($extra_data));
    }

    private function return() {
        $ApiLog = request()->get('ApiLog', null);
        if (!blank($ApiLog)) {
            $ApiLog->fill(['response_data' => $this->response])->save();
        }
        return response()->json($this->response, $this->code);
    }

    protected function success($data = null, string $message = "Success.", int $code = 200, $extra_data = []) {
        $this->set_variables($code, $message, $data, $extra_data);
        return $this->return();
    }

    protected function error(int $code, string $message = null, $data = null, $extra_data = []) {
        $this->set_variables($code, $message, $data, $extra_data);
        return $this->return();
    }

    protected function validation_error(string $message = null, int $code = 422, $data = null, $extra_data = []) {
        $this->set_variables($code, $message, $data, $extra_data);
        return $this->return();
    }

}
