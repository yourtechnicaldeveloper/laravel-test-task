<?php

function default_password_validation_array(array $rules = []) {
    return array_merge($rules, [
        'min:8',
//        'min:10',
//        'max:12',
//        'regex:/^.*(?=.{3,})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$/',
//        'regex:/^.*(?=.{3,})(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%@]).*$/',
    ]);
}

function get_current_api() {
    return (collect(request()->segments())->last());
}

function object_response_apis() {
    return [
        'users-list',
        'get-user'
    ];
}

function replace_null_with_empty_string($value) {
    $return = collect($value)->toArray();
    array_walk_recursive($return, function (&$item, $key) {
        $item = (null === $item) ? '' : ((is_array($item) || is_object($item)) ? replace_null_with_empty_string($item) : $item);
    });
    return $return;
}

function api_response($code, $message, $data = [], $extra_data = []) {
    $current_api = get_current_api();
    $object_response_apis = object_response_apis();
    $response_data = (in_array($current_api, $object_response_apis) && blank((array) $data)) ? (object) [] : replace_null_with_empty_string($data);
    $Response = array_merge([
        "status" => intval($code),
        "message" => trim($message),
        "msg" => trim($message),
        "data" => $response_data
            ], replace_null_with_empty_string((in_array($current_api, ['login'])) ? array_merge(['token' => ''], $extra_data) : $extra_data));
    $ApiLog = request()->get('ApiLog', null);
    if (!blank($ApiLog)) {
        $ApiLog->fill(['response_data' => $Response])->save();
    }
    return response($Response, $code);
}

function exception_message($e) {
    return ((isset($e->errorInfo[2]) && !blank($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage());
}

function upload_files($request, $input_name, $file_prefix, $directory, $default = NULL, $disk = "uploads") {
    $file_name = $default;
    if ($request->hasFile($input_name)) {
        $file_object = $request->file($input_name);
        if (is_array($file_object)) {
            $file_name = [];
            foreach ($file_object AS $key => $File) {
                $counter = ($key + 1);
                $file_name[$key] = $file_prefix . "_{$counter}_" . time() . "." . $File->getClientOriginalExtension();
                \Illuminate\Support\Facades\Storage::disk($disk)->putFileAs($directory, $File, $file_name[$key]);
            }
        } else {
            $file_name = $file_prefix . "_" . time() . "." . $file_object->getClientOriginalExtension();
            \Illuminate\Support\Facades\Storage::disk($disk)->putFileAs($directory, $file_object, $file_name);
        }
    }
    return $file_name;
}

function uploaded_file_url($file_name, $directory, $disk = "uploads") {
    $file_url = null;
    if (!empty($file_name) && \Illuminate\Support\Facades\Storage::disk($disk)->exists($directory . "/" . $file_name)) {
        $file_url = \Illuminate\Support\Facades\Storage::disk($disk)->url($directory . "/" . $file_name);
    }
    return $file_url;
}

function file_url($file_url) {
    if (!empty($file_url)) {
        $segment = request()->segment(1);
        $file_url = route($segment . '.file', \Illuminate\Support\Facades\Crypt::encryptString(collect(explode('/', $file_url))->take(-2)->implode('/')));
    }
    return $file_url;
}

function file_name_from_url($file_url) {
    try {
        $decrypt = \Illuminate\Support\Facades\Crypt::decryptString(last(explode('/', $file_url)));
    } catch (\Illuminate\Contracts\Encryption\DecryptException $ex) {
        $decrypt = "";
    }
    return last(explode('/', $decrypt));
}

function format_date($date, $format = DEFAULT_DATE_FORMAT) {
    if (!blank($date)) {
        return \Carbon\Carbon::parse($date)->format($format);
    }
    return $date;
}

function ordinal_suffix_of($i) {
    $j = $i % 10;
    $k = $i % 100;
    $suffix = "th";
    if ($j == 1 && $k != 11) {
        $suffix = "st";
    }
    if ($j == 2 && $k != 12) {
        $suffix = "nd";
    }
    if ($j == 3 && $k != 13) {
        $suffix = "rd";
    }
    return $i . "'" . $suffix;
}

function format_amount($amount, $currency = DEFAULT_CURRENCY_SYMBOL) {
    return $currency . number_format($amount, 2);
}

function get_paginate_array($query, $limit = 10) {
    $results = $query->paginate($limit)->toArray();
    $results['to'] = intval($results['to']);
    $results['from'] = intval($results['from']);
    return $results;
}

if (!function_exists('handle_artisan_command')) {

    function handle_artisan_command(string $signature, callable $fnHandel) {
        $commandcheck = \App\Models\CommandLog::where('command_name', $signature)->exists();
        if (!$commandcheck) {
            $CommandManager = \App\Models\CommandLog::create([
                        'command_name' => $signature
            ]);
            try {
                $fnHandel();
            } catch (\Exception $ex) {
                $CommandManager->fill(['error_exception' => $ex])->save();
            }
            $CommandManager->delete();
        }
    }

}
