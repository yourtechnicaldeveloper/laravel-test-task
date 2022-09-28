<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\User;

class UniqueUserEmail implements Rule {

    private $ignore_id = 0;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(int $UserID = 0) {
        $this->ignore_id = $UserID;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value) {
        $User = User::where('users.email', $value);
        if ($this->ignore_id > 0) {
            $User->where('users.id', '<>', $this->ignore_id);
        }
        return (!$User->exists());
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return 'The email address has already been taken.';
    }

}
