<?php

namespace App\Http\Requests\API\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\UniqueUserPhone,
    App\Rules\UniqueUserEmail,
    App\Rules\ExistsUserId;

class UpdateRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $rules = [
            'user_id' => ['required', new ExistsUserId],
            'name' => ['required'],
            'phone' => ['required', 'numeric', new UniqueUserPhone($this->input('user_id'))],
            //'email' => ['required', new UniqueUserEmail($this->input('user_id'))],
            'password' => default_password_validation_array(['required', 'confirmed']), // password_confirmation
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ];
        if ($this->filled('password')) {
            $rules['password'] = default_password_validation_array(['required', 'confirmed']); // password_confirmation
        }
        return $rules;
    }

}
